<?php
namespace core;
use core\Config;
use core\Db;

//基础模型类
class Model{
	//当前数据库操作对象
    protected $db               =   null;
    //数据库对象池
	private   $_db				=	array();
    // 主键名称
    protected $pk               =   'id';
    // 数据表前缀
    protected $tablePrefix      =   '';
    //数据库配置
 	protected $connection       =   [];
    // 数据表名（不包含表前缀）
    protected $tableName        =   '';
    // 实际数据表名（包含表前缀）
    protected $trueTableName    =   '';
    // 最近错误信息
    protected $error            =   '';
    //最后插入id
    protected $lastId			=	'';
    // 字段信息
    protected $fields           =   array();
    // 数据信息(用于增加和修改)
    protected $data             =   array();
    // 查询表达式参数
    protected $options          =   array();
    //最后执行的sql
    protected $sql 				= 	'';
    //链式操作方法
  	protected $methods          =   array('field','where','group','having','order','limit');


    // 回调方法 初始化模型
    protected function _initialize() {}

    /**
     * model类构造函数
     * @param string $tablename   表名
     * @param string $tablePrefix 表前缀
     * @param string $connection  连接信息
     */
    public function __construct($tablename='',$tablePrefix='',$connection=''){
    	// 模型初始化
        $this->_initialize();

        //获取表名
        if(!empty($tablename)){
        	$this->tableName = $tablename;
        }
        //表前缀
        if(!empty($tablePrefix)){
        	$this->tablePrefix = $tablePrefix;
        }elseif(empty($this->tablePrefix)){
        	$this->tablePrefix = Config::db()['db_prefix'];
        }
      	//数据库连接条件
      	if(!empty($connection)){
        	$this->connection = $connection;
        }else{
        	$this->connection = Config::db();
        }  

        //建立数据库连接
        return $this->conn_db();
    }

    /**
     * 是否强制重新连接
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    private function conn_db($force=false){
    	//先看连接池有没有此对象的存在
    	if(isset($this->_db[$this->connection['db_name']]) && !empty($this->_db[$this->connection['db_name']])){
    		return $db;
    	}
    	//不存在创建
    	if(!isset($this->_db[$this->connection['db_name']]) || $force ) {
    		$this->_db[$this->connection['db_name']] = Db::getInstance($this->connection);
    	}
    	$this->db = $this->_db[$this->connection['db_name']];
    	return $this->db;
    }

    /**
     * SQL查询
     * @param string $sql  SQL指令
     * @return mixed
     */
    public function query($sql) {
        return $this->db->db_query($sql);
    }

    /**
     * 执行SQL语句
     * @param string $sql  SQL指令
     * @return false | integer
     */
    public function execute($sql) {
        return $this->db->db_exec($sql);
    }


    /*****************链式查询****************/

    /**
     * 字段查询选择
     * @param  [type] $field 字段字符串
     * @return [type]    
     */
    public function field($field=false){
    	if(empty($field)){
    		throw new \Exception("字段信息不能为空", 1);
    	}
		$fields = $this->getDbFields();
    	if($field=="*"){	//全部
    		$files = implode(',',$fields);
    	}else{
    		//表示存在字段(验证)
    		$f = explode(",",$field);
    		foreach ($f as $key => $val) {
    			if(!in_array($val,$fields)){
    				echo '错误内容：' . $val."字段不存在";
    				throw new \Exception($val."字段不存在", 1);
    			}
    		}
    	}
    	//字段条件
    	$this->options['field']   =   $field;
    	return $this;
    }

    /**
     * 条件设置
     * @param  [type] $where 条件
     * @return [type]        [description]
     */
    public function where($where=false){
    	if(empty($where)){
    		throw new \Exception("查询条件不能为空", 1);
    	}
    	//判断是不是字符串
    	if(is_string($where) && '' != $where){
    		$this->options['where']  .="and ". $this->db->escapeString($where)." ";
    	}
    	//是数组
    	if(is_array($where)){
    		foreach ($where as $k => $v) {
    			@$this->options['where'] .= "and ". $k . $v[0] .$this->db->escapeString($v[1])." ";
    		}
    	}
    	return $this;
    }

    /**
     * 指定分组条件
     * @param  [type] $group 分组条件
     * @return [type]        [description]
     */
    public function group($group=false){
    	if(empty($group)){
    		throw new \Exception("分组条件不能为空", 1);
    	}
    	//判断是不是字符串
    	if(is_string($group) && '' != $group){
    		//判断字段是否存在
    		$fields = $this->getDbFields();
			if(!in_array($group,$fields)){
				echo '错误内容：分组' . $group."字段不存在";
				throw new \Exception("分组".$group."字段不存在", 1);
			}
    		$this->options['group'] = $group;
    	}
    	return $this;
    }

