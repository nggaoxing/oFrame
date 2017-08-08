<?php

//config处理类
namespace core;

class Config{
	//属性
	protected static $config = [];  //配置数组

	//加载所有配置文件
	public static function get($name=""){
		//加载系统配置文件
		$sys_config = require CONFIG_FILE;
		//加载用户配置文件
		$user_config = require LOAD_CONFIG_FILE;
		//合并配置
		self::$config = array_merge($sys_config,$user_config);
		if($name==""){
			return self::$config;
		}else{
			if (!strpos($name, '.')) {	//不带点的一个值
	            $name = strtolower($name);
	            return isset(self::$config[$name]) ? self::$config[$name] : null;
	        } else {
	            // 二维数组设置和获取支持
	            $name    = explode('.', $name, 2);
	            $name[0] = strtolower($name[0]);
	            return isset(self::$config[$name[0]][$name[1]]) ? self::$config[$name[0]][$name[1]] : null;
	        }
		}
		
	}

	//加载数据库配置文件
	public static function db(){
		$db_config = require DATABASE_FILE;
		return $db_config;
	}



}









