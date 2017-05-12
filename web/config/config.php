<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/20
 * Time: 14:58
 */
/*接口免认证*/
return [
    'func' => [
        'render' => true,
    ],
    'controller' => [

    ],
    'product' => [

    ],
    //公共后台接口
    'path' => [
        '/admin/index'=>true,
        '/admin/index/home'=>true,
        '/admin/department'=>true,
        '/admin/user/roleuser'=>true,
        '/admin/process/getProType'=>true,
        '/admin/department/getDepartment'=>true,
        '/admin/content/attention'=>true,
        '/admin/content/searchContent'=>true,
        '/admin/content/detail'=>true,
        '/admin/content/interpretation'=>true,
    ]
];