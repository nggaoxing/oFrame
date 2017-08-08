<?php

//系统初始化文件

//定义根目录
define('ROOT',str_replace("framework/init.php","",str_replace("\\","/",__FILE__)));

//定义开始时间
define('START_TIME',microtime(true));

//定义相关的常量
define('FRAME_PATH',ROOT."framework/");			//框架目录
define('CORE_PATH',FRAME_PATH."core/");			//核心目录
define('LIB_PATH',FRAME_PATH."lib/");			//lib目录
define('LOAD_CONFIG_PATH',ROOT."config/");			//用户配置文件目录
define('CONFIG_PATH',FRAME_PATH."config/");			//系统配置文件目录
define('LOAD_EXT_CONFIG',"");					//自定义配置文件
define('LOAD_CONFIG_FILE',LOAD_CONFIG_PATH."config.php");	//用户配置文件
define('CONFIG_FILE',CONFIG_PATH."config.php");	//系统配置文件
define('DATABASE_FILE',LOAD_CONFIG_PATH."database.php");	//数据库配置文件

define('LOAD_FUNC_PATH',ROOT."common/");			//用户函数文件目录
define('FUNC_PATH',FRAME_PATH."common/");			//系统函数文件目录

define('LOAD_FUNC_FILE',LOAD_FUNC_PATH."function.php");			//用户函数文件
define('FUNC_FILE',FUNC_PATH."function.php");			//系统函数文件
define('ERROR_FILE',FRAME_PATH."tpl/error.php");	//错误信息提示文件
define('ERROR_LANG',FRAME_PATH."lang/zh.php");	//定义错误语言包

//加载模板类
require_once FRAME_PATH."org\smarty\Smarty.class.php";

//加载方法
require_once FUNC_FILE;
require_once LOAD_FUNC_FILE;

//运行
require_once CORE_PATH."App.class.php";
App::run();





















