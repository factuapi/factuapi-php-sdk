<?php

namespace Factuapi\PhpSdk\Exceptions;

use Exception;
use Saloon\Http\Response;

class FactuapiException extends Exception
{
    public ?Response $response = null;

    public function __construct(Response $response, string $message, int $code)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }
}
