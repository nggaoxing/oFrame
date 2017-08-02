<?php

use core\Config;
use core\Request;

//初始化app类
class App{
	//属性
	private static $config = [];	//配置文件

	//框架运行
	public static function run(){
		//注册自动加载
		spl_autoload_register('self::autoload');
		//注册错误和异常处理

		//加载配置文件
		self::loadConfig();

		//处理一些配置的事

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
		$controller = Request::instance()->controller();
		$action = Request::instance()->action();
		//定义命名空间
		$namespace = '\\'.Config::get('app_namespace').'\\'.strtolower($module).'\\controller\\'.ucfirst($controller);
		$class = new $namespace();
		$class->$action();
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
				$file = frame_path.$classname.".class.php";
			}elseif(explode('/',$classname)[0] == 'org'){
				//扩展类
				$file = frame_path.$classname.".class.php";
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