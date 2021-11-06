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



function child($id_bsc_perspective, $parent, $i, $id_organisasi, $tahun){
	global $conn;
	
	$stmt = "select a.id_kpi, a.nama_kpi, a.satuan, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi,d.tahun, e.nama_organisasi
			from kpi a
			inner join kpi_organisasi d on a.id_kpi = d.id_kpi
			left join user_app b on d.id_user_input = b.id_user
			left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
			left join p_organisasi e on d.id_organisasi = e.id_organisasi
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				and d.id_organisasi = '".$id_organisasi."' and d.tahun = '".$tahun."'
				order by a.parent, a.urutan";
	
	$query = mysqli_query($conn,$stmt);
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
		?>
		<tr>
			<td><?php echo $i.".".$j; ?></td>
			<td><?php echo $row['nama_kpi']; ?></td>
			<td align="center"><?php echo $row['satuan']; ?></td>
			<td align="center"><a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\", \"".$row['nama_kpi']."\", \"".enkripsi($row['id_organisasi'])."\",\"".$row['nama_organisasi']."\", \"".$row['tahun']."\");'";  ?>><img src='img/cross-icon.png' title='Hapus'></a></td>
		</tr>           
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$row['id_organisasi'], $tahun);
		$j++;
	}
	
	
}

function list_data(){
	global $conn;
	
?>
	
	<script>
		  $(document).ready(function(){
			var dtTable = $('#datable_1').DataTable();
			dtTable.destroy();
			
			$('#datable_1').DataTable({
				"bLengthChange": false,
				"bFilter": false,
				"paging": false,
				"scrollY": "300px",
				 "scrollCollapse": true,
				//"scrollY": true,
				"scrollX": true,
				"fixedColumns": {
					"leftColumns": 2
				}
			});
			
		});
		
	</script>
    
    <br />
   	<table id="datable_1"  class="table table-bordered table-striped table-sm w-100">
         <thead class="thead-primary">
            <tr align="center">
            	<th><b>No</b></th>
                <th><b>Nama KPI</b></th>
                <th><b>Satuan</b></th>
                <th><b>Action</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
			
			$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective
											from p_bsc_perspective ");
			$i = 1;
			while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {			
				
				?>
                <tr bgcolor="#CCCCCC">
					<td><b><?php echo $i; ?></b></td>
                    <td><b><?php echo $row_bsc['nama_bsc_perspective']; ?></b></td>
	                <td></td>
                    <td align="center"></td>
                </tr>
                        
               <?php
			   
			   $tahun = $_REQUEST['tahun'];
			   
			   $stmt = "select a.id_kpi, a.nama_kpi, a.satuan, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi,d.tahun, e.nama_organisasi
						from kpi a
						inner join kpi_organisasi d on a.id_kpi = d.id_kpi
						left join user_app b on d.id_user_input = b.id_user
						left join p_organisasi e on d.id_organisasi = e.id_organisasi
						where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."'
						and d.id_organisasi = '".$_REQUEST['organisasi']."' and d.tahun = '".$tahun."' and a.parent = '0'
						order by a.parent, a.urutan";
				//echo "<pre>$stmt</pre>";
				$query = mysqli_query($conn,$stmt);
				
				$j = 1;
				while ($row = mysqli_fetch_array($query)){
					?>
					<tr bgcolor="EFEFEF">
						<td><?php echo $i.".".$j; ?></td>
						<td><?php echo $row['nama_kpi']; ?></td>
						<td align="center"><?php echo $row['satuan']; ?></td>
						<td align="center"><a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\", \"".$row['nama_kpi']."\", \"".enkripsi($row['id_organisasi'])."\", \"".$row['nama_organisasi']."\", \"".$row['tahun']."\");'";  ?>><img src='img/cross-icon.png' title='Hapus'></a></td>
					</tr>
				<?php
					child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $row['id_organisasi'],$tahun);
						
					$j++;
				}
			   
			   
				$i++;
			}
			?>
        </tbody>
    </table>
                        
<?php   
}
     

function input_data(){	
	global $conn;
	
	if ($_POST['group']==''){
		$msg .= "Group Organisasi  harus diisi<br>";
	}
	if ($_POST['tahun']==''){
		$msg .= "Tahun  harus diisi<br>";
	}
	if ($msg==''){	
		//$id_organisasi = $_POST['organisasi'];
		
		$stmt_org = "select a.id_organisasi
				from p_organisasi a 
				where a.id_group = '".$_POST['group']."'
				order by a.id_organisasi";
		
		$query_org = mysqli_query($conn,$stmt_org);
		while ($row_org = mysqli_fetch_array($query_org)){	
			$id_organisasi = $row_org['id_organisasi'];
			
			$stmt_del = mysqli_query($conn, "delete from kpi_organisasi where id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."'");
			
			$stmt = "select a.id_kpi, a.terbalik
					from kpi a
					order by a.parent, a.urutan";
			$query = mysqli_query($conn,$stmt);
			
			$j = 1;
			while ($row = mysqli_fetch_array($query)){	
				$id_kpi = $row['id_kpi'];	
				if ($_POST['kpi_'.$id_kpi]==1) {
					$qry_kpi = mysqli_query($conn,"select rumus from kpi where id_kpi = '".$id_kpi."'");
					$dt_kpi = mysqli_fetch_array($qry_kpi);
					$stm = "insert into kpi_organisasi (id_kpi, id_organisasi, terbalik,  tahun, tgl_input, id_user_input, ip_input) 
							values ('".$id_kpi."', '".$id_organisasi."', '".$row['terbalik']."', '".$_REQUEST['tahun']."',  
							now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
					$qry = mysqli_query($conn,$stm);
				}
			}
		}
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	
	echo $msg;
	
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpiorganisasi'</script>";
	*/
}

/*
function edit_data(){	
	global $conn;
	
	if ($_POST['organisasi']==''){
		$msg .= "Organisasi  harus diisi<br>";
	}
	if ($_POST['tahun']==''){
		$msg .= "Tahun  harus diisi<br>";
	}
	if ($msg==''){	
		$id = dekripsi($_POST['id']);	
		$idg = dekripsi($_POST['idg']);	
		
		$stm = "update kpi_organisasi set rumus = '".$_REQUEST['rumus']."', tgl_input = now(), id_user_input = '".$_SESSION['km_user']."', 
				ip_input = '".$_SERVER['REMOTE_ADDR']."'
				where id_kpi = '".$id."' and id_organisasi = '".$idg."' and tahun = '".$_REQUEST['tahun']."'";
		$qry = mysqli_query($conn,$stm);
	
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	echo $msg;
	
}
*/	
	
	
function delete_data(){
	global $conn;	
	
	$qry = mysqli_query($conn,"delete from kpi_organisasi 
								where id_kpi = '".dekripsi($_REQUEST['id'])."' and id_organisasi = '".dekripsi($_REQUEST['organisasi'])."' 
								and tahun = '".$_REQUEST['tahun']."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
}


?>