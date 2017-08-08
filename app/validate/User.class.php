<?php
//user验证类
namespace app\validate;
use core\Validate;

class User extends Validate{

	//创建验证规则
	protected $rules = [
    	'username'  =>  'min:',
    	'phone'  =>  'require|number|length:11',
    	'email'  =>  'require|email',
    	'address'  =>  'require|max:200',
    	'password'=>'require|length:6,14',
    	'id'=>'require|notIn:',
    	'url' => 'require|url'
		];

	//输出错误信息
	protected $message = [
    	// 'username.require'  =>  '用户名未填写！！',
    	// 'username.unique'  =>  '用户名重复！！',
    	// 'username.max'  =>  '用户名不能超过4个字符',
    	// // 'phone.number'  =>  '电话格式不正确！！',
    	// 'phone.length'  =>  '电话长度式不正确！！',
    	// 'email.email'  =>  '邮箱格式不正确！！',
    	// 'address.max'  =>  '地址长度超过范围！！',
    	// 'password.confirm'  =>  '两次密码输入不一致！！',
    	// 'password.length'  =>  '密码长度6-14位！！',
	];

	//验证场景的设置
	protected $scene = [
		'add' => 'username,phone,email',
	];



}










