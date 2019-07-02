<?php

namespace App\Exceptions;

use App\Api\Helper\ApiResponse;
use App\Http\Requests\Request;
use Exception;
use Throwable;

class InternalException extends Exception
{
    use ApiResponse;
    //
    protected  $msgForUser;

    public function __construct($message = "", $code = '500', string $msgForUser = '系统内部错误')
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request)
    {

        return  $this->failed($this->msgForUser,$this->code);
    }

}
