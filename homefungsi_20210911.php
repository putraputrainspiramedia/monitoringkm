<?php

function hitungchild($id_bsc_perspective, $parent, $i, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, $level) {
	global $conn;
	
	 $stmt = "select a.id_kpi, a.nama_kpi, 
			IFNULL(a.rumus,'0') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan, IFNULL(a.rata2,'0') rata2,
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
	
	$total_bobot = 0;
	$total_score = 0;
	$total_realisasi = 0;
	$x=0;	
	while ($row = mysqli_fetch_array($query)){
		$x++;
		
		$terbalik = $row['terbalik'];
		$realisasi_prognose =  $row['realisasi_prognose'];
		$realisasi_rekon =  $row['realisasi_rekon'];
		$target =  $row['target'];
		$penginput_prognose =  $row['penginput_prognose'];
		$penginput_rekon =  $row['penginput_rekon'];
									
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
		
		/*if ($terbalik==1) {
			if ($realisasi<=0) {
				$ach = 0;
			} else if ( $target <=0) {
				$ach = 0;
			} else {
				$ach = ( $target / $realisasi) * 100;
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
		
		$bobot = $row['bobot'];
		$score = ($bobot * $cap) / 100;
		$rumus = $row['rumus'];
		if ($rumus==1) {
			$realisasi = ($score/$bobot) * 100;
			$penginput = "-";
		}*/
		
		$nilai_child = hitungchild($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $id_status, $tahun, $id_periode, $status, $batas_cap1,$batas_cap2, '1');
		
		//$bobot = $row['bobot'];
		$rata2 = $row['rata2'];
		$rumus = $row['rumus'];
		if ($rumus==1) {
			$bobot = $nilai_child['bobot'];
			$score = $nilai_child['score'];
			if ($bobot == 0 or $score == 0) {
				$realisasi = 0;
			} else {
				$realisasi = ($score/$bobot) * 100;
			}
			$penginput = "-";
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
		
		
		} else {						
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
			$bobot = $row['bobot'];
			$score = ($bobot * $cap)/ 100;
		}
		$total_realisasi = $total_realisasi + $realisasi;
		$total_bobot = $total_bobot + $bobot;
		$total_score = $total_score + $score;
		
	}
	$rata2_realisasi = ($total_realisasi / $x);
	$nilai = array("bobot"=>$total_bobot, "score"=>$total_score, "realisasi"=>$rata2_realisasi);
	return $nilai;
	
}

