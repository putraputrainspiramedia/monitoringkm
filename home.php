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
				url			: "./homedata.php",
				data:{
					tp : 'view',
					organisasi : $("#organisasi").val(),
					tahun : $("#tahun").val(),
					//status : $("#status").val(),
					periode : $("#periode").val(),
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
					
					$.ajax({
						type		: "POST",
						url			: "./homedata.php",
						data:{
							tp : 'totalscore',
							organisasi : $("#organisasi").val(),
							tahun : $("#tahun").val(),
							//status : $("#status").val(),
							periode : $("#periode").val(),
						},
						cache:false,
						beforeSend:function(){
							$('#loading_dealer2').show();
							$("#totalscore").html("");					
						},
						success		: function(msg2){
							
							$('#loading_dealer2').hide();
							$("#totalscore").fadeIn('1000');
							$('#totalscore').html(msg2);
						},  
						error 		: function() { 
							$('#loading_dealer2').hide(); 
							alert("error");  
						}  
					});
					
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
					url			: "./homedata.php",
					data:{
						tp : 'view',
						organisasi : $("#organisasi").val(),
						tahun : $("#tahun").val(),
						//status : $("#status").val(),
						periode : $("#periode").val(),
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
						
						$.ajax({
						type		: "POST",
						url			: "./homedata.php",
						data:{
							tp : 'totalscore',
							organisasi : $("#organisasi").val(),
							tahun : $("#tahun").val(),
							//status : $("#status").val(),
							periode : $("#periode").val(),
						},
						cache:false,
						beforeSend:function(){
							$('#loading_dealer2').show();
							$("#totalscore").html("");					
						},
						success		: function(msg2){
							
							$('#loading_dealer2').hide();
							$("#totalscore").fadeIn('1000');
							$('#totalscore').html(msg2);
						},  
						error 		: function() { 
							$('#loading_dealer2').hide(); 
							alert("error");  
						}  
					});
					
					},  
					error 		: function() { 
						$('#loading_dealer').hide(); 
						alert("error");  
					}  
				});
			});
			
			$("#excel").click(function () {
				window.location.href='homeexcel.php?tahun='+$("#tahun").val()+'&periode='+$("#periode").val()+'&organisasi='+$("#organisasi").val()+'&status='+$("#status").val();
           	});
				
		});
					
    </script>

<div class="col-md-12">
         <div class="row">
            <div class="col-sm">
        		<h6>KONTRAK MANAJEMEN</h6> 
        	</div>
         </div>
         
        <form method="post" action="">
        <div class="row">
            <div class="col-sm">
                    <div class="form-row align-items-center">
                          <div class="col-md-2 form-group">
                             <select name="periode" id="periode" class="form-control">
                                 <?php 
                                    $qry_pil = mysqli_query($conn,"select id_periode, nama_periode 
                                                                from p_periode order by id_periode");
                                    while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                        echo "<option value='".$dt_pil['id_periode']."'>".$dt_pil['nama_periode']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                         </div>   
                         <div class="col-md-2 form-group">
                        <select name="organisasi" id="organisasi" class="form-control">
                            <?php 
                               $qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
																from p_organisasi a
																inner join p_group b on a.id_group = b.id_group
																order by a.nama_organisasi");
                                while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                    $idg = $dt_pil['id_organisasi'];
									$profil_arr = get_profilorg($idg);
	
									if ($profil_arr['tampil']==1) {
										$pos = strpos(str_replace(" ","",strtoupper($dt_pil['nama_organisasi'])),"REG4");
										if ($pos===false) {
											$select = "";
										} else {
											$select = "selected";
										}										
										echo "<option value='".$dt_pil['id_organisasi']."' $select>".$dt_pil['nama_organisasi']."</option>";
									}
                                }
                            ?>
                        </select>
                        </div>
                         <!-- <div class="col-md-2 form-group">
                             <select name="status" id="status" class="form-control">
                                <?php 
                                    $qry_pil = mysqli_query($conn,"select id_status, nama_status 
                                                                from p_status order by nama_status");
                                    while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                        echo "<option value='".$dt_pil['id_status']."'>".$dt_pil['nama_status']."</option>";
                                    }
                                ?>
                            </select>
                        </div>-->
                        <div class="col-md-3 form-group">
                            <button type="button" class="btn btn-info" id="kirim" name="kirim">Cari</button>
                              <button type="button" class="btn btn-success" name="excel" id="excel">Export</button>   
                        </div> 
                        <div class="col-md-3 form-group">
                        	  <h1><span class="badge badge-primary" id="totalscore">Total Score : 0</span></h1> 
                        </div>
                    </div>
            </div>
        </div>
        </form>
        
         <div id="loading_dealer2" align="center">
            <img src="img/loading.gif" />
          </div>
         
        
        <div class="row">
            <div class="col-sm">
                     <div id="loading_dealer" align="center">
                        <img src="img/loading.gif" />
                        <br />
                        Ambil Data KPI...
                    </div>
                    <div  id="hasil">
                    
                    </div>
                </div>
            </div>
        </div>

            
</div> 
	
    
<?php } 

?>