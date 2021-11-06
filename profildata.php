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
		$orderCol = " nama_profil";
	}
	
	if (!empty($searchVal)) {
		$where = " and (nama_profil like '%".$searchVal."%') ";
	}
	
	$stmt = "select id_profil, nama_profil, menu_id
			from user_profil 
				where id_profil is not null ".$where."
				ORDER BY $orderCol $orderDir
				limit $start, $limit";
			
	$stmt_count = "select id_profil from user_profil 
				where id_profil is not null ".$where."";
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
			$menu = "";
			$menu_id = $row['menu_id'];
			$menu_id = substr($menu_id, 0, strlen($menu_id)-1);
			$qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu
											where is_active = '1'  and id_menu in ($menu_id)
											and parent = 0
											order by parent, urutan");
			
			while ($dt_pil = mysqli_fetch_array($qry_pil)) {
				$menu .= $dt_pil['nama_menu'];
				
				$qry_pil2 = mysqli_query($conn,"select id_menu, nama_menu from user_menu
											where is_active = '1'  and id_menu in ($menu_id)
											and parent = '".$dt_pil['id_menu']."'
											order by parent, urutan");
				$menu2 = "";
				while ($dt_pil2 = mysqli_fetch_array($qry_pil2)) {
					$menu2 .= $dt_pil2['nama_menu'].",";
				}
				if (!empty($menu2)) {
					$menu2 = substr($menu2, 0, strlen($menu2)-1);
					$menu .= "(".$menu2."), ";				
				} else{
					$menu .= ", ";
				}
			
			}
			
			$data[$i]["nama_profil"] = $row['nama_profil'];
			$data[$i]["menu"] = $menu;
			#$data[$i]["action"] = "<a href='?fl=user&tp=edit&id=".enkripsi($row['id_user'])."'><img src='img/pencil-icon.png' title='Ubah'></a>&nbsp;<a href='?fl=user&tp=delete&id=".enkripsi($row['id_user'])."'><img src='img/cross-icon.png' title='Hapus'></a>";
			$data[$i]["action"] = "<a href='#'  onClick='editdata(\"".enkripsi($row['id_profil'])."\",\"".$row['nama_profil']."\",\"".$menu_id."\");'><img src='img/pencil-icon.png' title='Ubah' id='edit'></a>&nbsp;&nbsp;&nbsp;<a href='#' onClick='hapusdata(\"".enkripsi($row['id_profil'])."\",\"".$row['nama_profil']."\",\"".$menu_id."\");'><img src='img/cross-icon.png' title='Hapus' id='hapus'></a>";
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
		$msg .= "Username  harus diisi<br>";
	}
	
	if ($msg==''){
		$qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu where is_active = '1' order by parent, urutan");
																
		$menu_id = "";
		while ($dt_pil = mysqli_fetch_array($qry_pil)) {
			$i = $dt_pil['id_menu'];
			if (!empty($_POST['menu_'.$i])) {
				$menu_id .= $_POST['menu_'.$i].",";
			}
		}
		//$menu_id = substr($menu_id, 0, strlen($menu_id)-1);
		
		$qry = mysqli_query($conn,"insert into user_profil (nama_profil, menu_id) 
						values ('".$_POST['nama']."','".$menu_id."')");		
		$id = mysqli_insert_id($conn);
		
		$qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu where is_active = '1' order by parent, urutan");
		while ($dt_pil = mysqli_fetch_array($qry_pil)) {
			$i = $dt_pil['id_menu'];				
			if (!empty($_POST['menu_'.$i])) {
				if (!empty($_POST['menuinput_'.$i])) {
					$input = 1;
				} else {
					$input = 0;
				}
				if (!empty($_POST['menuedit_'.$i])) {
					$edit = 1;
				} else {
					$edit = 0;
				}
				if (!empty($_POST['menudelete_'.$i])) {
					$delete = 1;
				} else {
					$delete = 0;
				}
				if (!empty($_POST['menuview_'.$i])) {
					$view = 1;
				} else {
					$view = 0;
				}
				$qry_menu = mysqli_query($conn,"insert into user_profil_menu (id_profil, id_menu, is_input, is_edit, is_delete, is_view) 
						values ('".$id."','".$_POST['menu_'.$i]."', '".$input."','".$edit."','".$delete."','".$view."')");		
			}
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
	
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($msg==''){
		$id = dekripsi($_POST['id']);
		$jml = $_POST['jml'];
		
		$qry2 = mysqli_query($conn,"delete from user_profil_menu where id_profil = '".$id."'");
	
		$menu_id = "";
		$qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu where is_active = '1' order by parent, urutan");
		while ($dt_pil = mysqli_fetch_array($qry_pil)) {
			$i = $dt_pil['id_menu'];	
			if (!empty($_POST['menu_'.$i])) {
				$menu_id .= $_POST['menu_'.$i].",";
				if (!empty($_POST['menuinput_'.$i])) {
					$input = 1;
				} else {
					$input = 0;
				}
				if (!empty($_POST['menuedit_'.$i])) {
					$edit = 1;
				} else {
					$edit = 0;
				}
				if (!empty($_POST['menudelete_'.$i])) {
					$delete = 1;
				} else {
					$delete = 0;
				}
				if (!empty($_POST['menuview_'.$i])) {
					$view = 1;
				} else {
					$view = 0;
				}
				$qry_menu = mysqli_query($conn,"insert into user_profil_menu (id_profil, id_menu, is_input, is_edit, is_delete, is_view) 
						values ('".$id."','".$_POST['menu_'.$i]."', '".$input."','".$edit."','".$delete."','".$view."')");	
			}
		}
		//$menu_id = substr($menu_id, 0, strlen($menu_id)-1);
		
		$qry = mysqli_query($conn,"update user_profil set nama_profil = '".$_POST['nama']."',  menu_id  = '".$menu_id."'
							where id_profil = '".$id."'");
		
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

	
	$qry2 = mysqli_query($conn,"delete from user_profil_menu where id_profil = '".dekripsi($_REQUEST['id'])."'");
	$qry = mysqli_query($conn,"delete from user_profil where id_profil = '".dekripsi($_REQUEST['id'])."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=user'</script>";*/
}


?>