function hitungtotal($id_organisasi, $id_periode, $id_status, $tahun){
	global $conn;
	
	$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, cap, cap2
											from p_bsc_perspective ");
	$i = 1;
	$total_bobot = 0;
	$total_score = 0;
	$total_arr = array();
		
	while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {		
		$batas_cap1 = $row_bsc['cap'];
		$batas_cap2 = $row_bsc['cap2'];
		
	 	$stmt = "select a.id_kpi, a.nama_kpi, 				
			IFNULL(a.rumus,'0') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan, IFNULL(a.rata2,'0') rata2,
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
				and z.id_status = '3' ), 0) realisasi_rekon
				 
				from kpi a
				left join kpi_organisasi b on a.id_kpi = b.id_kpi
				where a.id_kpi is not null and b.tahun = '".$tahun."' and b.id_organisasi = '".$id_organisasi."' 
				and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
				order by a.parent, a.urutan";
				
		//echo "<pre>$stmt</pre>";
		$query = mysqli_query($conn,$stmt);		
		while ($row = mysqli_fetch_array($query)) {
			
			$terbalik = $row['terbalik'];
			$realisasi_prognose =  $row['realisasi_prognose'];
			$realisasi_rekon =  $row['realisasi_rekon'];
			$target =  $row['target'];			
								
			if ($realisasi_prognose==0 and $realisasi_rekon==0) {
				$realisasi = "";
			} else if ($realisasi_prognose>=1 and $realisasi_rekon==0) {
				$realisasi = $realisasi_prognose;
			} else if ($realisasi_prognose==0 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else if ($realisasi_prognose>=1 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else {
				$realisasi = "";
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
			$score = ($row['bobot'] * $cap) / 100;
			
			$total_bobot = $total_bobot + $row['bobot'];
			$total_score = $total_score + $score;
		
		}
	}
	
	$total_arr = array("bobot"=>$total_bobot, "score"=>$total_score);
	return $total_arr;
	
}     
 
function hitungtotalbsc($id_organisasi, $id_periode, $id_status, $tahun, $id_bsc_perspective){
	global $conn;
	
	$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, cap, cap2
											from p_bsc_perspective 
											where id_bsc_perspective = '".$id_bsc_perspective."'");
	$i = 1;
	$total_bobot = 0;
	$total_score = 0;
	$total_arr = array();
		
	while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {		
		$batas_cap1 = $row_bsc['cap'];
		$batas_cap2 = $row_bsc['cap2'];
		
	 	$stmt = "select a.id_kpi, a.nama_kpi, 
				IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan,
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
				and z.id_status = '3' ), 0) realisasi_rekon
				 
				from kpi a
				left join kpi_organisasi b on a.id_kpi = b.id_kpi
				where a.id_kpi is not null and b.tahun = '".$tahun."' and b.id_organisasi = '".$id_organisasi."' 
				and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = 0
				order by a.parent, a.urutan";
				
		//echo "<pre>$stmt</pre>";
		$query = mysqli_query($conn,$stmt);		
		
		$bobotx = 0;
		$scorex = 0;
		while ($row = mysqli_fetch_array($query)) {
			
			$terbalik = $row['terbalik'];
			$realisasi_prognose =  $row['realisasi_prognose'];
			$realisasi_rekon =  $row['realisasi_rekon'];
			$target =  $row['target'];			
								
			if ($realisasi_prognose==0 and $realisasi_rekon==0) {
				$realisasi = "";
			} else if ($realisasi_prognose>=1 and $realisasi_rekon==0) {
				$realisasi = $realisasi_prognose;
			} else if ($realisasi_prognose==0 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else if ($realisasi_prognose>=1 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else {
				$realisasi = "";
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
			$score = ($row['bobot'] * $cap) / 100;
			
			$bobotx = $bobotx + $row['bobot'];
			$scorex = $scorex + $score;
		
		}
		
		$total_bobot = $total_bobot + $bobotx;
		$total_score = $total_score + $scorex;
			
	}
	
	$total_arr = array("bobot"=>$total_bobot, "score"=>$total_score);
	return $total_arr;
	
}  

  
function totalscore(){
	global $conn;
	
	$id_organisasi = $_REQUEST['organisasi'];
	//$id_status = $_REQUEST['status'];
	//$id_unit = $_REQUEST['unitx'];
	$id_periode = $_REQUEST['periode'];
	$id_status = $_COOKIE['cookie_kmtelkom'];
	
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}
	
	$stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective, cap, cap2
											from p_bsc_perspective ");
	$i = 1;
	$total_bobot = 0;
	$total_score = 0;
	$total_arr = array();
		
	while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {		
		$batas_cap1 = $row_bsc['cap'];
		$batas_cap2 = $row_bsc['cap2'];
		
	 	$stmt = "select a.id_kpi, a.nama_kpi, 
				IFNULL(a.rumus,'') rumus, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan,
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
				and z.id_status = '3' ), 0) realisasi_rekon
				 
				from kpi a
				left join kpi_organisasi b on a.id_kpi = b.id_kpi
				where a.id_kpi is not null and b.tahun = '".$tahun."' and b.id_organisasi = '".$id_organisasi."' 
				and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
				order by a.parent, a.urutan";
				
		//echo "<pre>$stmt</pre>";
		$query = mysqli_query($conn,$stmt);		
		while ($row = mysqli_fetch_array($query)) {
			
			$terbalik = $row['terbalik'];
			$realisasi_prognose =  $row['realisasi_prognose'];
			$realisasi_rekon =  $row['realisasi_rekon'];
			$target =  $row['target'];			
								
			if ($realisasi_prognose==0 and $realisasi_rekon==0) {
				$realisasi = "";
			} else if ($realisasi_prognose>=1 and $realisasi_rekon==0) {
				$realisasi = $realisasi_prognose;
			} else if ($realisasi_prognose==0 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else if ($realisasi_prognose>=1 and $realisasi_rekon>=1) {
				$realisasi = $realisasi_rekon;
			} else {
				$realisasi = "";
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
			$score = ($row['bobot'] * $cap) / 100;
			
			$total_bobot = $total_bobot + $row['bobot'];
			$total_score = $total_score + $score;
		
		}
	}
	
	echo "Total Score:".number_format($total_score,2,",",".");
	
} 

?>