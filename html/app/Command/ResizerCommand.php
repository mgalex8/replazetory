<?php
namespace App\Command;

use File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ResizerCommand extends Command
{
    /**
     * Directories
     * @var string
     */
    protected $src;
    protected $dest;

    /**
     * @var any
     */
    protected $maxwidth = 1024;
    protected $maxheight = 800;
    protected $quality = 70;
    protected $output_format = 'jpg';
    protected $chunks = 100;
    protected $supported_format = ['jpg', 'jpeg', 'jpe', 'png', 'webp', 'gif'];
    protected $mask = '*';

    /**
     * @var array
     */
    protected $files;

    /**
     * In this method setup command, description, and its parameters
     */
    protected function configure()
    {
        $this->setName('resizer');
        $this->addArgument('dir', InputArgument::REQUIRED);
        $this->addOption('mask', '*', InputOption::VALUE_OPTIONAL);
        $this->addOption('q', 70, InputOption::VALUE_OPTIONAL);
        $this->addOption('f');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->src = $input->getArgument('dir');
        $this->dest = $input->getArgument('dir').'/../dest';

        $this->mask = $input->getOption('mask') ?: '*.png';
        $this->output_format = $input->getOption('f');

        if (!file_exists($this->src)) {
            mkdir($this->src, 0755, true);
        }
        if (!file_exists($this->dest)) {
            mkdir($this->dest, 0755, true);
        }

        $this->search_files($this->src, $this->mask);

        $output->write("\nPREPARE " . count($this->files) . " FILES\n");

        // create progress bar
        //$bar = $this->output->createProgressBar(count($this->files) % $this->chunks + 1);
        $bar = new ProgressBar($output, count($this->files));
        $bar->start();

        // resizer
        for ($inx = 0; $inx < count($this->files); $inx++) {
            $fullpath = $this->files[$inx];
            $ext = pathinfo($fullpath, PATHINFO_EXTENSION);

            $quality = $this->get_base_quality($ext, $input->getOption('q'));

            $f_filesize = filesize($fullpath);

            if (! in_array(strtolower($ext), $this->supported_format)) {
                $this->copy_original_file($fullpath);
            } else {
                $replace = $this->create_filename($fullpath, true);
                if (! file_exists($replace)) {
                    $r_filesize = 0;
                    $deleted = false;
                    do {
                        try {
                            $this->compressImage($fullpath, $replace, $quality);
                            $quality = $this->inc_quality($quality, $ext);
                            $r_filesize = filesize($replace);
                            if ($r_filesize > $f_filesize || $r_filesize <= 0) {
                                unlink($replace);
                                $deleted = true;
                            }
                        } catch (\Exception $e) {
                            $this->error('ERROR: ' . $fullpath);
                            $output->write($e->getMessage());
                            $deleted = true;
                        }
                    } while ($r_filesize > $f_filesize && $quality > 0);

                    if ($deleted) {
                        unlink($replace);
                        $this->copy_original_file($fullpath);
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();

        $after_all_files = glob(rtrim($this->dest, '/') . '/' . $this->mask);

        $output->write("\n\nFILE COUNT BEFORE:   " . count($this->files));
        $output->write("FILE COUNT AFTER:    " . count($after_all_files));
//        $output->write('TOTAL SIZE BEFORE:   ' . $this->get_dir_size($this->src));
//        $output->write('TOTAL SIZE AFTER:    ' . $this->get_dir_size($this->dest));

        return Command::SUCCESS;
    }

    /**
     * @param string $dir
     * @param string $file_to_search
     * @return void
     */
    protected function search_files(string $dir, string $search_extension = 'html')
    {
        $files = scandir($dir);
        foreach($files as $key => $filename){

            $path = realpath($dir.DIRECTORY_SEPARATOR.$filename);
            if(!is_dir($path)) {
                foreach (glob(rtrim($dir, '/').'/'.$search_extension) as $file) {
                    $this->files[] = $file;
                }
            } else if($filename != "." && $filename != "..") {
                $this->search_files($path, $search_extension);
            }
        }
    }

    /**
     * @param $fullpath
     * @return void
     */
    protected function copy_original_file($fullpath)
    {
        $ext = pathinfo($fullpath, PATHINFO_EXTENSION);
        $replace = $this->create_filename($fullpath, false);
        if (!file_exists($replace)) {
            copy($fullpath, $replace);
        }
    }

    /**
     * @param $fullpath
     * @param $replace_extension
     * @return array|string|string[]
     */
    protected function create_filename($fullpath, $replace_extension = false)
    {
        $fname = pathinfo($fullpath, PATHINFO_BASENAME);
        $ext = pathinfo($fullpath, PATHINFO_EXTENSION);

        $rep_fname = (!$replace_extension || empty($this->output_format))
            ? $fname
            : preg_replace('"\.(' . $ext . ')$"', '.' . $this->output_format, $fname);

        $newfullpath = str_replace($fname, $rep_fname, $fullpath);
        $replace = str_replace($this->src, $this->dest, $newfullpath);
        return $replace;
    }

    /**
     * @param $tempPath
     * @param $originalPath
     * @param $imageQuality
     * @return mixed
     */
    protected function compressImage($tempPath, $originalPath, $imageQuality)
    {
        list($width, $height) = getimagesize($tempPath);
        $width = min($width, $this->maxwidth);
        $height = min($height, $this->maxheight);

        $image = $this->resize_image($tempPath, $width, $height);

        $ext = pathinfo($tempPath, PATHINFO_EXTENSION);

        // Save image
        if ($ext == 'png') {
            imagepng($image, $originalPath, $imageQuality);
        } elseif ($ext == 'webp') {
            imagewebp($image, $originalPath, $imageQuality);
        } else {
            imagejpeg($image, $originalPath, $imageQuality);
        }
        imagedestroy($image);

        // Return compressed image
        return $originalPath;
    }

    /**
     * @param int $quality
     * @param string $ext
     * @return int
     */
    protected function inc_quality($quality, $ext)
    {
        if ($ext == 'png') {
            if ($quality > 11) {
                $quality = floor($quality / 10);
            } elseif ($quality < 0) {
                $quality = 1;
            } elseif ($quality > 9) {
                $quality = 9;
            }
        } else {
            if ($quality < 0) {
                $quality = 1;
            } elseif ($quality > 99) {
                $quality = 100;
            }
        }

        $step = ($ext == 'png') ? 1 : 5;
        return $quality < 1 ? 1 : $quality - $step;
    }

    /**
     * @param $ext
     * @return array|bool|int|string
     */
    protected function get_base_quality($ext, int $quality = null)
    {
        if ($ext == 'webp') {
            $quality = 100;
        } else {
            $quality = $quality ?: $this->quality;
        }
        return $quality;
    }

    /**
     * @param $file
     * @param $w
     * @param $h
     * @param $crop
     * @return false|\GdImage|resource
     */
    protected function resize_image($file, $w, $h, $crop = FALSE)
    {
        $imgInfo = getimagesize($file);
        $mime = $imgInfo['mime'];

        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        switch ($mime) {
            case 'image/jpeg':
                $baseimage = imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $baseimage = imagecreatefrompng($file);
                break;
            case 'image/gif':
                $baseimage = imagecreatefromgif($file);
                break;
            case 'image/webp':
                $baseimage = imagecreatefromwebp($file);
                break;
            default:
                $baseimage = imagecreatefromjpeg($file);
        }
        imagepalettetotruecolor($baseimage);
        imagealphablending($baseimage, true);
        imagesavealpha($baseimage, true);
        $retimage = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($retimage, $baseimage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $retimage;
    }

    /**
     * @param $directory
     * @return false|int
     */
    public function get_dir_size($directory)
    {
        $size = 0;
        $files = glob($directory . '/' . $this->mask);
        foreach ($files as $path) {
            is_file($path) && $size += filesize($path);
            is_dir($path) && $size += get_dir_size($path);
        }
        return $size;
    }
}
