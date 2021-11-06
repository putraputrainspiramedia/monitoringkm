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
		0 =>'nama',
		1 =>'nama_jenis_service');
		
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
		$orderCol = " nama_organisasi";
	}
	
	if (!empty($searchVal)) {
		$where = " and (nama_organisasi like '%".$searchVal."%' or nama_regional like '%".$searchVal."%' or nama_group like '%".$searchVal."%') ";
	}
	
	$stmt = "select a.id_organisasi, a.nama_organisasi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, c.nama_regional, a.id_regional, d.nama_group,
			d.id_group
			from p_organisasi a
			left join user_app b on a.id_user_input = b.id_user
			left join p_regional c on a.id_regional = c.id_regional
			left join p_group d on a.id_group = d.id_group
				where a.id_organisasi is not null ".$where."
				ORDER BY $orderCol $orderDir
				limit $start, $limit";
			
	$stmt_count = "select id_organisasi from p_organisasi 
				where id_organisasi is not null ".$where."";
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
				$data[$i]["nama_group"] = $row['nama_group'];
				$data[$i]["nama_organisasi"] = $row['nama_organisasi'];
				$data[$i]["nama"] = $row['nama'];
				$data[$i]["tgl_input"] = $row['tgl_input'];
				//$data[$i]["action"] = "<a href='?fl=morganisasi&tp=edit&id=".enkripsi($row['id_organisasi'])."'><img src='img/pencil-icon.png' title='Ubah'></a>&nbsp;&nbsp;&nbsp;<a href='?fl=morganisasi&tp=delete&id=".enkripsi($row['id_organisasi'])."'><img src='img/cross-icon.png' title='Hapus'></a>";
				
				$data[$i]["action"] = "<a href='#'  onClick='editdata(\"".enkripsi($row['id_organisasi'])."\",\"".$row['nama_organisasi']."\",\"".$row['id_regional']."\",\"".$row['id_group']."\");'><img src='img/pencil-icon.png' title='Ubah' id='edit'></a>&nbsp;&nbsp;&nbsp;<a href='#' onClick='hapusdata(\"".enkripsi($row['id_organisasi'])."\",\"".$row['nama_organisasi']."\",\"".$row['id_regional']."\",\"".$row['id_group']."\");'><img src='img/cross-icon.png' title='Hapus' id='hapus'></a>";
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
	if ($_POST['regional']==''){
		$msg .= "Regional  harus diisi<br>";
	}
	if ($_POST['group']==''){
		$msg .= "Group  harus diisi<br>";
	}
	if ($msg==''){	
		$stm = "insert into p_organisasi (id_regional, id_group, nama_organisasi, tgl_input, id_user_input, ip_input) 
					values ('".$_POST['regional']."', '".$_POST['group']."', '".$_POST['nama']."',now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
		$qry = mysqli_query($conn,$stm);
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=morganisasi'</script>";*/
}
	
function edit_data(){	
	global $conn;
	
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($_POST['regional']==''){
		$msg .= "Regional  harus diisi<br>";
	}
	if ($_POST['group']==''){
		$msg .= "Group  harus diisi<br>";
	}
	if ($msg==''){
		$id = dekripsi($_POST['id']);	
		
		$qry = mysqli_query($conn,"update p_organisasi set nama_organisasi = '".$_POST['nama']."', id_regional = '".$_POST['nama']."', 
									ip_input = '".$_SERVER['REMOTE_ADDR']."', id_group = '".$_POST['group']."', 
									tgl_input = now(), id_user_input = '".$_SESSION['km_user']."'
								where id_organisasi ='".$id."'");
			
		if($qry){
			$msg .="Sukses edit data";
		}else{
			$msg .="Gagal edit data";
		}
		
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=morganisasi'</script>";
	*/
}


function delete_data(){
	global $conn;
	
	$qry = mysqli_query($conn,"delete from p_organisasi where id_organisasi = '".dekripsi($_REQUEST['id'])."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=morganisasi'</script>";*/
}


?>