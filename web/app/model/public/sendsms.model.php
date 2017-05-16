<?php
/**
 * Created by PhpStorm.
 * User: Jey
 * Date: 2016/9/27
 * Time: 20:08
 */
class SendsmsModel{

    private $apikey = "23618f64c74798766b1bee0e33cdec6e ";
    private $ch;
    private $company = "【云片网】";

    /***
     * 发送短信
     * @param $mobile 手机号码，如果是批量发送，则为一维数组
     * @param $text 短信内容
     * @param int $type 发送类别，数字 0 为单条发送，数字 1 为批量发送
     * @return array|mixed 返回发送结果
     * 批量发送返回示例：
     * array (size=4)
            'total_count' => int 1
            'total_fee' => string '0.0550' (length=6)
            'unit' => string 'RMB' (length=3)
            'data' =>
              array (size=1)
                0 =>
                array (size=7)
                'code' => int 0
                'msg' => string '发送成功' (length=12)
                'count' => int 1
                'fee' => float 0.055
                'unit' => string 'RMB' (length=3)
                'mobile' => string '15549297431' (length=11)
                'sid' => float 10869359116
     */
    public function send($mobile, $text, $type = 0){
        if($mobile ==='' || $text=== '' || !in_array($type, [0,1])){
            return ['status'=>CODE_ERROR, 'msg'=>'参数错误'];
        }
        $text = $this->company .$text;
        // 初始化会话
        $this->ch = curl_init();
        /* 设置验证方式 */
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
        /* 设置返回结果为流 */
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        /* 设置超时时间*/
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
        /* 设置通信方式 */
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

        if($type === 0){
            // 发送单条短信
            $data=array('text'=>$text,'apikey'=>$this->apikey,'mobile'=>$mobile);
            $json_data = $this->sendSingle($this->ch,$data);
            curl_close($this->ch);
            return json_decode($json_data,true);
        }
        else if($type === 1){
            // 发送批量短信
            if(is_array($mobile) && count($mobile)<=1000){
                $mobileStr = implode(',',$mobile);
                $data=array('text'=>$text,'apikey'=>$this->apikey,'mobile'=>$mobileStr);
                $json_data = $this->sendBatch($this->ch,$data);
                curl_close($this->ch);
                return json_decode($json_data,true);
            }
            curl_close($this->ch);
            return ['status'=>CODE_ERROR, 'msg'=>'批量手机号码格式错误或号码数量过多'];
        }
    }

    /***
     * 单条发送
     * @param $ch
     * @param $data
     * @return mixed
     */
    function sendSingle($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

    /***
     * 批量发送
     * @param $ch
     * @param $data
     * @return mixed
     */
    function sendBatch($ch, $data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/batch_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

}