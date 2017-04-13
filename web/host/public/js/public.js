var CODE_SUCCESS = 200;
var CODE_ERROR = 300;
var CODE_RELOGIN = 211;
var CODE_Already_Registered =  212;
var CODE_NotAdmin =  213;
// var CODE_USER_HAVELOGIN = 205;
var scrollFixed = function(el) {
    if (!el) return;
    var loaded = true;
    var top = el.offset().top;

    function onWindowScroll() {
        var scrolla = $(window).scrollTop();
        var cha = parseInt(top) - parseInt(scrolla);
        if (loaded && cha <= 0) {
            el.addClass("pos-fixed");
            loaded = false;
        }
        if (!loaded && cha > 0) {
            el.removeClass("pos-fixed");
            loaded = true;
        }
    }
    $(window).scroll(onWindowScroll);
}

function getAppList() {
    // var token = Storage.get('token');
    // var model = new Model(token);
    var applisthtm = '';

    var param = getHashParams();
    var currentapp = param['name'] || '';
    Model.listMyApp({},
        function(res) {
            if (res.code === CODE_SUCCESS) {
                var list = res.result.rows || [];
                applisthtm += '<ul class="dropdown-menu">';
                var currentid = ''
                for (var i = 0; i < list.length; ++i) {
                    var detail = list[i]['detail'] || '{}';
                    detail = JSON.parse(detail);
                    var appname = detail.appname;
                    var id = list[i]['id'];
                    if (currentapp === '') {
                        currentapp = appname;
                    }
                    if (currentapp === appname) {
                        currentid = id;
                        continue;
                    }
                    applisthtm += '<li><a href="#applist?name=' + appname + '">' + appname + '</a></li>';
                }
                applisthtm += '</ul>';
            }

            $('#applist').append(applisthtm);
            $('#current-app').html(currentapp);
            $('#current-app').trigger('onAppChange', currentid);
        }
    );
}

function showLoading(tips) {
    var margin_top = $(this).height() / 4;

    var loadingModal = '<div class="modal fade bs-modal-sm" id="loading111" role="dialog" aria-hidden="true" \
     data-backdrop="static" style="width:600px; margin:0 auto"> \
        <div class="modal-dialog modal-sm"> \
            <div class="modal-content"> \
                <div class="modal-header"> \
                    <h4 id="loading-tips" class="modal-title"></h4> \
                </div> \
                <div class="modal-body"> \
                    <div id="site_statistics_loading"> \
                        <img src="img/loading.gif" alt="loading" /> </div> \
                </div> \
            </div> \
        </div> \
    </div>';

    $('#loading111').remove();
    $('body').append(loadingModal);
    var str = tips || 'loading....';
    $('#loading-tips').text(str);

    $('#loading111').modal('show');
}

function hideLoading() {
    $('#loading111').modal('hide');
}
function tip(msg) {
    var tipModal = '<div class="modal fade" id="tipModal" data-backdrop="static">\
                          <div class="modal-dialog modal-sm">\
                            <div class="modal-content">\
                              <div class="modal-body">\
                                <p>' + msg + '</p>\
                              </div>\
                              <div class="modal-footer">\
                                <button type="button" class="btn btn-default" id="hide-tip" data-dismiss="modal">确定</button>\
                              </div>\
                            </div>\
                          </div>\
                        </div>';

    $('#tipModal').remove();
    $('body').append(tipModal);
    $('#tipModal').modal('show');
    $('#hide-tip').click(function(ev) {
        $('#tipModal').modal('hide');
    })
    setTimeout(function () {
        $('#tipModal').modal('hide');
    },2000);
}
function showAlert(msg, callback) {
    var alertModal = '<div class="modal fade" id="alertModal" data-backdrop="static">\
                          <div class="modal-dialog modal-sm">\
                            <div class="modal-content">\
                              <div class="modal-body">\
                                <p>' + msg + '</p>\
                              </div>\
                              <div class="modal-footer">\
                                <button type="button" class="btn btn-default" id="hide-alert" data-dismiss="modal">确定</button>\
                              </div>\
                            </div>\
                          </div>\
                        </div>';

    $('#alertModal').remove();
    $('body').append(alertModal);
    $('#alertModal').modal('show');

    $('#hide-alert').click(function(ev) {
        // ev.stopPropagation();
        if (typeof callback === 'function') {
            callback();
        }
    })
}
function getHashParams() {
    var hashParams = {};
    var e,
        a = /\+/g, // Regex for replacing addition symbol with a space
        r = /([^&;=]+)=?([^&;]*)/g,
        d = function(s) {
            return decodeURIComponent(s.replace(a, " "));
        },
        q = window.location.hash.substring(1);
    t = q.split('?');
    p = t[1] || '';

    while (e = r.exec(p))
        hashParams[d(e[1])] = d(e[2]);

    return hashParams;
}
var Storage = (function() {
    var storage = window.localStorage;

    var set = function(key, val) {
        storage.setItem(key, val);
    }

    var get = function(key, defaultval) {
        return storage.getItem(key);
    }

    var del = function(key) {
        storage.removeItem(key);
    }

    return {
        'set': set,
        'get': get,
        'del': del
    }
})();

