<?php

namespace Stuart\Tests\Infrastructure;


use Desarrolla2\Cache\Adapter\AdapterInterface;
use Stuart\Helpers\ArrayHelper;

class InMemoryAdapter implements AdapterInterface
{
    private $store = array();

    /**
     * Check if adapter is working
     *
     * @return boolean
     */
    public function check()
    {
        // TODO: Implement check() method.
    }

    /**
     * Delete a value from the cache
     *
     * @param string $key
     */
    public function del($key)
    {
        // TODO: Implement del() method.
    }

    /**
     * Retrieve the value corresponding to a provided key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return ArrayHelper::getSafe($this->store, $key);
    }

    /**
     * Retrieve the if value corresponding to a provided key exist
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * * Add a value to the cache under a unique key
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     */
    public function set($key, $value, $ttl = null)
    {
        $this->store[$key] = $value;
    }

    /**
     * Set option for Adapter
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value)
    {
        // TODO: Implement setOption() method.
    }

    /**
     * clean all expired records from cache
     */
    public function clearCache()
    {
        // TODO: Implement clearCache() method.
    }

    /**
     * clear all cache
     */
    public function dropCache()
    {
        // TODO: Implement dropCache() method.
    }
}