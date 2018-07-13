# 基于jwt的Api验证
## 项目线上地址 [阿明的博客](https://www.soscoon.com)
## 安装CROS中间件
>* 创建Api路由的时候会用到一个“cors”中间件，虽然它不是强制性的，但是后面你会发现报类似这样的错
>* Cross-Origin Request Blocked: The Same Origin Policy disallows reading the remote resource at http://xxx.com/api/register. 
>* (Reason: CORS header 'Access-Control-Allow-Origin' missing)
````text
composer require barryvdh/laravel-cors
````
````php
// CROS.php的配置
app/Http/Middleware/CORS.php#
namespace App\Http\Middleware;
use Closure;
class CORS
{
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');

        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        if($request->getMethod() == "OPTIONS") {
            return Response::make('OK', 200, $headers);
        }

        $response = $next($request);
        foreach($headers as $key => $value)
            $response->header($key, $value);
        return $response;
    }
}
````
## Laravel json方法返回unicode
````php
return response()->json(['error'=>'出错了', 'code'=>'400101']);
// 输出结果
{"error":"\u51fa\u9519\u4e86","code":"400101"}
````
## 解决方法
````php
return response()->json(['error'=>'出错了', 'code'=>'400101'], 404, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
//输出结果
{"error":"出错了","code":"400101"}
````

## 错误码
````chef
    errorCode: 400500 //生成token出错
    errorCode: 400401 //用户账号密码错误
    errorCOde: 400404 //token错误、无效、没有作用
````

## 错误
### JWT使用token比对一直报找不到用户，是因为数据库id与查找的id不一致（config/jwt.php）
````php
'identifier' => 'user_id'
````