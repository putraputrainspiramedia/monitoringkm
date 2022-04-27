<?php 
/** Error reporting */
//session_start();
error_reporting(0); 
//error_reporting(E_ALL);
//require_once('./cek_priv.php');
require_once('./inc/conn.php');
require_once('./homefungsi.php');
//require_once('./inc/fungsi.php');
//require_once("./cek_priv.php");

/** Include path **/
//ini_set('include_path', ini_get('include_path').';Classes/');

/** PHPExcel */
//require_once './Classes/PHPExcel.php';
//require_once('./Classes/PHPExcel/IOFactory.php');

/** PHPExcel_Writer_Excel2007 */
//include './Classes/PHPExcel/Writer/Excel2007.php';

//require_once './Classes/PHPExcel/Cell/AdvancedValueBinder.php';



//set_time_limit(0);
ini_set('memory_limit', '512M');


if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once('./Classes/PHPExcel/IOFactory.php');



function  child($id_bsc_perspective, $parent, $i, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2){
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
		
	$query = mysqli_query($conn,$stmt);
	//$jml = mysqli_num_rows($query);
	
	//$jno = 1;
	$z=0;
	$arr_nama_kpi = array();
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
			
		//$score = round($score,3);
		
		$arr_nama_kpi[$z] = $row['nama_kpi']."###".$row['satuan']."###".$row['bobot']."###".$row['target']."###".$realisasi."###".$ach."###".$cap."###".$score."###".$row['terbalik']."###".$penginput."###".$row['id_kpi']."###".$status;
		$z++;
	}
	
	return $arr_nama_kpi;
	
}


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Andex Teddy / exnome@gmail.com")
							 ->setLastModifiedBy("Andex Teddy / exnome@gmail.com")
							 ->setTitle("KM")
							 ->setSubject("KM")
							 ->setDescription("KM")
							 ->setKeywords("KM")
							 ->setCategory("KM");

$id_organisasi = $_REQUEST['organisasi'];
$id_periode = $_REQUEST['periode'];
$id_organisasi = $_REQUEST['organisasi'];
//$id_status = $_REQUEST['status'];
//$id_unit = $_REQUEST['unitx'];
//$id_status = $_COOKIE['cookie_kmtelkom'];
/*if (empty($id_status)) {
	$id_status = "3";
}*/
/*
1	Prognose
3	Rekon
*/
/*
$qry_cek = mysqli_query($conn,"select id_status
								from kpi_realisasi 
								where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'
								and id_status = '1'");
$jml_prognose = mysqli_num_rows($qry_cek);

$qry_cek2 = mysqli_query($conn,"select id_status
								from kpi_realisasi 
								where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'
								and id_status = '3'");
$jml_rekon = mysqli_num_rows($qry_cek2);

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
*/

			
if (empty($_REQUEST['tahun'])) {
	$tahun = date("Y");
} else {
	$tahun = $_REQUEST['tahun'];
}

$stmt_status = mysqli_query($conn,"select nama_organisasi from p_organisasi where id_organisasi = '".$id_organisasi."'");
$row_status = mysqli_fetch_array($stmt_status);
$nama_organisasi = $row_status['nama_organisasi'];

/*
$stmt_status = mysqli_query($conn,"select nama_status from p_status where id_status = '".$id_status."'");
$row_status = mysqli_fetch_array($stmt_status);
$status = $row_status['nama_status'];
*/

$stmt_status = mysqli_query($conn,"select singk_periode from p_periode where id_periode = '".$id_periode."'");
$row_status = mysqli_fetch_array($stmt_status);
$namaperiode = $row_status['singk_periode'];

$active_sheet = 0;				
$rowscounter = 1;

$objPHPExcel->createSheet($active_sheet); 
$objPHPExcel->setActiveSheetIndex($active_sheet);
$objPHPExcel->getActiveSheet()->setTitle('KPI');

$i=0;
$j=1;

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Indicator');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Unit');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Weight');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Target');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Realisasi');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Ach');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Cap');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Score');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Status');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Penginput');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
//$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => '999999'
        )
    ));
	
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A1'), 'B1:K1' );	


