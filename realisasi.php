<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
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
			
			$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			 $.ajax({
				type		: "POST",
				url			: "./realisasidata.php",
				data:{
					tp : 'view',
					kpi : $("#kpi").val(),
					tahun : $("#tahun").val(),
					status : $("#status").val(),
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
					url			: "./realisasidata.php",
					data:{
						tp : 'view',
						kpi : $("#kpi").val(),
						tahun : $("#tahun").val(),
						status : $("#status").val(),
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
			
			$("#excel").click(function () {
				window.location.href='realisasiexcel.php?tahun='+$("#tahun").val()+'&kpi='+$("#kpi").val()+'&status='+$("#status").val();
           	});
				
		});
					
    </script>

<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Summary Realisasi</h6>
		</div>
		<div class="card-body"> 
			
            
			<form method="post" action="">
            <div class="row">
            	<div class="col-sm">
						<div class="form-row align-items-center">
                        	<div class="col-md-2 form-group">
                            	<input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                             </div>   
							 <div class="col-md-4 form-group">
                            <select name="kpi" id="kpi" class="form-control">
                            	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_kpi, nama_kpi
																from kpi order by parent, urutan");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										echo "<option value='".$dt_pil['id_kpi']."'>".$dt_pil['nama_kpi']."</option>";
									}
								?>
                            </select>
                            </div>
                              <div class="col-md-2 form-group">
                                 <select name="status" id="status" class="form-control">
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_status, nama_status 
                                                                    from p_status order by nama_status");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_status']."'>".$dt_pil['nama_status']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
							<div class="col-md-4 form-group">
								<button type="button" class="btn btn-info" id="kirim" name="kirim">Cari</button>
								  <button type="button" class="btn btn-success" name="excel" id="excel">Export</button>
							</div>
						</div>
				</div>
			</div>
			</form>
            
    		<div class="row">
				<div class="col-sm">
                	<?php  if ($_SESSION['km_user_view']==1) { ?>
                         <div id="loading_dealer" align="center">
                            <img src="img/loading.gif" />
                            <br />
                            Ambil Data KPI...
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

?>