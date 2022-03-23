<?php 
/** Error reporting */
//session_start();
#error_reporting(0); 
//error_reporting(E_ALL);
//require_once('./cek_priv.php');
require_once('./inc/conn.php');
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


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Andex Teddy / exnome@gmail.com")
							 ->setLastModifiedBy("Andex Teddy / exnome@gmail.com")
							 ->setTitle("Summary Realisasi")
							 ->setSubject("Summary Realisasi")
							 ->setDescription("Summary Realisasi")
							 ->setKeywords("Summary Realisasi")
							 ->setCategory("Summary Realisasi");


//$id_organisasi = $_REQUEST['organisasi'];
$id_status = $_REQUEST['status'];
//$id_unit = $_REQUEST['unitx'];
//$id_periode = $_REQUEST['periode'];
$id_kpi = $_REQUEST['kpi'];
//$id_group = $_REQUEST['group'];


if (empty($_REQUEST['tahun'])) {
	$tahun = date("Y");
} else {
	$tahun = $_REQUEST['tahun'];
}


$stmt_status = mysqli_query($conn,"select nama_status from p_status where id_status = '".$id_status."'");
$row_status = mysqli_fetch_array($stmt_status);
$status = $row_status['nama_status'];

$stmt_status = mysqli_query($conn,"select nama_kpi from kpi where id_kpi = '".$id_kpi."'");
$row_status = mysqli_fetch_array($stmt_status);
$namakpi = $row_status['nama_kpi'];

$active_sheet = 0;				
$rowscounter = 1;

$objPHPExcel->createSheet($active_sheet); 
$objPHPExcel->setActiveSheetIndex($active_sheet);
$objPHPExcel->getActiveSheet()->setTitle('KPI');

$i=0;
$j=1;

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Teritory');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
$jml_periode = 0;
$arr_periode = array();
$tot = array();
while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
	$arr_periode[$jml_periode] = $dt_periode['id_periode'];
	$tot[$jml_periode] = 0;
	
	$jml_periode++; 
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $dt_periode['singk_periode']);
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
}


$stmt = "select id_organisasi, nama_organisasi
					from p_organisasi
					where id_group = '1'
					order by id_organisasi";
			
//echo "<pre>$stmt</pre>";
$query = mysqli_query($conn,$stmt);

$j++;
while ($row = mysqli_fetch_array($query)){
	$i=0;
	$id_organisasi = $row['id_organisasi'];
	$profil_arr = get_profilorg($id_organisasi);
	
	if ($profil_arr['tampil']==1) {		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['nama_organisasi']);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		
		for($ii=0;$ii<$jml_periode;$ii++) {
			$nilai = hitungreal($id_organisasi, $id_status, $tahun, $arr_periode[$ii], $id_kpi);
			$tot[$ii] = $tot[$ii] + $nilai;	
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($nilai,2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		}	
		$j++;
	}
}

$i=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'NAS');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

for($ii=0;$ii<$jml_periode;$ii++) {
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($tot[$ii],2,",","."));
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
}	
$j++;	


$i=0;
$j++;

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Teritory');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
$jml_periode = 0;
$arr_periode = array();
$tot2 = array();
while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
	$arr_periode[$jml_periode] = $dt_periode['id_periode'];
	$tot2[$jml_periode] = 0;
	
	$jml_periode++; 	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $dt_periode['singk_periode']);
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
}


$stmt = "select id_organisasi, nama_organisasi
		from p_organisasi
		where id_group = '2'
		order by id_organisasi";
			
//echo "<pre>$stmt</pre>";
$query = mysqli_query($conn,$stmt);

$j++;
$tot = array();
while ($row = mysqli_fetch_array($query)){
	$i=0;
	$id_organisasi = $row['id_organisasi'];	
	$profil_arr = get_profilorg($id_organisasi);
	
	if ($profil_arr['tampil']==1) {
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['nama_organisasi']);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		
		for($ii=0;$ii<$jml_periode;$ii++) {
			$nilai = hitungreal($id_organisasi, $id_status, $tahun, $arr_periode[$ii], $id_kpi);
			$tot2[$ii] = $tot2[$ii] + $nilai;	
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($nilai,2,",","."));
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		}	
		$j++;
	}
}

$i=0;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'TReg 4');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

for($ii=0;$ii<$jml_periode;$ii++) {
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, number_format($tot2[$ii],2,",","."));
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
}	
$j++;


$tgl = date("Ymd_His");
$filename = "realisasi_".$namakpi."_".$tahun;


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
