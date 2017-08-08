<?php
namespace core;

//错误异常处理
class Error{
	//错误级别
	protected static $errNo = [
		'E_WARNING'	=>	2,
		'E_NOTICE'	=>	8,
		'E_USER_ERROR'	=>	256,
		'E_USER_WARNING'	=>	512,
		'E_USER_NOTICE'	=>	1024,
		'E_RECOVERABLE_ERROR'	=>	4096,
		'E_ALL'	=>	8191,
	];

	/**
	 * 错误，异常处理注册
	 * @return [type] [description]
	 */
	public static function register(){
		//设置报错级别
		error_reporting(0);
		//注册错误处理
		set_error_handler([__CLASS__,'ofError']);
		//注册异常处理
		set_exception_handler([__CLASS__,'ofException']);
		//注册最后的收集错误
		register_shutdown_function([__CLASS__,'ofShutDown']);
	}

	/**
	 * 自定义错误处理
	 * @param  [type] $errno   错误编号
	 * @param  [type] $errstr  错误信息
	 * @param  [type] $errfile 错误文件
	 * @param  [type] $errline 出错行号
	 * @return 
	 */
	public static function ofError($errno,$errstr,$errfile,$errline){
		//根据配置查询现在可以显示的错误信息
		$errReport = \core\Config::get('error_reporting');
		$errReport = explode(',',$errReport);
		foreach ($errReport as $key => $val) {
			if(array_key_exists($val,self::$errNo)){
				$report[]=self::$errNo[$val];
			}
		}
		//获取信息并截取
		$lang = self::getLang($errstr);
		if($lang){
			//$val = substr($errstr,strpos($errstr,":")) ? substr($errstr,strpos($errstr,":")) : '';
			$err['title'] = $lang.$errstr;
		}else{
			$err['title'] = $errstr;
		}
		$err['file']  = $errfile;
        $err['line']  = $errline;
        $err['trace']  = '';
        //写入日志数据
        Log::addLog($err['title']."	FILE：".$err['file']." LINE:".$err['line'],'ERR');
        //这里根据可以报错的设置显示页面
        if(in_array($errno,$report)){
        	//显示页面
			self::halt($err);
        }else{
        	return;
        }
	}

	/**
	 * 获取对应的错误信息
	 * @return [type] [description]
	 */
	protected static function getLang($errstr){
		$langs = lang();
		//循环遍历
		foreach ($langs as $key => $val) {
			if(strpos($errstr,$key) === 0){
				return $val;
			}
		}
		return false;
	}

	/**
	 * 自定义异常处理
	 * @param  Exception $e [description]
	 * @return [type]       [description]
	 */
	public static function ofException(\Exception $e){
		$message = $e->getMessage();
		$lang = self::getLang($message);
		if(isset($lang)){
			//$val = substr($message,strpos($message,":")+1) ? substr($message,strpos($message,":")) : '';
			$err['title'] = $lang.$message;
		}else{
			$err['title'] = $message;
		}
		$err['file']  = $e->getFile();
        $err['line']  = $e->getLine();
        $err['trace']  = self::treeTrace($e->getTrace());
        //写入日志数据
        Log::addLog($err['title'],'EXC');
        //显示页面
		self::halt($err);
	}

	/**
	 * 设置trace信息为字符串树
	 * @param  [type] $trace [description]
	 * @return [type]        [description]
	 */
	protected static function treeTrace($trace){
		//循环组合信息
		$tree = [];
		foreach($trace as $key => $val){
			//dd($val);
			$string = "";
			//文件
			$string .= "FILE：".$val['file']."；";
			//判断是否有class
			if(isset($val['class'])){
				$string .= $val['class'].$val['type'].$val['function']."()；";
			}elseif(!isset($val['class']) && isset($val['function'])){
				$string .= $val['function']."()；";
			}
			//行号
			$string .= "LINE：".$val['line'];
			$tree[] = $string;
		}
		return $tree;
	}

	/**
	 * 最后的错误收集
	 * @return
	 */
	public static function ofShutDown(){
		$err = error_get_last();
		if($err){
			$lang = self::getLang($err['message']);
			if($lang){
				$err['title'] = $lang.': '.$err['message'];
			}else{
				$err['title'] = $err['message'];
			}
        	$err['trace'] = '';
        	//写入日志数据
	        Log::addLog($err['title']."	FILE：".$err['file']." LINE:".$err['line'],'FAT');
	        //显示页面
			self::halt($err);
		}
		
	}

	/**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
   public static function halt($err){
    	include ERROR_FILE;
    	//日志记录
		Config::get('log_record') && \core\Log::save();
    	exit();
    }

}




