<?php
    header("Access-Control-Allow-Origin: *");
    define ('WEB_ROOT', __DIR__ . '/');		//\administrative\web\host\main
    define ('WEB_BASE', dirname(dirname(WEB_ROOT)));   //\administrative\web
    date_default_timezone_set('Etc/GMT-8');
    include_once WEB_BASE . '/vendor/autoload.php';

    $config = include_once WEB_BASE . '/config/config.php';

    $product = \Klein\Request::queryProduct($config['products'],'property');

    if ($product === false){
        header('Location:/404.html');
        exit();
    }

    define('PRODUCT', $product);
    define('DEBUG', false);

    include_once WEB_BASE . '/config/app.php';
    // include_once WEB_BASE . '/config/'.PRODUCT.'/dtprec.php';
	include_once WEB_BASE . '/config/dtprec.php';

    $router = new \Klein\Klein();

    $router->respond(['GET','POST'], '/[:product]/[:controller]/[:func]', function($request, $response, $service){
        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/[:product]/[:controller]', function($request, $response, $service){
        $request->func = "render";
        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/[:product]', function($request, $response, $service){
        $request->controller = "index";
        $request->func = "render";
        return routerCallback($request, $response, $service, $request->product);
    });

    $router->respond(['GET','POST'], '/', function($request, $response, $service){
        header('Location:/index.html');
        exit;
    });

    function routerCallback($request, $response, $service,$product){
        $obj = Box::getObject($request->controller, 'controller', $product);
        $func = $request->func;
        if($obj == null || !method_exists($obj,$func)){
            header('HTTP/1.1 404 Not Found');
            return;
        }

        /** 权限验证 */
        $permission = Box::getObject('Permission', 'controller', 'zhwy');
        //$permission->noLoginPermission($request);
        $permission->checkLogin($request);
        return $obj->$func($request, $response, $service);
    }


    $router->dispatch();

?>
