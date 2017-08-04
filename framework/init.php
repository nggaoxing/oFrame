<?php

//系统初始化文件

//定义根目录
define('ROOT',str_replace("framework/init.php","",str_replace("\\","/",__FILE__)));

//定义相关的常量
define('frame_path',ROOT."framework/");			//框架目录
define('core_path',frame_path."core/");			//核心目录
define('lib_path',frame_path."lib/");			//lib目录
define('load_config_path',ROOT."config/");			//用户配置文件目录
define('config_path',frame_path."config/");			//系统配置文件目录
define('load_ext_config',"");					//自定义配置文件
define('load_config_file',load_config_path."config.php");	//用户配置文件
define('config_file',config_path."config.php");	//系统配置文件
define('database_file',load_config_path."database.php");	//数据库配置文件

define('load_func_path',ROOT."common/");			//用户函数文件目录
define('func_path',frame_path."common/");			//系统函数文件目录

define('load_func_file',load_func_path."function.php");			//用户函数文件
define('func_file',func_path."function.php");			//系统函数文件

//加载模板类
require_once frame_path."org\Libs\Smarty.class.php";

//加载方法
require_once func_file;
require_once load_func_file;

//运行
require_once core_path."App.class.php";
App::run();





















