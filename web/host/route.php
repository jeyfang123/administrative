<?php
    header("Access-Control-Allow-Origin: *");
    define ('WEB_ROOT', __DIR__ . '/');		//\administrative\web\host\
    define ('WEB_BASE', dirname(WEB_ROOT));   //\administrative\web
    date_default_timezone_set('Etc/GMT-8');
    include_once WEB_BASE . '/vendor/autoload.php';
    include_once WEB_BASE . '/config/app.php';
    include_once WEB_BASE . '/config/dtprec.php';
    global $permissionConf;
    $permissionConf = include_once WEB_BASE . '/config/config.php';

    if(DEBUG){
        ini_set('display_errors','1');
    }
    else{
        ini_set('display_errors','0');
    }

    $router = new \Klein\Klein();

    $router->respond(['GET','POST'], '/[:product]/[:controller]/[:func]', function($request, $response, $service){
        define('PRODUCT', $request->product);
        define('PRODUCT_TEMPLATE_DIR', WEB_BASE . "/host/" . PRODUCT.'/views/');

        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/[:product]/[:controller]', function($request, $response, $service){
        define('PRODUCT', $request->product);
        define('PRODUCT_TEMPLATE_DIR', WEB_BASE . "/host/" . PRODUCT.'/views/');

        $request->func = "render";
        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/[:product]', function($request, $response, $service){
        define('PRODUCT', $request->product);
        define('PRODUCT_TEMPLATE_DIR', WEB_BASE . "/host/" . PRODUCT.'/views/');

        $request->controller = "index";
        $request->func = "render";
        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/', function($request, $response, $service){
        define('PRODUCT', $request->product);
        define('PRODUCT_TEMPLATE_DIR', WEB_BASE . "/host/" . PRODUCT.'/views/');

        header('Location:/index.html');
        exit;
    });

    function routerCallback($request, $response, $service,$product){
        $obj = Box::getObject($request->controller, 'controller', $product);
        $func = $request->func;
        if($obj == null || !method_exists($obj,$func)){
            header('Location:/404.html');
            exit();
        }
//        return $obj->$func($request, $response, $service);
        if($func == 'login' || $func == 'doLogin'){
            return $obj->$func($request, $response, $service);
        }

        $permission = Box::getObject('permission', 'controller', 'public');
        //后台验证
        if($product === 'admin'){
            $checkLoginRes = json_decode($permission->checkLogin($request));
            if($checkLoginRes->code === CODE_RELOGIN){
                return Box::getObject('user','controller',$product)->login();
            }
            else if($checkLoginRes->code === CODE_USER_HAVELOGIN){
                $request->user = $checkLoginRes->user;
                $checkPerRes = $permission->checkPermission($request);
                if($checkPerRes == false){
                    return Box::getObject('common','controller','public')->noPermission();
                }
                else{
                    return $obj->$func($request, $response, $service);
                }
            }
        }
        else if($product === 'public'){
            return $obj->$func($request, $response, $service);
        }
        //前台验证
        else{
            return $obj->$func($request, $response, $service);
        }
    }

    function straightPass($req){
        $product = $req->product;
        $controller = $req->controller;
        $func = $req->func;
        global $permissionConf;
        if($product != 'admin' && @$permissionConf['func'][$func] === true){
            return true;
        }
        else{
            return false;
        }
    }

    $router->dispatch();


