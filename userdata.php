<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");

switch($_REQUEST['tp']) {
	case 'input':
		if ($_SESSION['km_user_input']==1) {
			input_data();
		}
		break;
	case 'edit':
		if ($_SESSION['km_user_edit']==1) {
			edit_data();
		}
		break;
	case 'delete':
		if ($_SESSION['km_user_delete']==1) {
			delete_data();
		}
		break;	
	default :
		if ($_SESSION['km_user_view']==1) {
			list_data();
		}
		break;
}


function list_data(){
	global $conn;
	
		
	$columns = array(
	// datatable column index  => database column name
		0 =>'username',
		1 =>'nama');
		
	$draw = $_REQUEST['draw'];
	$start = $_REQUEST['start'];
	$length = $_REQUEST['length'];
	//$searchVal = $_REQUEST['search'];
	//searchVal = $_REQUEST['value'];
	$columns = $_REQUEST['columns'];
	$limit = $_REQUEST['length'];
	
	$search = $_REQUEST['search'];
	$searchVal = $search['value'];
	
	$order = $_REQUEST['order'];
	$orderCol = $columns[$order[0]['column']]['data'];
	$orderDir = $order[0]['dir'];
	
	$orderOpst = strtoupper($orderDir)=="ASC" ? "DESC":"ASC";
	
	if (empty($start)) {
		$start = 0;
	}
	if (empty($limit)) {
		$limit = 10;
	}
	
	if (empty($orderCol)) {
		$orderCol = " nama";
	}
	
	if (!empty($searchVal)) {
		$where = " and (nama like '%".$searchVal."%' or username like '%".$searchVal."%' or nama_profil like '%".$searchVal."%' or nama_unit like '%".$searchVal."%'or nama_organisasi like '%".$searchVal."%' ) ";
	}
	
	$stmt = "select a.nama, a.username, a.id_user, b.nama_profil, b.id_profil, c.id_unit, c.nama_unit, d.id_organisasi, d.nama_organisasi
			from user_app a
 			left join user_profil b on a.id_profil = b.id_profil
			left join p_unit c on a.id_unit = c.id_unit
			left join p_organisasi d on a.id_organisasi = d.id_organisasi
				where id_user is not null ".$where."
				ORDER BY $orderCol $orderDir
				limit $start, $limit";
			
	$stmt_count = "select a.id_user from user_app a
					left join user_profil b on a.id_profil = b.id_profil
					left join p_unit c on a.id_unit = c.id_unit
			left join p_organisasi d on a.id_organisasi = d.id_organisasi
				where a.id_user is not null ".$where."";
	$qryCount = mysqli_query($conn,$stmt_count);
	$totalCount = mysqli_num_rows($qryCount);
	
	$qry_filtercount = mysqli_query($conn,$stmt_count);
	$filterCount = mysqli_num_rows($qry_filtercount);
	
	$query = mysqli_query($conn,$stmt);
	
	// echo $koneksi->last_query();
	if($query){
		$status = TRUE;
		$data = array();
		$i=0;
		while ($row = mysqli_fetch_array($query)){
				$organisasi = "";
				$id_organisasi = "";
				//$data[$i]["id"] = $row['KodePr']."##".$row['Nama']."##".$row['Alamat']."##".$row['GSM']."##".base64_encode($row['KodePr']);
				$qry_org = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
												from p_organisasi a
												inner join user_app_organisasi b on a.id_organisasi = b.id_organisasi
												where b.id_user = '".$row['id_user']."' order by a.id_group, a.nama_organisasi");
				while ($row_org = mysqli_fetch_array($qry_org)) {
					$organisasi .= $row_org['nama_organisasi'].",";
					$id_organisasi .= $row_org['id_organisasi']."_";
				}
				$organisasi = substr($organisasi,0,strlen($organisasi)-1);
				$id_organisasi = substr($id_organisasi,0,strlen($id_organisasi)-1);
				
				$data[$i]["nama"] = $row['nama'];
				$data[$i]["username"] = $row['username'];
				$data[$i]["nama_unit"] = $row['nama_unit'];
				$data[$i]["nama_organisasi"] = $organisasi;
				$data[$i]["nama_profil"] = $row['nama_profil'];
				#$data[$i]["action"] = "<a href='?fl=user&tp=edit&id=".enkripsi($row['id_user'])."'><img src='img/pencil-icon.png' title='Ubah'></a>&nbsp;<a href='?fl=user&tp=delete&id=".enkripsi($row['id_user'])."'><img src='img/cross-icon.png' title='Hapus'></a>";
				$data[$i]["action"] = "<a href='#'  onClick='editdata(\"".enkripsi($row['id_user'])."\",\"".$row['username']."\",\"".$row['nama']."\",\"".$row['id_profil']."\",\"".$row['id_unit']."\",\"".$id_organisasi."\");'><img src='img/pencil-icon.png' title='Ubah' id='edit'></a>&nbsp;&nbsp;&nbsp;<a href='#' onClick='hapusdata(\"".enkripsi($row['id_user'])."\",\"".$row['username']."\",\"".$row['nama']."\",\"".$row['id_profil']."\",\"".$row['id_unit']."\",\"".$id_organisasi."\");'><img src='img/cross-icon.png' title='Hapus' id='hapus'></a>";
			$i++;
		}
	
	} else {
		$status = FALSE;
		$data = array();
	}	
	//$return = array($status, $data, $totalCount, $filterCount);
//	return $return;
	

	$datax['draw'] = intval($draw);
	$datax['recordsTotal'] = intval($totalCount);
	$datax['recordsFiltered'] = intval($filterCount);	
	$datax['data'] = $data;

	echo json_encode($datax);
}



