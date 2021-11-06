<?php
ini_set('max_execution_time', 600);
date_default_timezone_set('Asia/Jakarta');
//set_time_limit(600);
//require_once("conn.php");


function chiper($enkrip, $chiper, $kondisi, &$dekrip) {
	$val_chiper = 0;
	
	for ($x=0;$x<strlen($chiper);$x++) {
		$val_chiper = $val_chiper + ord(substr($chiper,$x,1)) + 13;
	}
	
	$dekrip = "";
	for ($x=0;$x<strlen($enkrip);$x++) {
		$j = $x + 1;
		if ($kondisi) {
			$val_enkrip = ord(substr($enkrip,$x,1)) + $val_chiper + ($j * $j);
			while ($val_enkrip>255) {
				$val_enkrip = $val_enkrip - 255;
			} 
		} else {
			$val_enkrip = ord(substr($enkrip,$x,1)) - $val_chiper - ($j * $j);
			while ($val_enkrip<=0) {
				$val_enkrip = $val_enkrip + 255;
			}
		}
		$dekrip .= chr($val_enkrip);
	}
	
	return $dekrip;
}


//anti sql injeksi/ hack
function antisqlinjection($value){
	// Stripslashes
	if 	(get_magic_quotes_gpc()){
		$value = stripslashes($value);
	}
	/*
	if (!is_numeric($value)){
		$value = mysql_real_escape_string($value);
	}*/
	return $value;
}

// anti cross site scripting
function anti_xss($data){ 
	$xss=stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES))); 
	return $xss; 
} 



function enkripsi($data)
{
  $enkrip1=base64_encode($data);
  $enkrip2=$enkrip1."_kMt3lK0m";
  $enkrip3=base64_encode($enkrip2);
	
  return $enkrip3;
}

function dekripsi($data)
{
  $dekrip1=base64_decode($data);
  $dekrip2=explode("_kMt3lK0m",$dekrip1);
  $dekrip3=base64_decode($dekrip2[0]);
  
  return $dekrip3;
}


function uploadImage($img_name,$img_temp,$vdir_upload,$width, $height){
	//header("Content-type: image/jpeg");
	
	//direktori gambar
	//vdir_upload = "img/";
	//$vfile_upload = $vdir_upload . $img_name;
	
	$vfile_upload = $vdir_upload;
	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($img_temp, $vfile_upload);
	
	//identitas file asli
	$im_src = imagecreatefromjpeg($vfile_upload);
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);
	
	//Simpan dalam versi small 110 pixel
	//set ukuran gambar hasil perubahan
	$dst_width = $width;
	//$dst_height = ($dst_width/$src_width)*$src_height;
	$dst_height = $height;
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
	
	//Simpan gambar
	imagejpeg($im,$vdir_upload,60);
	imagedestroy($im);
	
	//$im = imagecreatetruecolor(120, 20);
	//$text_color = imagecolorallocate($im, 233, 14, 91);
	//imagestring($im, 1, 5, 5,  'WebP with PHP', $text_color);
	
	// Save the image
	//imagewebp($im, $vdir_upload.".webp");
	
	//$file='hnbrnocz.jpg';
	$image=  imagecreatefromjpeg($vfile_upload);
	ob_start();
	imagejpeg($image,NULL,10);
	$cont=  ob_get_contents();
	ob_end_clean();
	imagedestroy($image);
	$content =  imagecreatefromstring($cont);
	
	$vdir_upload2 = str_replace(".jpg",".webp",$vdir_upload);
	imagewebp($content,$vdir_upload2,10);
	imagedestroy($content);

	// Free up memory
	//imagedestroy($im);
	//imagedestroy($im_src);
	//unlink($vfile_upload);
}

function cek_priuser($fl){
	global $conn;
	
	$qry_menu = mysqli_query($conn,"select a.id_menu, b.nama_menu, b.link, a.is_input, a.is_edit, a.is_delete, a.is_view
									from user_profil_menu a
									inner join user_menu b on a.id_menu = b.id_menu
									where a.id_profil = '".$_SESSION['km_profil']."' and b.is_active = '1' and b.fl = '".$fl."'");
	$menu_arr = array();								
	while ($dt_menu = mysqli_fetch_array($qry_menu)) {
		$menu_arr = array("input"=>$dt_menu['is_input'],"edit"=>$dt_menu['is_edit'],
						"delete"=>$dt_menu['is_delete'],"view"=>$dt_menu['is_view']);
	}
	
	return $menu_arr;
}

function get_profiluser(){
	global $conn;
	$qry_menu = mysqli_query($conn,"select a.id_profil, a.id_unit, a.id_organisasi, b.id_group
									from user_app a
									inner join p_organisasi b on a.id_organisasi = b.id_organisasi
									where a.id_user = '".$_SESSION['km_user']."'");
	$menu_arr = array();								
	while ($dt_menu = mysqli_fetch_array($qry_menu)) {
		$menu_arr = array("profil"=>$dt_menu['id_profil'],"unit"=>$dt_menu['id_unit'],
						"organisasi"=>$dt_menu['id_organisasi'],"group"=>$dt_menu['id_group']);
	}
	
	return $menu_arr;
	
}


?>