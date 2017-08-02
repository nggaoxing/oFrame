<?php
namespace app\index\controller;
use core\Controller;

class Login extends Controller{

	public function index(){
		$this->assign('welcome',"登录");
		$this->display('index.html');
	}

}