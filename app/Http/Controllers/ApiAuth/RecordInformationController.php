<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Model\AccessInformation;
use App\Http\Model\DeviceInfo;
use App\Http\Model\IpInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecordInformationController extends Controller
{
    public function analysisInfo(Request $request) {
        $insert = DeviceInfo::insert([
        "platform" => $request["platform"],
		"browser" => $request["browser"],
		"isiPad" => $request["isiPad"],
		"isMobile" => $request["isMobile"],
        "isiPhone" => $request["isiPhone"],
		"isAndroid" => $request["isAndroid"],
		"isIE" => $request["isIE"],
		"isFirefox" => $request["isFirefox"],
		"isEdge" => $request["isEdge"],
		"isChrome" => $request["isChrome"],
		"isSafari" => $request["isSafari"],
		"isWindows" => $request["isWindows"],
		"isLinux" => $request["isLinux"],
		"isMac"=> $request["isMac"],
		"isUC" => $request["isUC"],
		"version" => $request["version"],
        ]);
        if ($insert) {
            return response()->json(['result' => true]);
        } else {
            return response()->json(['result' => false, 'msg' => '出错了']);
        }
    }
    public function analysisIp(Request $request) {
        $insert = IpInfo::insert([
            "timestamp" => time(),
            "country" => $request["country"],
			"country_id" => $request["country_id"],
			"region" => $request["region"],
			"city" => $request["city"],
			"county" => $request["county"],
			"ip" => $request["ip"],
        ]);
        if ($insert) {
            return response()->json(['result' => true]);
        } else {
            return response()->json(['result' => false, 'msg' => '出错了']);
        }
    }
}
