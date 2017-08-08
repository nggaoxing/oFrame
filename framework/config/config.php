<?php

//用户配置文件
return [

	/*****模块配置*****/
	'default_module' => 'home',	//模块
	'default_controller' => 'index',		//控制器
	'default_action' => 'index',	//方法
    'empty_controller' => 'Enpty',  //默认的空控制器
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

    /*******日志设置***********/
    'log_record'            =>  true,   // 默认不记录日志
    'log_type'              =>  'File', // 日志记录类型 默认为文件方式
    'log_level'             =>  'SQL,EXC,ERR,FAT',// 允许记录的日志级别()
    'log_file_size'         =>  2097152,    // 日志文件大小限制
    'log_path'              =>  'runtime/log/',
    /*******错误提示设置**********/
    'error_reporting'   => 'E_NOTICE,E_WARNING,E_ALL',
   


];