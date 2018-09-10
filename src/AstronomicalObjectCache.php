<?php

namespace Andrmoel\AstronomyBundle;

use Andrmoel\AstronomyBundle\AstronomicalObjects\AstronomicalObject;

class AstronomicalObjectCache
{
    /** @var AstronomicalObject */
    private $astronomicalObject;


    /**
     * Constructor
     * @param AstronomicalObject $astronomicalObject
     */
    public function __construct(AstronomicalObject $astronomicalObject)
    {
        $this->astronomicalObject = $astronomicalObject;
        $this->toi = $astronomicalObject->getTimeOfInterest();
    }


    /**
     * Get cache file
     * @param $identifier
     * @return string
     */
    public function getCacheFile($identifier = '')
    {
        $dir = 'astro/';

        $className = get_class($this->astronomicalObject);
        $classNameArr = explode('\\', $className);
        $toiStr = $this->toi->getFormattedTimeString('Ymd_His');
        $fileName = array_pop($classNameArr) . '_' . $toiStr;

        $cacheFile = CACHE_DIR . $dir . $fileName;

        return $cacheFile . '_' . $identifier . '.txt';
    }


    /**
     * Write cache
     * @param $identifier
     * @param $object
     */
    public function set($identifier, $object)
    {
        $cacheFile = $this->getCacheFile($identifier);

        $serialized = serialize($object);
        $content = gzdeflate($serialized);
        file_put_contents($cacheFile, $content);
    }


    /**
     * Read cache
     * @param $identifier
     * @return null|\stdClass
     */
    public function get($identifier)
    {
        $object = null;
        $cacheFile = $this->getCacheFile($identifier);

        if (file_exists($cacheFile)) {
            $content = file_get_contents($cacheFile);
            $serialized = gzinflate($content);
            $object = unserialize($serialized);
        }

        return $object;
    }


    /**
     * Check if cache exists
     * @param $identifier
     * @return bool
     */
    public function has($identifier)
    {
        $cacheFile = $this->getCacheFile($identifier);
        return file_exists($cacheFile);
    }
}
