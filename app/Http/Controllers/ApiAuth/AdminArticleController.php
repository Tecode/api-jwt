<?php

namespace App\Http\Controllers\ApiAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Model\Article;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request['index'] || !$request['size']){
            return response()->json(['msg' => '未找到页码和分页数', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        $index = ($request['index']-1)*$request['size'];
        $allData = Article::skip($index)->take($request['size'])->get();
        if($allData) {
            $arr = array();
            foreach ($allData as $number => $value ){
                $arr[$number]['aid'] = $value['article_id'];
                $arr[$number]['title'] = $value['article_title'];
                $arr[$number]['type'] = $value['article_type'];
                $arr[$number]['author'] = $value['article_author'];
                $arr[$number]['keywords'] = $value['article_keywords'];
                $arr[$number]['discript'] = $value['article_discript'];
                $arr[$number]['imageurl'] = $value['article_imageurl'];
                $arr[$number]['time'] = $value['timestamp'];
                $arr[$number]['view'] = $value['article_view'];
                $arr[$number]['comment'] = $value['article_comment'];
            }
            return response()->json([
                'total' => Article::count(),
                'msg' => '查询成功',
                'data' => $arr,]);
        }else{
            return response()->json([
                'msg' => '未找到结果',
                'code' => '404406',
            ], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * 新增文章
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputArr = $request->input();
        $validator = Validator::make($inputArr, [
            'title' => 'required',
            'discript' => 'required',
        ],
            [
                'title.required' => '文章标题不能为空',
                'discript.required' => '文章描述不能为空',
            ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $msg) {
                return response()->json(['error' => $msg, 'code' => '404402'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        $create = Article::insert([
            'article_title' => $inputArr['title'],
            'article_type' => $inputArr['articleType'],
            'article_author' => $inputArr['author'],
            'article_keyWords' => $inputArr['keyWords'],
            'article_discript' => $inputArr['discript'],
            'article_imageurl' => $inputArr['updateImage'],
            'file_name' => $inputArr['fileName'],
            'article_content' => $inputArr['content'],
            'timestamp' => time()
        ]);
        if ($create) {
            return response()->json(['code' => 1, 'msg' => '保存成功']);
        } else {
            return response()->json(['msg' => '保存失败', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articleData = Article::where('article_id', $id) ->get();
        if ($articleData) {
            return response()->json(['code' => 1, 'msg' => '查询成功', 'data' => $articleData[0]]);
        } else {
            return response()->json(['msg' => '查询失败', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updateData = Article::where('article_id', $id);
        if ($updateData) {
            $inputArr = $request->input();
            $validator = Validator::make($inputArr, [
                'title' => 'required',
                'discript' => 'required',
            ],[
               'title.required' => '文章标题不能为空',
               'discript.required' => '文章描述不能为空',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $msg) {
                    return response()->json(['msg' => $msg, 'code' => '404420'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
                }
            }
            $create = $updateData->update([
                'article_title' => $inputArr['title'],
                'article_type' => $inputArr['articleType'],
                'article_author' => $inputArr['author'],
                'article_keyWords' => $inputArr['keyWords'],
                'article_discript' => $inputArr['discript'],
                'article_imageurl' => $inputArr['updateImage'],
                'article_content' => $inputArr['content'],
                'file_name' => $inputArr['fileName'],
                'update_time' => time()
            ]);
            if ($create) {
                return response()->json(['msg' => '文章更新成功', 'code' => '1'], 200, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(['msg' => '文章更新失败', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json(['msg' => '找不到该文章', 'code' => '404430'], 403, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
