<?php
	if(DDOS>=1){
		$last=getcookie('lastrequest')?getcookie('lastrequest'):'';
		dsetcookie('lastrequest',TIMENOW,816400);
		if(empty($last) || (TIMENOW-$last)>360000 ){
			message('禁止二次请求或采集,页面重新载入',A,0);
			echo '<script language="JavaScript">';
			echo 'function reload() {';
			echo '	document.location.reload();';
			echo '}';
			echo 'setTimeout("reload()", 1000);';
			echo '</script>';
			exit;
		}
	}
	if(DDOS>=2){
		if( ($_SERVER['HTTP_X_FORWARDED_FOR'] ||
		$_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] ||
		$_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_CACHE_INFO'] ||
		$_SERVER['HTTP_PROXY_CONNECTION'])) {
			exit(message('禁止代理访问',A,0));
		}
	}

	if(DDOS>=3){
		if( (TIMENOW-$last) < 1){
			exit(message('禁止2秒内重复刷新本页面',A,0));
		}
	}
	