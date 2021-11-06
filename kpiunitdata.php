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
	case 'kpi':
		if ($_SESSION['km_user_view']==1) {
			kpi_data();
		}
		break;	
	default :
		if ($_SESSION['km_user_view']==1) {
			list_data();
		}
		break;
}



function child($id_bsc_perspective, $parent, $i, $id_unit){
	global $conn;
	
	$stmt = "select a.id_kpi, a.nama_kpi, a.satuan, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_unit, e.nama_unit
			from kpi a
			inner join kpi_unit d on a.id_kpi = d.id_kpi
			left join user_app b on d.id_user_input = b.id_user
			left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
			left join p_unit e on d.id_unit = e.id_unit
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				and d.id_unit = '".$id_unit."' 
				order by a.parent, a.urutan";
	
	$query = mysqli_query($conn,$stmt);
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
		?>
		<tr>
			<td><?php echo $i.".".$j; ?></td>
			<td><?php echo $row['nama_kpi']; ?></td>
			<td align="center"><?php echo $row['satuan']; ?></td>
			<td align="center"><a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\", \"".$row['nama_kpi']."\", \"".enkripsi($row['id_unit'])."\",\"".$row['nama_unit']."\");'";  ?>><img src='img/cross-icon.png' title='Hapus'></a></td>
		</tr>           
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$row['id_unit'], $tahun);
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
			   
			   $stmt = "select a.id_kpi, a.nama_kpi, a.satuan, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_unit, e.nama_unit
						from kpi a
						inner join kpi_unit d on a.id_kpi = d.id_kpi
						left join user_app b on d.id_user_input = b.id_user
						left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
						left join p_unit e on d.id_unit = e.id_unit
						where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."'
						and d.id_unit = '".$_REQUEST['unit']."' and a.parent = '0'
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
						<td align="center"><a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\", \"".$row['nama_kpi']."\", \"".enkripsi($row['id_unit'])."\", \"".$row['nama_unit']."\");'";  ?>><img src='img/cross-icon.png' title='Hapus'></a></td>
					</tr>
				<?php
					child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $row['id_unit']);
						
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
	
	if ($_POST['unit']==''){
		$msg .= "Unit harus diisi<br>";
	}
	if ($msg==''){	
		//$id_organisasi = $_POST['organisasi'];
		
		$id_unit = $_POST['unit'];		
		$stmt_del = mysqli_query($conn, "delete from kpi_unit where id_unit = '".$id_unit."'");
		
		$stmt = "select a.id_kpi, a.terbalik
				from kpi a
				order by a.parent, a.urutan";
		$query = mysqli_query($conn,$stmt);
		
		$j = 1;
		while ($row = mysqli_fetch_array($query)){	
			$id_kpi = $row['id_kpi'];	
			if ($_POST['kpi_'.$id_kpi]==1) {
				$stm = "insert into kpi_unit (id_kpi, id_unit, tgl_input, id_user_input, ip_input) 
						values ('".$id_kpi."', '".$id_unit."', now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
				$qry = mysqli_query($conn,$stm);
			}
		}
	
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	
	echo $msg;
	
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpiunit'</script>";
	*/
}

	
function delete_data(){
	global $conn;	
	
	$qry = mysqli_query($conn,"delete from kpi_unit 
								where id_kpi = '".dekripsi($_REQUEST['id'])."' and id_unit = '".dekripsi($_REQUEST['unit'])."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
}


?>