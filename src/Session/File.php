<?php
namespace Humphries\Session;

use Humphries\Contracts\SessionInterface;
use Phalcon\Session\Adapter\Files as Session;

class File extends Session implements SessionInterface
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