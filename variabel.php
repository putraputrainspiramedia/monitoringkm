<?php

require_once("inc/conn.php");

switch($_REQUEST['tp']) {
	case 'organisasi':
		organisasi();
		break;
}


function organisasi(){
	global $conn;
	
	echo "<option value='0'>Semua Organisasi</option>";
	
	$qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi 
								from p_organisasi 
								where id_group = '".$_REQUEST['group']."'
								order by nama_organisasi");
	while ($dt_pil = mysqli_fetch_array($qry_pil)) {
		echo "<option value='".$dt_pil['id_organisasi']."'>".$dt_pil['nama_organisasi']."</option>";
	}
}
?>