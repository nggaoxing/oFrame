<?php
namespace app\index\controller;
use core\Controller;
use app\model\User as UserModel;
use core\Model;
use core\Request;
use core\Config;
use app\validate\user as UserValidate;
use core\Validate;
use core\Session;
use core\Cookie;

class Index extends Controller{

	public function index(){
		$this->assign('welcome',"欢迎使用of框架");
		$this->display('index.html');
	}

	public function user(){
		$user = new UserModel();
//		$a = ['b'=>'sss'];
//echo ();
		$user->getUserssList();
	}

	public function db(){
		new Model();
require('s');
		$where['id'] = array('>',1);
		$where2['id'] = array('<',1000);

		//$db = new Model('user');
		$db = new UserModel();

		//$db->username="撒飒飒";
		$res = $db->field('id,usesrname')->where($where)->where($where2)->group('status')->order('ids desc')->limit(0,4)->select();
//$db->getField('sss');
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
			'isssd'=>'lllllll',
			'hgf'=>'eqwe',
			'eeee'=>'大发',
			'aaa'=>'阿萨',
		];
		// $db->setPk('ids');
		// //$res = $db->insert($data);	
		// //$res = $db->where(['id'=>['>',1]])->update($data);	
		//$res = $db->update($data);	
		$res = $db->update();	

		var_dump($res);

		var_dump($db->getLastInsID());
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

	public function validate(){
		$data = [
			'username'=>'sssssssssssssssss',
			'pass'=>'12345678',
			'email'=>'ssssssssssss',
			'uid'=>'U9999',
			'create_time'=>time(),
			'statussssss'=>1,
			'id'=>'283',
			'hgf'=>'eqwe',
			'eeee'=>'大发',
			'aaa'=>'阿萨',
			'url'=>'ssssssssssss',
		];
		
		$db = new UserModel();
		$data = $db->create($data);
		
		$UserValidate = new UserValidate();
		$result = $UserValidate->checkAll(true)->scene('add')->check($data);
// 		$rules = [
//     	'username'  =>  'require|max:40|min:3',
//     	'phone'  =>  'number|length:11',
//     	'email'  =>  'require|email',
//     	'address'  =>  'require|max:200',
//     	'password'=>'require|length:6,14',
//     	'id'=>'require|notIn:28,29',
//     	'url' => 'require|url'
// 		];
// 		$validate = new Validate($rules);
// 		$result = $validate->checkAll(true)->check($data);
dd($result);
dd($UserValidate->error);
		if(!$result){
			$msg = $UserValidate->getError();
			dd($msg);
		}
		

		
	}

	public function s(){
		Session::set('name','gaoxing');
		dd(Session::get('name'));	//获取name
		Session::set('name','');
		dd(Session::get('name'));	//获取name
		//Session::delete('name');	//删除name
		//Session::clear('gx_');	//删除name
		
		dd(Session::has('name'));

		dd($_SESSION);				//打印全部
	}

	public function c(){

		(new UserModel())->aaa();

		Cookie::init();
		$data = [
			'username'=>'sssssssssssssssss',
			'pass'=>'12345678',
			'email'=>'ssssssssssss',
			'uid'=>'U9999',
			'create_time'=>time(),
			'statussssss'=>1,
			'id'=>'283',
			'hgf'=>'eqwe',
			'eeee'=>'大发',
			'aaa'=>'阿萨',
			'url'=>'ssssssssssss',
		];
		Cookie::set('name',$data,['prefix'=>'of1_']);
		Cookie::set('id','11111',['prefix'=>'of2_']);
		Cookie::set('email','ssssssssssss',['prefix'=>'of3_']);
		$value = Cookie::get('name','of1_');
		$value2 = Cookie::get('email');
		dd($value);
		dd($value2);

		// Cookie::delete('id');
		// Cookie::delete('email');
		 Cookie::delete('name');

		 Cookie::clear('of1_');

		 dd(Cookie::has('email','of3_'));

		dd($_COOKIE);
	}
	

}