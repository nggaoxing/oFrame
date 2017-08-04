<?php
namespace core;
use core\Config;
use core\Db;
use core\Request;

//基础模型类
class Model{
	//当前数据库操作对象
    protected $db               =   null;
    //数据库对象池
	private   $_db				=	array();
    // 主键名称
    protected $pk               =   '';
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
    protected $tableFields           =   array();
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
        if(empty($tablename) && empty($this->tableName)){
        	throw new \Exception("未传递表名称", 1);   
        }elseif(!empty($tablename)){
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
        $this->conn_db();
        //获取主键
        $this->getDbFields();
        $this->pk = empty($this->pk) ? $this-> getTablePk($this->fields): $this->pk;

        //返回连接
        return  $this->db;
    }

    /**
     * 获取主键
     * @param  [type] $f 字段信息
     * @return [type]
     */
    private function getTablePk($f){
        foreach ($f as $key => $val) {   
            if($val['primary'] == true){
                return $key;
            }
        }
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
    	if($field=="*"){	//全部
    		$files = implode(',',$this->tableFields);
    	}else{
    		//表示存在字段(验证)
    		$f = explode(",",$field);
    		foreach ($f as $key => $val) {
    			if(!in_array($val,$this->tableFields)){
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
            throw new \Exception("请使用数组传递条件", 1);
    		//$this->options['where']  .="and ". $this->db->escapeString($where)." ";
    	}
    	//是数组
    	if(is_array($where)){
    		foreach ($where as $k => $v) {
                if(!in_array($k,$this->tableFields)){
                    throw new \Exception("条件".$k."字段不存在", 1);
                }
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
			if(!in_array($group,$this->tableFields)){
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
    		$o = explode(" ",$order)[0];
			if(!in_array($o,$this->tableFields)){
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
                //先删除主键条件
                if(isset($data[$this->pk])){
                    unset($data[$this->pk]);
                }
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
    		$options['field'] = implode(",",$this->tableFields);
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
    	$this->tableFields = array_keys($fields);
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
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 设置数据对象的值
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
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     * @param string $name 名称
     * @return void
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     * 设置主键
     * @param string $name 名称
     * @return void
     */
    public function setPk($val) {
        $this->pk = $val;
    }
    public function getPk() {
        return $this->pk;
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
    	$f = explode(",",$field);
		foreach ($f as $key => $val) {
			if(!in_array($val,$this->tableFields)){
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

        //判断主键值或者where条件是否存在

        if(!isset($op['where']) && !array_key_exists($this->pk,$data) && empty($options)){
        	throw new \Exception("缺少必要条件！！", 1);
        }
    	//数据的过滤(字段筛选和字符串过滤)
        $data = $this->create($data);
        //当没有传递条件是判断是否存在主键条件
        if(array_key_exists($this->pk,$data) && empty($options) && !isset($op['where'])){
            $this->where([$this->pk=>['=',$data[$this->pk]]]);
        }elseif(!array_key_exists($this->pk,$data) && !empty($options) && !isset($op['where'])){
            if(array_key_exists($this->pk,$options)){
                $this->where($options);
            }else{
                throw new \Exception("缺少必要条件！！", 1);
            }
        }  
         //分析表达式
        $op = $this->_parseOptions($options);
    	//组装表达式
    	$sql = $this->createSaveSql('update',$data,$op);
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

    /**
     * 数据的过滤
     * @return [type] 
     */
    public function create($data=""){
        //没有值传递默认post
        if(empty($data)){
            $data = Request::instance()->input('post.'); 
        }elseif(is_object($data)){
            //传入的是对象,获取属性
            $data   =   get_object_vars($data);
        }
        // 如果现在还没数据或者数据不是数组,直接返回错误
        if(empty($data) || !is_array($data)) {
            throw new \Exception("数据有误，请确认！", 1);
            return false;
        }
        //检查字段映射，防止过滤了应该存在的数据
        $data = $this->setFieldMap($data);

        //检测字段是否存在，不存在的去掉
        //1.用户自己传递了field
        if(isset($this->options['field'])){
            $fields = explode(",",$this->options['field']);
            unset($this->options['field']);//删除，不能影响别的查询
        }else{  //还有插入是字段，查询是字段的设置，暂时没考虑
            $fields = $this->tableFields;
        }
        //这里还有一个表单令牌验证
        // do.....
        //存在字段就开始过滤
        if(isset($fields)) {
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }
            }
        }
        //这里存在一个数据的自动验证，准备写一个数据验证类
        // do.....
        //这里还有一个自动填充
        // do.....
        // 赋值当前数据对象
        $this->data = $data;
        // 返回创建的数据以供其他调用
        return $data;
    }

    /**
     * 处理字段映射
     * @access public
     * @param array $data 当前数据
     * @param integer $type 类型 0 写入 1 读取
     * @return array
     */
    private function setFieldMap($data,$type=0) {
        //字段映射的处理（传递的数据字段 ==>  对应的表的字段）
        if(!empty($this->map)) {
            foreach ($this->map as $key=>$val){
                //1读取，要把表的字段转为设定的字段记录值
                if($type==1) { 
                    if(isset($data[$val])) {
                        $data[$key] =   $data[$val];
                        unset($data[$val]);
                    }
                }else{
                    //0写入，要把数据的字段转为表的字段记录值
                    if(isset($data[$key])) {
                        $data[$val] =   $data[$key];
                        unset($data[$key]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans() {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     * 提交事务
     * @access public
     * @return boolean
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback() {
        return $this->db->rollback();
    }

    
}