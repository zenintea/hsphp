<?php 
	class index_model{
		function __construct(){
			if( getcookie('login') !='yes' && getcookie('manager')!='yes'){
				message('未登录无权限此操作','?act=login&fun=loginform',1);
			}
		}
	}