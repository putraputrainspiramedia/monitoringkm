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
		kpi_data();
		break;	
	default :
		if ($_SESSION['km_user_view']==1) {
			list_data();
		}
		break;
}


function kpi_data() {
	global $conn;
	
	echo "<option value='0'>Tidak Ada</option>";
	$qry = mysqli_query($conn,"select id_kpi, nama_kpi from kpi where id_bsc_perspective = '".$_REQUEST['perspective']."' 
							order by id_bsc_perspective, parent, nama_kpi");
	while ($data = mysqli_fetch_array($qry)) {
		echo "<option value='".$data['id_kpi']."'>".$data['nama_kpi']."</option>";
	}
	
}


function child($id_bsc_perspective, $parent, $i, $menu_arr){
	global $conn;
	
	// realisasi 0 = input, 1 = rumus score/bobot, 2 = rata2, 3 = penjumlahan
	// score 0 = rumus, 1 = jumlah
	
	$stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, a.rumus, a.parent, a.urutan, a.satuan,
			a.id_bsc_perspective, c.nama_bsc_perspective, case a.terbalik when '1' then 'Ya' else 'Tidak' end terbalik2, a.terbalik,
			case a.realisasi when '1' then 'Score/Bobot' when '2' then 'Rata2 dibawahnya' when '3' then 'Penjumlahan dibawahnya' else 'Inputan' end realisasi2, 
			a.realisasi,  case a.score when '1' then 'Penjumlahan dibawahnya' else 'Cap/Bobot' end score2, a.score,
				case a.status when '1' then 'Aktif' else 'Tidak Aktif' end status2,  a.status
			from kpi a
			left join user_app b on a.id_user_input = b.id_user
			left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				order by a.parent, a.urutan";
	
	$query = mysqli_query($conn,$stmt);
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
		?>
		<tr>
			<td><?php echo $i.".".$j; ?></td>
			<td><?php echo $row['nama_kpi']; ?></td>
			<td align="center"><?php echo $row['satuan']; ?></td>
			<td align="center"><?php echo $row['terbalik2']; ?></td>
			<td align="center"><?php echo $row['realisasi2']; ?></td>
			<td align="center"><?php echo $row['score2']; ?></td>
			<td align="center"><?php echo $row['urutan']; ?></td>
			<td align="center"><?php echo $row['status2']; ?></td>
			<!--<td align="center"><?php echo $row['nama']; ?></td>
			<td align="center"><?php echo $row['tgl_input']; ?></td>-->
             <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1 or $_SESSION['km_user_delete']==1) { ?>
			<td align="center">
            <?php if ($_SESSION['km_user_input']==1) { ?>
            <a href='#' <?php echo "onClick='inputdata(\"".$id_bsc_perspective."\",\"".$row['id_kpi']."\");'"; ?>><img src='img/add.png' title='Tambah'></a>&nbsp;&nbsp;&nbsp;<?php } ?>
            <?php if ($_SESSION['km_user_edit']==1) { ?>
             <a href='#' <?php echo "onClick='editdata(\"".enkripsi($row['id_kpi'])."\", \"".$id_bsc_perspective."\",\"".$row['parent']."\", \"".$row['nama_kpi']."\", \"".$row['terbalik']."\", \"".$row['urutan']."\", \"".$row['satuan']."\", \"".$row['realisasi']."\", \"".$row['score']."\", \"".$row['status']."\");'"; ?>><img src='img/pencil-icon.png' title='Ubah'></a>&nbsp;&nbsp;&nbsp;
<?php } ?>
		<?php if ($_SESSION['km_user_delete']==1) { ?>
             <a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\", \"".$id_bsc_perspective."\",\"".$row['parent']."\",\"".$row['nama_kpi']."\", \"".$row['terbalik']."\", \"".$row['urutan']."\", \"".$row['satuan']."\", \"".$row['realisasi']."\", \"".$row['score']."\", \"".$row['status']."\");'"; ?>><img src='img/cross-icon.png' title='Hapus'></a></td>
			<?php } ?>
		</tr>    
        	<?php } ?>       
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $menu_arr);
		$j++;
	}
	
	
}

