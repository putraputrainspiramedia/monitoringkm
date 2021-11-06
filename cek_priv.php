<?php
error_reporting(0);
session_start();
#require_once("./inc/conn.php");
#require_once('./inc/fungsi.php');

#ini_set("max_execution_time",300);

if (empty($_SESSION['km_user'])) {
	session_destroy();	
	//session_unset($_SESSION['km_user']);
	//session_unset($_SESSION['km_timeout']);
	//session_unset($_SESSION['km_nama']);
	
		
	$_SESSION['km_user'] = "";
	$_SESSION['km_timeout'] = "";
	$_SESSION['km_nama'] = "";
	$_SESSION['km_profil'] = "";
	
	echo "<script>alert('Anda belum login, silahkan login ulang !!');window.location.href='index.php'</script>";

} else {

	if (($_SESSION['km_timeout'] * 3600) < time()) { // 15 mniet 900
				
		$_SESSION['km_user'] = "";
		$_SESSION['km_timeout'] = "";
		$_SESSION['km_nama'] = "";
		$_SESSION['km_profil'] = "";

		echo "<script>alert('Session telah habis, silahkan login ulang !!');window.location.href='index.php'</script>";

	} else {
	
		#------------------------------------ cek menu
		if (!empty($_REQUEST['fl'])) {
			$qry_cek = mysqli_query($conn,"select a.id_menu, a.is_input, a.is_edit, a.is_delete, a.is_view
												from user_profil_menu a
												inner join user_menu b on a.id_menu = b.id_menu
												where a.id_profil = '".$_SESSION['km_profil']."' and b.fl = '".$_REQUEST['fl']."' and b.is_active = '1'");
			$jml_cek = mysqli_num_rows($qry_cek);
			$data_cek = mysqli_fetch_array($qry_cek);
			
			if ($jml_cek>0) {
				$km_timeout = time();
				//session_register("sk_timeout");
				$_SESSION['km_timeout'] = $km_timeout;
				$_SESSION['km_user_input'] = $data_cek['is_input'];
				$_SESSION['km_user_edit'] = $data_cek['is_edit'];
				$_SESSION['km_user_delete'] = $data_cek['is_delete'];
				$_SESSION['km_user_view'] = $data_cek['is_view'];
			} else {
				echo "<script>alert('Maaf, halaman tidak ditemukan !!');window.location.href='index.php'</script>";
			}
			
		} else {	
			$km_timeout = time();
			//session_register("km_timeout");
			$_SESSION['km_timeout'] = $km_timeout;
			
		}
	
	}
	
}

?>