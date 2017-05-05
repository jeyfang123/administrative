$(function() {
    //如果选择记住密码  页面加载时自动填写
    window.onload = function(){
        if ($.cookie("rmbUser") == "true") {
            $("#checkbox-signup").prop("checked", true);
            $("#username").val($.cookie("username"));
            $("#password").val($.cookie("password"));
        }
        $('#more').popover({trigger:"hover"})
    }

    $("#loginBtn").click(function () {
        var username = $('#username').val();
        var password = $('#password').val();
        if (username != '' && password != '') {
            Model.loginin({"username": username, "password": hex_md5(password)}, function (res) {
                if (res.code === CODE_SUCCESS) {
                    if ($("#checkbox-signup").prop("checked")) {
                        $.cookie("rmbUser", "true", { expires: 7 });
                        $.cookie("username", username, { expires: 7 });
                        $.cookie("password", password, { expires: 7 });
                    }else{
                        $.cookie("rmbUser", "false", { expires: -1 });
                        $.cookie("username", "", { expires: -1 });
                        $.cookie("password", "", { expires: -1 });
                    }
                    window.location.href = "/admin/index";
                }
                else {
                    tip(res.msg);
                }
            }, {}, {});
        }
    });

    $("body").keydown(function(event){
        if (event.keyCode == 13) {
            $("#loginBtn").click();
            event.preventDefault();
        }
    });
    $("body").height($(document).height());
});

$(window).resize(function(){
    $("body").height($(window).height());
});

