<?php
namespace Humphries\Http;

use Phalcon\Http\Request as PhalconRequest;
use Humphries\Contracts\RequestInterface;

class Request extends PhalconRequest implements RequestInterface
{
    /**
     * Get Bearer Token from authorization headers.
     *
     * @return string
     */
    public function getBearerToken()
    {
        foreach ([$this->getHeader('Authorization'), $this->getHeader('HTTP_AUTHORIZATION')] as $header) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }

}