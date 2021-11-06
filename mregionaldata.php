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
		0 =>'nama_regional');
		
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
		$orderCol = " nama_regional";
	}
	
	if (!empty($searchVal)) {
		$where = " and (nama_regional like '%".$searchVal."%') ";
	}
	
	$stmt = "select a.id_regional, a.nama_regional, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input
			from p_regional a
			left join user_app b on a.id_user_input = b.id_user
				where a.id_regional is not null ".$where."
				ORDER BY $orderCol $orderDir
				limit $start, $limit";
			
	$stmt_count = "select id_regional from p_regional 
				where id_regional is not null ".$where."";
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
				$data[$i]["nama_regional"] = $row['nama_regional'];
				$data[$i]["nama"] = $row['nama'];
				$data[$i]["tgl_input"] = $row['tgl_input'];
				$data[$i]["action"] = "<a href='#'  onClick='editdata(\"".enkripsi($row['id_regional'])."\",\"".$row['nama_regional']."\");'><img src='img/pencil-icon.png' title='Ubah' id='edit'></a>&nbsp;&nbsp;&nbsp;<a href='#' onClick='hapusdata(\"".enkripsi($row['id_regional'])."\",\"".$row['nama_regional']."\");'><img src='img/cross-icon.png' title='Hapus' id='hapus'></a>";
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
	
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($msg==''){	
		$stm = "insert into p_regional (nama_regional, tgl_input, id_user_input, ip_input) 
					values ('".$_POST['nama']."',now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
		$qry = mysqli_query($conn,$stm);
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	echo $msg;
	/*
	echo "<script>alert('".$msg."');window.location.href='index.php?fl=mregional'</script>";
	*/
}
	
function edit_data(){	
	global $conn;
	
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($msg==''){
		$id = dekripsi($_POST['id']);	
		
		$qry = mysqli_query($conn,"update p_regional set nama_regional = '".$_POST['nama']."',ip_input = '".$_SERVER['REMOTE_ADDR']."',
									tgl_input = now(), id_user_input = '".$_SESSION['km_user']."'
								where id_regional ='".$id."'");
			
		if($qry){
			$msg .="Sukses edit data";
		}else{
			$msg .="Gagal edit data";
		}
		
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=mregional'</script>";
	*/
}


function delete_data(){
	global $conn;
	
	$qry = mysqli_query($conn,"delete from p_regional where id_regional = '".dekripsi($_REQUEST['id'])."'");
	if($qry){
		$msg = "sukses hapus data";
	}else{
		$msg = "gagal hapus data";
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=mregional'</script>";
	*/
}


?>