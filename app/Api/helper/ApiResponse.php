<?php

namespace  App\Api\Helper;

use Response;

trait  ApiResponse
{
    protected  $statusCode = "200";


    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data,$header = [])
    {

        return Response::json($data,$this->getStatusCode(),$header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($status, array $data, $code = null)
    {

        if($code){
            $this->setStatusCode($code);
        }
        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        $data = array_merge($status,$data);
        return $this->respond($data);
    }

    /**
     * @param $message
     * @param string $code
     * @param string $status
     * @return mixed
     */
    public function failed($message,$code = '400',$status = 'error')
    {

        return  $this->setStatusCode($code)->message($message,$status);
    }

    /**
     * @param $message
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function message($message, $status = "success")
    {

        return $this->status($status,['message' => $message]);
    }

    /**
     * @param $data
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data,$status = "success")
    {

        return  $this->status($status,compact('data'));
    }

    /**
     * @param string $message
     * @param string $code
     * @return mixed
     */
    public function notFound($message = 'Not found!',$code = '404')
    {

        return  $this->failed($message,$code);
    }

}
