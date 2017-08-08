<?php
namespace core;
use core\Request;

//日志记录
class Log{
	//日志信息
    static protected $log = array();

    /**
     * 累计日志信息
     * @param [type] $message 日志信息
     * @param string $level   日志级别
     */
    public static function addLog($message,$level=""){
    	self::$log[] =   "{$level}: {$message}\r\n";
    }

    /**
     * 存储日志
     * @return [type] [description]
     */
    public static function save(){
    	//无日志
    	if(empty(self::$log)) return ;
    	//拆分日志
    	$message = implode('',self::$log);
    	//写入日志
    	self::wirte($message);
		//保存后清空日志缓存
        self::$log = array();
    }

    /**
     * 日志写入
     * @param  [type] $message 信息
     * @param  string $type    存储方式
     * @return
     */
    public static function wirte($message,$type=""){
        //判断筛选日志
        $log_level = \core\Config::get('log_level');
        $log_level = explode(',',$log_level);
        $message = explode("\r\n",$message);
        foreach ($log_level as $key => $level) {
           foreach ($message as $mkey => $mval) {
              if(strpos($mval,$level.":") === 0){
                  $logs[] = $mval;  //日志储存为数组数据
              }
           }
        }
    	//增加一些备注
    	$msg = "";
    	$msg .= "[ ".date('Y-m-d H:i:s')." ] ".Request::instance()->host()." ".Request::instance()->url()."\r\n";
        $msg .= "INFO: -------------[ APP_START ]-------------\r\n";
        $msg .= implode("\r\n",$logs)."\r\n";
        $msg .= "RUNTIME: ".number_format((microtime(true) - START_TIME),6)."\r\n";
        $msg .= "INFO: -------------[ APP_END ]-------------";
    	$msg .= "\r\n\r\n";
    	//日志名
    	$file = ROOT.\core\Config::get('log_path').date('y_m_d').'.log';  
        // 自动创建日志目录
        $log_dir = dirname($file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }  
        //判断日志大小写入日志,重新生成
        if(is_file($file) && floor(\core\Config::get('log_file_size')) <= filesize($file) ){
            rename($file,dirname($file).'/'.time().'-'.basename($file));
        }
    	//写日志
    	error_log($msg,3,$file);
    }



}	






















