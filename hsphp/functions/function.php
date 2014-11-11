<?php
!defined('ZF') && exit('Access Denied');

function p(){    //打印调试
	$args=func_get_args();  //获取多个参数
	if(count($args)<1){
		echo "<font color='red'>必须为p()函数提供参数!";
		return;
	}	
	echo '<div style="width:100%;text-align:left"><pre>';
	//多个参数循环输出
	foreach($args as $arg){
		if(is_array($arg)){  
			print_r($arg);
			echo '<br>';
		}else if(is_string($arg)){
			echo $arg.'<br>';
		}else{
			var_dump($arg);
			echo '<br>';
		}
	}
	echo '</pre></div>';	
}

function tosize($bytes) {  //文件大小单位转换函数
	if ($bytes >= pow(2,40)) {      		     //如果提供的字节数大于等于2的40次方，则条件成立
		$return = round($bytes / pow(1024,4), 2);    //将字节大小转换为同等的T大小
		$suffix = "TB";                        	     //单位为TB
	} elseif ($bytes >= pow(2,30)) {  		     //如果提供的字节数大于等于2的30次方，则条件成立
		$return = round($bytes / pow(1024,3), 2);    //将字节大小转换为同等的G大小
		$suffix = "GB";                              //单位为GB
	} elseif ($bytes >= pow(2,20)) {  		     //如果提供的字节数大于等于2的20次方，则条件成立
		$return = round($bytes / pow(1024,2), 2);    //将字节大小转换为同等的M大小
		$suffix = "MB";                              //单位为MB
	} elseif ($bytes >= pow(2,10)) {  		     //如果提供的字节数大于等于2的10次方，则条件成立
		$return = round($bytes / pow(1024,1), 2);    //将字节大小转换为同等的K大小
		$suffix = "KB";                              //单位为KB
	} else {                     			     //否则提供的字节数小于2的10次方，则条件成立
		$return = $bytes;                            //字节大小单位不变
		$suffix = "Byte";                            //单位为Byte
	}
	return $return ." " . $suffix;                       //返回合适的文件大小和单位
}

function dsetcookie($var, $value = '', $life = 2592000) {
	$life=$life?TIMENOW+$life:0;
	setcookie($var, base64_encode($value), $life, '/','', 0, false);
}

function getcookie($key){  //获取cookie
	return isset($_COOKIE[$key]) ? base64_decode($_COOKIE[$key]) : false;
}

function delcookie($key){	//销毁cookie
	if(is_array($key)){
		foreach($key as $val){
			setcookie($val,'',TIMENOW-3600,'/');
		}
	}else{
		setcookie($key,'',TIMENOW-3600,'/');
	}
}

function thems(){ //主题模版
	$thems=($thems=getcookie('thems'))?"{$thems}/":'default/';
	return $thems;
}

function template($tmp){  //加载模板
	$thems=thems().AT;
	$template=TPL.$thems.'/'.$tmp.'.tpl.php';
	!file_exists($template) && exit(message("{$template}模版文件不存在",A,0));
	return $template;
}

function comfile($file){   //公共文件加载
	return TPL.thems().$file.'.tpl.php';
}

