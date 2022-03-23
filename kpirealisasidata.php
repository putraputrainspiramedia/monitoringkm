<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");

switch($_REQUEST['tp']) {
	case 'inputlist':
		if ($_SESSION['km_user_input']==1) {
			inputlist_data();
		}
		break;
	case 'input':
		if ($_SESSION['km_user_input']==1) {
			inputform_data();
		}
		break;
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
	case 'reset':
		if ($_SESSION['km_user_delete']==1) {
			reset_data();
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
    
   	<table id="datable_1" class="table table-bordered table-striped table-sm w-100">
		<thead class="thead-primary">
            <tr align="center">
            	<th><b>No</b></th>
                <th><b>Nama KPI</b></th>
                <th><b>Satuan</b></th>
                <?php 
				$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
				$jml_periode = 0;
				$arr_periode = array();
				while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
					$arr_periode[$jml_periode] = $dt_periode['id_periode'];
					$jml_periode++; 
					?>
                	<th><b><?php echo $dt_periode['singk_periode'];?></b></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
			if (empty($_REQUEST['tahun'])) {
				$tahun = date("Y");
			} else {
				$tahun = $_REQUEST['tahun'];
			}
			$id_organisasi = $_REQUEST['organisasi'];
			$status = $_REQUEST['status'];
			$id_unit = $_REQUEST['unitx'];
			
			$userorg_arr = get_profiluserorg();
			setcookie("cookie_kmtelkom", $status, time()+3600); 
			
			$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, nilai
											from p_bsc_perspective ");
			$i = 1;
			$arr_bobotbsc = array();
			$bobot_perbsc = 0;
			while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {			
				
				?>
                <tr bgcolor="#CCCCCC">
					<td><b><?php echo $i; ?></b></td>
                    <td><b><?php echo $row_bsc['nama_bsc_perspective']; ?></b></td>
                    <td></td>
                    <?php for($ii=0;$ii<$jml_periode;$ii++) { ?>
                    	<td  align="center"><?php 
							//echo $bobot_bsc = bobotbsc($row_bsc['id_bsc_perspective'], $id_organisasi, $tahun, $arr_periode[$ii], $status);
							//echo $row_bsc['nilai'];
							//$arr_bobotbsc[$ii] = $bobot_bsc;
							?></td>
                    <?php } ?>         
                </tr>
                        
               <?php
			   
			   
			   /*$stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(d.tgl_input,'%d-%m-%Y') tgl_input, 
						IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, d.tahun, a.satuan,
						(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child
						from kpi a
						inner join kpi_organisasi d on a.id_kpi = d.id_kpi
						left join user_app b on d.id_user_input = b.id_user
						where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
						and d.id_organisasi = '".$id_organisasi."' and d.tahun = '".$tahun."' 
						order by a.parent, a.urutan";
				*/
				
				$stmt = "select a.id_kpi, a.nama_kpi, DATE_FORMAT(d.tgl_input,'%d-%m-%Y') tgl_input, 
						IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, d.tahun, a.satuan,
						(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child
						from kpi a
						inner join kpi_organisasi d on a.id_kpi = d.id_kpi						
						where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
						and d.id_organisasi = '".$id_organisasi."'  and d.tahun = '".$tahun."' 
						order by a.parent, a.urutan";
						
				//echo "<pre>$stmt</pre>";
				$query = mysqli_query($conn,$stmt);
				
				$j = 1;
				while ($row = mysqli_fetch_array($query)){
					?>
					<tr bgcolor="EFEFEF">
						<td><?php echo $i.".".$j; ?></td>
						<td><?php echo $row['nama_kpi']; ?></td>
						<td><?php echo $row['satuan']; ?></td>
                        <?php for($ii=0;$ii<$jml_periode;$ii++) { ?>
						<td align="center"><?php 
							//if ($row['jml_child']>0) {
							//	echo bobot($row['id_kpi'],$row['id_organisasi'],$tahun, $arr_periode[$ii], $status); 
							//} else {
								echo bobotdet($row['id_kpi'],$row['id_organisasi'],$tahun, $arr_periode[$ii], $status);
							//}
						?></td>
                        <?php } ?>
					</tr>
				<?php
					child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $row['id_organisasi'],$tahun, $jml_periode, $arr_periode, $status);
						
					$j++;
				}
			   
			   
				$i++;
			}
			?>
        </tbody>
       <!-- <tfoot>
        		<td></td>
                <td>Total</td>
                <td></td>
                <?php for($ii=0;$ii<$jml_periode;$ii++) { ?>
                <td align="center"><?php echo $arr_bobotbsc[$ii];?></td>
                <?php } ?>
        </tfoot>-->
    </table>
                        
<?php   
}

function child($id_bsc_perspective, $parent, $i, $id_organisasi, $tahun, $jml_periode, $arr_periode, $status){
	global $conn;
	
	 $stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(d.tgl_input,'%d-%m-%Y') tgl_input, 
						IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, d.tahun,	a.satuan,					
						(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child
						from kpi a
						inner join kpi_organisasi d on a.id_kpi = d.id_kpi
						left join user_app b on d.id_user_input = b.id_user
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
			<td><?php echo $row['satuan']; ?></td>
            <?php for($ii=0;$ii<$jml_periode;$ii++) { ?>
            <td align="center"><?php 
				//if ($row['jml_child']>0) {
				//	echo bobot($row['id_kpi'],$row['id_organisasi'],$tahun, $arr_periode[$ii], $status);
				//} else {
					echo bobotdet($row['id_kpi'],$row['id_organisasi'],$tahun, $arr_periode[$ii], $status);
				//} ?></td>
            <?php } ?>
		</tr>           
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$row['id_organisasi'], $tahun, $jml_periode, $arr_periode, $status);
		$j++;
	}
	
	
}
   
