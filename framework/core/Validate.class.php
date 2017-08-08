<?php
namespace core;
use core\Config;

//数据验证类
class Validate{

	//实例
    protected static $validate;
    //验证规则
    protected $rules=[];
    //提示信息
    protected $message=[];
    //是否全部验证
    protected $checkAll = false;
    //默认提示信息
    protected $defaultMsg =[
    	'require'     => ':attribute不能为空',
        'number'      => ':attribute必须是数字',
        'email'       => ':attribute格式不符',
        'date'        => ':attribute时间格式不符合',
        'url'         => ':attribute不是有效的URL地址',
        'in'          => ':attribute必须是 :rule 其中之一',
        'notIn'       => ':attribute不在 :rule 其中之一',
        'between'     => ':attribute只能在 :1 - :2 之间',
        'notBetween'  => ':attribute不能在 :1 - :2 之间',
        'length'      => ':attribute长度不符合要求 :rule',
        'max'         => ':attribute长度不能超过 :rule',
        'min'         => ':attribute长度不能小于 :rule',
        'egt'         => ':attribute必须大于等于 :rule',
        'gt'          => ':attribute必须大于 :rule',
        'elt'         => ':attribute必须小于等于 :rule',
        'lt'          => ':attribute必须小于 :rule',
        'eq'          => ':attribute必须等于 :rule',
    ];
    //有规则附加值的规则
    protected $rule_add = ['between','notBetween'];

    //错误信息
    public $error;

    /**
     * 构造函数
     * @param array $rules 验证规则
     * @param array $message 验证提示信息
     */
    public function __construct($rules = [], $message = []){
    	//设定验证规则和提示信息
        $this->rules  = array_merge($this->rules, $rules);
        $this->message = array_merge($this->message, $message);
    }

    /**
     * 类的实例化
     * @param  [type] $rules   验证规则
     * @param  [type] $message 验证提示信息
     * @return [type]          [description]
     */
    public static function instance($rules = [], $message = []){
    	if(is_null(self::$validate)){
    		self::$validate = new self($rules, $message);	
    	}
    	return self::$validate;
    }

    /**
     * 获取错误信息
     * @return [type] [description]
     */
    public function getError(){
    	//这里就要判断是全部验证，还是单个验证了
		if($this->checkAll){
			//全部
			return $this->error;
		}else{
			//单个
			return $this->error[0];
		}
    }

    /**
     * 是否全部验证还是单个验证
     * @param  boolean $type 验证类型
     * @return 
     */
    public function checkAll($type=true){
    	$this->checkAll = $type;
    	return $this;
    }

    /**
     * 场景验证设置
     * @param  [type] $scene [description]
     * @return [type]        [description]
     */
    public function scene($scene=false){
    	//必须传值
    	if(!$scene){
    		throw new \Exception("argumet not exists:scene", 1);
    	}
    	//判断传递的值
    	if(is_array($scene)){//数组
    		//['url' => 'require|url','id'=>'require|notIn:28,29']，传递的字段数组
    		$this->rules = $scene;
    	}elseif(is_string($scene)){//字符串
    		//'add' => 'username,phone,email',这种形式,传入的是add
    		if(!isset($this->scene[$scene])){
    			throw new \Exception("scene not exists", 1);
    		}
			$fields = explode(',',$this->scene[$scene]);
    		foreach ($fields as $k => $field) {
    			$rules[$field] = $this->rules[$field]; 
    		}
    		$this->rules = $rules;
    	}
    	//返回对象
    	return $this;
    }

   	/**
   	 * 进行字段的验证
   	 * @param  [type] $data    数据数组
   	 * @param  [type] $rules   验证规则
   	 * @param  string $message 验证提示信息
   	 * @return [type]          [description]
   	 */
    public function check($data, $rules = [], $message = []){
    	//设定验证规则和提示信息
    	$rules    = array_merge($this->rules, $rules);
    	if(empty($rules)){
    		return true; //没有验证规则,为真
    	}
        $message = array_merge($this->message, $message);
        //错误信息初始化
        $this->error = [];
        $flag = true;

        //遍历验证规则
        foreach ($rules as $field => $rule) {
        	//'username' => 'unique:admin|require|max:10',
        	if(empty($rule)){
        		continue;
        	}
        	$field = $field;  //字段
        	$value = $this->getFieldData($data,$field);  
        	$rule = explode('|',$rule);	
        	//如果规则中没有require表示数据存在时才会判断
        	if(!in_array('require',$rule) && empty($value)){
        		//不存在必须规则且数据也为空就可以不用处理
        		continue;
        	}
        	//循环规则
        	foreach ($rule as $k => $r) {
        		//调用单条数据验证的方法
        		$result = $this->checkItem($field,$value,$r,$message);
        		//判断
        		if(!$result){//失败
        			$flag = false;
					//这里就要判断是全部验证，还是单个验证了
					if(!$this->checkAll){
						//单个
						return false;
					}
					break; //第一个规则就没通过,后面的就不检测了
        		}
        	}
        }
        return $flag ? true : false;
    }

