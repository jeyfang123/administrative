$(function() {
    //如果选择记住密码  页面加载时自动填写
    window.onload = function(){
        if ($.cookie("rmbUser") == "true") {
            $("#checkbox").prop("checked", true);
            $("#username").val($.cookie("username"));
            $("#password").val($.cookie("password"));
        }
        $('#more').popover({trigger:"hover"})
    }

    var logining = false;
    $("#loginBtn").click(function () {
        if (!logining) {
            logining = true;
            var username = $('#username').val();
            var password = $('#password').val();
            if (username != '' && password != '') {
                Model.loginin({"username": username, "password": hex_md5(password)}, function (res) {
                    if (res.code === CODE_SUCCESS) {
                        if ($("#checkbox").prop("checked")) {
                            $.cookie("rmbUser", "true", { expires: 7 }); //存储一个带7天期限的cookie   
                            $.cookie("username", username, { expires: 7 }); 
                            $.cookie("password", password, { expires: 7 }); 
                        }else{ 
                            $.cookie("rmbUser", "false", { expires: -1 });
                            $.cookie("username", "", { expires: -1 }); 
                            $.cookie("password", "", { expires: -1 }); 
                        }
                        window.location.href = "/population/index";
                    }
                    else {
                        tip(res.msg);
                    }
                    logining = false;
                }, {}, function (res) {
                    logining = false;
                });
            } else {
                tip("用户名或者密码未填写");
                logining = false;
            }
        }
    });
    
    $("body").keydown(function(event){   
        if (event.keyCode == 13) {           
            $("#loginBtn").click();    
            event.preventDefault();        
        }
    });
    $("body").height($(document).height());

    //$("#loginBtn").trigger("click");
});
$(window).resize(function(){
    $("body").height($(window).height());
});

