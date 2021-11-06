<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");

switch($_REQUEST['tp']) {
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
				"scrollX": true
				/*,
				"fixedColumns": {
					"leftColumns": 1
				}*/
			});
			
			var dtTable2 = $('#datable_2').DataTable();
			dtTable2.destroy();
			
			$('#datable_2').DataTable({
				"bLengthChange": false,
				"bFilter": false,
				"paging": false,
				"scrollY": "300px",
				 "scrollCollapse": true,
				//"scrollY": true,
				"scrollX": true
				/*,
				"fixedColumns": {
					"leftColumns": 1
				}*/
			});
			
		});
		
	</script>
	<style>
        table.dataTable  {
		  table-layout: fixed;
		  width:100%;
		}
     table.dataTable tfoot td, table.dataTable tfoot th {
	 	font-weight:normal;
	}
	</style>
    <br />
    <?php 
	if ($_SESSION['km_profil']==1) { 
		$group_user = 1;
		
	} else { 		
		$user_profil_arr = get_profiluser();
		$group_user = $user_profil_arr['group'];
	}
	
	if ($group_user==1) {
	?>
    
   	<table id="datable_1" class="table table-bordered table-striped table-sm w-100">
		<thead class="thead-primary">
            <tr align="center">
                <th width="15%"><b>Teritory</b></th>
                <?php 
				$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
				$jml_periode = 0;
				$arr_periode = array();
				$tot = array();
				while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
					$arr_periode[$jml_periode] = $dt_periode['id_periode'];
					$tot[$jml_periode] = 0;
					$jml_periode++; 
					?>
                	<th><b><?php echo $dt_periode['singk_periode'];?></b></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
			 
			//$id_organisasi = $_REQUEST['organisasi'];
			$id_status = $_REQUEST['status'];
			//$id_unit = $_REQUEST['unitx'];
			//$id_periode = $_REQUEST['periode'];
			$id_kpi = $_REQUEST['kpi'];
			$id_group = $_REQUEST['group'];
			
			if (empty($_REQUEST['tahun'])) {
				$tahun = date("Y");
			} else {
				$tahun = $_REQUEST['tahun'];
			}
				
			$user_profil_arr = get_profiluser();
				   
			$stmt = "select id_organisasi, nama_organisasi
					from p_organisasi
					where id_group = '1'
					order by id_organisasi";
			
			//echo "<pre>$stmt</pre>";
			$query = mysqli_query($conn,$stmt);
			
			$j = 1;
			$tot = array();
			while ($row = mysqli_fetch_array($query)){
				$id_organisasi = $row['id_organisasi'];
				?>
				<tr>
					<td><?php echo $row['nama_organisasi']; ?></td>
			<?php
				for($ii=0;$ii<$jml_periode;$ii++) {
					$nilai = hitungreal($id_organisasi, $id_status, $tahun, $arr_periode[$ii], $id_kpi);
					$tot[$ii] = $tot[$ii] + $nilai;	
					?>
                    <td align="center"><?php echo number_format($nilai,2,",","."); ?></td>
                    <?php	
				}
			?>
               </tr>
              <?php 
					
				$j++;
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
                <td width="15%">NAS</td>
				<?php
                    for($ii=0;$ii<$jml_periode;$ii++) {
                    ?>
                    <td align="center"><?php echo number_format($tot[$ii],2,",","."); ?></td>
                    <?php	
                    }
                ?>
            </tr>
        </tfoot>
    </table>
    
    <br />
    
    <?php 
	} 
	
	
	if ($_SESSION['km_profil']==1) { 
		$group_user = 2;		
	} else { 		
		$user_profil_arr = get_profiluser();
		$group_user = $user_profil_arr['group'];
	}
	
	if ($group_user==2) {
	?>
     <br />
   	<table id="datable_2" class="table table-bordered table-striped table-sm w-100">
		<thead class="thead-primary">
            <tr align="center">
                <th width="15%"><b>Teritory</b></th>
                <?php 
				$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
				$jml_periode = 0;
				$arr_periode = array();
				$tot2 = array();
				while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
					$arr_periode[$jml_periode] = $dt_periode['id_periode'];
					$tot2[$jml_periode] = 0;
	
					$jml_periode++; 
					?>
                	<th><b><?php echo $dt_periode['singk_periode'];?></b></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
			 
			//$id_organisasi = $_REQUEST['organisasi'];
			$id_status = $_REQUEST['status'];
			//$id_unit = $_REQUEST['unitx'];
			//$id_periode = $_REQUEST['periode'];
			$id_kpi = $_REQUEST['kpi'];
			$id_group = $_REQUEST['group'];
			
			if (empty($_REQUEST['tahun'])) {
				$tahun = date("Y");
			} else {
				$tahun = $_REQUEST['tahun'];
			}
				
				   
			$stmt = "select id_organisasi, nama_organisasi
					from p_organisasi
					where id_group = '2'
					order by id_organisasi";
			
			//echo "<pre>$stmt</pre>";
			$query = mysqli_query($conn,$stmt);
			
			$j = 1;
			$tot2 = array();
			while ($row = mysqli_fetch_array($query)){
				$id_organisasi = $row['id_organisasi'];
				?>
				<tr>
					<td><?php echo $row['nama_organisasi']; ?></td>
			<?php
				for($ii=0;$ii<$jml_periode;$ii++) {	
					$nilai = hitungreal($id_organisasi, $id_status, $tahun, $arr_periode[$ii], $id_kpi);	
					$tot2[$ii] = $tot2[$ii] + $nilai;	
					?>
                    <td align="center"><?php  echo number_format($nilai,2,",","."); ?></td>
                    <?php	
				}
			?>
               </tr>
              <?php 
					
				$j++;
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
                <td width="15%">Treg 4</td>
				<?php
                    for($ii=0;$ii<$jml_periode;$ii++) {
                    ?>
                    <td align="center"><?php echo number_format($tot2[$ii],2,",","."); ?></td>
                    <?php	
                    }
                ?>
            </tr>
        </tfoot>
    </table>
                        
<?php  
	}
	 
}
     

function hitungreal($id_organisasi, $id_status, $tahun, $id_periode, $id_kpi){
    global $conn;
	
	$qry = mysqli_query($conn,"select a.realisasi
								from kpi_realisasi a
								where a.id_kpi = '".$id_kpi."' and a.id_organisasi = '".$id_organisasi."' 
								and a.tahun = '".$tahun."' and a.id_periode = '".$id_periode."' and a.id_status = '".$id_status."'");
	$dt = mysqli_fetch_array($qry);
	$nilai = $dt['realisasi'];
	if (empty($nilai)) {
		$nilai = 0;
	}
	return $nilai;
	
}

?>