    /**
     * 根据规则检测字段的值
     * @param  [type] $field   字段名
     * @param  [type] $value   字段值
     * @param  [type] $rule    规则
     * @param  [type] $message 提示信息
     * @return [type]          
     */
    public function checkItem($field,$value,$rule,$message){
    	//先分规则
    	$r_value = "";		//初始化一个rule规则的附加值
    	if(strpos($rule,":")){
    		//表示要拆分
    		list($rule,$r_value) = explode(':',$rule);
    	}
    	//进行具体规则的验证
    	if(!empty($r_value)){
    		//这是具体规则的验证
    		$result = $this->hasRule($value,$rule,$r_value);
    	}else{
    		$result = $this->hasRule($value,$rule);
    	}
    	//判断
    	if(!$result){
    		if(isset($message[$field.".".$rule])){
    			$error = $message[$field.".".$rule];   //查询是否有默认的提示信息
    		}else{
    			if(isset($r_value)){//这里是查询默认提示信息
    				$error = $this->getDefaultErr($field,$rule,$r_value);
       			}else{	
    				$error = $this->getDefaultErr($field,$rule);
    			}
    		}
       		$this->error[] = $error;
    		return false;
    	}
    	return true;
    }

    /**
     * 获取默认的提示信息
     * @param  [type] $field   字段
     * @param  [type] $rule    规则
     * @param  string $r_value 规则附加值
     * @return string
     */
    protected function getDefaultErr($field,$rule,$r_value=""){
    	//查询规则是否存在
    	if(isset($this->defaultMsg[$rule])){
    		$error = $this->defaultMsg[$rule];
    		//替换属性
    		$error = str_replace(":attribute",$field,$error);
    	}
    	//存在规则附加值要进行判断
    	if($r_value){
    		//判断是不是有两个附加规则的规则
    		if(in_array($rule,$this->rule_add)){  //2个:attribute只能在 :1 - :2 之间
    			list($min,$max) = explode(',',$r_value);
    			$error = str_replace(":1",$min,$error);
    			$error = str_replace(":2",$max,$error);
    		}else{	//1个:attribute必须等于 :rule
    			$error = str_replace(":rule",$r_value,$error);
    		}
    	}
    	//返回错误
    	return $error;
    }

    /**
     * 具体跪着的验证
     * @param  [type]  $value   值
     * @param  [type]  $rule    规则
     * @param  string  $r_value 规则附加值
     * @return boolean
     */
    protected function hasRule($value,$rule,$r_value=""){
    	switch ($rule) {
    		case 'require':	//必须字段
    			$result = !empty($value) || $value=="0";  //0和非空
    			break;
    		case 'max':	//最大长度
    			$result = $this->max($value,$r_value);
    			break;
    		case 'min':	//最小长度
    			$result = $this->min($value,$r_value);
    			break;
    		case 'number':	//数字
    			$result = is_numeric($value);
    			break;
    		case 'length':	//固定长度
    			$result = $this->length($value,$r_value);
    			break;
    		case 'email':	//email格式
    			$result = $this->email($value);
    			break;
    		case 'egt':	//大于等于
    			$result = is_numeric($value) && $value >= $r_value;
    			break;
    		case 'gt':	//大于
    			$result = is_numeric($value) && $value > $r_value;
    			break;
    		case 'elt':	//小于等于
    			$result = is_numeric($value) && $value <= $r_value;
    			break;
    		case 'lt':	//小于
    			$result = is_numeric($value) && $value < $r_value;
    			break;
    		case 'eq':	//等于
    			$result = is_numeric($value) && $value = $r_value;
    			break;	
    		case 'date'://有效日期
                $result = false !== strtotime($value);
                break;
            case 'url'://url地址
                $result = $this->url($value);
                break;
        	case 'between':  //在x--xx之间
                $result = $this->checkBetween($value,$r_value,'between');
                break;
            case 'notBetween': //在x--xx之外
                $result = $this->checkBetween($value,$r_value,'notBetween');
                break;
            case 'in': //在x，xx，xxx中的某一个
                $result = $this->checkIn($value,$r_value,'in');
                break;
            case 'notIn': //不在x，xx，xxx中的某一个
                $result = $this->checkIn($value,$r_value,'notIn');
                break;
    		default:
    			$result = false;
    			break;
    	}
    	//返回结果
    	return $result;
    }

