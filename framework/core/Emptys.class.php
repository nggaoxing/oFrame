<?php
namespace core;

//空控制类
class Emptys{

	/**
	 * 错误类或者方法提示信息
	 * @param  [type] $name  名称
	 * @param  [type] $field 提示字段
	 * @return 
	 */
	public function _empty($name="",$field=""){
		throw new \Exception($field.":".$name, 1);      
	}








}

















