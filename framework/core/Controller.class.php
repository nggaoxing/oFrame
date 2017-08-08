<?php

namespace core;
use core\Config;

//基础控制器类
class Controller{
	//属性
	protected $smarty;

	//构造方法
	public function __construct(){
		//加载模板
		$this->smarty = new \Smarty();
		//设置左右括号
		$this->smarty->left_delimiter = Config::get('left_delimiter');
		$this->smarty->right_delimiter = Config::get('right_delimiter');
		//获取控制器和方法
		$module = Request::instance()->module();
		$controller = Request::instance()->controller();
		//设置每个控制器对应的模板文件夹
		$this->smarty->template_dir = Config::get('template_dir').$module.'/'.$controller;
		//缓存文件
		$this->smarty->compile_dir = Config::get('compile_dir').$module.'/'.$controller;	
	}

	/**
	 * 模板赋值
	 * @param  [type] $field 字段
	 * @param  [type] $data  值
	 * @return [type]        
	 */
	public function assign($field,$data){
		$this->smarty->assign($field,$data);
	}

	/**
	 * 模板显示
	 * @param  [type] $html 模板页面
	 * @return [type] 
	 */
	public function display($html){
		$this->smarty->display($html);
		//日志记录
		Config::get('log_record') && \core\Log::save();
	}

	/**
     * 魔术方法__call
     * @param  [type] $method 方法
     * @param  [type] $args   参数
     * @return 
     */
    public function __call($method,$args){
    	//检测有没有_empty的方法
    	if(method_exists($this,'_empty')){
    		$this->_empty($method,$args);
    	}else{
    		throw new \Exception('method not exists:'.$method, 1);      
    	}
    }

    /**
	 * 页面重定向方法
	 * @param	$url		重定向的页面地址
	 * @param 	$message	页面跳转的提示信息
	 * @param 	$time	页面停留时间
	 * @return 	无  		结果是跳转重定向
	*/
	public function redirect($url,$message='',$time=2){
		if($message == ''){
			header('location:'.$url);
		}else{
			//跳转
			echo <<<HTML
				<HTML>
				 <HEAD>
				  <TITLE> 提示：$message </TITLE>
				  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
				  <META HTTP-EQUIV="Refresh" CONTENT="$time; url=$url" target="all"/>
				 </HEAD>
				 <BODY>
					$message
				 </BODY>
				</HTML>
HTML;
			
		}
		die;
	}



}