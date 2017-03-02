<?php
namespace Humphries\Contracts;

interface CacheInterface
{

    /**
     * Returns a cached content
     *
     * @param string $keyName
     * @param int $lifetime
     * @return mixed|null
     */
    public function get($keyName, $lifetime = null);

    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string $content
     * @param long $lifetime
     * @param boolean $stopBuffer
     * @return bool
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true);

    /**
     * Deletes a value from the cache by its key
     *
     * @param int|string $keyName
     * @return bool
     */
    public function delete($keyName);

    /**
     * Query the existing cached keys
     *
     * @param string $prefix
     * @return array
     */
    public function queryKeys($prefix = null);

    /**
     * Checks if cache exists and it isn't expired
     *
     * @param string $keyName
     * @param long $lifetime
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null);

    /**
     * Immediately invalidates all existing items.
     *
     * @return bool
     */
    public function flush();

    /**
     * Get an item from the cache, or store the default value forever.
     *
     * @param  string   $key
     * @param  \Closure $callback
     * @return mixed
     */
    public function rememberForever($key, \Closure $callback);

}