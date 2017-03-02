<?php
namespace Humphries\Http;

use Phalcon\Http\Response as PhalconResponse;
use Humphries\Contracts\ResponseInterface;

class Response extends PhalconResponse implements ResponseInterface
{

}