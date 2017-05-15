/**
 * Created by jey on 2017/5/15.
 */
$(function () {
    var access = $.cookie('access');
    if(access != ''){
        Model.checkUser({
            'access':access
        },function (res) {
            if(res.code == CODE_RELOGIN){
                var unLogin = '<a href="/easier/views/login.html">注册</a> / <a href="/easier/views/login.html">登录</a>';
                $('.user-info').html(unLogin);
            }
            else if(res.code == CODE_USER_HAVELOGIN){
                var login = '<span>您好，'+ (res.user.nickname ? res.user.nickname : res.user.username) +'欢迎登录</span>';
                $('.user-info').html(login);
            }
        },{},{});
    }
})

