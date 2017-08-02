<?php

//config处理类
namespace core;

class Config{
	//属性
	protected static $config = [];  //配置数组

	//加载所有配置文件
	public static function get($field=""){
		//加载系统配置文件
		$sys_config = require config_file;
		//加载用户配置文件
		$user_config = require load_config_file;
		//合并配置
		self::$config = array_merge($sys_config,$user_config);
		if($field==""){
			return self::$config;
		}else{
			if(array_key_exists($field,self::$config)){
				return self::$config[$field];
			}else{
				return '';
			}	
		}
		
	}

	//加载数据库配置文件
	public static function db(){
		$db_config = require database_file;
		return $db_config;
	}



}









