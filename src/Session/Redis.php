<?php
namespace Humphries\Session;

use Humphries\Contracts\SessionInterface;
use Phalcon\Session\Adapter\Redis as SessionAdapter;

class Redis extends SessionAdapter implements SessionInterface
{

    /**
     * Creates a new session
     *
     * @param string $key
     * @param array $values
     *
     * @return bool
     */
    public function create($key, $values)
    {
        $this->set($key, $values);
        return true;
    }

}