function dstripslashes($string) {   //去掉转义
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function daddslashes($string, $force = 1) {  //增加转义
	if(is_array($string)) {
		$keys = array_keys($string);
		foreach($keys as $key) {
			$val = $string[$key];
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

function message($str,$url=A,$time=3){   //消息提示
	if(!$time){
		$mess="</div>";	
	}else{
		$mess="<p>
			 在( <span id='sec' style='color:blue;font-weight:bold'>{$time}</span> )秒后自动跳转，或直接点击 <a onclick=\"location='{$url}'\" href='javascript:;''>这里</a> 跳转<br>
			 </p>
	 </div>			
	 <script>
			var seco=document.getElementById(\"sec\");
			var time={$time};
			var tt=setInterval(function(){
					time--;
					seco.innerHTML=time;	
					if(time<=0){
						location='{$url}';
						return;
					}
				}, 1000);
	</script>";	
	}
$html="<html>
<head>
	<meta charset='utf-8' />
	<title>提示消息 -GTPHP</title>
	<style type='text/css'>
		body { font: 75% Arail; text-align: center; }
		#notice { width: 300px;border: 3px solid #BBB; background: #aaa;border-radius:3px; padding: 3px;
		position: absolute; left: 50%; top: 50%; margin-left: -155px; margin-top: -100px;box-shadow:1px 1px 15px #999 }
		#notice div { background: #FFF; padding: 30px 0 20px; font-size: 1.2em; font-weight:bold }
		#notice p { background: #FFF; margin: 0; padding: 0 0 20px; }
		a { color: #f00} a:hover { text-decoration: none; }		
	</style>
</head>
<body>
	<div id='notice'>
		<div style='width:100%;text-align:center;'>{$str}</div>
			<p style='font: italic bold 1.5cm cursive,serif; color:red'>
				GTPHP
			</p>
{$mess}	
	</div>		
</body>
</html>";
echo $html;

}

function getip(){   //获取IP
	if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif( isset($_SERVER['HTTP_CLIENT_IP']) ){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip = preg_match( "/^[\d]([\d\.]){5,13}[\d]$/", $ip ) ? $ip : '';
}

function headergo($url){  //跳转
	header("Location:{$url}");
}

function cutstr($string, $length, $dot = ' ...') {  //截取
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	if(strtolower(DBCHARSET) == 'utf8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}
function isemail($email) { //验证email
	return strlen($email) > 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
}

function timeformat($time,$format='Y-m-d H:i:s'){   //时间格式化
	return date($format,$time);
}

function toarea( $ip ){  //IP地址归属地
	 static $iploca;
	 if($iploca===null){
	 	$iploca = new qqwry;
	 }
	 $iploca->qqwry($ip);
	 return iconv("GB2312","UTF-8//IGNORE",$iploca->Country);
}

function wlogs($user,$time,$act,$ip,$area,$file='other'){  //操作日志
	//$logdate=date('Ym',$time);
	$filename = RES."other/log/{$file}_log.php";
	!file_exists(RES.'other/log') && @mkdir(RES.'other/log',0755);	
	$f_open = fopen($filename,'ab');
	$str = $user.','.timeformat($time).','.$act.','.$ip.','.$area."\n";
	if(flock($f_open , LOCK_EX)){    //文件锁
		fwrite($f_open,$str);
		flock($f_open , LOCK_UN);  
	}
	fclose($f_open);		
}
function dtrim($array,$tag=false){   //过滤空格及标签
	foreach($array as $k=>$v){
		$arr[$k]=$tag ? strip_tags(trim($v)) : trim($v);
	}	
	return $arr;
}
function phpmail( $sendto_email, $subject='', $body=''){      //发送邮件
	static $mail;
 	if($mail===null){
		$mail = new phpmail();  
	} 
	
	if(MAIL_OS){
		$mail->IsSMTP();                  // send via SMTP    
		$mail->Host = SMTP;   // SMTP servers    
		$mail->SMTPAuth = true;           // turn on SMTP authentication    
		$mail->Username = SMTP_USER;     // SMTP username  注意：普通邮件认证不需要加 @域名    
		$mail->Password = SMTP_PASSWD; // SMTP password    
	}
	$mail->From = SMTP_FROM;      // 发件人邮箱    
	$mail->FromName = SMTP_FROMNAME;  // 发件人    
  
	$mail->CharSet = "utf-8";   // 这里指定字符集！    
	$mail->Encoding = "base64";    
	$mail->AddAddress($sendto_email,"英豪校友");  // 收件人邮箱和姓名    
	//$mail->AddReplyTo("yourmail@yourdomain.com","yourdomain.com");    
	//$mail->WordWrap = 50; // set word wrap 换行字数    
	//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件    
	//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    
	$mail->IsHTML(true);  // send as HTML    
	// 邮件主题    
	$mail->Subject = $subject;    
	// 邮件内容    
	$mail->Body = '<html><head><meta http-equiv="Content-Language" content="zh-cn"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>'.$body.'</body></html>';                                                                          
	 $mail->AltBody ="text/html";    
	if(!$mail->Send())    {    
		echo "邮件发送有误 <p>";    
		echo "邮件错误信息: " . $mail->ErrorInfo;    
		return false;   
	}else {    
		echo "邮件发送成功!<br />";
		return true;    
	}    
}

function encauth($str){    //加密函数
	return base64_encode($str).substr(md5(ZF),0,10);		
}
function decauth($str){   //解密函数	
	return base64_decode(substr($str,0,-10));
}

function savecache($data,$filename){   //保存缓存
	!file_exists(RES.'runtime') && mkdir(RES.'runtime','0755');
	@file_put_contents(RES.'runtime/'.$filename.'.php',serialize($data));
}

function checkcache($filename,$times){  //判断缓存有效期
	$filename=RES.'runtime/'.$filename.'.php';
	return file_exists($filename) && ( TIMENOW - filemtime($filename) < $times );
}

function loadcache($filename){  //加载缓存
	return unserialize(file_get_contents(RES.'runtime/'.$filename.'.php'));
}

function delcache($file=''){  //删除缓存,若没有指定文件则删除所有缓存
	if($file==''){
		$dir = opendir(RES.'runtime/');
		while ($fileName = readdir($dir)) {
			$file = RES.'runtime/'. $fileName;
			/***过滤.和..上级目录 ***/
			if ($fileName != '.' && $fileName != '..') {
				@unlink($file);
			}
		}
	}else{
		$arrfile=is_array($file)?$file:array($file); //转化数组
		foreach($arrfile as $file){
			@unlink(RES.'runtime/'.$file.'.php');
		}
	}
}

function formhash(){		//form表单提交数据hash加密串
	$hash=substr(sha1(ZF).md5(substr(TIMENOW,0,5)),32,48);
	return $hash;
}

function checksubmit($submit){   //检测数据来源,禁止非法外部提交数据
	if(!isset($_POST[$submit]) || $_POST[$submit]==false){
		exit(message('来路不明,禁止外部提交数据',A,0));
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['formhash']) && $_POST['formhash'] == formhash() && empty($_SERVER['HTTP_X_FLASH_VERSION']) && (empty($_SERVER['HTTP_REFERER']) ||
			preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))) {
			return true;	
	}else{
		exit(message('来路不明,禁止外部提交数据',A,0));
	}
}