function bobotbsc($id_bsc_perspective, $id_organisasi, $tahun, $id_periode, $status){
    global $conn;
	
	$qry = mysqli_query($conn,"select sum(realisasi) jml
								from kpi_realisasi a
								inner join kpi b on a.id_kpi = b.id_kpi
								where b.id_bsc_perspective = '".$id_bsc_perspective."' and a.id_organisasi = '".$id_organisasi."' 
								and a.tahun = '".$tahun."' and a.id_periode = '".$id_periode."' and id_status = '".$status."'
								and id_kpi not in (select parent from kpi)");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['jml'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

function bobot($id_kpi, $id_organisasi, $tahun, $id_periode, $status){
    global $conn;
	
	$qry = mysqli_query($conn,"select sum(realisasi) jml
								from kpi_realisasi a
								inner join kpi b on a.id_kpi = b.id_kpi
								where b.parent = '".$id_kpi."' and a.id_organisasi = '".$id_organisasi."' 
								and a.tahun = '".$tahun."' and a.id_periode = '".$id_periode."'  and id_status = '".$status."'");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['jml'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

function bobotdet($id_kpi, $id_organisasi, $tahun, $id_periode, $status){
    global $conn;
	
	$qry = mysqli_query($conn,"select realisasi 
								from kpi_realisasi 
								where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
								and tahun = '".$tahun."' and id_periode = '".$id_periode."' and id_status = '".$status."' ");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['realisasi'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

/*
function input_data(){	
	global $conn;
	
	if ($_REQUEST['group']==''){
		$msg .= "Group Organisasi  harus diisi";
	}
	if ($_REQUEST['unit']==''){
		$msg .= "Unit  harus diisi";
	}
	if ($_REQUEST['tahun']==''){
		$msg .= "Tahun  harus diisi";
	}
	if ($msg==''){	
		
		if (!empty($_FILES['filex']['name'])) {		
			if ($_FILES['filex']['type'] == "application/csv" or $_FILES['filex']['type'] == "application/vnd.ms-excel" or $_FILES['filex']['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {	
				
				$maxsize = 10000000;
				if ($_FILES['filex']['size'] > $maxsize) {
					$msg .= "File : ".$_FILES['filex']['size']." kb. Your file over from 10 Mb. Please try again !!";					
				} else {		
					$file = $_FILES['filex']['name'];		
					$folder = "tmp/".$file;
					move_uploaded_file($_FILES['filex']['tmp_name'],$folder);
					
					if ($_FILES['filex']['type'] == "application/csv" or $_FILES['filex']['type'] == "application/vnd.ms-excel") {
						$jns_excel = "application/vnd.ms-excel"; 
						$type_excel = 'Excel5';
					}else {
						$jns_excel = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; 
						$type_excel = 'Excel2007';
					}
					
					
					$objReader = PHPExcel_IOFactory::createReader($type_excel);
					$objReader->setReadDataOnly(true);
					$objPHPExcel = $objReader->load($file);
					$objWorksheet = $objPHPExcel->getActiveSheet();
					$cell = array();
					
					$filename = $_FILES['filex']['name'];	
					$baris = 0;
					
					$stmt_del = mysqli_query($conn, "delete from kpi_realisasi 
													where id_organisasi = '".$_REQUEST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'
													and id_status = '".$_REQUEST['status']."' and id_unit = '".$_REQUEST['unit']."'");
		
					foreach ($objWorksheet->getRowIterator() as $row) {	
						$a=0;
						$i=0;
						$baris++;
						
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false);
						foreach ($cellIterator as $cell_raw) {
							//$cell[$i] = PHPExcel_Style_NumberFormat::toFormattedString($cell_raw->getValue(), 'MM/DD/YYYY');
							$cell[$i] = $cell_raw->getValue();
							$i++;
							
						}
						
						if	($baris>=2) {					
							$kpi	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[1]))));
							$kpi 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($kpi), "HTML-ENTITIES", 'UTF-8')));
							$jan	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[3]))));
							$jan 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($jan), "HTML-ENTITIES", 'UTF-8')));
							$feb	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[4]))));
							$feb 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($feb), "HTML-ENTITIES", 'UTF-8')));
							$mar	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[5]))));
							$mar 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($mar), "HTML-ENTITIES", 'UTF-8')));
							$apr	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[6]))));
							$apr 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($apr), "HTML-ENTITIES", 'UTF-8')));
							$mei	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[7]))));
							$mei 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($mei), "HTML-ENTITIES", 'UTF-8')));
							$jun	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[8]))));
							$jun 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($jun), "HTML-ENTITIES", 'UTF-8')));
							$jul	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[9]))));
							$jul 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($jul), "HTML-ENTITIES", 'UTF-8')));
							$ags	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[10]))));
							$ags 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($ags), "HTML-ENTITIES", 'UTF-8')));
							$sep	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[11]))));
							$sep 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($sep), "HTML-ENTITIES", 'UTF-8')));
							$okt	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[12]))));
							$okt 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($okt), "HTML-ENTITIES", 'UTF-8')));
							$nov	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[13]))));
							$nov 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($nov), "HTML-ENTITIES", 'UTF-8')));
							$des	= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[14]))));
							$des 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($des), "HTML-ENTITIES", 'UTF-8')));
							
							$q1		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[15]))));
							$q1 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($q1), "HTML-ENTITIES", 'UTF-8')));
							$q2		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[16]))));
							$q2 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($q2), "HTML-ENTITIES", 'UTF-8')));
							$q3		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[17]))));
							$q3 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($q3), "HTML-ENTITIES", 'UTF-8')));
							$q4		= str_replace('"','',str_replace("'","",rtrim(ltrim($cell[18]))));
							$q4 	= str_replace('&lrm;','',html_entity_decode(mb_convert_encoding(stripslashes($q4), "HTML-ENTITIES", 'UTF-8')));
							
							
							$id_organisasi = $_REQUEST['organisasi'];
							$id_unit = $_REQUEST['unit'];
								
							$stmt = "select a.id_kpi
									from kpi a
									inner join kpi_organisasi d on a.id_kpi = d.id_kpi
									where d.id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."'
									and ltrim(rtrim(a.nama_kpi)) = '".ltrim(rtrim($kpi))."' and d.id_unit = '".$id_unit."'
									order by a.parent, a.urutan";
							
							$query = mysqli_query($conn,$stmt);
							
							$j = 1;
							while ($row = mysqli_fetch_array($query)){	
								$id_kpi = $row['id_kpi'];	
										
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$jan."', '".$_REQUEST['tahun']."', 1, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
										
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$feb."', '".$_REQUEST['tahun']."', 2, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$mar."', '".$_REQUEST['tahun']."', 3, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$apr."', '".$_REQUEST['tahun']."', 4, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$mei."', '".$_REQUEST['tahun']."', 5, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$jun."', '".$_REQUEST['tahun']."', 6, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$jul."', '".$_REQUEST['tahun']."', 7, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$ags."', '".$_REQUEST['tahun']."', 8, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$sep."', '".$_REQUEST['tahun']."', 9, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$okt."', '".$_REQUEST['tahun']."', 10, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$nov."', '".$_REQUEST['tahun']."', 11, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$des."', '".$_REQUEST['tahun']."', 12, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$q1."', '".$_REQUEST['tahun']."', 13, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$q2."', '".$_REQUEST['tahun']."', 14, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$q3."', '".$_REQUEST['tahun']."', 15, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
											tgl_input, id_user_input, ip_input) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$id_unit."', '".$q4."', '".$_REQUEST['tahun']."', 16, '".$_REQUEST['status']."',
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
								$qry = mysqli_query($conn,$stm);
							
							}
							
							if($qry){
								$msg ="Sukses Tambah data";
							}else{
								$msg ="Gagal Tambah data";
							}
							
						}
					}
					
					
					unlink($folder);
	
				}
				
					
			}
		}
		
		
	}	
	
    echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpirealisasi'</script>";
}
*/

function inputform_data(){	
	global $conn;
	
	if ($_POST['tahun']==''){
		$msg .= "Tahun  harus diisi<br>";
	}
	if ($_POST['periode']==''){
		$msg .= "Periode  harus diisi<br>";
	}
	if ($_POST['status']==''){
		$msg .= "Status  harus diisi<br>";
	}
	if ($_POST['kpi']==''){
		$msg .= "KPI  harus diisi<br>";
	}
	if ($_REQUEST['unit']==''){
		$msg .= "Unit  harus diisi";
	}
	if ($msg==''){	
		$stmt_del = mysqli_query($conn, "delete from kpi_realisasi
									where tahun = '".$_REQUEST['tahun']."' and id_kpi = '".$_REQUEST['kpi']."'
									 and id_status = '".$_REQUEST['status']."' and id_periode = '".$_REQUEST['periode']."'");
		
		$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
									from p_organisasi a
									order by a.id_group");
		while ($dt_pil = mysqli_fetch_array($qry_pil)) {
			$id_organisasi = $dt_pil['id_organisasi'];
			
			$realisasi = str_replace(",",".",str_replace(".","",$_POST['realisasi_'.$id_organisasi]));	
			if ($realisasi!=0){			
				$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, realisasi, id_status, id_unit, 
						tahun, id_periode,tgl_input, id_user_input, ip_input) 
						values ('".$_REQUEST['kpi']."', '".$id_organisasi."', '".$realisasi."', '".$_REQUEST['status']."', '".$_REQUEST['unit']."',
						'".$_REQUEST['tahun']."', '".$_REQUEST['periode']."',now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
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
	/*
	echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpirealisasi'</script>";
	*/
}

function edit_data(){	
	global $conn;
	
	if ($_REQUEST['organisasi']==''){
		$msg .= "Organisasi  harus diisi";
	}
	if ($_REQUEST['unit']==''){
		$msg .= "Unit  harus diisi";
	}
	if ($_REQUEST['tahun']==''){
		$msg .= "Tahun  harus diisi";
	}
	if ($_REQUEST['periode']==''){
		$msg .= "Periode  harus diisi";
	}
	if ($_REQUEST['kpi']==''){
		$msg .= "KPI  harus diisi";
	}
	if ($_REQUEST['status']==''){
		$msg .= "Status  harus diisi";
	}
	if ($_REQUEST['realisasi']==''){
		$msg .= "Realisasi  harus diisi";
	}
	if ($msg==''){	
					
		$stmt_del = mysqli_query($conn, "delete from kpi_realisasi 
										where id_organisasi = '".$_REQUEST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'
										and id_status = '".$_REQUEST['status']."' and id_unit = '".$_REQUEST['unit']."'
										and id_periode = '".$_REQUEST['periode']."' and id_kpi = '".$_REQUEST['kpi']."' ");
		$realisasi = $_POST['realisasi'];	
		if ($realisasi!=0){			
			$stm = "insert into kpi_realisasi (id_kpi, id_organisasi, id_unit, realisasi, tahun, id_periode, id_status,
						tgl_input, id_user_input, ip_input) 
						values ('".$_REQUEST['kpi']."', '".$_REQUEST['organisasi']."', '".$_REQUEST['unit']."', 
						'".str_replace(",",".",str_replace(".","",$_REQUEST['realisasi']))."',
						'".$_REQUEST['tahun']."', '".$_REQUEST['periode']."', '".$_REQUEST['status']."',
						now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
			$qry = mysqli_query($conn,$stm);
		}
		if($qry){
			$msg ="Sukses Tambah data";
		}else{
			$msg ="Gagal Tambah data";
		}	
	}	
	echo $msg;
	
}
	
	
function reset_data(){
	global $conn;
	
	/*if ($_SESSION['km_profil']==1) {
		$id_organisasi = $_REQUEST['organisasi'];
	} else {
		$user_profil_arr = get_profiluser();		
		$id_organisasi = $user_profil_arr['organisasi'];
	}*/
	
	$id_organisasi = $_REQUEST['organisasi'];
	if ($_REQUEST['organisasi']==$id_organisasi) {
		$qry = mysqli_query($conn,"delete from kpi_realisasi
									where id_organisasi = '".$_REQUEST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'
									and id_status = '".$_REQUEST['status']."'");
		if($qry){
			$msg = "sukses";
		}else{
			$msg = "gagal";
		}
	} else {
		$msg = "Maaf, bukan organisasi anda. anda tidak berhak menghapus";
	}
	
	echo $msg;
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpi'</script>";*/
}

function inputlist_data(){
	global $conn;
	
?>
	<style>
		.kecil{
			font-size:10px;
		}
		
	</style>
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
				},
				"columnDefs": [
					{ "width": "2%" ,targets: [0] },
					{ "width": "2%" ,targets: [1] },
					{ "width": "8%" ,targets: [2,3,4,5,6,7,8,9,10,11,12,13] }
				  ]
			});
			
			$(".kecil").keydown(function (e) {
					// Allow: backspace, delete, tab, escape, enter and .
					if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
						 // Allow: Ctrl+A
						(e.keyCode == 65 && e.ctrlKey === true) || 
						 // Allow: home, end, left, right
						(e.keyCode >= 35 && e.keyCode <= 39)) {
							 // let it happen, don't do anything
							 return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});
			
		});
		
	</script>
    
    <br />
   <table id="datable_1" class="table table-hover w-100 display cell-border kecil" width="100%">
     <thead>
        <tr align="center">
            <th><b>No</b></th>
            <th><b>Nama KPI</b></th>
            <th><b>Jan</b></th>
            <th><b>Feb</b></th>
            <th><b>Mar</b></th>
            <th><b>Apr</b></th>
            <th><b>Mei</b></th>
            <th><b>Jun</b></th>
            <th><b>Jul</b></th>
            <th><b>Ags</b></th>
            <th><b>Sept</b></th>
            <th><b>Okt</b></th>
            <th><b>Nov</b></th>
            <th><b>Des</b></th>
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
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>               
        </tr>
                
       <?php
        if (empty($_REQUEST['tahun'])) {
            $where_tahun = "";
			$tahun = "";
        } else {
            $where_tahun = " and e.tahun = '".$_REQUEST['tahun']."' ";
        	$tahun = $_REQUEST['tahun'];
		}
       	$status = $_REQUEST['status'];
		
		 $stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, 
				IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, 
				
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 1 and x.tahun = '".$tahun."' 
					and x.id_status = '".$status."'),0) realisasi_jan,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 2 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_feb,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 3 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_mar,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 4 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_apr,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 5 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_mei,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 6 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_jun,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 7 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_jul,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 8 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_ags,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 9 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_sep,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 10 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_okt,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 11 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_nov,
				ifnull((select realisasi from kpi_realisasi x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 12 and x.tahun = '".$tahun."'
					and x.id_status = '".$status."'),0) realisasi_des
				
			  from kpi a
				inner join kpi_organisasi d on a.id_kpi = d.id_kpi
				left join user_app b on a.id_user_input = b.id_user
				
				where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
				and d.id_organisasi = '".$_REQUEST['organisasi']."' 
				order by a.parent, a.urutan";
        
        //echo "<pre>$stmt</pre>";
        $query = mysqli_query($conn,$stmt);
        
        $j = 1;
        while ($row = mysqli_fetch_array($query)){
            ?>
            <tr bgcolor="EFEFEF">
                <td><?php echo $i.".".$j; ?></td>
                <td><?php echo $row['nama_kpi']; ?></td>
                <td align="center"><input class="form-control kecil" name="realisasi_jan_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jan'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_feb_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_feb'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_mar_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_mar'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_apr_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_apr'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_mei_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_mei'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_jun_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jun'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_jul_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jul'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_ags_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_ags'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_sep_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_sep'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_okt_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_okt'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_nov_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_nov'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="realisasi_des_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_des'];?>"  /></td>
                
            </tr>
        <?php
            childinput($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $row['id_organisasi'], $tahun, $status);
                
            $j++;
        }
       
       
        $i++;
    }
    ?>
    </tbody>
</table>
             
<?php   
}
  
function childinput($id_bsc_perspective, $parent, $i, $id_organisasi, $tahun, $status){
	global $conn;
	
	if (empty($tahun)) {
		$where_tahun = "";
	} else {
		$where_tahun = " and e.tahun = '".$_REQUEST['tahun']."' ";
	}
	
	$stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, 
		IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, 
		
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 1 and x.tahun = '".$tahun."' 
			and x.id_status = '".$status."'),0) realisasi_jan,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 2 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_feb,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 3 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_mar,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 4 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_apr,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 5 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_mei,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 6 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_jun,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 7 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_jul,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 8 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_ags,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 9 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_sep,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 10 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_okt,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 11 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_nov,
		ifnull((select realisasi from kpi_realisasi x 
		where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 12 and x.tahun = '".$tahun."'
			and x.id_status = '".$status."'),0) realisasi_des
		
		from kpi a
		inner join kpi_organisasi d on a.id_kpi = d.id_kpi
		left join user_app b on a.id_user_input = b.id_user
		
		where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
		and d.id_organisasi = '".$id_organisasi."' 
		order by a.parent, a.urutan";
		
	$query = mysqli_query($conn,$stmt);
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
		?>
		<tr>
			<td><?php echo $i.".".$j; ?></td>
			<td><?php echo $row['nama_kpi']; ?></td>
        	<td align="center"><input class="form-control kecil" name="realisasi_jan_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jan'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_feb_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_feb'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_mar_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_mar'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_apr_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_apr'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_mei_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_mei'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_jun_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jun'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_jul_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_jul'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_ags_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_ags'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_sep_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_sep'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_okt_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_okt'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_nov_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_nov'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="realisasi_des_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['realisasi_des'];?>"  /></td>
        </tr>           
	<?php
		childinput($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$row['id_organisasi'], $tahun, $status);
		$j++;
	}
	
	
}  
?>