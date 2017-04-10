<?php
/**
* 	@descript: 封装权限
*	@date；	2014年11月19日 10:32:46
*/
class Auth
{
	public static function getOauth($url) {
        $url_array = parse_url($url);
        $path = $url_array['path'];
        $access_key = 'DATATOM4UAcOR3uLx3bx49kIDdAhaRCAr';
        $secret_key = 'skGqMOrcnOcu35Z9APzjfCZuauWQ7BWfIsoEJ1FI';
        $sign = hash_hmac('sha1', $path, $secret_key);
        $sign = strtoupper($sign);
        $oauth_sign = 'DATATOM ' . $access_key . ':' . base64_encode($sign);
        return $oauth_sign;
    }

/*
    public static function getOldOauth($url) {
        $url_array = parse_url($url);
        $path = $url_array['path'];
        $username = 'datatom';
        $secret_key = '2BE3B7CEE750B01F0A7949FA6B8B58B3';
        $sign = hash_hmac('sha1', $path, $secret_key);
        $oauth_sign = 'DATATOM ' . base64_encode("${username}:${sign}");
        return $oauth_sign;
    }
*/
}



/**
* 	@descript: 封装curl
*/
class Curl
{
	private $_timeout;
	private $_connecttimeout;
	private $_header;
	private $_ch;
	private $_httpcode;
	private $_response;

	function __construct (	$header = [],
							$timeout = 15,
							$connecttimeout = 0){

		$this->_timeout = $timeout;
		$this->_connecttimeout = $connecttimeout;
		$this->_header = $header;

		$this->_init ();
	}


	function __destruct (){
		if ($this->_ch)
			$this->close ();
	}


	public function get ($url, $param = []){
		$this->_setGetUrl ($url, $param);
		$this->_exec ();
        return $this->_response;
	}

	public function post ($url, $param = []){
		$this->_setPostUrl ($url, $param);
		$this->_exec ();
        return $this->_response;
	}

	public function setHeader( $key, $val ){
		$this->_header[$key] = $val;
	}

	private function _setPostUrl ($url, $param){
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_POST, 1);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $param);
	}

	private function _setGetUrl ($url, $param = []){
		if (count($param) > 0) {
			if (strpos($url, '?')) {
				$url .= "&".http_build_query ($param);
			}else {
				$url .= "?".http_build_query ($param);
			}
		}

		curl_setopt($this->_ch, CURLOPT_URL, $url);
	}

	private function _init (){
		if ($this->_ch)
			return;

		$this->_ch = curl_init();
		curl_setopt($this->_ch, CURLOPT_HEADER, 0);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
	}

	private function _exec (){
		curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT,
										$this->_connecttimeout);
		curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $this->_header);
		$this->_response = curl_exec($this->_ch);
        $this->_httpcode = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
	}

	public function setTimeout ($timeout) {
		$this->_timeout = (int)$timeout;
		return true;
	}

	public function setConnectTimeout ($connecttimeout) {
		$this->_connecttimeout = (int)$connecttimeout;
		return true;
	}

	public function getHttpCode () {
        if(isset($this->_httpcode))
            return $this->_httpcode;
    }

    public function setResponse ($response) {
		$this->_response = $response;
	}

	public function getResponse () {
		if (isset($this->_response))
			return $this->_response;
	}


	public function getTimeout () {
		return $this->_timeout;
	}

	public function getConnectTimeout () {
		return $this->_connecttimeout;
	}

	public function getHearder () {
		return $this->_header;
	}

	public function getCurl () {
		return $this->_ch;
	}

	public function close (){
		curl_close($this->_ch);
	}
}

?>