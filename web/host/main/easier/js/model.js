var Request = (function(){
    var post = function(url, data, success, error, opts){
        return request(url, data, 'post', success, error, opts);
    };

    var get =  function(url, data, success, error, opts){
        return request(url, data, 'get', success, error, opts);
    };

    function request(url, data, type, success, error, opts){
        var param = opts || {};
        param['url'] = url;//apache 跳转的原因
        param['dataType'] = 'json';
        param['data'] = data;
        param['type'] = type;
        param['success'] = success;
        param['error'] = error;
        var xhr = $.ajax(param);
        return;
    }

    return {
        post:post,
        get:get
    };
})();

function getToken(){
    var storage = window.localStorage;
    return storage.getItem('token');
}

var Model = (function(){
    var urlMap = {
        'loginin':{'url':'/population/login/loginin'},
    };
    var handler = {};
    for(var name in urlMap){
        handler[name] = (function(obj){
                var reqtype = obj['type'] || 'post';
                var url = obj['url'] || '';
                var fun = function(param, success, inopts, error){
                    var token = getToken();
                    var opts = inopts || {};
                    return Request[reqtype](url, param, function(res){
                        if(res.code === CODE_RELOGIN){
                            window.location.href = "/population/login";
                            return;                            
                        }
                        // if(res.code === CODE_USER_HAVELOGIN){
                        //     window.location.href = "/house/login.html";
                        //     return;                            
                        // }
                        success(res);
                    }, function(res){
                        var callback = error || '';
                        if(callback === ''){
                            success({code:CODE_ERROR});
                        }else{
                            error(res)
                        }
                    }, opts);
                }
            return fun;
        })(urlMap[name] || {});
    }
    return handler;
})();
