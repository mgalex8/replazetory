<?php
namespace App\Service\FileFinder;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Cache\CacheInterface;

class FileFinder
{

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @param CacheInterface $cache
     */
    public function __construct(?CacheInterface $cache = null)
    {
        $this->cache = $cache ?: new FilesystemAdapter('finder', 3600 * 24 * 180, ABS_PATH.'/cache');
        $this->finder = new Finder();
    }

    /**
     * @param string $path
     * @param string $name
     * @param string|null $sort
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findFiles(string $path, string $name, ?string $sort = null)
    {
        return $this->cache->get('find.files.'.md5($path.'.'.$name.'.'.$sort), function (CacheItem $item) use ($path, $name, $sort) {
            $item->expiresAfter(3600 * 24 * 180);

            if ($sort == 'filename') {
                $this->finder->sortByName();
            } elseif ($sort == 'type') {
                $this->finder->sortByType();
            } elseif ($sort == 'accessed_at') {
                $this->finder->sortByAccessedTime();
            } elseif ($sort == 'changed_at') {
                $this->finder->sortByChangedTime();
            } elseif ($sort == 'modified_at') {
                $this->finder->sortByModifiedTime();
            }

            return $this->finder->in($path)->name($name)->files();
        });
    }

    /**
     * Find directories from filesystem with name
     * @param string $path
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findDirectories(string $path)
    {
        return $this->cache->get('find.directories.'.md5($path), function (CacheItem $item) use ($path) {
            $item->expiresAfter(3600 * 24 * 180);

            return $this->finder->in($path)->depth(0)->directories();
        });
    }
}