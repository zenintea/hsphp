<?php 
	class admin_model{
		function __construct(){
			session_start();
			if(!$_SESSION['login']){
				echo '<script type="text/javascript">window.top.location="admin.php";</script>';
			}
		}
	}