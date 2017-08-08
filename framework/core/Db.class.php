<?php

namespace core;
use core\Log;

//pdo方式
class Db{
	//参数属性
	private $host;		//主机
	private $port;		//端口号
	private $user;		//用户名
	private $password;	//密码
	private $dbname;	//数据库名
	private $charset;	//字符集
	//private $prefix;	//表前缀
	
	//pdo的参数
	private $dsn;		//pdo连接参数
	private $options;	//pdo附加条件
	
	//使用参数
	private $pdo;		//连接
	
	//一私
	private function __construct($config=array()){
		//初始化信息
		$this->initServer($config);
		//连接数据库
		$this->newPDO();
	}
	//二私
	private function __clone(){
		//无方法体，意为禁止克隆
	}
	//三私
	private static $instance;
	//一公 ，获取单例对象的方法
	public static function getInstance($config=array()){
		if(!(static::$instance instanceof static)){
			//创建对象
			static::$instance=new static($config);
		}
		//返回对象
		return static::$instance;
	}
	
	//初始化信息的方法
	private function initServer($config){
		//设置参数属性
		$this->host=isset($config['db_host'])?$config['db_host']:'localhost';
		$this->port=isset($config['db_port'])?$config['db_port']:'3306';
		$this->user=isset($config['db_username'])?$config['db_username']:'root';
		$this->password=isset($config['db_password'])?$config['db_password']:'root';
		$this->dbname=isset($config['db_name'])?$config['db_name']:'mysql';
		$this->charset=isset($config['db_charset'])?$config['db_charset']:'utf8';
		$this->prefix=isset($config['db_prefix'])?$config['db_prefix']:'';
	}
	
	//连接数据库
	private function newPDO(){
		//1.设置参数，dsn，option，
		$this->getDSN();
		$this->getOptions();
		$this->getPDO();
	}
	
	//获取dsn
	private function getDSN(){
		$this->dsn="mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";	
	}
	
	//获取options
	private function getOptions(){
		$this->options=array(
			\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
		);
	}
	
	//获取pdo连接
	private function getPDO(){
		$this->pdo=new \PDO($this->dsn,$this->user,$this->password,$this->options);
	}
	

	//执行sql语句的方法(query)
	public function db_query($sql=''){
		Log::addLog($sql,'SQL');
		//执行sql语句
		$res=$this->pdo->query($sql);
		//判断
		if(!$res){
            throw new \Exception($this->pdo->errorInfo()[2], 1);
            exit;
		}
		//返回结果集
		return $res;
		
	}
	
	//执行sql语句的方法(exec)
	public function db_exec($sql=''){
		Log::addLog($sql,'SQL');
		//执行sql语句
		$res=$this->pdo->exec($sql);
		//判断(表示完全错误的情况下才会返回信息。0也可以表示操作成功，但没有行数影响)
		if ($res === false) {
			$err = $this->pdo->errorInfo();
			if ($err[0] === '00000' || $err[0] === '01000') {
				return true;
			}
		}
		//返回结果集
		return $res;
		
	}
	
	//查询获取一条数据的方法
	public function fetchRow($sql=''){
		//执行sql语句的方法
		$result=$this->db_query($sql);
		//获取结果，释放结果集
		$row=$result->fetch(\PDO::FETCH_ASSOC);
		$result->closeCursor();
		//返回结果集
		return $row;
		
	}
	
	
	//查询获取一个数值的方法
	public function fetchOne($sql=''){
		//执行sql语句的方法
		$result=$this->db_query($sql);
		//获取结果，释放结果集
		$num=$result->fetchColumn();
		$result->closeCursor();
		//返回结果集
		return $num;
	}
	
	
	//查询获取多条数据的方法
	public function fetchAll($sql=''){
		//执行sql语句的方法
		$result=$this->db_query($sql);
		//获取结果，释放结果集
		$rows=$result->fetchAll(\PDO::FETCH_ASSOC);
		$result->closeCursor();
		//返回结果集
		return $rows;
	}
	
	//获取增，删，改影响行数 的方法
	public function affectRow($sql=''){
		//执行sql语句的方法
		$result=$this->db_exec($sql);
		if($lastId = $this->pdo->lastInsertId()){
			return $lastId;
		}
		//返回结果集
		return $result;
	}
	
	
	//转义字符串，防止sql注入的方法
	public function escapeString($str=''){
		return $this->pdo->quote($str);
	}

	/**
	 * 获取数据表字段信息
	 * @return [type] [description]
	 */
	public function getfields($tableName){
		//创建sql查询
		$sql = 'SHOW COLUMNS FROM `'.$tableName.'`';
		$result = $this->fetchAll($sql);
		//组装数据
		$info = array();
		if($result){
			foreach ($result as $key => $val) {
				//把key转为小写
				$val = array_change_key_case($val,CASE_LOWER);
				//组装
				$info[$val['field']] = array(
                    'name'    => $val['field'],
                    'type'    => $val['type'],
                    'notnull' => (bool) ($val['null'] === ''), // not null is empty, null is yes
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
                );
			}
			return $info;
		}
	}
	
	/**
	 * 开启事物(暂不完善，不使用)
	 * @return [type] [description]
	 */
	public function startTrans(){
		$this->pdo->beginTransaction();
	}

	/**
	 * 事物提交(暂不完善，不使用)
	 * @return [type] [description]
	 */
	public function commit(){
		$this->pdo->commit();
	}

	/**
	 * 事物回滚(暂不完善，不使用)
	 * @return [type] [description]
	 */
	public function rollback(){
		$this->pdo->rollBack();
	}
	
} 


	