    /**
     * in相关规则检测
     * @param  [type] $value   值
     * @param  [type] $r_value 规则附加值
     * @param  [type] $type    类型
     * @return boolean
     */
    protected function checkIn($value,$r_value,$type){
    	//判断存在不存在$r_value
    	if(empty($r_value)){
    		throw new \Exception("rule format definition error:".$type, 1);
    	}
    	//判断规则附加值
		$ins = explode(',',$r_value);
		//根据类型判断
		if($type == 'in'){
			return in_array($value,$ins);
		}else{
			return !in_array($value,$ins);
		}
    }

    /**
     * between相关规则检测
     * @param  [type] $value   值
     * @param  [type] $r_value 规则附加值
     * @param  [type] $type    类型
     * @return boolean
     */
    protected function checkBetween($value,$r_value,$type){
    	//判断存在不存在$r_value
    	if(empty($r_value) || !strpos($r_value, ',')){
    		throw new \Exception("rule format definition error:".$type, 1);
    	}
    	//判断规则附加值
		list($min,$max) = explode(',',$r_value);
		if(!is_numeric($min) || !is_numeric($max) || $min >= $max){
			throw new \Exception("rule format definition error:".$type, 1);
		}else{
			//根据类型判断
			if($type == 'between'){
				return $value >= $min && $value <= $max;
			}else{
				return $value <= $min || $value >= $max;
			}
		}
    }

    /**
     * url格式检测
     * @param  [type] $value 值
     * @return boolean
     */
    protected function url($value){
    	$regex = "/^(https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/%=~_|]*$/i";
    	preg_match($regex,$value,$url);
    	if(!empty($url[0])){
    		return true;
    	}
    	return false;
    }

    /**
     * email格式检测
     * @param  [type] $value 值
     * @return boolean
     */
    protected function email($value){
    	$regex = "/^[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z]+\.[a-zA-Z]+(\.[a-zA-Z]+)*$/i";
    	preg_match($regex,$value,$email);
    	if(!empty($email[0])){
    		return true;
    	}
    	return false;
    }

    /**
     * max的规则判断
     * @param  [type] $value   字段值
     * @param  [type] $r_value 规则附加值
     * @return boolean
     */
    protected function max($value,$r_value){
    	//判断存在不存在$r_value
    	if(empty($r_value) || $r_value <= 0){
    		throw new \Exception("rule format definition error:max", 1);
    	}
    	//判断长度
    	$length = mb_strlen($value,Config::get('default_charset'));
    	if($length > $r_value){
    		return false;
    	}
    	return true;
    }

    /**
     * min的规则判断
     * @param  [type] $value   字段值
     * @param  [type] $r_value 规则附加值
     * @return boolean
     */
    protected function min($value,$r_value){
    	//判断存在不存在$r_value
    	if(empty($r_value) || $r_value <= 0){
    		throw new \Exception("rule format definition error:min", 1);
    	}
    	//判断长度
    	$length = mb_strlen($value,Config::get('default_charset'));
    	if($length < $r_value){
    		return false;
    	}
    	return true;
    }

    /**
     * length的规则判断
     * @param  [type] $value   字段值
     * @param  [type] $r_value 规则附加值
     * @return boolean
     */
    protected function length($value,$r_value){
    	//判断存在不存在$r_value
    	if(empty($r_value) || $r_value <= 0){
    		throw new \Exception("rule format definition error:length", 1);
    	}
    	//判断长度
    	$length = mb_strlen($value,Config::get('default_charset'));
    	//判断规则附加值
    	if(strpos($r_value, ',')){  //length:6,15
    		list($min,$max) = explode(',',$r_value);
    		if(!is_numeric($min) || !is_numeric($max) || $min >= $max){
				throw new \Exception("rule format definition error:length", 1);
			}
    		return $length >= $min && $length <= $max;  //都成立真
    	}
    	return $length == $r_value;
    }


    /**
     * 获取字段对应的值
     * @param  [type] $data  数据数组
     * @param  [type] $field 字段
     * @return [type]  
     */
    public function getFieldData($data,$field){
    	//形式 xxx.xxx
    	if(strpos($field, '.')){
            //二维数组取值
            list($name1, $name2) = explode('.', $field);
            $value = isset($data[$name1][$name2]) ? $data[$name1][$name2] : null;
        }else{
            $value = isset($data[$field]) ? $data[$field] : null;
        }
        return $value;
    }






}


































