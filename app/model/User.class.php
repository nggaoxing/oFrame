<?php
namespace app\model;
use core\Model;

class User extends Model{
	protected $tableName = 'user';
	protected $tablePrefix = 'sx_';
	protected $pk = 'id';

	protected $map = [
		'statussssss'=> 'status',
		'name'=> 'username',
		'pass'=> 'password',

	];

	public function getUserList(){
		
		// $where['id'] = array('>',1);
		// $where2['id'] = array('<',1000);

		// $res = $this->field('id,username')->where($where)->where($where2)->group('status')->order('id desc')->limit(0,4)->select();

		$data = [
			'username'=>'测试',
			'password'=>'asasadsdsafsasas',
			'email'=>'99999@qq.com',
			'uid'=>'U9999',
			'create_time'=>time(),
			'statussssss'=>1,
			'id'=>'lllllll'
		];
		//$this->setPk('ids');
		//$res = $db->insert($data);	
		//$res = $db->where(['id'=>['>',1]])->update($data);	
		$res = $this->update($data);	
		//$res = $this->update($data,['idsss'=>['>',1]]);	

		var_dump($res);

		var_dump($this->getLastInsID());
		var_dump($this->getLastSql());


	}

	
}


