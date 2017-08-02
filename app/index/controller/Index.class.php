<?php
namespace app\index\controller;
use core\Controller;
use app\model\User as UserModel;
use core\Model;

class Index extends Controller{

	public function index(){
		$this->assign('welcome',"欢迎使用");
		$this->display('index.html');
	}

	public function user(){
		$user = new UserModel();
		$user->getUserList();
	}

	public function db(){



		$where['id'] = array('>',1);
		$where2['id'] = array('<',1000);

		$db = new Model('user');
		//$db->username="撒飒飒";
		$res = $db->field('id,username')->where($where)->where($where2)->group('status')->order('id desc')->limit(0,4)->select();

// var_dump($res);
// // var_dump($db->username);
// var_dump($db->getLastSql());
// var_dump($db->getDbFields());
// var_dump($db->fields);
		$data = [

			'username'=>'测试',
			'password'=>'asasadsdsafsasas',
			'email'=>'99999@qq.com',
			'uid'=>'U9999',
			'create_time'=>time(),
			'status'=>1,
			// 'haha'=>'lllllll'

		];

		//$res = $db->insert($data);	
		//$res = $db->where(['id'=>['>',1]])->update($data);	

		var_dump($res);

		var_dump($db->getLastInsID());
	}

}