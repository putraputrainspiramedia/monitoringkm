<?php
require_once("./cek_priv.php");

	switch($_REQUEST['tp']) {
		case 'input':
			if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) {
				input_data();
			}
			break;
		default :
			list_data();
			break;
	}


function list_data() {
	global $conn;
	
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}

	//$menu_arr = cek_priuser($_REQUEST['fl']);
	
	if ($_SESSION['km_profil']==1) {
		$where_unit = "";
		$where_group = "";
	} else {
		$user_profil_arr = get_profiluser();
		$where_unit = " and id_user = '".$user_profil_arr['unit']."' ";
		$where_group = " where a.id_group = '".$user_profil_arr['group']."'";
	}
	
	
	
?>
	<script language="javascript">
		
		//var table = $('#datable_1').DataTable();
		$(document).ready(function(){
			$('#loading_dealer').hide();
			$('#hasil').html('');
			
			 $.ajax({
				type		: "POST",
				url			: "./manajemenuserdata.php",
				data:{
					tp : 'view',
				},
				cache:false,
				beforeSend:function(){
					$('#loading_dealer').show();
					$("#hasil").html("");					
				},
				success		: function(msg){
					
					$('#loading_dealer').hide();
					$("#hasil").fadeIn('1000');
					$('#hasil').html(msg);
				},  
				error 		: function() { 
					$('#loading_dealer').hide(); 
					alert("error");  
				}  
			});
			
			
			$("#kirim").click(function(){    // 2n
				$('#hasil').hide();
				$('#loading').show();
						
				$.ajax({
					type		: "POST",
					url			: "./manajemenuserdata.php",
					data:{
						tp : 'view',
					},
					cache:false,
					beforeSend:function(){
						$('#loading_dealer').show();
						$("#hasil").html("");
					},
					success		: function(msg){
						$('#loading_dealer').hide();
						$("#hasil").fadeIn('1000');
						$('#hasil').html(msg);
					},  
					error 		: function() { 
						$('#loading_dealer').hide(); 
						alert("error");  
					}  
				});
			});
			
			 <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
			$("#edit").click(function () {
           		window.location.href='index.php?fl=manajemenuser&tp=input';
       		});
			<?php } ?>
			
		});
					
    </script>

<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Manajemen User</h6>
		</div>
		<div class="card-body"> 
			
            <form method="post" action="">
		    <div class="row">
            	<div class="col-sm">
						<div class="form-row align-items-center">
                            <div class="col-md-6 form-group">			
                                 <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
                            	 <button type="button" class="btn btn-warning" id="edit">Ubah</button>
                                 <?php } ?> 
							</div>
                            <div class="col-md-6 form-group">
                            
                            </div>
						</div>
				</div>
			</div>
            </form>
            
    		<div class="row">
				<div class="col-sm">
                		<?php if ($_SESSION['km_user_view']==1) { ?>
                         <div id="loading_dealer" align="center">
                            <img src="img/loading.gif" />
                            <br />
                            Ambil Data User...
                        </div>
                        <div  id="hasil">
                        
                        </div>
                        <?php } ?>
					</div>
				</div>
			</div>
	
				
		</div> 
	</div> 
	</div> 
	
        
<?php } 


function input_data() {
	global $conn;
	
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}

	
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
			
			$("#simpan").click(function(){    
						
				$.ajax({
					type		: "POST",
					url			: "./manajemenuserdata.php",
					data		: $("#frm").serialize(),
					cache		: false,
					success		: function(msg){
						alert(msg);
						window.location.href='index.php?fl=manajemenuser';
					},  
					error 		: function() { 
						alert("error");  
					}  
				});
			});
			
			
		});
		
	</script>
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Manajemen User</h6>
		</div>
		<div class="card-body"> 
		   <form method="post" action="" enctype="multipart/form-data" id="frm">
            <input type="hidden" name="tp" value="input" readonly="readonly" />
          	<div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-12 form-group">
							
                            <table id="datable_1" class="table table-bordered table-striped table-sm w-100">
                            <thead class="thead-primary">
                                <tr align="center">
                                    <th><b>No</b></th>
                                    <th><b>KM</b></th>
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
						</div>
					</div>
				</div>
			</div>
            
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-4 form-group"><label>&nbsp;</label><br />
							<button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                            <button type="button" class="btn btn-danger mb-2" name="batal" onclick="window.history.back();">Batal</button>
						</div>
                        <div class="col-md-8 form-group">
							
						</div>
					</div>
				</div>
			</div>
			</form>
				
		</div> 
	</div> 
</div> 
	
<?php
} 



function cekstatus($id_organisasi, $id_profil) {
	global $conn;
								
	$qry = mysqli_query($conn,"select tampil from user_profil_organisasi 
								where id_organisasi = '".$id_organisasi."' and id_profil = '".$id_profil."'");

	$dt = mysqli_fetch_array($qry);
	$tampil = $dt['tampil'];
	if ($tampil==1) {
		$cek = "checked";
	} else {
		$cek = "";
	}
	?>
     <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="cek<?php echo $id_organisasi;?><?php echo $id_profil;?>" name="cek_<?php echo $id_organisasi;?>_<?php echo $id_profil;?>" value="1" <?php echo $cek;?>>	<label class="custom-control-label" for="cek<?php echo $id_organisasi;?><?php echo $id_profil;?>">&nbsp;</label>
    </div>
    
    <?php
	
}



?>