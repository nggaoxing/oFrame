<?php
namespace app\model;
use core\Model;

class User extends Model{
	protected $tableName = 'user';
	protected $tablePrefix = 'sx_';
	protected $pk = 'id';

	public function getUserList(){

		$where['id'] = array('>',1);
		$where2['id'] = array('<',1000);

		$res = $this->field('id,username')->where($where)->where($where2)->group('status')->order('id desc')->limit(0,4)->select();


	}

	
}


