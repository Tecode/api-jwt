<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Http\Model\LoginInfo;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
// 注册
    public function register(Request $request)
    {
        // 将编码的密码转码
        $key = pack("H*", "0123456789abcdef0123456789abcdef");
        $iv = pack("H*", "abcdef9876543210abcdef9876543210");
        //Now we receive the encrypted from the post, we should decode it from base64,
        $encrypted = base64_decode($request['password']);
        $shown = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv);

        // 解码以后会出现/50 /0F 的字符要替换
        $input['password'] = trim(preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '', $shown));
        $input['name'] = $request['name'];
        $input['email'] = $request['email'];
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:15',
            'email' => 'required|string|email|max:255|unique:users,user_email',
        ],
            [
                'name.required' => '用户名称需要填写',
                'email.required' => '邮箱账号需要填写',
                'password.required' => '密码不能为空',
                'email.email' => '邮箱格式错误',
                'email.unique' => '该邮箱已被其它用户注册',
                'password.min' => '密码长度不能小于6个字符',
                'password.max' => '密码长度不能大于16个字符',
            ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $msg) {
                return response()->json(['error' => $msg, 'code' => '404402'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }

        $input['password'] = Hash::make($input['password']);
        $create = User::create([
            'user_name' => $input['name'],
            'password' => $input['password'],
            'user_email' => $input['email'],
            'timestamps' => time()
        ]);
        if ($create) {
            return response()->json(['result' => true]);
        } else {
            return response()->json(['msg' => '注册失败，请稍后重试。', 'code' => '403400'], 403, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

// 登录验证
    public function login(Request $request)
    {
        // 将编码的密码转码
        $key = pack("H*", "0123456789abcdef0123456789abcdef");
        $iv = pack("H*", "abcdef9876543210abcdef9876543210");
        //Now we receive the encrypted from the post, we should decode it from base64,
        $encrypted = base64_decode($request['password']);
        $shown = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv);

        $loginData = $request;
        // 解码以后会出现/50 /0F 的字符要替换
        $loginData['password'] = trim(preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '', $shown));
        // grab credentials from the request
        $credentials = $loginData->only('user_email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => '账号或密码错误', 'errorCode' => '400401'], 401, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token', 'errorCode' => '400500'], 500, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
//        返回token值
//        return response()->json(compact('token'));
        // 全部验证通过得到token保存在cookies里面
        $data = User::where('user_email', $request['user_email'])->get();
        echo json_encode($data[0]);
        return Response::make()->withCookie('aming_token', $token, config('jwt.ttl'), "/", null, false, true);
    }

// 获取用户信息
    public function getUserDetails(Request $request)
    {
//        $data = User::where('user_email', $request['user_email'])->get();
        return JWTAuth::toUser(Cookie::get('aming_token'));
    }

    // 保存第三方登录信息
    public function savaLogin(Request $request)
    {
        $inputArr = $request->input();
        $validator = Validator::make($inputArr, [
            'nickname' => 'required',
            'gender' => 'required'
        ],
            [
                'nickname.required' => '请填写昵称参数',
            ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $msg) {
                return response()->json(['error' => $msg, 'code' => '404402'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        $create = LoginInfo::insert([
            'nickname' => $inputArr['nickname'],
            'gender' => $inputArr['gender'],
            'province' => $inputArr['province'],
            'city' => $inputArr['city'],
            'year' => $inputArr['year'],
            'image_url' => $inputArr['figureurl_qq_2'],
            'timestamp' => time()
        ]);
        if ($create) {
            return response()->json(['code' => 1, 'msg' => '保存成功']);
        } else {
            return response()->json(['msg' => '保存失败', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }
}
