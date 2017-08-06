<?php

//用户配置文件
return [

	/*****模块配置*****/
	'default_module' => 'home',	//模块
	'default_controller' => 'index',		//控制器
	'default_action' => 'index',	//方法
    'empty_controller' => '',  //默认的空控制器
    'module_list' => ['index','admin'], //现有的模块

	/******path模式*******/
	'path_info'	=>	2,				//url模式(1.基础模式，2.重写模式)

	/******命名空间*****/
	'app_namespace' => 'app',

	/*****模板处理****/
	'left_delimiter' =>	'<{',		//左标签
	'right_delimiter'	=>	'}>',	//右标签
	'template_dir'	=> ROOT.'view/',	//html目录
	'compile_dir'	=>	ROOT.'runtime/cache/',	//编译文件目录

	/*******字符串过滤*******/
	'default_filter'	=>	'htmlspecialchars',		//过滤方法

	/******字符编码**********/
	'default_charset' => 'utf-8',

	/******session设置**********/
	'session' => [
        // SESSION 前缀
        'prefix'         => 'of',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    /******cookie的参数配置*******/
    'cookie' => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

   


];