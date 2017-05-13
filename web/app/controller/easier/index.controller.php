<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/4/10
 * Time: 16:56
 */

class IndexController extends Controller{

    function __construct(){
        parent::__construct();
    }

    function render(){
        $contentObj =  Box::getObject('content','model','admin');
        $commonObj = Box::getObject('common','controller','public');
        $imgAtt = $commonObj::entityStripHtml($contentObj->indexAttImg(),'contents');
        $att = $contentObj->indexAtt();
        $poli = $contentObj->indexPoli();
        $heat = Box::getObject('process','model','admin')->getHeatPro();
        $heatIcon = [
            'icon-xingzhengbangong','icon-bangong','icon-bangongxiezuoruanjian','icon-bangongchangdi','icon-zonghebangong',
            'icon-weibangongguanli','icon-bangongdizhi','icon-bangongshenling','icon-xietongbangong','icon-15',
            'icon-yunlai--bangongxietong','icon-bangongxiezuoruanjian','icon-weibiaoti47','icon-91juchangbangonghuiguanli','icon-perfoemance-old'
        ];

        $this->_twig->assign('data',['imgAtt'=>$imgAtt,'att'=>$att,'poli'=>$poli,'heat'=>$heat,'icon'=>$heatIcon]);
        return $this->viewTpl ('index.html');
    }
}