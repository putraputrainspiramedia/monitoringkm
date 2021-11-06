<?php
session_start();
require_once ("./inc/conn.php");
									
session_destroy();	
#session_unset($_SESSION['km_user']);
#session_unset($_SESSION['km_timeout']);	

$_SESSION['km_user'] = "";
$_SESSION['km_timeout'] = "";
$_SESSION['km_nama'] = "";
$_SESSION['km_profil'] = "";
		
		
echo "<script>alert('Terima kasih telah keluar dari aplikasi !!');</script>";
echo "<script>window.location.href='index.php';</script>";
?>
