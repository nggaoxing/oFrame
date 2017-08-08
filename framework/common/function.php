<?php
//系统函数库

/**
 * 递归过滤函数
 * @param  [type] $filter 过滤方法
 * @param  [type] $data   数据
 * @return 数据
 */
function array_map_recursive($filter, $data) {
	if(is_string($data)){
		return $filter($data);
	}
     $result = array();
     foreach ($data as $key => $val) {
         $result[$key] = is_array($val) ? array_map_recursive($filter, $val) : call_user_func($filter, $val);
     }
     return $result;
}

/**
 * 获取对应的语言
 * @param  [type] $field [description]
 * @return [type]        [description]
 */
function lang($field=""){
	$lang = require ERROR_LANG;
	if($field==""){
		return $lang;
	}else{
		return isset($lang[$field]) ? $lang[$field] : '';
	}
}

/**
 * 数据的var_dump打印
 * @param  [type] $data 数据
 * @return [type]
 */
function dd($data){
	echo "<pre>";
	var_dump($data);
}
/**
 * 数据的var_dump打印并终止脚本
 * @param  [type] $data 数据
 * @return [type]
 */
function dt($data){
	echo "<pre>";
	var_dump($data);
	exit();
}


