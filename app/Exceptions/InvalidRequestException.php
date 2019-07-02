<?php

namespace App\Exceptions;

use App\Api\Helper\ApiResponse;
use App\Http\Requests\Request;
use Exception;

class InvalidRequestException extends Exception
{
    use ApiResponse;
    //

    public function __construct(string $message = "", $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function  render(Request $request)
    {

        return  $this->failed($this->message);
    }
}
