<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");
/** Include path **/
ini_set('include_path', ini_get('include_path').';Classes/');

/** PHPExcel */
require_once './Classes/PHPExcel.php';
require_once('./Classes/PHPExcel/IOFactory.php');

/** PHPExcel_Writer_Excel2007 */
include './Classes/PHPExcel/Writer/Excel2007.php';

set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time',7200);


switch($_REQUEST['tp']) {
	case 'input':
		if ($_SESSION['km_user_input']==1) {
			input_data();
		}
		break;
	case 'inputlist':
		if ($_SESSION['km_user_input']==1) {
			inputlist_data();
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
							//echo $bobot_bsc = bobotbsc($row_bsc['id_bsc_perspective'], $id_organisasi, $tahun, $arr_periode[$ii]);
							//echo $row_bsc['nilai'];
							//$arr_bobotbsc[$ii] = $bobot_bsc;
							?></td>
                    <?php } ?>         
                </tr>
                        
               <?php
			   
			   
			   $stmt = "select distinct a.id_kpi, a.nama_kpi, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan
						from kpi a
							where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
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
							/*if ($row['jml_child']>0) {
								echo bobotdet($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]); 
							} else {
								echo bobotdet($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]);
							}*/
							echo bobotdet($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]);
						?></td>
                        <?php } ?>
					</tr>
				<?php
					child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi,$tahun, $jml_periode, $arr_periode);
						
					$j++;
				}
			   
			   
				$i++;
			}
			?>
        </tbody><!--
        <tfoot>
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


function child($id_bsc_perspective, $parent, $i, $id_organisasi, $tahun, $jml_periode, $arr_periode){
	global $conn;
	$stmt = "select distinct a.id_kpi, a.nama_kpi, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan
			from kpi a
			left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				order by a.parent, a.urutan";
				
	//echo "<pre>$stmt</pre>";
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
				/*if ($row['jml_child']>0) {
					echo bobot($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]);
				} else {
					echo bobotdet($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]);
				} */
				echo bobotdet($row['id_kpi'],$id_organisasi,$tahun, $arr_periode[$ii]); ?></td>
            <?php } ?>
		</tr>           
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$id_organisasi, $tahun, $jml_periode, $arr_periode);
		$j++;
	}
	
	
}
   
