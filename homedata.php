<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");
require_once('./homefungsi.php');

switch($_REQUEST['tp']) {
	case 'tampil':
		tampil_data();
		break;
	case 'totalscore':
		totalscore();
		break;
	default :
		list_data();
		break;
}


function child($id_bsc_perspective, $parent, $i, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, $level){
	global $conn;

 
	  $stmt = "select a.id_kpi, a.nama_kpi, 
			IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan, IFNULL(a.realisasi,'0') realisasi2, IFNULL(a.score,'0') score2,
			(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child, a.terbalik, 
			
				IFNULL((select sum(z.bobot) from kpi_bobottarget z where z.id_kpi = a.id_kpi and
					z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'), 0) bobot,
					
				IFNULL((select sum(z.target) from kpi_bobottarget z where z.id_kpi = a.id_kpi and
				z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'), 0) target,
				
		
				IFNULL((select sum(z.realisasi) from kpi_realisasi z where z.id_kpi = a.id_kpi and
				z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
				and z.id_status = '1' ), 0) realisasi_prognose,
				
				IFNULL((select sum(z.realisasi) from kpi_realisasi z where z.id_kpi = a.id_kpi and
				z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
				and z.id_status = '3' ), 0) realisasi_rekon,
				
				IFNULL((select y.nama_unit from kpi_realisasi z 
				left join p_unit y on z.id_unit = y.id_unit 
				where z.id_kpi = a.id_kpi and z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
				 and z.id_status = '1' ),'Belum diinput') penginput_prognose,
							 
				IFNULL((select y.nama_unit from kpi_realisasi z 
				left join p_unit y on z.id_unit = y.id_unit 
				where z.id_kpi = a.id_kpi and z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
				 and z.id_status = '3' ),'Belum diinput') penginput_rekon
		 
				from kpi a
				left join kpi_organisasi b on a.id_kpi = b.id_kpi
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				and b.tahun = '".$tahun."' and b.id_organisasi = '".$id_organisasi."' 
						
				order by a.parent, a.urutan";
		
		/*
		IFNULL((select y.nama_unit from kpi_realisasi z
				left join user_app x on z.id_user_input = x.id_user 
				left join p_unit y on x.id_unit = y.id_unit 
				where z.id_kpi = a.id_kpi and
				z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
				and z.id_status = '".$id_status."' order by z.tgl_input desc  limit 1 ),'Belum diinput') penginput*/
				
	if ($parent==9) {				
		//echo "<pre>$stmt</pre>";
	}
	$query = mysqli_query($conn,$stmt);
	$cap = 0;
	$ach = 0;
	$score = 0;
	$bobot = 0;
	$realisasi = 0;
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
	
		$terbalik = $row['terbalik'];
		$realisasi_prognose =  $row['realisasi_prognose'];
		$realisasi_rekon =  $row['realisasi_rekon'];
		$target =  $row['target'];
		$penginput_prognose =  $row['penginput_prognose'];
		$penginput_rekon =  $row['penginput_rekon'];
		$bobot =  $row['bobot'];							
		if ($realisasi_prognose==0 and $realisasi_rekon==0) {
			$realisasi = "0";
			$status = "";
			$penginput = "Belum diinput";
		} else if ($realisasi_prognose>=1 and $realisasi_rekon==0) {
			$realisasi = $realisasi_prognose;
			$status = "Prognose";
			$penginput = $penginput_prognose;
		} else if ($realisasi_prognose==0 and $realisasi_rekon>=1) {
			$realisasi = $realisasi_rekon;
			$status = "Rekon";
			$penginput = $penginput_rekon;
		} else if ($realisasi_prognose>=1 and $realisasi_rekon>=1) {
			$realisasi = $realisasi_rekon;
			$status = "Rekon";
			$penginput = $penginput_rekon;
		} else {
			$realisasi = "0";
			$status = "";
			$penginput = "";
		}	
		
		// ach = (realiasasi / target) * 100% . ada yg kebalik (100%+target-realisasi) / target
		/*
		rumus yg terbalik sebenarnya ada 2, tapi utk KM 2021 hanya pakai 1 saja yaitu target/real*100% ==> itu untuk KPI BPPU saja ya mas. yang lainnya pakai rumus normal real/target*100
		*/
		
		
		$realisasi2 =  $row['realisasi2'];
		$score2 =  $row['score2'];
		
		// realisasi 0 = input, 1 = rumus score/bobot, 2 = rata2, 3 = penjumlahan
		if ($realisasi2==1) {
			$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
			$score = $nilai_child['score'];
				
			//$score = round($score,2);
			$realisasi = ($score/$bobot) * 100;
			$penginput = "-";
			
		} else if ($realisasi2==2) { // rata2 dibawahnya
			$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
			//$score = round($score,2);
			$realisasi = $nilai_child['realisasi_rata2'];
			$penginput = "-";
			
		} else if ($realisasi2==3) { // penjumlahan bawahnya
			$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
			$realisasi = $nilai_child['realisasi'];
			$penginput = "-";
		} 
		
		if ($terbalik==1) {
			if ($realisasi<=0) {
				$ach = 0;
			} else if ( $target <=0) {
				$ach = 0;
			} else {
				$ach = ( $target / $realisasi ) * 100;
			}
		} else {
			if ($realisasi<=0) {
				$ach = 0;
			} else if ( $target <=0) {
				$ach = 0;
			} else {
				$ach = ( $realisasi / $target ) * 100;
			}
		}	
		
		if ($ach<$batas_cap1) {
			$cap = $batas_cap1;
		} else if ($ach>$batas_cap2) {
			$cap = $batas_cap2;
		} else {
			$cap = $ach;
		}
	
		// score 0 = rumus, 1 = jumlah
		// score 0 = rumus, 1 = jumlah
		if ($score2==1){
			$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
			$score = $nilai_child['score'];
		} else {
			$score = ($bobot * $cap)/ 100;
		}	
			
		//$score = round($score,2);
		
		if ($level==1) {
			$style = ' style="background-color:#CCCCCC;"';
		}else {
			$style = ' style="background-color:#FFFFFF;"';
		}
		
		?>
		<tr <?php echo $style;?>>
			<td><?php echo $i.".".$j; ?></td>
            <td><?php echo $row['nama_kpi']; ?></td>
            <td align="center"><?php echo $row['satuan']; ?></td>
            <td align="center"><?php echo number_format($bobot,2,",","."); ?></td>
            <td align="center"><?php echo number_format($row['target'],2,",","."); ?></td>
            <td align="center"><?php echo number_format($realisasi,2,",","."); ?></td>
            <td align="center"><?php echo number_format($ach,2,",","."); ?> %</td>
            <td align="center"><?php echo number_format($cap,2,",","."); ?> %</td>
            <td align="center"><?php echo number_format($score,2,",","."); ?></td>
            <td align="center"><?php echo $status; ?></td>
            <td align="center"><?php echo $penginput; ?></td>
		</tr>           
	<?php
		//$level++;
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '2');
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
			
			var height = $(window).height() - 300;
			
			$('#datable_1').DataTable({
				"bLengthChange": false,
				"bFilter": false,
				"paging": false,
				"scrollY": height + 'px',
				 "scrollCollapse": true,
				//"scrollY": true,
				"scrollX": true,
				"fixedColumns": {
					"leftColumns": 2
				}
			});
			
		});
		
	</script>
    <link href="dist/css/stylenew.css" rel="stylesheet" type="text/css" />
    <style>
		 table.dataTable tfoot td, table.dataTable tfoot th {
			background-color:#009966; 
			color:#FFFFFF;
			/*background: #e0e3e4;*/ 
		}
		table.dataTable thead td, table.dataTable thead th {
			color:#FFFFFF; 
			background-color:#666666;
			/*background: #e0e3e4;*/ 
		}
		table.dataTable td {
		  white-space: normal !important; 
		  word-wrap: break-word;  
		}
		table.dataTable  {
		  table-layout: fixed;
		  width:100%;
		}
		table.dataTable tbody td {
		 	color:#000000;
		}
	</style>
    
     <?php
	 	
		if (empty($_REQUEST['tahun'])) {
			$tahun = date("Y");
		} else {
			$tahun = $_REQUEST['tahun'];
		}
		 
		$id_organisasi = $_REQUEST['organisasi'];
		//$id_status = $_REQUEST['status'];
		//$id_unit = $_REQUEST['unitx'];
		$id_periode = $_REQUEST['periode'];
		//$id_status = $_COOKIE['cookie_kmtelkom'];
		/*if (empty($id_status)) {
			$id_status = "3";
		}*/
		/*
		1	Prognose
		3	Rekon
		*/
		
		$qry = mysqli_query($conn,"select tampil from kpi_setting 
									where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'");
		$data = mysqli_fetch_array($qry);
		$status_tampil = $data['tampil'];
		
		$profil_arr = get_profilorg($id_organisasi);
		
	?>
   	<table id="datable_1" class="table table-bordered table-striped table-sm w-100">
		<thead>
            <tr align="center">
            	<th width="3%"><b>No</b></th>
                <th width="20%"><b>Indicator</b></th>
                <th><b>Unit</b></th>
                <th><b>Weight</b></th>
                <th><b>Target</b></th>
                <th><b>Realisasi</b></th>
                <th><b>Ach</b></th>
                <th><b>Cap</b></th>
                <th><b>Score</b></th>
                <th><b>Status</b></th>
                <th><b>Peng<br />input</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
			
			$total_bobot = 0;
			$total_score = 0;
			
			if ($status_tampil==1 and $profil_arr['tampil']==1) {
											
				/*$qry_cek = mysqli_query($conn,"select id_status
												from kpi_realisasi 
												where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'
												and id_status = '1'");
				$jml_prognose = mysqli_num_rows($qry_cek);
				
				$qry_cek = mysqli_query($conn,"select id_status
												from kpi_realisasi 
												where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'
												and id_status = '3'");
				$jml_rekon = mysqli_num_rows($qry_cek);
				
				if ($jml_prognose==0 and $jml_rekon==0) {
					$id_status = "";
				} else if ($jml_prognose>=1 and $jml_rekon==0) {
					$id_status = 1;
				} else if ($jml_prognose==0 and $jml_rekon>=1) {
					$id_status = 3;
				} else if ($jml_prognose>=1 and $jml_rekon>=1) {
					$id_status = 3;
				} else {
					$id_status = "";
				}				
				
				$stmt_status = mysqli_query($conn,"select nama_status from p_status where id_status = '".$id_status."'");
				$row_status = mysqli_fetch_array($stmt_status);
				$status = $row_status['nama_status'];
				*/
				
				$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, cap, cap2
												from p_bsc_perspective ");
				$i = 1;
				$total_bobot = 0;
				$total_score = 0;
				while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {		
					$batas_cap1 = $row_bsc['cap'];
					$batas_cap2 = $row_bsc['cap2'];
						
					$total_arr = hitungtotalbsc($id_organisasi, $id_periode, $id_status, $tahun, $row_bsc['id_bsc_perspective']);	
					$total_bobot = $total_arr['bobot'];
					$total_score = $total_arr['score'];	
						
					?>
					<tr style="background-color:#009999; color:#FFFFFF;">
						<td><b><?php echo $i; ?></b></td>
						<td><b><?php echo $row_bsc['nama_bsc_perspective']; ?></b></td>
						<td></td>
						<td align="center"><?php echo number_format($total_bobot,2,",",".");?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="center"><?php echo number_format($total_score,2,",",".");?></td>
						<td></td>
						<td></td>          
					</tr>
							
				   <?php
				  
					/*if (empty($_REQUEST['periode'])) {
						$periode = date("m");
						$where_periode = " and bulan = '".$periode."'";
						
					} else {
						$periode = $_REQUEST['periode'];
						$qry_periode = mysqli_query($conn,"select bulan from p_periode where id_periode = '".$_REQUEST['periode']."'");
						$dt_periode = mysqli_fetch_array($qry_periode);
						$bulan_periode = $dt_periode['bulan'];
						$where_periode = " and bulan in (".$bulan_periode.") ";
					}*/
				   
					
					/*
					1	Prognose
					3	Rekon
					*/
					 $stmt = "select a.id_kpi, a.nama_kpi, 
							IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan, IFNULL(a.realisasi,'0') realisasi2, IFNULL(a.score,'0') score2,
							(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child, a.terbalik, 
							
							IFNULL((select sum(z.bobot) from kpi_bobottarget z where z.id_kpi = a.id_kpi and
								z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'), 0) bobot,
								
							IFNULL((select sum(z.target) from kpi_bobottarget z where z.id_kpi = a.id_kpi and
							z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'), 0) target,
							
							IFNULL((select sum(z.realisasi) from kpi_realisasi z where z.id_kpi = a.id_kpi and
							z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
							and z.id_status = '1' ), 0) realisasi_prognose,
							
							IFNULL((select sum(z.realisasi) from kpi_realisasi z where z.id_kpi = a.id_kpi and
							z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
							and z.id_status = '3' ), 0) realisasi_rekon,
							
							IFNULL((select y.nama_unit from kpi_realisasi z 
							left join p_unit y on z.id_unit = y.id_unit 
							where z.id_kpi = a.id_kpi and z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
							 and z.id_status = '1' ),'Belum diinput') penginput_prognose,
										 
							IFNULL((select y.nama_unit from kpi_realisasi z 
							left join p_unit y on z.id_unit = y.id_unit 
							where z.id_kpi = a.id_kpi and z.id_organisasi = '".$id_organisasi."' and z.id_periode = '".$id_periode."' and z.tahun = '".$tahun."'
							 and z.id_status = '3' ),'Belum diinput') penginput_rekon
							 
							from kpi a
							left join kpi_organisasi b on a.id_kpi = b.id_kpi
							where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
							and b.tahun = '".$tahun."' and b.id_organisasi = '".$id_organisasi."' 
							order by a.parent, a.urutan";
					
					
					//echo "<pre>$stmt</pre>";
					$query = mysqli_query($conn,$stmt);
					$cap = 0;
					$ach = 0;
					$score = 0;
					$bobot = 0;
					$realisasi = 0;
					
					$j = 1;
					while ($row = mysqli_fetch_array($query)){
						$terbalik = $row['terbalik'];
						$realisasi_prognose =  $row['realisasi_prognose'];
						$realisasi_rekon =  $row['realisasi_rekon'];
						$target =  $row['target'];
						$penginput_prognose =  $row['penginput_prognose'];
						$penginput_rekon =  $row['penginput_rekon'];
						$bobot =  $row['bobot'];										
						if ($realisasi_prognose==0 and $realisasi_rekon==0) {
							$realisasi = "";
							$status = "";
							$penginput = "Belum diinput";
						} else if ($realisasi_prognose>=1 and $realisasi_rekon==0) {
							$realisasi = $realisasi_prognose;
							$status = "Prognose";
							$penginput = $penginput_prognose;
						} else if ($realisasi_prognose==0 and $realisasi_rekon>=1) {
							$realisasi = $realisasi_rekon;
							$status = "Rekon";
							$penginput = $penginput_rekon;
						} else if ($realisasi_prognose>=1 and $realisasi_rekon>=1) {
							$realisasi = $realisasi_rekon;
							$status = "Rekon";
							$penginput = $penginput_rekon;
						} else {
							$realisasi = "";
							$status = "";
							$penginput = "";
						}	
						
						// ach = (realiasasi / target) * 100% . ada yg kebalik (100%+target-realisasi) / target
						/*
						rumus yg terbalik sebenarnya ada 2, tapi utk KM 2021 hanya pakai 1 saja yaitu target/real*100% ==> itu untuk KPI BPPU saja ya mas. yang lainnya pakai rumus normal real/target*100
						*/
						
						$realisasi2 =  $row['realisasi2'];
						$score2 =  $row['score2'];
						
						// realisasi 0 = input, 1 = rumus score/bobot, 2 = rata2, 3 = penjumlahan
						if ($realisasi2==1) {
							$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
							$score = $nilai_child['score'];							
							//$score = round($score,2);
							$realisasi = ($score/$bobot) * 100;
							$penginput = "-";
							
						} else if ($realisasi2==2) { // rata2 dibawahnya
							$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
							//$score = round($score,2);
							$score = $nilai_child['score'];
							$realisasi = $nilai_child['realisasi_rata2'];
							$penginput = "-";
							
						} else if ($realisasi2==3) { // penjumlahan bawahnya
							$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
							$score = $nilai_child['score'];
							$realisasi = $nilai_child['realisasi'];
							$penginput = "-";
							
						} 
						
						if ($terbalik==1) {
							if ($realisasi<=0) {
								$ach = 0;
							} else if ( $target <=0) {
								$ach = 0;
							} else {
								$ach = ( $target / $realisasi ) * 100;
							}
						} else {
							if ($realisasi<=0) {
								$ach = 0;
							} else if ( $target <=0) {
								$ach = 0;
							} else {
								$ach = ( $realisasi / $target ) * 100;
							}
						}	
						
						if ($ach<$batas_cap1) {
							$cap = $batas_cap1;
						} else if ($ach>$batas_cap2) {
							$cap = $batas_cap2;
						} else {
							$cap = $ach;
						}
					
						// score 0 = rumus, 1 = jumlah
						if ($score2==1){
							$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
							$score = $nilai_child['score'];
						} else {
							$score = ($bobot * $cap)/ 100;
						}	
							
						//$score = round($score,2);
						?>
						<tr style="background-color:#99FFCC;">
							<td><?php echo $i.".".$j; ?></td>
							<td><?php echo $row['nama_kpi']; ?></td>
							<td align="center"><?php echo $row['satuan']; ?></td>
							<td align="center"><?php echo number_format($bobot,2,",","."); ?></td>
							<td align="center"><?php echo number_format($row['target'],2,",","."); ?></td>
							<td align="center"><?php echo number_format($realisasi,2,",","."); ?></td>
							<td align="center"><?php echo number_format($ach,2,",","."); ?> %</td>
							<td align="center"><?php echo number_format($cap,2,",","."); ?> %</td>
							<td align="center"><?php echo number_format($score,2,",","."); ?></td>
							<td align="center"><?php echo $status; ?></td>
							<td align="center"><?php echo $penginput; ?></td>
						</tr>
					<?php
					
						child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
							
						$j++;
					}
				   
				   
					$i++;
				}
					
				$total_arr = hitungtotal($id_organisasi, $id_periode, $id_status, $tahun);	
				$total_bobot = $total_arr['bobot'];
				$total_score = $total_arr['score'];		
				
			}
			
			?>
        </tbody>
        <tfoot>	
        	  <tr align="center">
            	<td>&nbsp;</td>
                <td>Total</td>
                <td>&nbsp;</td>
                <td><?php echo number_format($total_bobot,2,",",".");?></td>
                <td>&nbsp;</td>
               <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?php echo number_format($total_score,2,",",".");?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>        
        </tfoot>
    </table>
                        
<?php   
}

function tampil_data(){
	global $conn;
	
	if ($_SESSION['km_profil']==1) {
		
		$qry1 = mysqli_query($conn,"delete from kpi_setting
									where id_organisasi = '".$_REQUEST['organisasi']."' and tahun = '".$_REQUEST['tahun']."'
									and id_periode = '".$_REQUEST['periode']."'");
															
		$qry = mysqli_query($conn,"insert into kpi_setting (id_organisasi, tahun, id_periode, tampil, tgl_input, id_user_input, ip_input)
									values ('".$_REQUEST['organisasi']."', '".$_REQUEST['tahun']."', '".$_REQUEST['periode']."', '".$_REQUEST['tampil']."',
									now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ");
									
		if($qry){
			$msg = "sukses";
		}else{
			$msg = "gagal";
		}
	
		echo $msg;
	}
}
  
?>