    /**
     * 数据排序条件
     * @param  [type] $order 排序条件
     * @return [type]        [description]
     */
    public function order($order=false){
    	if(empty($order)){
    		throw new \Exception("排序条件不能为空", 1);
    	}
    	//判断是不是字符串
    	if(is_string($order) && '' != $order){
    		//判断字段是否存在
    		$fields = $this->getDbFields();
    		$o = explode(" ",$order)[0];
			if(!in_array($o,$fields)){
				echo '错误内容：排序' . $o."字段不存在";
				throw new \Exception("排序".$o."字段不存在", 1);
			}
    		$this->options['order'] = $order;
    	}
    	return $this;
    }

    /**
     * 指定查询数量
     * @access public
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return Model
     */
    public function limit($offset,$length=null){
        if(is_null($length) && strpos($offset,',')){
            list($offset,$length)   =   explode(',',$offset);
        }
        $this->options['limit']     =   intval($offset).( $length? ','.intval($length) : '' );
        return $this;
    }

    /**
     * 查询单条信息
     * @param  array  $options 查询条件
     * @return [type]   
     */
    public function find($options=array()){
    	//暂不处理带条件的
    	
    	//总是查找一条记录
        $options['limit']   =   1;
        //分析表达式
        $options = $this->_parseOptions($options);
    	//组装表达式
    	$sql = $this->createSql($options,'find');

    var_dump($sql);
    	//执行语句
    	$this->data = $this->db->fetchRow($sql);
    	return $this->data;
    }

    /**
     * 查询多条信息信息
     * @param  array  $options 查询条件
     * @return [type]   
     */
    public function select($options=array()){
    	//暂不处理带条件的
    	
        //分析表达式
        $options = $this->_parseOptions($options);
    	//组装表达式
    	$sql = $this->createSql($options,'select');

    var_dump($sql);
    	//执行语句
    	$this->data = $this->db->fetchAll($sql);
    	return $this->data;
    }

    /**
     * 查询sql表达式的组装
     * @param  [type] $options 条件
     * @param  [type] $type    查询类型
     * @return [type]  
     */
    public function createSql($options,$type){
    	$sql = '';
    	//判断类型
    	if($type == 'find' || $type == 'select'){
    		$sql .= "SELECT ".$options['field']." FROM ".$options['tableName'];
    	}
    	//条件where
    	if(isset($options['where'])){
    		$sql .= " WHERE ".ltrim($options['where'],'and ');
    	}
  		//group
  		if(isset($options['group'])){
    		$sql .= " GROUP BY ".$options['group'];
    	}
    	//order
  		if(isset($options['order'])){
    		$sql .= " ORDER BY ".$options['order'];
    	}
		//limit
    	if(isset($options['limit'])){
    		if(strpos($options['limit'],",")){
    			list($offset,$length)   =   explode(',',$options['limit']);
    			$sql .= " LIMIT ".$offset.",".$length;
    		}else{
    			$sql .= " LIMIT 0,".$options['limit'];
    		}
    	}
  		//返回结果
  		$this->sql = $sql;
  		return $sql;
    }

    /**
     * 更新sql表达式的组装
     * @param  [type] $options 条件
     * @param  [type] $type    查询类型
     * @param  [type] $data    数据
     * @return [type]  
     */
    public function createSaveSql($type,$data,$options){
    	$sql = '';
    	$key="";
    	$value="";
    	//判断类型
    	switch ($type) {
    		case 'insert':
    			$sql .= "INSERT INTO ".$options['tableName']." (";
    			foreach ($data as $k => $v) {
    				$key .= $k.',';
    				$value .= "'{$v}',";
    			}
    			$sql .= trim($key,",").") ";
    			$sql .= "VALUES (".trim($value,",").")";
    			break;
    		case 'update':
    			$sql .= "UPDATE ".$options['tableName']." SET ";
    			foreach ($data as $k => $v) {
    				$value .= "{$k} = '{$v}', ";
    			}
    			$sql .= trim($value,", ");
    			//条件where
		    	if(isset($options['where'])){
		    		$sql .= " WHERE ".ltrim($options['where'],'and ');
		    	}
    			break;
    		default:
    			# code...
    			break;
    	}
    	
  		//返回结果
  		$this->sql = $sql;
  		return $sql;
    }

