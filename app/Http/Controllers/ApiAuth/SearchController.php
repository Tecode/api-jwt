<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Model\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function index($keyWords, Request $request)
    {
        if (!$request['index'] || !$request['size']) {
            return response()->json(['msg' => '未找到页码和分页数', 'code' => '404400'], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
        $index = ($request['index'] - 1) * $request['size'];
        $allData = Article::where('article_title','like','%'.$keyWords.'%')->skip($index)->take($request['size'])->get([
            'article_title',
            'article_id',
            'article_type',
            'article_keywords',
            'article_author',
            'article_imageurl',
            'timestamp',
            'article_view',
            'article_comment',
            'article_discript']);
        if ($allData) {
            $arr = array();
            foreach ($allData as $number => $value) {
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
                'total' => Article::where('article_title','like','%'.$keyWords.'%')->count(),
                'msg' => '查询成功',
                'data' => $arr,]);
        } else {
            return response()->json([
                'msg' => '未找到结果',
                'code' => '404406',
            ], 404, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }
}
