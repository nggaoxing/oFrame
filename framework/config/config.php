<?php

//用户配置文件
return [

	/*****模块配置*****/
	'default_module' => 'home',	//模块
	'default_controller' => 'index',		//控制器
	'default_action' => 'index',	//方法

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
];