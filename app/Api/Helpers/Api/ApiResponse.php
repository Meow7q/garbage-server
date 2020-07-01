<?php
/**
 * Created by PhpStorm.
 * UserController: meow7
 * Date: 2018/3/19
 * Time: 10:21
 */

namespace App\Api\Helpers\Api;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

//自定义api返回
trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * errcode只有在失败得时候才会出现
     * @var string
     */
    protected $errcode = '';

    /**
     * @return mixed
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

    public function setErrCode($errcode){
        $this->errcode = $errcode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {
        return Response::create($data,$this->getStatusCode(),$header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null){

        if ($code){
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
     * api请求失败,status = 0
     * @param $message
     * @param string $errcode
     * @param int $code
     * @param int $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 0 ){
        return $this->setStatusCode($code)->message($message,$status);
    }


    /**
     * @param $message
     * @param int $status
     * @return mixed
     */
    public function message($message, $status = 1){
        $arr = [
            'message' => $message,
        ];
        if($this->errcode){
            $arr = [
                'errcode' => $this->errcode,
                'message' => $message
            ];
        }
        return $this->status($status, $arr);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!"){

        return $this->failed($message,FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created($message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);

    }

    /**
     * api请求成功，status = 1
     * @param $data
     * @param $code
     * @return mixed
     */
    public function success($data, $code){
        $status = 1;
        return $this->status($status,compact('data'),$code);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message,Foundationresponse::HTTP_NOT_FOUND);
    }

}