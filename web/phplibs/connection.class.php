<?php
class Connection
{
    private $_host;
    private $_port;
    private $_curl;
    private $_headers;
    
    private $_hosts;
    
    function __construct ($host, $port)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_init();
    }
    
    // function __construct($hosts)
    // {
    //     if (is_string($hosts))
    //     {
    //         $this->_hosts[] = [
    //             'host' => $hosts,
    //             'valid' => false
    //         ];
    //     }
    //     else if (is_array($hosts))
    //     {
    //         if (is_string($host))
    //         {
    //             $this->_hosts[] = [
    //                 'host' => $host,
    //                 'valid' => false
    //             ]
    //         }
    //     }
    // }
    
    function __destruct()
    {
        if ($this->_curl)
        {
            curl_close($this->_curl);
        }
    }

    ///
    /// do get request
    /// $uri        http request uri
    /// $body       http request body
    /// $params     http request params
    public function get($uri, $params = [], $body = NULL)
    {
        return $this->_request($uri, 'GET', $body, $params);
    }
    
    ///
    /// do post request
    /// $uri        http request uri
    /// $body       http request body
    /// $params     http request params
    public function post($uri, $params = [], $body = NULL)
    {
        return $this->_request($uri, "POST", $body, $params);
    }
    
    
    ///
    /// do delete request
    /// $uri        http request uri
    /// $body       http request body
    /// $params     http request params
    public function delete($uri, $params = [], $body = NULL)
    {
        return $this->_request($uri, "DELETE", $body, $params);
    }
    
    
    ///
    /// do put request
    /// $uri        http request uri
    /// $body       http request body
    /// $params     http request params
    public function put($uri, $params = [], $body = NULL)
    {
        return $this->_request($uri, "PUT", $body, $params);
    }
    
    ///
    /// do head request
    /// $uri        http request uri
    /// $body       http request body
    /// $params     http request params
    public function head($uri, $params = [], $body = NULL)
    {
        return $this->_request($uri, "HEAD", $body, $params);
    }
    

    public function header($key, $val){
            $this->_headers[$key] = $val;
    }

    private function _init()
    {
        if ($this->_curl)
        {
            return;
        }
        else
        {
            $this->_curl = curl_init();
        }
        $this->_headers['Accept'] = 'application/json';
        $this->_headers['Content-Type'] = 'application/json';
        $this->_headers['charset'] = 'utf-8';
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
    }
    private function _request($uri, $method = 'GET', $body, $params)
    {
        $url = 'http://' .$this->_host .':' . $this->_port .$uri;
        if (($params != NULL) and (count($params) > 0))
        {
            if (strpos($url, '?'))
            {
                $url .= "&".http_build_query($params);
            }
            else
            {
                $url .= "?".http_build_query($params);
            }
        }
        
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        
        $this->_headers['Content-Length'] = 0;
        if($body != NULL){
            $strbody = '';
            if (is_array($body)){
                $strbody = json_encode($body, JSON_UNESCAPED_UNICODE);
            }else if (is_string($body)){
                $strbody = $body;
            }
            $this->_headers['Content-Length'] = strlen($strbody);
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $strbody); 
        }else{
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, '');
        }

        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $method);
        if($method == 'POST'){
            curl_setopt($this->_curl, CURLOPT_POST, 1);
        }
        $headers = [];
        foreach($this->_headers as $name => $val){
            $headers[] = $name . ':' . $val;
        }
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($this->_curl);
        $code = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
        $result = [
            'code' => $code,
            'result' => json_decode($response, true)
        ];
        return $result;
    }
}
?>