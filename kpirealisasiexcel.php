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

#set_time_limit(0);
ini_set('memory_limit', '512M');


if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Andex Teddy / exnome@gmail.com")
							 ->setLastModifiedBy("Andex Teddy / exnome@gmail.com")
							 ->setTitle("KPI Realisasi")
							 ->setSubject("KPI Realisasi")
							 ->setDescription("KPI Realisasi")
							 ->setKeywords("KPI Realisasi")
							 ->setCategory("KPI Realisasi");

$active_sheet = 0;				
$rowscounter = 1;
$nama_file = "";

$objPHPExcel->setActiveSheetIndex($active_sheet);
$objPHPExcel->getActiveSheet()->setTitle('KPI Realisasi');

$i=0;
$j=1;


$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'No');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'KPI');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, 'Satuan');
$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;

$qry_periode = mysqli_query($conn,"select id_periode, singk_periode from p_periode");
$jml_periode = 0;
$arr_periode = array();
while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
	$arr_periode[$jml_periode] = $dt_periode['id_periode'];
	$jml_periode++; 
	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $dt_periode['singk_periode']);
	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
}



if (empty($_REQUEST['tahun'])) {
	$tahun = date("Y");
} else {
	$tahun = $_REQUEST['tahun'];
}
$id_organisasi = $_REQUEST['organisasi'];
$profil_arr = get_profilorg($id_organisasi);

if ($profil_arr['tampil']==1) {
	
	$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, nilai
									from p_bsc_perspective ");
	$ino = 1;
	$arr_bobotbsc = array();
	$bobot_perbsc = 0;
	while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {			
		
		$i=0;
		$j++;
	
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ino);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row_bsc['nama_bsc_perspective']);
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
		$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			
		for($ii=0;$ii<$jml_periode;$ii++) { 
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		}
	
	   
	   $stmt = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(d.tgl_input,'%d-%m-%Y') tgl_input, 
				IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, d.tahun,
				(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child, a.satuan
				from kpi a
				inner join kpi_organisasi d on a.id_kpi = d.id_kpi
				left join user_app b on d.id_user_input = b.id_user
				where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
				and d.id_organisasi = '".$id_organisasi."' and d.tahun = '".$tahun."'
				order by a.parent, a.urutan";
		
		//echo "<pre>$stmt</pre>";
		$query = mysqli_query($conn,$stmt);
		
		$jno = 1;
		while ($row = mysqli_fetch_array($query)){
			$i=0;
			$j++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ino.". ".$jno);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['nama_kpi']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['satuan']);
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
	
			for($ii=0;$ii<$jml_periode;$ii++) { 
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
			}
			//child($row['id_bsc_perspective'], $row['id_kpi'], $ino.".".$jno, $row['id_organisasi'],$tahun, $jml_periode, $arr_periode, $i, $j);
			
			 $stmtx = "select a.id_kpi, a.nama_kpi, b.nama, DATE_FORMAT(d.tgl_input,'%d-%m-%Y') tgl_input, 
							IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, d.id_organisasi, d.tahun,	a.satuan,					
							(select count(*) from kpi x where x.parent = a.id_kpi ) jml_child
							from kpi a
							inner join kpi_organisasi d on a.id_kpi = d.id_kpi
							left join user_app b on d.id_user_input = b.id_user
							where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '".$row['id_kpi']."'
							and d.id_organisasi = '".$id_organisasi."' and d.tahun = '".$tahun."'
							order by a.parent, a.urutan";
						
			$queryx = mysqli_query($conn,$stmtx);
			
			$kno = 1;
			while ($rowx = mysqli_fetch_array($queryx)){
					
				$i=0;
				$j++;
				
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ino.". ".$jno. ". ".$kno);
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $rowx['nama_kpi']);
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $rowx['satuan']);
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
				for($ii=0;$ii<$jml_periode;$ii++) { 
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
				}
				$arr_child = child($rowx['id_bsc_perspective'], $rowx['id_kpi'], $ino.".".$j,$rowx['id_organisasi'], $tahun, $jml_periode, $arr_periode);
				
				$jml_child = count($arr_child);
				
				if($jml_child>0) {
					$lno = 1;
					for ($xc=0;$xc<$jml_child;$xc++) {
						$arr_child_pecah = explode("###",$arr_child[$xc]);
						
						$i=0;
						$j++;
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ino.". ".$jno. ". ".$kno. ". ".$lno);
						$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[0]);
						$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $arr_child_pecah[1]);
						$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
						for($ii=0;$ii<$jml_periode;$ii++) { 
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
						}
						$lno++;
						
					}
				}
				
				$kno++;
			}	
			$jno++;
		}
	   
	   
		$ino++;
	}

}

$tgl = date("Ymd_His");
$filename = "kpirealisasi".$tahun;
	
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



function child($id_bsc_perspective, $parent, $ino, $id_organisasi, $tahun, $jml_periode, $arr_periode){
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
	//$jml = mysqli_num_rows($query);
	
	//$jno = 1;
	$z=0;
	$arr_nama_kpi = array();
	while ($row = mysqli_fetch_array($query)){
			
		//$i=0;
		//$j++;
		
		//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $ino.". ".$jno);
		//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $row['nama_kpi']);
		//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		//for($ii=0;$ii<$jml_periode;$ii++) { 
			//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, '');
			//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);		$i++;
		//}
		//child($row['id_bsc_perspective'], $row['id_kpi'], $ino.".".$j,$row['id_organisasi'], $tahun, $jml_periode, $arr_periode, $i, $j);
		//$jno++;
		$arr_nama_kpi[$z] = $row['nama_kpi']."###".$row['satuan'];
		$z++;
	}
	
	return $arr_nama_kpi;
	
}
?>