function bobotbsc($id_bsc_perspective, $id_organisasi, $tahun, $id_periode){
    global $conn;
	
	$qry = mysqli_query($conn,"select sum(target) jml
								from kpi_bobottarget a
								inner join kpi b on a.id_kpi = b.id_kpi
								where b.id_bsc_perspective = '".$id_bsc_perspective."' and a.id_organisasi = '".$id_organisasi."' 
								and a.tahun = '".$tahun."' and a.id_periode = '".$id_periode."' and parent = '0'");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['jml'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

function bobot($id_kpi, $id_organisasi, $tahun, $id_periode){
    global $conn;
	
	$qry = mysqli_query($conn,"select sum(target) jml
								from kpi_bobottarget a
								inner join kpi b on a.id_kpi = b.id_kpi
								where b.parent = '".$id_kpi."' and a.id_organisasi = '".$id_organisasi."' 
								and a.tahun = '".$tahun."' and a.id_periode = '".$id_periode."'");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['jml'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

function bobotdet($id_kpi, $id_organisasi, $tahun, $id_periode){
    global $conn;
	
	$qry = mysqli_query($conn,"select sum(target) jml 
								from kpi_bobottarget 
								where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$tahun."' and id_periode = '".$id_periode."'");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['jml'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return number_format($nilai,2,",",".");
	
}

function input_data(){	
	global $conn;
	
	if ($_REQUEST['organisasi']==''){
		$msg .= "Organisasi  harus diisi";
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
					$file = $_SESSION['km_user'].date("YmdHis").$_FILES['filex']['name'];	
					$lokasi = "tmp/".$file;	
					move_uploaded_file($_FILES['filex']['tmp_name'],$lokasi);
					
					if ($_FILES['filex']['type'] == "application/csv" or $_FILES['filex']['type'] == "application/vnd.ms-excel") {
						$jns_excel = "application/vnd.ms-excel"; 
						$type_excel = 'Excel5';
					}else {
						$jns_excel = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; 
						$type_excel = 'Excel2007';
					}
					
					
					$objReader = PHPExcel_IOFactory::createReader($type_excel);
					$objReader->setReadDataOnly(true);
					$objPHPExcel = $objReader->load($lokasi);
					$objWorksheet = $objPHPExcel->getActiveSheet();
					$cell = array();
					
					//$filename = $_FILES['filex']['name'];	
					$baris = 0;
					
					
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
							
							$stmt = "select a.id_kpi
									from kpi a
									where ltrim(rtrim(a.nama_kpi)) = '".ltrim(rtrim($kpi))."'
									order by a.parent, a.urutan";
							
							$query = mysqli_query($conn,$stmt);
							
							$j = 1;
							while ($row = mysqli_fetch_array($query)){	
								$id_kpi = $row['id_kpi'];	
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 1");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {						
									$stm = "update kpi_bobottarget set target = '".$jan."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 1";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$jan."', '".$_REQUEST['tahun']."', 1,
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);									
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 2");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {			
									$stm = "update kpi_bobottarget set target = '".$feb."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 2";
									$qry = mysqli_query($conn,$stm);
								} else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
											values ('".$id_kpi."', '".$id_organisasi."', '".$feb."', '".$_REQUEST['tahun']."', 2,
											now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);	
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 3");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {	
									$stm = "update kpi_bobottarget set target = '".$mar."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 3";
									$qry = mysqli_query($conn,$stm);
								}else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$mar."', '".$_REQUEST['tahun']."', 3,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);	
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 4");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$apr."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 4";
									$qry = mysqli_query($conn,$stm);
								}else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$apr."', '".$_REQUEST['tahun']."', 4,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);										
								}		
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 5");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {	
									$stm = "update kpi_bobottarget set target = '".$mei."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 5";
									$qry = mysqli_query($conn,$stm);
								} else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$mei."', '".$_REQUEST['tahun']."', 5,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
									
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 6");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {	
									$stm = "update kpi_bobottarget set target = '".$jun."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 6";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$jun."', '".$_REQUEST['tahun']."', 6,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 7");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$jul."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 7";
									$qry = mysqli_query($conn,$stm);
								}else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$jul."', '".$_REQUEST['tahun']."', 7,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 8");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$ags."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 8";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$ags."', '".$_REQUEST['tahun']."', 8,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 9");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$sep."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 9";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$sep."', '".$_REQUEST['tahun']."', 9,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);										
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 10");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$okt."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 10";
									$qry = mysqli_query($conn,$stm);
								}else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$okt."', '".$_REQUEST['tahun']."', 10,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 11");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {	
									
									$stm = "update kpi_bobottarget set target = '".$nov."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 11";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$nov."', '".$_REQUEST['tahun']."', 11,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 12");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$des."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 12";
									$qry = mysqli_query($conn,$stm);
								}else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$des."', '".$_REQUEST['tahun']."', 12,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);									
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 13");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$q1."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 13";
									$qry = mysqli_query($conn,$stm);
								}else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$q1."', '".$_REQUEST['tahun']."', 13,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
									
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 14");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$q2."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 14";
									$qry = mysqli_query($conn,$stm);
								} else {
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$q2."', '".$_REQUEST['tahun']."', 14,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);										
								}
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 15");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$q3."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 15";
									$qry = mysqli_query($conn,$stm);
								}else{
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$q3."', '".$_REQUEST['tahun']."', 15,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);										
								}
								
								
								$qry_cek = mysqli_query($conn,"select id_kpi from kpi_bobottarget
														where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
														and tahun = '".$_REQUEST['tahun']."' and id_periode = 16");
								$jml_cek = mysqli_num_rows($qry_cek);
								
								if ($jml_cek>0) {		
									$stm = "update kpi_bobottarget set target = '".$q4."',  tgl_input_target = now(), 
											id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
											where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' 
											and tahun = '".$_REQUEST['tahun']."' and id_periode = 16";
									$qry = mysqli_query($conn,$stm);
								}else {										
									$stm = "insert into kpi_bobottarget (id_kpi, id_organisasi, target, tahun, id_periode,
											tgl_input_target, id_user_input_target, ip_input_target) 
												values ('".$id_kpi."', '".$id_organisasi."', '".$q4."', '".$_REQUEST['tahun']."', 16,
												now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
									$qry = mysqli_query($conn,$stm);
								}
										
								/*		
								$stm = "update kpi_bobottarget set target = '".$jan."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 1";
								$qry = mysqli_query($conn,$stm);
										
								$stm = "update kpi_bobottarget set target = '".$feb."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 2";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$mar."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 3";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$apr."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 4";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$mei."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 5";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$jun."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 6";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$jul."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 7";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$ags."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 8";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$sep."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 9";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$okt."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 10";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$nov."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 11";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$des."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 12";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$q1."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 13";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$q2."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 14";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$q3."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 15";
								$qry = mysqli_query($conn,$stm);
								
								$stm = "update kpi_bobottarget set target = '".$q4."',  tgl_input_target = now(), 
										id_user_input_target = '".$_SESSION['km_user']."', ip_input_target = '".$_SERVER['REMOTE_ADDR']."'
										where id_kpi = '".$id_kpi."' and id_organisasi = '".$id_organisasi."' and tahun = '".$_REQUEST['tahun']."' and id_periode = 16";
								$qry = mysqli_query($conn,$stm);
								*/
								
								if($qry){
									$msg ="Sukses Tambah data";
								}else{
									$msg ="Gagal Tambah data";
								}
								
							
							}	
							
							
						}
					}
				}
				
					
			}
		}
		
		
	}	
	
	unlink($lokasi);
    echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpitarget'</script>";
	
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
	if ($_REQUEST['target']==''){
		$msg .= "Target  harus diisi";
	}
	if ($msg==''){	
					
		$stm = "update kpi_bobottarget set target = '".str_replace(",",".",str_replace(".","",$_REQUEST['target']))."', 
				tgl_input = now(), id_user_input = '".$_SESSION['km_user']."',  ip_input =  '".$_SERVER['REMOTE_ADDR']."'
				where id_kpi = '".$_REQUEST['kpi']."' and id_organisasi = '".$_REQUEST['organisasi']."' 
				and tahun  = '".$_REQUEST['tahun']."' and id_periode = '".$_REQUEST['periode']."'";
		$qry = mysqli_query($conn,$stm);
		
		if($qry){
			$msg ="Sukses Tambah data";
		}else{
			$msg ="Gagal Tambah data";
		}	
	}	
	echo $msg;
	
}

		 
function input_data2(){	
	global $conn;
	
	if ($_POST['organisasi']==''){
		$msg .= "Organisasi  harus diisi<br>";
	}
	if ($msg==''){	
		//$stmt_del = mysqli_query($conn, "delete from kpi_bobottarget where id_organisasi = '".$_POST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'");
		
		$stmt = "select a.id_kpi
				from kpi a
				inner join kpi_organisasi d on a.id_kpi = d.id_kpi
				where d.id_organisasi = '".$_POST['organisasi']."'
				order by a.parent, a.urutan";
		
		$query = mysqli_query($conn,$stmt);
		
		$j = 1;
		while ($row = mysqli_fetch_array($query)){	
			$id_kpi = $row['id_kpi'];	
			
			$target = $_POST['target_jan_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 1";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_feb_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 2";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_mar_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 3";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_apr_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 4";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_mei_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 5";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_jun_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 6";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_jul_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 7";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_ags_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 8";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_sep_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 9";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_okt_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 10";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_nov_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 11";
			$qry = mysqli_query($conn,$stm);
			
			$target = $_POST['target_des_'.$id_kpi];
			$stm = "update kpi_bobottarget set target = '".$target."',
						tgl_input_realisasi = now(), id_user_input_realisasi = '".$_SESSION['km_user']."', ip_input_realisasi = '".$_SERVER['REMOTE_ADDR']."'
						where id_organisasi = '".$_POST['organisasi']."' and id_kpi = '".$id_kpi."'
						and tahun = '".$_REQUEST['tahun']."' and bulan = 12";
			$qry = mysqli_query($conn,$stm);
		}
		
		if($qry){
			$msg .="Sukses Tambah data";
		}else{
			$msg .="Gagal Tambah data";
		}
		
	}
	
	echo "<script>alert('".$msg."');window.location.href='index.php?fl=kpitarget'</script>";
	
}
	
function reset_data(){
	global $conn;
	
	
	$qry = mysqli_query($conn,"update kpi_bobottarget set target = 0 
								where id_organisasi = '".$_REQUEST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'");
	if($qry){
		$msg = "sukses";
	}else{
		$msg = "gagal";
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
            <th><b>Sep</b></th>
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
            $tahun = date("Y");
        } else {
            $tahun = $_REQUEST['tahun'];
        }
       
       $stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, 
				IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, 
					
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 1 and x.tahun = '".$tahun."'),0) target_jan,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 2 and x.tahun = '".$tahun."'),0) target_feb,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 3 and x.tahun = '".$tahun."'),0) target_mar,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 4 and x.tahun = '".$tahun."'),0) target_apr,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 5 and x.tahun = '".$tahun."'),0) target_mei,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 6 and x.tahun = '".$tahun."'),0) target_jun,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 7 and x.tahun = '".$tahun."'),0) target_jul,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 8 and x.tahun = '".$tahun."'),0) target_ags,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 9 and x.tahun = '".$tahun."'),0) target_sep,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 10 and x.tahun = '".$tahun."'),0) target_okt,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 11 and x.tahun = '".$tahun."'),0) target_nov,
				ifnull((select target from kpi_bobottarget x 
				where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 12 and x.tahun = '".$tahun."'),0) target_des
				
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
                <td align="center"><input class="form-control kecil" name="target_jan_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jan'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_feb_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_feb'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_mar_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_mar'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_apr_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_apr'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_mei_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_mei'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_jun_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jun'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_jul_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jul'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_ags_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_ags'];?>"   /></td>
                <td align="center"><input class="form-control kecil" name="target_sep_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_sep'];?>"  /></td>
                <td align="center"><input class="form-control kecil" name="target_okt_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_okt'];?>" /></td>
                <td align="center"><input class="form-control kecil" name="target_nov_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5"size="10"  value="<?php echo $row['target_nov'];?>" /></td>
                <td align="center"><input class="form-control kecil" name="target_des_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_des'];?>"  /></td>
             </tr>     
        <?php
            childinput($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $row['id_organisasi'], $tahun);
                
            $j++;
        }
       
       
        $i++;
    }
    ?>
    </tbody>
