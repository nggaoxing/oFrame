<?php
namespace app\index\controller;
use core\Controller;
use app\model\User as UserModel;
use core\Model;
use core\Request;

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

		//$db = new Model('user');
		$db = new UserModel();

		//$db->username="撒飒飒";
		//$res = $db->field('id,username')->where($where)->where($where2)->group('status')->order('id desc')->limit(0,4)->select();

		// var_dump($res);
		// // var_dump($db->username);
		// var_dump($db->getLastSql());
		// var_dump($db->getDbFields());
		// var_dump($db->fields);
		$data = [
			'name'=>'测试',
			'pass'=>'asasadsdsafsasas',
			'email'=>'99999@qq.com',
			'uid'=>'U9999',
			'create_time'=>time(),
			'statussssss'=>1,
			'id'=>'lllllll',
			'hgf'=>'eqwe',
			'eeee'=>'大发',
			'aaa'=>'阿萨',
		];
		// $db->setPk('ids');
		// //$res = $db->insert($data);	
		// //$res = $db->where(['id'=>['>',1]])->update($data);	
		$res = $db->update($data);	
		// $res = $db->update($data,['ids'=>['>',1]]);	

		// var_dump($res);

		// var_dump($db->getLastInsID());
		 var_dump($db->getLastSql());
	}


	public function data(){

		// var_dump($_POST);
		$data = [
			'name'=>'测试',
			'pass'=>'asasadsdsafsasas',
			'email'=>'99999@qq.com',
			'uid'=>'U9999',
			'create_time'=>time(),
			'statussssss'=>1,
			'id'=>'lllllll',
			'hgf'=>'eqwe',
			'eeee'=>'大发',
			'aaa'=>'阿萨',
		];
		var_dump($data);
		// $data = Request::instance()->param();
		$db = new UserModel();
		//$db->field('id,username')->create($data);
		$data = $db->create($data);

		// $data = Request::instance()->input();
		// $data = Request::instance()->input('post.','沙和尚');
		// $data = Request::instance()->input('post.aaa','第三方的');
		// $data = Request::instance()->input('post.bbb');
		// $data = Request::instance()->input('get.');
		// $data = Request::instance()->input('get.a');
		// $data = Request::instance()->input('get.b');
		// $data = Request::instance()->input('get.bsasa');
		// $data = Request::instance()->input('aaa');
		// $data = Request::instance()->input('bbb');
		// $data = Request::instance()->input('a');
		// $data = Request::instance()->input('b');
		// $data = Request::instance()->input('asas');
		// $data = Request::instance()->input('user/a');

 var_dump($data );
// //echo $data['user']['a'];

		$this->display('data.html');
	}

	

}