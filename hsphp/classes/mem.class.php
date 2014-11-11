<?php
	!defined('ZF') && exit('Access Denied');
	class Mem{
		static $mc=null;

		public function __construct($params){
			if(is_null(self::$mc)){
				$check=extension_loaded('memcache') && class_exists('memcache');
				if(!$check){
					message('PHP未安装memcache扩展库',A,0);
					exit;
				}
				//有多个memcache服务器
				if( count($params) >= 1){
					$mc = new Memcache;
					foreach ($params as $v){
						call_user_func_array(array($mc, 'addServer'), $v);
						$stats=$mc->getStats();
						if(empty($stats)){	
							message("{$v[0]},memcache服务器未启动",A,0);
							exit;
						}
					}
				}else{
					message('缺少memcache服务器',A,0);
				}
				self::$mc=$mc;
				unset($mc);
			}
		}
		
		static function connect(){ //memcache服务检测
			if(is_null(self::$mc)){
				message('请在配置文件中先开启Memcache',A,0);
				exit;
			}else{
				return self::$mc;
			}
		}

		static function set($key,$val,$time=3600){  //增加键值
			$mem=self::connect();
			$key=MEMPRE.$key;
			return $mem->set($key, $val, MEMCACHE_COMPRESSED, $time);	
		}

		static function get($key){ //获取缓存
			$mem=self::connect();
			if(is_array($key)){
				foreach($key as $v){
					$keys[]=MEMPRE.$v;	
				}
				$data= $mem->get($keys);
				foreach($data as $k=>$v){
					$datas[substr($k,strlen(MEMPRE))]=$v;
				}
				return $datas;
			}else{
				return $mem->get(MEMPRE.$key);
			}
		}
		 
		static function del($key,$time=0) {    //删除单个缓存
			$mem=self::connect();
			return $mem->delete(MEMPRE.$key,$time);
		}
	
		static function clear() {     //清除所有缓存
			$mem=self::connect();
			return $mem->flush();
		}
	
		static function increment($key, $step = 1){   //值加1
			$mem=self::connect();
			return $mem->increment(MEMPRE.$key, $step);
		}
	
		static function decrement($key, $step = 1){  //值减1
			$mem=self::connect();
			return $mem->decrement(MEMPRE.$key, $step);
		}
		
		static function stats(){  //返回状态信息
			$mem=self::connect();
			return $mem->getextendedstats();
		}
		
		static function version(){ //返回版本信息
			$mem=self::connect();
			return $mem->getVersion();
	
		}
}