function input_data(){
	
	global $conn;
	
	if ($_POST['username']==''){
		$msg .= "Username  harus diisi<br>";
	}
	if ($_POST['pass']==''){
		$msg .= "Password  harus diisi<br>";
	}
	if ($_POST['nama']==''){
		$msg .= "Nama harus diisi<br>";
	}
	if ($_POST['profil']==''){
		$msg .= "profil  harus diisi<br>";
	}
	if ($_POST['organisasi']==''){
		$msg .= "organisasi  harus diisi<br>";
	}
	if ($_POST['unit']==''){
		$msg .= "unit  harus diisi<br>";
	}
	if ($msg==''){
		$qry = mysqli_query($conn,"insert into user_app (username, nama, pass, id_profil, id_unit, id_organisasi, tgl_input) 
						values ('".$_POST['username']."','".$_POST['nama']."', '".md5(md5($_POST['pass']))."','".$_POST['profil']."',  
						'".$_POST['unit']."','".$_POST['organisasi']."',now()) ");
		$id_user = mysqli_insert_id($conn);
		
		$org_arr = explode(",",$_POST['organisasi']);
		for ($i=0;$i<count($org_arr);$i++) {
			$qry_org = mysqli_query($conn,"insert into user_app_organisasi (id_user, id_organisasi, tgl_input) 
								values ('".$id_user."', '".$org_arr[$i]."',now()) ");
		}
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=user'</script>";*/
}
	
function edit_data(){
	
	global $conn;
	
	if ($_POST['username']==''){
		$msg .= "Username  harus diisi<br>";
	}
	if ($_POST['nama']==''){
		$msg .= "Nama harus diisi<br>";
	}
	if ($_POST['profil']==''){
		$msg .= "profil  harus diisi<br>";
	}
	if ($_POST['organisasi']==''){
		$msg .= "organisasi  harus diisi<br>";
	}
	if ($_POST['unit']==''){
		$msg .= "unit  harus diisi<br>";
	}
	if ($msg==''){
		$id = dekripsi($_POST['id']);
		if (!empty($_POST['pass'])) {
			$edit_pass = " pass = '".md5(md5($_POST['pass']))."', ";
		} else {
			$edit_pass = "";
		}
		$qry = mysqli_query($conn,"update user_app set username = '".$_POST['username']."',nama = '".$_POST['nama']."', 
							id_profil = '".$_POST['profil']."', id_unit = '".$_POST['unit']."',  
							$edit_pass tgl_input = now()
							where id_user ='".$id."'");
							
		$qry_org = mysqli_query($conn,"delete from user_app_organisasi where id_user= '".$id."'");
													
		$org_arr = explode(",",$_POST['organisasi']);
		for ($i=0;$i<count($org_arr);$i++) {
			$qry_org = mysqli_query($conn,"insert into user_app_organisasi (id_user, id_organisasi, tgl_input) 
								values ('".$id."', '".$org_arr[$i]."',now()) ");
		}
		if($qry){
			$msg .="Sukses edit data";
		}else{
			$msg .="Gagal edit data";
		}
		
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=user'</script>";*/
	
}


function delete_data(){
	global $conn;

	$qry_org = mysqli_query($conn,"delete from user_app_organisasi where id_user= '".dekripsi($_REQUEST['id'])."'");
	$qry = mysqli_query($conn,"delete from user_app where id_user = '".dekripsi($_REQUEST['id'])."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=user'</script>";*/
}


?>