     /**
     * 分析表达式
     * @access protected
     * @param array $options 表达式参数
     * @return array
     */
    protected function _parseOptions($options=array()) {
    	//判断是否传递条件
    	if(is_array($options)){
    		$options = array_merge($this->options,$options);
    	}
    	//判断是否存在表名
    	if(!isset($options['tableName'])){
    		$options['tableName'] = $this->getTableName();
    	}
    	//判断是否选在字段
    	if(!isset($options['field'])){
    		$options['field'] = implode(",",$this->getDbFields());
    	}
    	//剩下的条件等扩展
    	
    	//清空条件属性返回当前条件
    	$this->options = [];
    	return $options;
    }

    /**
     * 获取数据表字段信息
     * @return [type] [description]
     */
    public function getDbFields(){
    	//获取完整表名
    	$tableName = $this->getTableName();
    	//获取字段
    	$fields = $this->db->getfields($tableName);
    	$this->fields = $fields;
    	//返回信息
    	return  $fields ? array_keys($fields) : false;
    }

    /**
     * 获取完整的表名
     * @return [type] [description]
     */
    public function getTableName(){
    	//不存在
    	if(empty($this->trueTableName)){
    		$this->trueTableName = $this->tablePrefix . $this->tableName;
    	}
    	return $this->trueTableName;
    }

    /**
     * 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 设置数据对象的值
     * @access public
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value) {
        // 设置数据对象属性
        $this->data[$name]  =   $value;
    }

    /**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     * 返回最后执行的sql语句
     * @access public
     * @return string
     */
    public function getLastSql() {
        return $this->sql;
    }

    /**
     * 获取所有数据的某个字段
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function getField($field=false,$char=null){
    	if(empty($field)){
    		throw new \Exception("字段不能为空", 1);
    	}
    	//判断存在不存在
    	$field  =  trim($field);
    	$fields = $this->getDbFields();
    	$f = explode(",",$field);
		foreach ($f as $key => $val) {
			if(!in_array($val,$fields)){
				echo '错误内容：' . $val."字段不存在";
				throw new \Exception($val."字段不存在", 1);
			}
		}
		$this->options['field']   =   $field;
		//判断多字段
    	if(strpos($field,',') && false !== $char){
    		//多字段,表示存在字段(验证)
    		if(is_numeric($char)){
    			$this->options['limit'] = is_numeric($char) ? $char : 1;
    		}
    		$this->data = $this->select();
    	}else{	
    		//这是单字段
    		if($char == null){
    			//返回数组
    			$this->data = $this->select();
    		}elseif(is_string($char)){
    			//逗号
    			$this->options['field']   =  "GROUP_CONCAT({$field}) as {$field}";
    			$this->data = $this->find();
    		}elseif(is_numeric($char)){
    			//限定条数
    			$this->options['limit'] = $char ? $char : 1;
    			$this->data = $this->select();
    		}
    	}
    	return $this->data;
    }

   	/**
   	 * 插入数据
   	 * @param  array  $data    数据
   	 * @param  array  $options 条件
   	 * @return [type]          
   	 */
    public function insert($data=array(),$options=array()){
    	//空数组
    	if(empty($data)){
    		//没有传递数据，获取当前数据对象的值
    		if(!empty($this->data)){
    			$data = $this->data;
    			$this->data = []; 
    		}else{
    			return false;
    		}
    	}
    	//分析表达式
        $options = $this->_parseOptions($options);
    	//数据的过滤(字段筛选和字符串过滤)
        
    	//组装表达式
    	$sql = $this->createSaveSql('insert',$data,$options);

    var_dump($sql);
    	//执行语句
    	$this->lastId = $this->db->affectRow($sql);
    	return $this->lastId;
    }

    /**
     * 更新数据
     * @param  array  $data    数据
     * @param  array  $options 主键值
     * @return [type]          
     */
    public function update($data=array(),$options=array()){
    	//空数组
    	if(empty($data)){
    		//没有传递数据，获取当前数据对象的值
    		if(!empty($this->data)){
    			$data = $this->data;
    			$this->data = []; 
    		}else{
    			return false;
    		}
    	}
    	//分析表达式
        $op = $this->_parseOptions($options);
        //判断主键值或者where条件是否存在
        // if(){

        // }
    	//数据的过滤(字段筛选和字符串过滤)
        
    	//组装表达式
    	$sql = $this->createSaveSql('update',$data,$op);

    var_dump($sql);
    	//执行语句
    	return $this->db->affectRow($sql);
    }

    /**
     * 获取最后插入id
     * @return [type] 
     */
    public function getLastInsID(){
    	 return isset($this->lastId) ? $this->lastId : null;
    }


}