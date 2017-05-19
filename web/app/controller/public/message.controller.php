<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/18
 * Time: 0:19
 */
class MessageController extends Controller{

    function testMsg($req){
        $content = 'proName=行政审批;fromUser=方成;time=11:03:59';
        return $this->systemToWorker('fc3454de-f81e-401b-9626-db5938f092ba',$content,'alert');
    }

    function systemToWorker($toUser,$content){
        $content = "proName={$content['proName']};fromUser={$content['fromUser']};time={$content['time']}";
        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = $toUser;
        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://127.0.0.1:2121/";
        $post_data = array(
            "type" => 'publish',
            "content" => $content,
            "to" => $to_uid,
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
}