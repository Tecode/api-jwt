<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Model\Massage;
use App\Http\Model\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LivingMessegeController extends Controller
{
    public function index(Request $request)
    {
        $inputArr = $request->input();
        $validator = Validator::make($inputArr, [
            'name' => 'required',
            'content' => 'required|max:255',
        ], [
            'name.required' => '回复人不能为空',
            'content.required' => '回复内容不能为空',
            'content.max' => '回复内容不能超过240个字符',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $msg) {
                return response()->json(['msg' => $msg, 'code' => '404410'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        };
        $insert = Massage::insert([
            "message_name" => $request["name"],
            "message_content" => $request["content"],
            "message_img" => $request["imgUrl"],
            'timestamp' => time()
        ]);
        if ($insert) {
            return response()->json(['msg' => '留言成功', 'code' => '100200']);
        } else {
            return response()->json(['result' => false, 'msg' => '出错了,请稍后重试']);
        }
    }

    // 查找回复信息
    public function getRepyInfo($id)
    {
        $allData = Reply::where('message_id', '=', $id) -> orderBy('reply_id', 'desc') -> get();
        if ($allData) {
            $arr = array();
            foreach ($allData as $number => $value) {
                $arr[$number]['id'] = $value['reply_id'];
                $arr[$number]['name'] = $value['reply_name'];
                $arr[$number]['imgUrl'] = $value['reply_img'];
                $arr[$number]['messege'] = $value['reply_content'];
                $arr[$number]['beAnswered'] = $value['be_answered'];
                $arr[$number]['personId'] = $value['be_answered'];
                $arr[$number]['dateTime'] = date("Y-m-d H:i", $value['timestamp']);
            }
            return $arr;
        }
        return array();
    }

    public function getLeavingMessage(Request $request)
    {
        if (!$request['index'] || !$request['size']) {
            return response()->json(['msg' => '未找到页码和分页数', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        $index = ($request['index'] - 1) * $request['size'];
        $allData = Massage::skip($index)->take($request['size'])-> orderBy('message_id', 'desc') -> get();
        if ($allData) {
            $arr = array();
            foreach ($allData as $number => $value) {
                $arr[$number]['id'] = $value['message_id'];
                $arr[$number]['name'] = $value['message_name'];
                $arr[$number]['imgUrl'] = $value['message_img'];
                $arr[$number]['messege'] = $value['message_content'];
                $arr[$number]['index'] = $value['message_id'] - 10005;
                $arr[$number]['dateTime'] = date("Y-m-d H:i", $value['timestamp']);
                $arr[$number]['content'] = $this->getRepyInfo($value['message_id']);
            }
            return response()->json([
                'msg' => '查询成功',
                'total' => Massage::count(),
                'data' => $arr,]);
        } else {
            return response()->json([
                'msg' => '未找到结果',
                'code' => '404406',
            ], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    // 回复留言
    public function replyMessage(Request $request)
    {
        $inputArr = $request->input();
        $validator = Validator::make($inputArr, [
            'name' => 'required',
            'content' => 'required|max:255',
            'parentId' => 'required',
            'beAnswered' => 'required',
        ], [
            'name.required' => '回复人不能为空',
            'content.required' => '回复内容不能为空',
            'content.max' => '回复内容不能超过240个字符',
            'parentId.required' => '回复ID不能为空',
            'beAnswered' => '被回复人不能为空'
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $msg) {
                return response()->json(['msg' => $msg, 'code' => '404410'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        };
        $create = Reply::create([
            "message_id" => $request["parentId"],
            "be_answered" => $request["beAnswered"],
            "reply_content" => $request["content"],
            "reply_name" => $request["name"],
            'timestamp' => time()
        ]);
        if ($create) {
            return response()->json(['id' => $create['reply_id'], 'name' => $create['reply_name'], 'time' => date("Y-m-d H:i", $create['timestamp'])]);
        } else {
            return response()->json(['result' => false, 'msg' => '出错了,请稍后重试']);
        }
    }
}
