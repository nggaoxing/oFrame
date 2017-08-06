<?php
namespace core;
use core\Config;

//cookie类
class Cookie{
	//基础配置
	protected static $config = [
        //cookie前缀
        'prefix'    => '',
        //cookie有效时间
        'expire'    => 0,
        //cookie路径
        'path'      => '/',
        //cookie有效域名
        'domain'    => '',
        //cookie 启用安全传输
        'secure'    => false,
        //httponly设置成 TRUE，Cookie 仅可通过 HTTP 协议访问。 这意思就是 Cookie 无法通过类似 JavaScript 这样的脚本语言访问。
        'httponly'  => '',
        //是否使用setcookie
        'setcookie' => true,
    ];
    //cookie开启表示
    protected static $cookie=false;

    /**
     * 设置或者获取cookie前缀
     * @param string $prefix
     * @return string|void
     */
    public static function prefix($prefix = ''){
        if(empty($prefix)){
            return self::$config['prefix'];
        }
        self::$config['prefix'] = $prefix;
    }

    /**
     * cookie的初始化
     * @param  array  $config 参数（数组）
     * @return 
     */
    public static function init($config = array()){
    	//没有传递参数就用默认配置参数
    	if(empty($config)){
    		$config = Config::get('cookie');
    	}
    	//和默认的配置合并(array_change_key_cause转换key的大小写，默认小写)
    	self::$config = array_merge(self::$config,array_change_key_case($config,CASE_LOWER));
    	//设置cookie的可访问设置
    	if (!empty(self::$config['httponly'])) {
            ini_set('session.cookie_httponly', 1); //配置文件设置
        }
        //保存cookie状态
        self::$cookie = true;
    }

    /**
     * 设置cookie值
     * @param [type] $name   名称
     * @param string $value  值
     * @param [type] $option 条件(数字是有效时间，数组就是配置)
     */
    public static function set($name, $value = '', $option = null){
    	//判断和初始化cookie
    	!isset(self::$cookie) && self::init();
    	//判断条件
    	if(!is_null($option)){	//有条件
    		if(is_numeric($option)){	//数字，表示有效时间
    			$option = ['expire'=>$option];
    		}elseif(is_string($option)){	//字符串，拆分为数组
    			parse_str($option, $option);	//这里是把字符串转成数组
    		}//array就不用判断了
			$config = array_merge(self::$config, array_change_key_case($option));//合并配置
    	}else{ //没条件使用默认配置
            $config = self::$config;
        }
        //组装前缀
        $name = $config['prefix'] . $name;
        //设置有效时间（存在：当前时间+有效，不存在：0）
        $expire = !empty($config['expire']) ? $_SERVER['REQUEST_TIME'] + intval($config['expire']) : 0;
        //转化值的格式为字符串
        if(is_array($value)){	//值是数组
        	//加encode:注明是编码的
        	$value = "encode:".urlencode(json_encode($value));	//转为json格式再urlencode编码
        }
       	//最后判断存储方式
       	if($config['setcookie']){  //存到本地
       		//名称，值，有效时间，路径，域名，安全，限制可访问
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }
        $_COOKIE[$name] = $value;	//存到数组
    }

    /**
     * 获取cookie的值
     * @param  string $name   名称
     * @param  [type] $prefix 前缀
     * @return
     */
    public static function get($name = '', $prefix = null){
    	//判断和初始化cookie
    	!isset(self::$cookie) && self::init();
    	//根据前缀获取名称
    	$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
        //判断传递的名称
    	if($name == ''){  //没，获取全部
    		if($prefix){	//有前缀
    			$value = [];
    			//循环所有的数组，找出有前缀的值
    			foreach ($_COOKIE as $key => $val) {
    				if( strpos($key, $prefix) === 0 ){ //表示有前缀
    					$value[$key] = $val;
    				}
    			}
    		}else{ //没前缀，整个cookie
                $value = $_COOKIE;
            }
    	}elseif(($key = $prefix.$name) && isset($_COOKIE[$key])){  //存在这个名称的cookie
    		$value = $_COOKIE[$key];
    		//在判断这个value是不是被编码了
    		if(strpos($value,'encode:') === 0){
    			$value = substr($value,7); //把前几个去掉
    			//反编码
    			$value = json_decode(urldecode($value),TRUE);
    		}
    	}else{	//都不存在
            $value = null;
        }
        //返回结果
        return $value;
    }

    /**
     * Cookie删除
     * @param  [type] $name   名称
     * @param  [type] $prefix 前缀
     * @return
     */
    public static function delete($name,$prefix = null){
    	//判断和初始化cookie
    	!isset(self::$cookie) && self::init();
    	//根据前缀获取名称
    	$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
    	$name   = $prefix . $name;
    	//配置
    	$config = self::$config;
    	//是否有本地cookie
    	if($config['setcookie']) {
    		//删除本地cookie(有效时间减少)
            setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }
        //删除指定cookie
        unset($_COOKIE[$name]);
    }

    /**
     * 情况cookie
     * @param  [type] $prefix 前缀
     * @return
     */
    public static function clear($prefix = null){
    	//已经没有cookie
        if(empty($_COOKIE)) {
            return;
        }
        //判断和初始化cookie
    	!isset(self::$cookie) && self::init();
    	//获取前缀
    	$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
    	//配置
    	$config = self::$config;
    	//存在前缀
    	if($prefix){
    		//循环所有的cookie
    		foreach($_COOKIE as $key => $val){
    			//查询前缀
    			if (strpos($key, $prefix) === 0) {
    				//本地存储
                    if($config['setcookie']) {
                    	//设置失效
                        setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    }
                    //删除cookie数组的值
                    unset($_COOKIE[$key]);
                }
    		}
    	}
    	return;
    }

    /**
     * 判断cookie的值是否存在
     * @param  [type]  $name   名称
     * @param  [type]  $prefix 前缀
     * @return
     */
    public static function has($name,$prefix = null){
    	//判断和初始化cookie
    	!isset(self::$cookie) && self::init();
    	//根据前缀获取名称
    	$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
    	$name   = $prefix . $name;
    	//判断
    	return isset($_COOKIE[$name]);
    }

}