function list_data(){
	global $conn;
	
	
	$menu_arr = cek_priuser($_REQUEST['fl']);
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
      <style>
		td {
			  white-space: normal !important; 
			  word-wrap: break-word;  
			}
			table {
			  table-layout: fixed;
			}
	</style>
    <br />
   	<table id="datable_1"  class="table table-bordered table-striped table-sm w-100">
         <thead class="thead-primary">
            <tr align="center">
            	<th width="5%"><b>No</b></th>
                <th><b>Nama KPI</b></th>
                <th width="10%"><b>Unit</b></th>
                <th width="10%"><b>Formula <br />Terbalik</b></th>
                <th width="10%"><b>Realisasi</b></th>
                <th width="10%"><b>Score</b></th>
                <th width="5%"><b>Urutan</b></th>
                <th width="5%"><b>Status</b></th>
               <!-- <th width="10%"><b>User Input</b></th>
                <th width="10%"><b>Tgl Input</b></th>-->
                <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1 or $_SESSION['km_user_delete']==1) { ?>
                <th width="10%"><b>Action</b></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
			$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective
											from p_bsc_perspective ");
			$i = 1;
			while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {			
				
				?>
                <tr>
					<td><b><?php echo $i; ?></b></td>
                    <td><b><?php echo $row_bsc['nama_bsc_perspective']; ?></b></td>
                    <td></td>
	                <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                   <!-- <td></td>
                    <td></td>-->
                      <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1 or $_SESSION['km_user_delete']==1) { ?>
                    <td align="center"><a href='#' <?php echo "onClick='inputdata(\"".$row_bsc['id_bsc_perspective']."\",0);'"; ?>><img src='img/add.png' title='Tambah'></a></td>
                    <?php } ?>
                </tr>
                        
               <?php
			   // realisasi 0 = input, 1 = rumus score/bobot, 2 = rata2, 3 = penjumlahan
	// score 0 = rumus, 1 = jumlah
	
			  $stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, a.satuan,
			   			IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, 
						case a.terbalik when '1' then 'Ya' else 'Tidak' end terbalik2, a.terbalik,
					case a.realisasi when '1' then 'Score/Bobot' when '2' then 'Rata2 dibawahnya' when '3' then 'Penjumlahan dibawahnya' else 'Inputan' end realisasi2, 
					a.realisasi,  case a.score when '1' then 'Penjumlahan dibawahnya' else 'Cap/Bobot' end score2, a.score,
					case a.status when '1' then 'Aktif' else 'Tidak Aktif' end status2,  a.status
						from kpi a
						left join user_app b on a.id_user_input = b.id_user
							where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
							order by a.parent, a.urutan";
				
				$query = mysqli_query($conn,$stmt);
				
				$j = 1;
				while ($row = mysqli_fetch_array($query)){
					?>
					<tr>
						<td><?php echo $i.".".$j; ?></td>
						<td><?php echo $row['nama_kpi']; ?></td>
						<td align="center"><?php echo $row['satuan']; ?></td>
						<td align="center"><?php echo $row['terbalik2']; ?></td>
                        <td align="center"><?php echo $row['realisasi2']; ?></td>
                        <td align="center"><?php echo $row['score2']; ?></td>
						<td align="center"><?php echo $row['urutan']; ?></td>
						<td align="center"><?php echo $row['status2']; ?></td>
						<!--<td align="center"><?php echo $row['nama']; ?></td>
						<td align="center"><?php echo $row['tgl_input']; ?></td>-->
                          <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1 or $_SESSION['km_user_delete']==1) { ?>
						<td align="center">
							<?php if ($_SESSION['km_user_input']==1) { ?>
                                <a href='#' <?php echo "onClick='inputdata(\"".$row_bsc['id_bsc_perspective']."\",\"".$row['id_kpi']."\");'"; ?>>
                                <img src='img/add.png' title='Tambah'></a>&nbsp;&nbsp;&nbsp;
                            <?php } ?>
                              <?php if ($_SESSION['km_user_edit']==1) { ?>
                              <a href='#' <?php echo "onClick='editdata(\"".enkripsi($row['id_kpi'])."\", \"".$row_bsc['id_bsc_perspective']."\",\"".$row['parent']."\", \"".$row['nama_kpi']."\", \"".$row['terbalik']."\", \"".$row['urutan']."\", \"".$row['satuan']."\", \"".$row['realisasi']."\", \"".$row['score']."\", \"".$row['status']."\");'"; ?>><img src='img/pencil-icon.png' title='Ubah'></a>&nbsp;&nbsp;&nbsp;
                              <?php } ?>
                               <?php if ($_SESSION['km_user_delete']==1) { ?>
                        			<a href='#' <?php echo "onClick='hapusdata(\"".enkripsi($row['id_kpi'])."\",\"".$row_bsc['id_bsc_perspective']."\",\"".$row['parent']."\", \"".$row['nama_kpi']."\", \"".$row['terbalik']."\", \"".$row['urutan']."\", \"".$row['satuan']."\", \"".$row['realisasi']."\", \"".$row['score']."\", \"".$row['status']."\");'"; ?>><img src='img/cross-icon.png' title='Hapus'></a>
                        		<?php } ?>
                        </td>
                        <?php } ?>
					</tr>
				<?php
					//child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $menu_arr);
						
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
	
	if ($_POST['perspective']==''){
		$msg .= "Perspective  harus diisi<br>";
	}
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($_POST['terbalik']==''){
		$msg .= "Rumus terbalik  harus diisi<br>";
	}
	if ($_POST['satuan']==''){
		$msg .= "Unit  harus diisi<br>";
	}
	if ($msg==''){	
		$stm = "insert into kpi (id_bsc_perspective, parent, nama_kpi, satuan, terbalik, urutan, realisasi, score, status,  tgl_input, id_user_input, ip_input) 
					values ('".$_POST['perspective']."', '".$_POST['parent']."', '".$_POST['nama']."', '".$_POST['satuan']."', '".$_POST['terbalik']."', 
					'".$_POST['urutan']."', '".$_POST['realisasi']."','".$_POST['score']."', '".$_POST['status']."', now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
		$qry = mysqli_query($conn,$stm);
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	
	echo $msg;
	/*
	echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
}
	
	
function edit_data(){	
	global $conn;
	
	if ($_POST['perspective']==''){
		$msg .= "Perspective  harus diisi<br>";
	}
	if ($_POST['nama']==''){
		$msg .= "Nama  harus diisi<br>";
	}
	if ($_POST['terbalik']==''){
		$msg .= "Rumus terbalik  harus diisi<br>";
	}
	if ($_POST['satuan']==''){
		$msg .= "Unit  harus diisi<br>";
	}
	if ($msg==''){
		$id = dekripsi($_POST['id']);	
		
		$qry = mysqli_query($conn,"update kpi set nama_kpi = '".$_POST['nama']."', terbalik = '".$_POST['terbalik']."', 
									id_bsc_perspective = '".$_POST['perspective']."', parent =  '".$_POST['parent']."',
									ip_input = '".$_SERVER['REMOTE_ADDR']."', urutan = '".$_POST['urutan']."',  satuan = '".$_POST['satuan']."', 
									realisasi = '".$_POST['realisasi']."', score = '".$_POST['score']."', status = '".$_POST['status']."',
									tgl_input = now(), id_user_input = '".$_SESSION['km_user']."'
								where id_kpi ='".$id."'");
			
		if($qry){
			$msg .="Sukses edit data";
		}else{
			$msg .="Gagal edit data";
		}
		
	}
	
	echo $msg;/*
	echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
	
}


function delete_data(){
	global $conn;
	
	$qry = mysqli_query($conn,"delete from kpi where id_kpi = '".dekripsi($_REQUEST['id'])."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
}


?>