<?php
	ob_start();
	header("Content-Type:text/html;charset=utf-8");  //设置系统的输出字符为utf-8
	date_default_timezone_set("PRC");    		 //设置时区（中国）
	define('TIMENOW',time());    //当前时间UNIX时间戳
	define('ZF',ROOT.'gtphp');     //ZF框架的路径

	define('SITEURL','http://'.$_SERVER['HTTP_HOST'].rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])),'/'));//域名URL
	define('A',basename($_SERVER['SCRIPT_NAME']));

	if(!file_exists(ROOT.'config.inc.php')){   //配置文件不存在则生成
		$config=<<<st
<?php
	define("HOST", "localhost");			      //数据库主机
	define("USER", "root");                               //数据库用户
	define("PASS", "1");                                   //数据库密码
	define("PORT",3306);							//数据库端口
	define("DBNAME","yh1993");			      //数据库名
	define('DBCHARSET','utf8');            //数据库编码
	define('DBPRE','yh_');                 //数据库前缀
	define('SQLCHECK',1);                  //是否开启SQL安全检测，可自动预防SQL注入攻击
	//DDOS,CC等攻击防御级别,0:关闭防御,1:防止二次请求及采集,2:增加限制代理访问,3:增加2秒内重复刷新页面,3为最高级别
	define('DDOS',1);
	
	/****邮件系统,0代表使用系统组件,1代表使用SMTP服务器,推荐使用系统组件发送***/
	define('MAIL_OS',0);
	define('SMTP',"smtp.163.com");				//邮件发送smtp,若选择系统组件则可为空
	define('SMTP_USER',"gz_yh1993");			//邮件发送smtp用户名,若选择系统组件则可为空
	define('SMTP_PASSWD',"yh1993");				//邮件发送smtp密码,若选择系统组件则可为空
	define('SMTP_FROM',"gz_yh1993@163.com");    //邮件发送smtp来源
	define('SMTP_FROMNAME',"英豪校友管理员");    //邮件发送smtp发件人

	/************Memcache缓存服务器相关配置**************/
	define('MEMCACHE',0);     //开启memcache服务1,关闭0
	define('MEMPRE','gtphp_');  //缓存键前辍
	\$memservers = array(    //memcache服务器数组
			array("localhost", '11211'),
			//array("www.test.com", '11211')
	);
st;
	file_put_contents(ROOT.'config.inc.php',$config);
	}

	include ROOT.'config.inc.php';    //加载配置文件
	include ZF.'/functions/function.php';  //加载公共函数文件

	include ZF.'/init/attack.sys.php';   //加载防入侵挂马插件
	include ZF.'/init/security.sys.php';   //加载防DDOS,CC攻击插件

	include ZF.'/init/db.class.php';   //加载数据库接口
	include ZF.'/init/sqlcheck.class.php';   //加载SQL检测接口

	if(!file_exists(ZF.'/init/'.APP.'.class.php')){
		$str="<?php \r\n\tclass ".APP."_model{\r\n\t}";
		file_put_contents(ZF.'/init/'.APP.'.class.php',$str);
	}

	include ZF.'/init/'.APP.'.class.php';  //加载公共类文件
	
	define('CIP',getip());  //客户端IP
	define('FORMHASH',formhash());  //form表单hash值
	
	PHP_VERSION < '5.3.0' && set_magic_quotes_runtime(0);   //去掉自动转义
	function_exists('ini_get') && ini_set('memory_limit', '128m');  //提高运行内存限制
	define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
	if(MAGIC_QUOTES_GPC) {
		$_GET = dstripslashes($_GET);
		$_POST = dstripslashes($_POST);
		$_COOKIE = dstripslashes($_COOKIE);
	}
	
	//设置包含目录（类所在的全部目录）,  PATH_SEPARATOR 分隔符号 Linux(:) Windows(;)
	$include_path=get_include_path();                         //原基目录
	$include_path.=PATH_SEPARATOR.ZF."/classes/";       //框架中公共基类所在的目录

	//设置include包含文件所在的所有目录	
	set_include_path($include_path);
	
	//自动加载类 	
	function __autoload($className){
		if(strtolower($className=="memcache")){        //如果是memcache与smarty类则不包含
			return false;
		}else{                             //如果是其他类，将类名转为小写
			include strtolower($className).".class.php";	
		}
	}

	MEMCACHE && new Mem($memservers);  //开启memcache缓存系统

	define('COMM',ROOT.'commons/');  //公共资源
	define('COMM_JS',COMM.'js/');	//公共资源js
	define('COMM_CSS',COMM.'css/');	 //公共资源css
	define('COMM_IMG',COMM.'images/'); //公共资源images
	define('COMM_UP',COMM.'uploads/'); //公共资源uploads
	define('COMM_OT',COMM.'other/'); //公共资源uploads

	!file_exists(COMM) && mkdir(COMM,'0755');
	!file_exists(COMM_JS) && mkdir(COMM_JS,'0755');
	!file_exists(COMM_CSS) && mkdir(COMM_CSS,'0755');
	!file_exists(COMM_IMG) && mkdir(COMM_IMG,'0755');
	!file_exists(COMM_UP) && mkdir(COMM_UP,'0755');
	!file_exists(COMM_OT) && mkdir(COMM_OT,'0755');

	define('PATH',ROOT.APP.'/');  //应用目录
	define('ACT',PATH.'act/');  //应用控制器目录action
	define('TPL',PATH.'tpl/');  //应用模板目录tpl
	define('RES',PATH.'res/');  //应用资源目录res

	!file_exists(PATH) && mkdir(PATH,'0755');
	$str=<<<st
<?php
	!defined('ZF') && exit('Access Denied');
	class index {
		function indexop(){
			echo "<h1>:)</h1><b>欢迎使用GTPHP框架,第一次访问时会自动生成项目结构</b>";
		}		
	}
st;
	!file_exists(ACT) && mkdir(ACT,'0755') && file_put_contents(ACT.'index.class.php',$str);
	!file_exists(TPL) && mkdir(TPL,'0755');
	!file_exists(RES) && mkdir(RES,'0755') && mkdir(RES.'css','0755') && mkdir(RES.'images','0755') && mkdir(RES.'js','0755')&& mkdir(RES.'runtime','0755') && mkdir(RES.'other','0755');

	
	$act=isset($_GET['act'])?$_GET['act']:'index';  //action类
	define('AT',$act);                    //当前控制器类名
	$fun=isset($_GET['fun'])?$_GET['fun']:'indexop'; //action类调用函数,默认调用indexop
	define('FUN',$fun);             //当前控制器函数
	
	if(file_exists(ACT.$act.'.class.php')){   //判断控制器类文件是否创建
		require ACT.$act.'.class.php';
	}else{
		message("控制器<font color='#990066'>{$act}</font>文件尚未创建",A,0);
	}
	
	$class=new $act;    //声明控制器对象
	
	if(!method_exists($class,$fun)){  //检测控制器类函数是否定义
		message("控制器类函数<font color='#990066'>{$fun}()</font>未定义",A,0);
	}

	call_user_func_array(array($class,$fun),array());
	exit;
