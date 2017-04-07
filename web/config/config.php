<?php
/**
 * 公共配置
 * @since 2016-11-24
 * @author fushangui
 */

return [
	/* 所有可接受的项目名称 */
	'products'=>[
		'admin',
		'committee',
		'data',
		'datrix',
		'house',
		'property',
		'street',
		'wechat',
		'zhwy',
		'app',
        'firecontrol',
        'financialoffice',
        'anjian',
        'jiandu',
		'population',
        'enterprise',
		'overall',
        'humansocial',
        'business',
        'publicsecurity'
	],
	/* 不做验证的api */
	'publicApi'=>[
		/** admin **/
		'/admin/admin/login',

        '/committee/login',
        '/committee/login/loginin',

		/** 房管局 **/
        '/house/login',
        '/house/login/loginin',
		'/house/login/loginHtml',
		'/house/user/login',

		/** 物业管理端 **/
        '/property/login',
		'/property/user/login',
		'/property/login/loginHtml',

		/** 物业app端 **/
		'/app/user/login'
	],
	
	/* 不做验证的项目名 */
	'publicProduct' => [
		'wechat',
		'zhwy',
		'data',
		'datrix',
        'firecontrol',
        'financialoffice',
        'anjian',
        'jiandu',
		'population',
        'enterprise',
		'overall',
        'humansocial',
        'business',
        'publicsecurity'
	] 

];
?>