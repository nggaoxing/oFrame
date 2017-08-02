<?php

namespace core;
use core\Config;

//request类(请求的处理)
class Request{
	//属性
	protected static $instance;	//实例对象
	protected $path_info;  	//PATH_INFO
	protected $path_style;  //PATH_INFO模式
	protected $module;  	//module
	protected $controller; 	//controller
	protected $action;  	//action

	//构造函数
	protected  function __construct(){
		//获取path_info方式
		$this->path_style = Config::get('path_info');
		//获取path_info
		$this->path_info = !empty($this->path_info) ? $this->path_info : $this->pathinfo();
	}
	//克隆
	protected function __clone(){

	}

	/**
	 * 初始对象
	 */
	public static function instance(){
		if(empty(self::$instance)){
			//创建对象
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * 解析url的参数
	 * @return [type] [description]
	 */
	public  function route(){
		
	}

	/**
	 * 获取pathinfo
	 * @return [type] [description]
	 */
	public function pathinfo(){
		if(empty($this->path_info)){
			//设置path_info
			$this->path_info = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
			//根据pathinfo的模式截取信息
			if($this->path_style == 1){
				//基础模式
				
			}elseif($this->path_style == 2){
				//根
				if($this->path_info == "/"){
					$this->module = Config::get('default_module');
					$this->controller = Config::get('default_controller');
					$this->action = Config::get('default_action');
				}else{
					//重写模式
					$arr_path = explode('/',$this->path_info);
					//控制器，分组，方法
					$this->module = isset($arr_path[0]) ? $arr_path[0] : Config::get('default_module') ;unset($arr_path[0]);
					$this->controller = isset($arr_path[1]) ? $arr_path[1] : Config::get('default_controller');unset($arr_path[1]);
					$this->action = isset($arr_path[2]) ? $arr_path[2] : Config::get('default_action');unset($arr_path[2]);
					//参数
					foreach ($arr_path as $k => $v) {
						if(isset($arr_path[$k]) && isset($arr_path[$k+1])){
							$_GET[$v] = $arr_path[$k+1];
							unset($arr_path[$k]);
							unset($arr_path[$k+1]);
						}
					}
				}
				
			}
		}
		return $this->path_info;
	}

	/**
	 * 获取分组
	 * @return
	 */
	public function module(){
		if(!empty($this->module)){
			return $this->module;
		}
	}

	/**
	 * 获取控制器
	 * @return
	 */
	public function controller(){
		if(!empty($this->controller)){
			return $this->controller;
		}
	}

	/**
	 * 获取方法
	 * @return
	 */
	public function action(){
		if(!empty($this->action)){
			return $this->action;
		}
	}

}



















