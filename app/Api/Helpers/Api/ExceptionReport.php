<?php
/**
 * Created by PhpStorm.
 * UserController: meow7
 * Date: 2018/3/19
 * Time: 13:33
 */

namespace App\Api\Helpers\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//自定义异常返回
class ExceptionReport
{
    use ApiResponse;

    /**
     * @var
     */
    public $exception;

    /**
     * @var
     */
    public $request;

    /**
     * @var
     */
    public $report;

    /**
     * ExceptionReport constructor.
     * @param $exception
     * @param $request
     * @param $report
     */
    public function __construct(Request $request, \Exception $exception) {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @var array
     */
    public $doReport = [
        AuthenticationException::class => ['未授权', 401],
        ModelNotFoundException::class => ['该模型未找到', 404],
        ValidationException::class => [],
        NotFoundHttpException::class => ['方法未定义', 500]
    ];

    /**
     * @return bool
     */
    public function shouldReturn(){
        //通过wantsJson()和ajax()判断是否是API
        if(!($this->request->wantsJson() || $this->request->ajax())){
            return false;
        }

        foreach (array_keys($this->doReport) as $report){
            if($this->exception instanceof $report){
                $this->report = $report;
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Exception $e
     * @return static
     */
    public static function make(\Exception $e){
        //后期动态绑定，此处相当于self
        return new static(\request(), $e);
    }

    /**
     * 自定义错误返回，Validator错误返回和普通的API返回
     * @return mixed
     */
    public function report(){
        if($this->exception instanceof ValidationException){
            return $this->failed($this->exception->errors(),422);
        }
        $message = $this->doReport[$this->report];
        return $this->failed($message[0], $message[1]);
    }
}