</table>
     
<?php   
}
  
function childinput($id_bsc_perspective, $parent, $i, $id_organisasi, $tahun){
	global $conn;
	
	$stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(a.tgl_input,'%d-%m-%Y') tgl_input, 
			IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, 
					
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 1 and x.tahun = '".$tahun."'),0) target_jan,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 2 and x.tahun = '".$tahun."'),0) target_feb,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 3 and x.tahun = '".$tahun."'),0) target_mar,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 4 and x.tahun = '".$tahun."'),0) target_apr,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 5 and x.tahun = '".$tahun."'),0) target_mei,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 6 and x.tahun = '".$tahun."'),0) target_jun,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 7 and x.tahun = '".$tahun."'),0) target_jul,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 8 and x.tahun = '".$tahun."'),0) target_ags,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 9 and x.tahun = '".$tahun."'),0) target_sep,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 10 and x.tahun = '".$tahun."'),0) target_okt,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 11 and x.tahun = '".$tahun."'),0) target_nov,
			ifnull((select target from kpi_bobottarget x 
			where x.id_kpi = d.id_kpi and x.id_organisasi = d.id_organisasi and bulan = 12 and x.tahun = '".$tahun."'),0) target_des
				
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
            <td align="center"><input class="form-control kecil" name="target_jan_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jan'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_feb_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_feb'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_mar_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_mar'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_apr_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_apr'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_mei_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_mei'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_jun_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jun'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_jul_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_jul'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_ags_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_ags'];?>"   /></td>
            <td align="center"><input class="form-control kecil" name="target_sep_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_sep'];?>"  /></td>
            <td align="center"><input class="form-control kecil" name="target_okt_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_okt'];?>" /></td>
            <td align="center"><input class="form-control kecil" name="target_nov_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5"size="10"  value="<?php echo $row['target_nov'];?>" /></td>
            <td align="center"><input class="form-control kecil" name="target_des_<?php echo $row['id_kpi'];?>"  type="text" maxlength="5" size="10" value="<?php echo $row['target_des'];?>"  /></td>
         </tr>         
	<?php
		childinput($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j,$row['id_organisasi'], $tahun);
		$j++;
	}
	
	
}  
?>