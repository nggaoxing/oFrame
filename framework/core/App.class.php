<?php

use core\Config;
use core\Request;
use core\Session;
use core\Error;

//初始化app类
class App{
	//属性
	private static $config = [];	//配置文件

	//框架运行
	public static function run(){
		//注册自动加载
		spl_autoload_register('self::autoload');
		//开启日志
		
		//注册错误和异常处理
		Error::register();
		//加载配置文件
		self::loadConfig();
		//开启session
		Config::get('session.auto_start') && Session::start();
		//路由分发处理
		self::exec();
	}

	/**
	 * 执行应用程序
	 * @param  [type] $config 配置文件
	 * @return [type] 
	 */
	private static function exec(){
		//url地址处理
		Request::instance()->route();
		//获取分组，模块，方法
		$module = Request::instance()->module();
		if(!in_array($module,Config::get('module_list'))){
			(new \core\Emptys())->_empty($module,"module not exists");			
		}
		$controller = Request::instance()->controller();
		$action = Request::instance()->action();
		//定义命名空间
		$namespace = '\\'.Config::get('app_namespace').'\\'.strtolower($module).'\\controller\\'.ucfirst($controller);
		if(class_exists($namespace)){
			$class = new $namespace();
			$class->$action();

		}else{
			//这里加载空类(用户定义用用户的，没有用系统的)
			if($empty = Config::get('empty_controller')){
				$space = substr($namespace,0,strrpos($namespace,'\\')+1).$empty;

				if(class_exists($space)){
					//调用方法
					(new $space())->_empty();exit();
				}
			}
			//调用系统的空类
			(new \core\Emptys())->_empty($namespace,"controller not exists");			
		}
		
	} 

	/**
	 * 自定义自动加载
	 * @param  [type] $classname 类的名称
	 * @return [type]            [description]
	 */
	private static function autoload($classname){
		if($classname){
			//转化换行符
			$classname = str_replace("\\","/",$classname);
			//判断是不是控制器类
			if(isset(self::$config['app_namespace']) && explode('/',$classname)[0] == self::$config['app_namespace']){
				$file = ROOT.$classname.".class.php";
			}elseif(explode('/',$classname)[0] == 'core'){
				//核心类
				$file = FRAME_PATH.$classname.".class.php";
			}elseif(explode('/',$classname)[0] == 'org'){
				//扩展类
				$file = FRAME_PATH.$classname.".class.php";
			}
			if(file_exists($file)){
				include_once($file);
			}
		}
	}

	/**
	 * 加载配置文件
	 * @return [type] [description]
	 */
	private static function loadConfig(){
		//加载配置类
		$config = Config::get();
		//返回
		self::$config = $config;
	}

}