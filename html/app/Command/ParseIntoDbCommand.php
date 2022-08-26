<?php
namespace App\Command;

use App\Bundle\YamlReplacerParser\ContentParser;
use App\Bundle\YamlReplacerParser\Saver\WordpressTableSaver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  the name of the command is what users type after "php bin/console"
 */
class ParseIntoDbCommand extends Command
{

    /**
     * @var ContentParser
     */
    protected $contentParser;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->contentParser = new ContentParser();
        $this->contentParser->setSaver(new WordpressTableSaver());
        parent::__construct($name);
    }

    /**
     * In this method setup command, description, and its parameters
     */
    protected function configure()
    {
        $this->setName('app:parse_into_db');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception|\Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->search_files(ABS_PATH.'/_HTML', 'html');

        // creates a new progress bar (50 units)
        $countFiles = count($this->files);
        $progressBar = new ProgressBar($output, $countFiles);
        $progressBar->start();

        $i = 0;
        while ($i++ < $countFiles) {
            try {
                $this->contentParser->parse($this->files[$i]);
            } catch(\Exception|\Throwable $e) {
                throw $e;
            } finally {
                $progressBar->advance();
            }
        }
        $progressBar->finish();

        $output->write(PHP_EOL);

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
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if(strtolower($ext) == strtolower($search_extension)) {
                    $this->files[] =  $path;
                }
            } else if($filename != "." && $filename != "..") {
                $this->search_files($path, $search_extension);
            }
        }
    }
}