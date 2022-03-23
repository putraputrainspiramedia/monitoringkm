<?php
require_once("inc/conn.php");
require_once("./cek_priv.php");
require_once("inc/fungsi.php");

switch($_REQUEST['tp']) {
	case 'input':
		if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) {
			input_data();
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
                <th><b>Nama Organisasi</b></th>
                <?php 
				  $qry_periode = mysqli_query($conn,"select id_profil, nama_profil from user_profil order by nama_profil");
				$jml_periode = 0;
				$arr_periode = array();
				while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
					$arr_periode[$jml_periode] = $dt_periode['id_profil'];
					$jml_periode++; 
					?>
                	<th><b><?php echo $dt_periode['nama_profil'];?></b></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php			
			
			 $stmt = mysqli_query($conn,"select id_organisasi, nama_organisasi
                                     from p_organisasi order by nama_organisasi ");
			$i = 1;
			while ($row = mysqli_fetch_array($stmt)) {		
				$id_organisasi = $row['id_organisasi'];	
				
				?>
                <tr bgcolor="#CCCCCC">
					<td><b><?php echo $i; ?></b></td>
                    <td><b><?php echo $row['nama_organisasi']; ?></b></td>
                    <?php for($ii=0;$ii<$jml_periode;$ii++) { ?>
                    	<td  align="center"><?php 
							cekstatus($id_organisasi, $arr_periode[$ii]);
							?></td>
                    <?php } ?>         
                </tr>
                        
            <?php
			   
				$i++;
			}
			?>
        </tbody>
    </table>
                        
<?php   
}

function cekstatus($id_organisasi, $id_profil) {
	global $conn;
	
	$qry = mysqli_query($conn,"select tampil from user_profil_organisasi 
								where id_organisasi = '".$id_organisasi."' and id_profil = '".$id_profil."' ");

	$dt = mysqli_fetch_array($qry);
	$tampil = $dt['tampil'];
	if ($tampil==1) {
		$img = "<img src='img/tick-icon.png' />";
	} else {
		$img = "<img src='img/cross-icon.png' />";
	}
	echo $img;
	
}

function input_data(){	
	global $conn;
	
	$stmt_del = mysqli_query($conn, "delete from user_profil_organisasi");
	
	$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
								from p_organisasi a
								order by a.id_group");
	while ($dt_pil = mysqli_fetch_array($qry_pil)) {
		$id_organisasi = $dt_pil['id_organisasi'];
		
		$organisasi = $_REQUEST['organisasi_'.$id_organisasi];	
	
		$qry_periode = mysqli_query($conn,"select id_profil, nama_profil from user_profil");
		while ($dt_periode = mysqli_fetch_array($qry_periode)) { 
			$id_profil = $dt_periode['id_profil'];
			
			$cek = $_REQUEST['cek_'.$id_organisasi.'_'.$id_profil];
										
			$stm = "insert into user_profil_organisasi (id_organisasi, id_profil, tampil, 
					tgl_input, id_user_input, ip_input)
					values ('".$id_organisasi."', '".$id_profil."', '".$cek."',
					now(), '".$_SESSION['km_user']."', '".$_SERVER['REMOTE_ADDR']."') ";
			$qry = mysqli_query($conn,$stm);
		}
	}	
	
	if($qry){
		$msg .="Sukses setting user profil";
	}else{
		$msg .="Gagal setting user profil";
	}
	

	echo $msg;
	
	/*echo "<script>alert('".$msg."');window.location.href='index.php?fl=manajemenuser'</script>";
	*/
}
?>