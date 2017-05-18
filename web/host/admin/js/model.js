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

var
    Model = (function(){
    var urlMap = {
        'loginin':{'url':'/admin/user/doLogin'},    //登录
        'logout':{'url':'/admin/user/logout'},  //退出
        'addDepartment':{'url':'/admin/department/addDepartment'},  //添加部门（角色）
        'addRoleuser':{'url':'/admin/user/addRoleuser'},    //添加部门人员
        'getRoleUser':{'url':'/admin/user/getRoleUser'},    //搜索部门人员
        'getProType':{'url':'/admin/process/getProType'},    //事项分类
        'getDepartment':{'url':'/admin/department/getDepartment'},  //获取所有部门
        'addProcess':{'url':'/admin/process/addProcess'},   //添加新事项
        'getContent':{'url':'/admin/content/searchContent'},    //搜索内容
        'getProUnInstance':{'url':'/admin/process/getProUnInstance'},   //获取待处理事项
        'getMaterial':{'url':'/admin/process/getMaterial'},     //获取材料
        'acceptPro':{'url':'/admin/process/acceptPro'},     //受理
        'pending':{'url':'/admin/process/pending'},     //获取个人待审批事项
        'getProInfo':{'url':'/admin/process/getProInfo'},   //获取实例详情信息
        'denyPro':{'url':'/admin/process/denyPro'},     //拒绝申请
        'agreePro':{'url':'/admin/process/agreePro'},   //同意申请
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
                            swal({
                                title: "出错了！",
                                text: '登录已过期，请重新登录'
                            });
                            return;
                        }
                        else if(res.code === CODE_PERMISSION_DEND){
                            swal({
                                title: "出错了！",
                                text: '抱歉，您没有权限操作'
                            });
                        }
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
