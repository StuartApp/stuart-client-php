<?php

namespace Stuart\Cache;

use Psr\SimpleCache\CacheInterface;
use Stuart\Infrastructure\StuartAccessToken;

class DiskCache implements CacheInterface
{
    private $fileName;

    /**
     * DiskCache constructor.
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    private function getContent()
    {
        if (file_exists(urlencode($this->fileName))) {
            return file_get_contents(urlencode($this->fileName));
        } else {
            return "";
        }
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $content = $this->getContent();
        if ($content && strlen($content) > 0) {
            $objectWithTokens = json_decode($content);
            $asArray = json_decode(json_encode($objectWithTokens), true);
            $token = $asArray[$key];
            if ($token) {
                print "Token cache HIT.\n";
                return new StuartAccessToken($token);
            }
        }
        print "Token cache miss.\n";
        return null;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $arrToWrite = null;
        $content = $this->getContent();
        if ($content && strlen($content) > 0) {
            $objectWithTokens = json_decode($content);
            $asArray = json_decode(json_encode($objectWithTokens), true);
            $asArray[$key] = $value;
            $arrToWrite = $asArray;
        } else {
            $arrToWrite = array($key => $value);
        }
        $file = fopen(urlencode($this->fileName), "w");
        $textToWrite = json_encode($arrToWrite); // minimize time spent in the critical section
        do {
            if(flock($file, LOCK_EX)) {
                fwrite($file, $textToWrite);
                fflush($file);
                flock($file, LOCK_UN);
                break;
            } else {
                usleep(50);
            }
        } while(true);
        
        fclose($file);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        throw new Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        throw new Exception('Not implemented');
    }
}