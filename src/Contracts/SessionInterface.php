<?php
namespace Humphries\Contracts;

interface SessionInterface
{

    /**
     * Creates a new session
     *
     * @param string $key
     * @param array  $values
     *
     * @return bool
     */
    public function create($key, $values);

    /**
     * Gets a session, if exists
     *
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null);

    /**
     * Removes a session
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);

}