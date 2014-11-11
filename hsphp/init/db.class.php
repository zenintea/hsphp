<?php
	!defined('ZF') && exit('Access Denied');
	class DB{
		static $mysqli;
		static function connect(){
			if(is_null(self::$mysqli)) {
				$mysqli=new mysqli(HOST, USER, PASS,DBNAME,PORT);
				if (mysqli_connect_errno()) {
					echo "<font color='red'>连接失败: ".mysqli_connect_error().",请查看config.inc.php文件设置是否有误！</font>";
					return false;
				}else{
					$mysqli->query("set names ".DBCHARSET);
					self::$mysqli=$mysqli;
					unset($mysqli);
					return self::$mysqli;
				}
			}else{
				return self::$mysqli;
			}
		}
		
		static function fetch($result){  //获取一条记录
			return DB::rows($result)? $result->fetch_array() : false ;
		}
		
		static function rows($result){
			return is_object($result) ? $result->num_rows : false ;
		}
				
		static function affected_rows(){   //上一条SQL语句影响行数
			return self::connect()->affected_rows;
		}

		static function insert_id(){  //最后插入数据ID
			return self::connect()->insert_id;
		}
		
		static function t($table){  //表前缀
			return 	' '.DBPRE.$table.' ';
		}
		
		static function query($sql,$check=SQLCHECK){  //执行SQL语句
			$mysqli=self::connect();
			$check && self::checkquery($sql);
			return $mysqli->query($sql);
		}
 
		static function checkquery($sql) {  //SQL语句检测
			return sqlcheck::checkquery($sql);
		}


		static function db_version(){  //数据库版本信息
			return self::connect()->server_info;
		}

		static function db_size(){  //数据库已使用空间
			$sql = "SHOW TABLE STATUS FROM " . DBNAME;
			$sql .= " LIKE '%'";
			$result=self::connect()->query($sql);
			$size = 0;
			while($row=$result->fetch_assoc())
				$size += $row["Data_length"] + $row["Index_length"];
			return tosize($size);		
		}
		
		static function startcommit() {  //开启事务处理
			return self::connect()->autocommit(false);	
		}
		
		static function commit() {  //提交事务处理
			$mysqli=self::connect();
 			$mysqli->commit();
        	return $mysqli->autocommit(true);

		}
		
		static function rollback() {   //事务回流
			$mysqli=self::connect();
  			$act=$mysqli->rollback();
        	$mysqli->autocommit(true);
			return $act;
		}
	}
	