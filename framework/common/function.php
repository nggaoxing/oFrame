<?php
//系统函数库

//递归过量函数
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