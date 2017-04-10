<?php
class Images extends OpenStack
{
    function listImages($filters = []){
        $querys = [];
        foreach($filters as $key => $val){
            if($val === ''){
                continue;
            }
            $querys[] = $key . '=' . $val;
        }

        $url = "/v2/images?status=active";
        if(count($querys) > 0){
            $url = "/v2/images?status=active&" . implode('&', $querys);
        }
        $res = $this->_req->get($url, [], []);
        return $res;        
    }
    
    function deleteImages($image_id){
        $url="/v2/images/$image_id";        

        $res=$this->_req->delete($url,[],[]);

        return $res;
    }
    
}
?>