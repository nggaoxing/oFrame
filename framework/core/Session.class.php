<?php
namespace core;

//session管理类

class Session{

	//session前缀(作用域)
	protected static $prefix = '';	
	//记录session的开启状态
	protected static $session = false;

	/**
	 * 开启session
	 * @return [type] [description]
	 */
	public static function start(){
		//判断当前session是否启动
		if(PHP_SESSION_ACTIVE != session_status()){
			//没启动，开始启动
			session_start();
		}
		self::$session = true;
	}

	/**
     * 设置或者获取session前缀
     * @param string $prefix
     * @return 
     */
    public static function prefix($prefix = '')
    {
        if (empty($prefix) && null !== $prefix) {
            return self::$prefix;
        } else {
            self::$prefix = $prefix;
        }
    }

	/**
	 * 设置session的值
	 * @param string $name   名称
	 * @param string $value  值
	 * @param [type] $prefix 前缀
	 */
	public static function set($name, $value = '', $prefix = null){
		//判断是否开启了session
		!self::$session && self::start();
		//前缀判断
		$prefix = !is_null($prefix) ? $prefix : self::$prefix;
		//判断name赋值
		if(strpos($name,'.')){	//user.username  
			//user下的username是个二维数组
			list($name1,$name2) = explode('.',$name);
			//再判断是否有前缀
			if($prefix){	//有前缀
				$_SESSION[$prefix][$name1][$name2] = $value;
			}else{	//没前缀
                $_SESSION[$name1][$name2] = $value;
            }
		}elseif($prefix){	//有前缀，但一个名称
			$_SESSION[$prefix][$name] = $value;
		}else{	//没前缀，直接存在session下面
            $_SESSION[$name] = $value;
        }
	}

	/**
	 * 获取session的值
	 * @param  string $name   名称
	 * @param  [type] $prefix 前缀
	 * @return 
	 */
	public static function get($name = '', $prefix = null){
		//判断是否开启了session
		!self::$session && self::start();
		//前缀判断
		$prefix = !is_null($prefix) ? $prefix : self::$prefix;
		//判断name获取值
		if($name == ""){
			//取全部值
			if($prefix){
				$value = !empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : [];
			}else{
				$value = $_SESSION;
			}
		}elseif($prefix){//name不空,有前缀
			//判断name赋值
			if(strpos($name,'.')){	//user.username  
				list($name1, $name2) = explode('.', $name);
				$value = isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;	
			}else{
				$value = isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
			}
		}else{	//有name没前缀
			if(strpos($name,'.')){	//user.username  
				list($name1, $name2) = explode('.', $name);
				$value = isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;	
			}else{
				$value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
			}
		}
		return $value;
	}

	/**
	 * 判断session数据是否存在
	 * @param  [type]  $name   名称
	 * @param  [type]  $prefix 前缀
	 * @return boolean 
	 */
	public static function has($name,$prefix=null){
		//判断是否开启了session
		!self::$session && self::start();
		//前缀判断
		$prefix = !is_null($prefix) ? $prefix : self::$prefix;
		//是否是二级session
		if(strpos($name, '.')){
            // 支持数组
            list($name1, $name2) = explode('.', $name);
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        }else{
            return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
        }
	}

	/**
	 * 删除指定session的值
	 * @param  [type] $name   名称
	 * @param  string $prefix 前缀
	 * @return 
	 */
	public static function delete($name, $prefix = ''){
		//判断是否开启了session
		!self::$session && self::start();
		//前缀判断
		$prefix = !is_null($prefix) ? $prefix : self::$prefix;	
		//判断传入的参数
		if(is_array($name)){
			//循环删除
			foreach ($name as $v) {
                self::delete($v, $prefix); //递归
            }
		}elseif(strpos($name, '.')){ //点连接的字符串
			list($name1, $name2) = explode('.', $name);	//user.name
			if($prefix){	//带前缀	
                unset($_SESSION[$prefix][$name1][$name2]);
            }else{
                unset($_SESSION[$name1][$name2]);
            }
		}else{	//单个字符串
			if($prefix){	//带前缀
                unset($_SESSION[$prefix][$name]);
            }else{
                unset($_SESSION[$name]);
            }
		}
	}

	/**
     * 清空session数据
     * @param string|null   $prefix前缀
     * @return void
     */
    public static function clear($prefix = null){
        //判断是否开启了session
		!self::$session && self::start();
		//前缀判断
		$prefix = !is_null($prefix) ? $prefix : self::$prefix;	
        if ($prefix) {
            unset($_SESSION[$prefix]);	//清除前缀
        } else {
            $_SESSION = [];	//清空所有
        }
    }

    /**
     * 销毁session
     * @return void
     */
    public static function destroy(){
        if(!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        self::$session = false;
    }

    /**
     * 停止session的写入
     * @return void
     */
    public static function stop(){
        //停止session写入
        session_write_close();
        self::$session = false;
    }


}













