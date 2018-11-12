<?php

namespace app\api\controller;

use think\Request;
use Qcloud\Sms\SmsSingleSender;

class Sms
{
	public function Sms()
	{
	 	$phoneNumbers = [];
        $tel = input('tel');
        $phoneNumbers[] = $tel;
        // 短信应用SDK AppID
        $appid = 1400158537; // 1400开头

        // 短信应用SDK AppKey
        $appkey = "9cf9d7aab63f393310e2e5594a5d3a58";
        // 短信模板ID，需要在短信应用中申请
        $templateId = 7839;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

        // 签名
        $smsSign = "二居"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
                try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $result = $ssender->send(0, "86", $phoneNumbers[0],
                "【二居商城】您的验证码是: 5678");
            $rsp = json_decode($result);
            echo $result;
        } catch(\Exception $e) {
            echo var_dump($e);
        }
        echo "\n";
	}
}