$qry = mysqli_query($conn,"select tampil from kpi_setting 
							where id_organisasi = '".$id_organisasi."' and id_periode = '".$id_periode."' and tahun = '".$tahun."'");
$data = mysqli_fetch_array($qry);
$status_tampil = $data['tampil'];

$profil_arr = get_profilorg($id_organisasi);
$profil_arr = get_profilorg($id_organisasi);

if ($status_tampil==1 and $profil_arr['tampil']==1) {

	
	$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, cap, cap2
									from p_bsc_perspective ");
	
	$ii=1;
	while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {		
		$batas_cap1 = $row_bsc['cap'];
		$batas_cap2 = $row_bsc['cap2'];
			
		$i=0;
		$j++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ii);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row_bsc['nama_bsc_perspective']);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
		$total_arr = hitungtotalbsc($id_organisasi, $id_periode, $id_status, $tahun, $row_bsc['id_bsc_perspective']);	
		$total_bobot = $total_arr['bobot'];
		$total_score = $total_arr['score'];	
									
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,  number_format($total_bobot,2,",","."));
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($total_score,2,",","."));
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		
	
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				 'rgb' => '009999'
			)
		));
		$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	
	
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
		
		$jj = 1;
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
				
			
			$i=0;
			$j++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ii.". ".$jj);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['nama_kpi']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['satuan']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($row['bobot'],2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($row['target'],2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($realisasi,2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($ach,2,",",".").' %');
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($cap,2,",",".").' %');
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($score,2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $status);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $penginput);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			
			//$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					 'rgb' => '99FFCC'
				)
			));
			$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	
	
			
			$arr_child = child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2);
			$jml_child = count($arr_child);
			
			if($jml_child>0) {
				$kk = 1;
				for ($xc=0;$xc<$jml_child;$xc++) {
					$arr_child_pecah = explode("###",$arr_child[$xc]);
					// $arr_nama_kpi[$z] = $row['nama_kpi']."###".$row['satuan']."###".$row['bobot']."###".$row['target']."###".$row['realisasi']."###".$ach."###".$cap."###".$score."###".$row['terbalik'];
					$i=0;
					$j++;
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ii.". ".$jj. ". ".$kk);
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[0]);
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[1]);
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[2],2,",","."));
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[3],2,",","."));
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[4],2,",","."));
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					
					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[5],2,",",".").' %');
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[6],2,",",".").' %');
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah[7],2,",","."));
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[8]);
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[9]);
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							 'rgb' => 'CCCCCC'
						)
					));
					$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	
					
					
					
					$arr_child2 = child($row['id_bsc_perspective'], $arr_child_pecah[10], $i.".".$j.".".$kk, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2);
					$jml_child2 = count($arr_child2);
					
					if($jml_child2>0) {
						$kk2 = 1;
						for ($xc2=0;$xc2<$jml_child2;$xc2++) {
							$arr_child_pecah2 = explode("###",$arr_child2[$xc2]);
							// $arr_nama_kpi[$z] = $row['nama_kpi']."###".$row['satuan']."###".$row['bobot']."###".$row['target']."###".$row['realisasi']."###".$ach."###".$cap."###".$score."###".$row['terbalik'];
							$i=0;
							$j++;
							
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ii.". ".$jj. ". ".$kk. ". ".$kk2);
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah2[0]);
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah2[1]);
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[2],2,",","."));
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[3],2,",","."));
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[4],2,",","."));
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[5],2,",",".").' %');
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[6],2,",",".").' %');
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah2[7],2,",","."));
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah2[8]);
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah2[9]);
							
							$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'startcolor' => array(
									 'rgb' => 'FFFFFF'
								)
							));
							$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	
							
							
							$arr_child3 = child($row['id_bsc_perspective'], $arr_child_pecah2[10], $i.".".$j.".".$kk.".".$kk2, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2);
							$jml_child3 = count($arr_child3);
							
							if($jml_child3>0) {
								$kk3 = 1;
								for ($xc3=0;$xc3<$jml_child3;$xc3++) {
									$arr_child_pecah3 = explode("###",$arr_child3[$xc3]);
									// $arr_nama_kpi[$z] = $row['nama_kpi']."###".$row['satuan']."###".$row['bobot']."###".$row['target']."###".$row['realisasi']."###".$ach."###".$cap."###".$score."###".$row['terbalik'];
									$i=0;
									$j++;
									
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ii.". ".$jj. ". ".$kk. ". ".$kk2. ". ".$kk3);
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah3[0]);
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah3[1]);
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[2],2,",","."));
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[3],2,",","."));
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[4],2,",","."));
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[5],2,",",".").' %');
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[6],2,",",".").' %');
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($arr_child_pecah3[7],2,",","."));
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah3[8]);
									$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
									$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah3[9]);
									
									$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'startcolor' => array(
											 'rgb' => 'FFFFFF'
										)
									));
									$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	
									
									$kk3++;
								}
								
							}
							
							
							
							$kk2++;
						}
						
					}
					
					
					
					
					$kk++;
				}
				
			}
			
					
			$jj++;
		}
	   
		$ii++;
	}

}

$total_arr = hitungtotal($id_organisasi, $id_periode, $id_status, $tahun);	
$total_bobot = $total_arr['bobot'];
$total_score = $total_arr['score'];		

$i=0;
$j++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Total');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j,  number_format($total_bobot,2,",","."));
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;


$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;		
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($total_score,2,",","."));
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFill()->applyFromArray(array(
	'type' => PHPExcel_Style_Fill::FILL_SOLID,
	'startcolor' => array(
		 'rgb' => '009966'
	)
));
$objPHPExcel->getActiveSheet()->duplicateStyle( $objPHPExcel->getActiveSheet()->getStyle('A'.$j), 'B'.$j.':K'.$j.'' );	


$styleArray = array( 'borders' => array( 'allborders' => array( 
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => '00000000'), ), ), );

$objPHPExcel->getActiveSheet()->getStyle('A1:K'.$j)->applyFromArray($styleArray);
								
								
			
		
$tgl = date("Ymd_His");
$filename = "KPI_".$nama_organisasi."_".$namaperiode.$tahun;


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
//header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
