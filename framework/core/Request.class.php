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
	protected $param_get=array();		//get参数
	protected $param_post=array();		//post参数
	protected $param;		//参数

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
	 * 获取传递的参数
	 * @return [type] [description]
	 */
	public function param(){
		//获取post参数
		$this->param_post = $this->input('post.');
		//返回
		return ['get'=>$this->param_get,'post'=>$this->param_post];
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
							$this->param_get[$v] = $arr_path[$k+1];
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
	 * 获取提交的数据
	 * @param  [type] $name    名称
	 * @param  string $default 默认值
	 * @param  [type] $filter  过滤方法
	 * @return [type]
	 */
	public function input($name="",$default=null,$filter=''){
		//	先判断传递的参数是什么
		//  / 表示获取的结果设定（/a表示是数据）
		$p = "";
		if(strpos($name,"/")){
			list($p,$type) = explode("/",$name,2);
			$method = "post";
		}elseif(strpos($name,".")){ //  post.  get. 表示获取整个post，get
			if(substr($name,-1) == '.'){
				$method = rtrim($name,'.');
			}else{
				//  post.name  get.name 表示获取post，get的name的值
				list($method,$p) = explode(".",$name,2);
			}
		}elseif($name == ""){
			$method = "get,post";
		}else{
			//  name 表示获取当前的name的值
			$method = "param";
			$p = $name;
		}
		//根据方法获取数据
		switch ($method) {
			case 'get':
				if(!empty($p)){
					$data = isset($this->param_get[$p]) ? $this->param_get[$p] : ''; //获取单个数据
				}else{
					$data = $this->param_get; //获取整个
				}
				break;
			case 'post':
				if(!empty($p)){
					$data = isset($_POST[$p]) ? $_POST[$p] : ''; //获取单个数据
				}else{
					$data = $_POST; //获取整个
					$this->param_post = $_POST; //获取整个
				}
				break;
			case 'param':
				$param_get = isset($this->param_get[$name]) ? $this->param_get[$name] : "";
				$data = isset($_POST[$p]) ? $_POST[$p] : $param_get; //获取单个数据
				break;
			case 'get,post':
				$data = array_merge($this->param_get,$_POST);
				break;
			default:
				return null;
				break;
		}

		//根据$type组合数据
		if(!empty($type)){
        	switch(strtolower($type)){
        		case 'a':	// 数组
        			$data 	=	(array)$data;
        			break;
        		case 'd':	// 数字
        		 	$data 	=	(int)$data;
        		 	break;
        		case 'f':	// 浮点
        			$data 	=	(float)$data;
        			break;
        		case 'b':	// 布尔
        			$data 	=	(boolean)$data;
        			break;
                default:
        	}
        }

        //根据参数过滤字符串
        $filter = !empty($filter) ? $filter : Config::get('default_filter');
        if($filter){
        	//进行过滤
        	$data = $this->filter($data,$filter);
        }
        //返回结果
		return !empty($data) ? $data : $default;
	}

	/**
	 * 字符串的过滤
	 * @param  [type] $data   数据
	 * @param  [type] $filter 过滤方法
	 * @return data
	 */
	private function filter($data,$filter){
		//把过量函数转为数组
		$filters = explode(',',$filter);
		//循环过滤
		foreach ($filters as $k => $filter) {
			$data = \array_map_recursive($filter,$data);
		}
		//返回结果
		return $data;
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



















