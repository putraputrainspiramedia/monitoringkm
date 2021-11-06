<?php
require_once("./cek_priv.php");

	switch($_REQUEST['tp']) {
		case 'input':
			if ($_SESSION['km_user_input']==1) {
				input_data();
			}
			break;
		case 'edit':
			if ($_SESSION['km_user_edit']==1) {
				edit_data();
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
			
			$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			 $.ajax({
				type		: "POST",
				url			: "./kpirealisasidata.php",
				data:{
					tp : 'view',
					organisasi : $("#organisasi").val(),
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
					url			: "./kpirealisasidata.php",
					data:{
						tp : 'view',
						organisasi : $("#organisasi").val(),
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
			
			<?php if ($_SESSION['km_user_input']==1) { ?>
			$("#tambah").click(function () {
           		window.location.href='index.php?fl=kpirealisasi&tp=input';
       		});
			/*$("#upload").click(function () {
				$("#id").val('');
				$("#tahun2").val($("#tahun").val());
				$("#organisasi2").val('');
				$("#status2").val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input KPI Realisasi');
           		$('#InputModalForms').modal('show');
       		});*/
			<?php } ?>
			
			<?php if ($_SESSION['km_user_delete']==1) { ?>		
			$("#reset").click(function () {
				var organisasi = $("#organisasi").val();				
				var tahun = $("#tahun").val();
						
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=reset&organisasi='+organisasi+'&tahun='+tahun;
				$.ajax({
					type	: "POST",
					url		: "kpirealisasidata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						$.ajax({
							type		: "POST",
							url			: "./kpirealisasidata.php",
							data:{
								tp : 'view',
								organisasi : $("#organisasi").val(),
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
						$("#load2").fadeOut('fast');
					}
				});
			
       		});
			<?php } ?>
			
			<?php if ($_SESSION['km_user_edit']==1) { ?>
			$("#edit").click(function () {
           		window.location.href='index.php?fl=kpirealisasi&tp=edit';
       		});
			<?php } ?>
			
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			$("#excel").click(function () {
				window.location.href='kpirealisasiexcel.php?tahun='+$("#tahun").val()+'&organisasi='+$("#organisasi").val()+'&status='+$("#status").val();
           	});
			
		});
					
    </script>

<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Data KPI Realisasi</h6>
		</div>
		<div class="card-body"> 
			
            <form method="post" action="">
		    <div class="row">
            	<div class="col-sm">
						<div class="form-row align-items-center">
                        	 <div class="col-md-2 form-group">
                            	<input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                             </div>   
                             <div class="col-md-2 form-group">
                            <select name="organisasi" id="organisasi" class="form-control">
                            	<?php 
									$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
																from p_organisasi a
																inner join p_group b on a.id_group = b.id_group
																$where_group
																order by a.nama_organisasi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										$id_organisasi = $dt_pil['id_organisasi'];
										//$pos = strpos(str_replace(" ","",strtoupper($dt_pil['nama_organisasi'])),"REG4");
										//if ($pos===false) {
										//	$select = "";
										//} else 
										if ($id_organisasi==$id_organisasi_user) {
											$select = "selected";
										} else {
											$select = "";
										}
										echo "<option value='".$dt_pil['id_organisasi']."' $select>".$dt_pil['nama_organisasi']."</option>";
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
                            <div class="col-md-6 form-group">
								<button type="button" class="btn btn-info" id="kirim" name="kirim">Cari Data</button>   								
                                <!--<button type="button" class="btn btn-success mb-2" id="excel">Export Excel</button>-->
                                 <?php if ($_SESSION['km_user_input']==1) { ?>
                            	 <button type="button" class="btn btn-primary" id="tambah">Tambah Data</button><!--
                            	 <button type="button" class="btn btn-success" id="upload">Upload Data</button>-->
                                 <?php } ?> 
                                 <?php if ($_SESSION['km_user_edit']==1) { ?>
                            	 <button type="button" class="btn btn-warning" id="edit">Ubah Data</button>
                                 <?php } ?>  
                                  <?php if ($_SESSION['km_user_delete']==1) { ?>
                            	 <button type="button" class="btn btn-danger" id="reset">Reset Data</button>
								<?php } ?>
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
	
    
      <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1 or $_SESSION['km_user_delete']==1) { ?>
    <div class="modal fade" id="InputModalForms" tabindex="-1" role="dialog" aria-labelledby="InputModalForms" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                	<form method="post" enctype="multipart/form-data" action="kpirealisasidata.php">
            			<input type="hidden" name="tp" id="tp" value="input" readonly="readonly" />
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                         <div class="form-group">
                            <label>Tahun</label>
                           <input type="text" class="form-control mb-2 date-picker" data-date-format="yyyy" id="tahun2" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                       </div>
                       <div class="form-group">
                            <label>Organisasi</label>
                           <select name="organisasi2" id="organisasi2" class="form-control">
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi 
																from p_organisasi order by nama_organisasi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										$pos = strpos(str_replace(" ","",strtoupper($dt_pil['nama_organisasi'])),"EVP TReg");
										if ($pos===false) {
											$select = "";
										} else {
											$select = "selected";
										}
										echo "<option value='".$dt_pil['id_organisasi']."' $select>".$dt_pil['nama_organisasi']."</option>";
									}
								?>
                            </select>
                       </div>
                        <div class="form-group">
                            <label>Status</label>
                        <select name="status2" id="status2" class="form-control">
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_status, nama_status 
                                                                    from p_status order by nama_status");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_status']."'>".$dt_pil['nama_status']."</option>";
                                        }
                                    ?>
                                </select>
                        </div>        
                        <div class="form-group">
                            <label>File upload</label>
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Upload</span>
                                    </div>
                                    <div class="form-control text-truncate" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-append">
                                            <span class=" btn btn-primary btn-file"><span class="fileinput-new">Select file</span><span class="fileinput-exists">Change</span>
                                    <input type="file" name="filex" id="filex" accept='.xlsx,.xls'>
                                    </span>
                                    <a href="#" class="btn btn-secondary fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </span>
                                </div>
                            </div>
                       </div>
                       <div class="form-group">
                            <label>&nbsp;</label><br />
                            <button type="button" class="btn btn-success mb-2" name="excel" id="excel">Format File Upload</button>
                            <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
                            <button type="submit" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                             <?php 
							 }
							 
							 if ($_SESSION['km_user_delete']==1) { ?>
                            <button type="button" class="btn btn-primary mb-2" name="hapus" id="hapus2">Hapus</button>
                            <?php } ?>
                            <button type="button" class="btn btn-danger mb-2" name="batal" id="batal">Batal</button>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
    
    <?php } ?>
    
<?php } 


function input_data() {
	global $conn;
	
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}

	$menu_arr = cek_priuser($_REQUEST['fl']);
	if ($_SESSION['km_profil']==1) {
		$where_unit = "";
	} else {
		$user_profil_arr = get_profiluser();
		$where_unit = " and id_unit = '".$user_profil_arr['unit']."' ";
	}
	
	
	?>
	<script>
		  $(document).ready(function(){
		  	
			$(".angka").keydown(function (e) {
					
					// Allow: backspace, delete, tab, escape, enter and .
					/*if ($.inArray(e.keyCode, [44, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
						 // Allow: Ctrl+A
						//(e.keyCode == 65 && e.ctrlKey === true) ||  
						 // Allow: home, end, left, right
						(e.keyCode >= 35 && e.keyCode <= 39)) {
							 // let it happen, don't do anything
							 return;
					}*/
					// Allow: backspace, delete, tab, escape, enter and . , 
					if ($.inArray(e.keyCode, [44, 46, 8, 9, 27, 13, 110, 190, 188]) !== -1) {
							 // let it happen, don't do anything
							 return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});
				
		  	$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			$("#tombol").hide();
			/*
			 $.ajax({
				type		: "POST",
				url			: "./kpirealisasidata.php",
				data:{
					tp : 'inputlist',
					organisasi : $("#organisasi").val(),
					tahun : $("#tahun").val(),
					status : $("#status").val(),
				},
				cache:false,
				beforeSend:function(){
					$('#loading_dealer').show();
					$("#hasil").html("");					
					$("#tombol").hide();
				},
				success		: function(msg){
					$("#tombol").show();
					$('#loading_dealer').hide();
					$("#hasil").fadeIn('1000');
					$('#hasil').html(msg);
				},  
				error 		: function() { 
					$('#loading_dealer').hide(); 
					alert("error");  
				}  
			});
			
			$("#tombol").hide();
			$("#organisasi, #tahun, #status").change(function(){    // 2n
				$('#hasil').hide();
				$('#loading').show();
						
				$.ajax({
					type		: "POST",
					url			: "./kpirealisasidata.php",
					data:{
						tp : 'inputlist',
						organisasi : $("#organisasi").val(),
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
			});*/
			
			$('#tahun, #unit, #kpi, #status, #periode').on('change', function() {
                this.form.submit();
            });
			
			$("#simpan").click(function(){
				//var datane = 'tp=input&nama='+nama+'&urutan='+urutan+'&parent='+parent+'&perspective='+perspective+'&terbalik='+terbalik+'&satuan='+satuan;
				//alert('asdsa');
				$.ajax({
					type	: "POST",
					url		: "kpirealisasidata.php",
					data	: $( "#frm" ).serialize(),
					success	: function(data){
						alert(data);
						window.location.href='?fl=kpirealisasi';
					}
				});
			});
			
		});
		
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Input KPI Realisasi</h6>
		</div>
		<div class="card-body"> 
		   <form method="post" action="" enctype="multipart/form-data" id="frm">
            <input type="hidden" name="tp" value="input" readonly="readonly" />
          	 <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-4 form-group">
							<label>Unit</label>
							 <select name="unit" id="unit" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_unit, nama_unit 
																from p_unit where id_unit is not null ".$where_unit." order by nama_unit");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_unit']==$_REQUEST['unit']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_unit']."' $cek>".$dt_pil['nama_unit']."</option>";
									}
								?>
                            </select>
						</div>
						<div class="col-md-4 form-group">
							<label>Status</label>
							 <select name="status" id="status" class="form-control">
                             	<option value="">Pilih</option>
                             	<?php 
									$qry_pil = mysqli_query($conn,"select id_status, nama_status 
																from p_status order by nama_status");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_status']==$_REQUEST['status']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_status']."' $cek>".$dt_pil['nama_status']."</option>";
									}
								?>
                            </select>
						</div>
						<div class="col-md-2 form-group">
							<label>Periode</label>
							<select name="periode" id="periode" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_periode, nama_periode
																from p_periode");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_periode']==$_REQUEST['periode']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_periode']."' $cek>".$dt_pil['nama_periode']."</option>";
									}
								?>
                            </select>
						</div>
                        <div class="col-md-2 form-group">
                        <label>&nbsp;</label>
							 <input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
						</div>  
					</div>
				</div>
			</div>
            
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-12 form-group">
							<label>KPI</label>
							 <select name="kpi" id="kpi" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_kpi, nama_kpi, satuan
																from kpi 
																where id_kpi in (
																	select id_kpi from kpi_unit
																	where id_unit = '".$_REQUEST['unit']."'
																)
																order by nama_kpi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_kpi']==$_REQUEST['kpi']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_kpi']."' $cek>".$dt_pil['nama_kpi']." (".$dt_pil['satuan'].")</option>";
									}
								?>
                            </select>
						</div>
					</div>
				</div>
			</div>
             <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-12 form-group">
							<label style="color:#FF0000">misal 1.234,56 (2 digit dibelakang koma)</label>
						</div>
					</div>
				</div>
			</div>
            
            <?php
			
			if ($_SESSION['km_profil']==1) {
			?>
            
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-4">
                        	<?php 
							
							if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['status'])) {
								$where = " where id_kpi = '".$_REQUEST['kpi']."'
											and id_periode = '".$_REQUEST['periode']."'
											and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."' ";
											
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, ifnull(c.realisasi,0)  realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														left join (select realisasi, id_organisasi
														from kpi_realisasi $where) c on a.id_organisasi = c.id_organisasi
														where b.nama_group = 'EVP TReg'
														order by a.nama_organisasi
														");
																		
							} else {
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, '0' realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														where b.nama_group = 'EVP TReg'
														order by a.nama_organisasi
														");
						
							}	
							
							while ($dt_pil = mysqli_fetch_array($qry_pil)) { ?>
								 <div class="row">
                                    <div class="col form-group">
										<?php echo $dt_pil['nama_organisasi'];?>
                                    </div>
                                    <div class="col form-group">
                                    	<input type="text" name="realisasi_<?php echo $dt_pil['id_organisasi'];?>" maxlength="10"  class="form-control angka" value="<?php echo number_format($dt_pil['realisasi'],2,",",".");?>" />
                                    </div>
                                    <div class="col-4"></div>
                                </div>
							<?php		
							}
						?>
                        </div>
                        <div class="col-md-4">
                        	<?php 
							if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['status'])) {
								$where = " where id_kpi = '".$_REQUEST['kpi']."'
											and id_periode = '".$_REQUEST['periode']."'
											and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."' ";
											
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, ifnull(c.realisasi,0)  realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														left join (select realisasi, id_organisasi
														from kpi_realisasi $where) c on a.id_organisasi = c.id_organisasi
														where b.nama_group = 'Witel'
														order by a.nama_organisasi
														");
																		
							} else {
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, '0' realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														where b.nama_group = 'Witel'
														order by a.nama_organisasi
														");
						
							}	
							
							while ($dt_pil = mysqli_fetch_array($qry_pil)) { ?>
								 <div class="row">
                                    <div class="col form-group">
										<?php echo $dt_pil['nama_organisasi'];?>
                                    </div>
                                    <div class="col form-group">
                                    	<input type="text" name="realisasi_<?php echo $dt_pil['id_organisasi'];?>" maxlength="10"  class="form-control angka" value="<?php echo number_format($dt_pil['realisasi'],2,",",".");?>" />
                                    </div>
                                </div>
							<?php		
							}
						?>
                        </div>
                        
                        <div class="col-md-4">
                        	<?php 
							if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['status'])) {
								$where = " where id_kpi = '".$_REQUEST['kpi']."'
											and id_periode = '".$_REQUEST['periode']."'
											and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."' ";
											
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, ifnull(c.realisasi,0)  realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														left join (select realisasi, id_organisasi
														from kpi_realisasi $where) c on a.id_organisasi = c.id_organisasi
														where b.nama_group in ('DEVP Marketing', 'DEVP Infrastructure')
														order by a.nama_organisasi
														");
																		
							} else {
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, '0' realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														where b.nama_group in ('DEVP Marketing', 'DEVP Infrastructure')
														order by a.nama_organisasi
														");
						
							}	
							
							while ($dt_pil = mysqli_fetch_array($qry_pil)) { ?>
								 <div class="row">
                                    <div class="col form-group">
										<?php echo $dt_pil['nama_organisasi'];?>
                                    </div>
                                    <div class="col form-group">
                                    	<input type="text" name="realisasi_<?php echo $dt_pil['id_organisasi'];?>" maxlength="10"  class="form-control angka" value="<?php echo number_format($dt_pil['realisasi'],2,",",".");?>" />
                                    </div>
                                </div>
							<?php		
							}
						?>
                        </div>
                        
					</div>
				</div>
			</div>  		
		
        	<?php } else { 
				$user_profil_arr = get_profiluser();
				$id_organisasi_user = $user_profil_arr['organisasi'];
			?>
            
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-6">
                        	<?php 
							
							if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['status'])) {
								$where = " where id_kpi = '".$_REQUEST['kpi']."'
											and id_periode = '".$_REQUEST['periode']."'
											and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."' ";
											
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, ifnull(c.realisasi,0)  realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														left join (select realisasi, id_organisasi
														from kpi_realisasi $where) c on a.id_organisasi = c.id_organisasi
														where a.id_organisasi = '".$id_organisasi_user."'
														order by a.nama_organisasi
														");
																		
							} else {
								$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi, '0' realisasi
														from p_organisasi a
														inner join p_group b on a.id_group = b.id_group
														where a.id_organisasi = '".$id_organisasi_user."'
														order by a.nama_organisasi
														");
						
							}	
							
							while ($dt_pil = mysqli_fetch_array($qry_pil)) { ?>
								 <div class="row">
                                    <div class="col form-group">
										<?php echo $dt_pil['nama_organisasi'];?>
                                    </div>
                                    <div class="col form-group">
                                    	<input type="text" name="realisasi_<?php echo $dt_pil['id_organisasi'];?>" maxlength="10"  class="form-control angka" value="<?php echo number_format($dt_pil['realisasi'],2,",",".");?>" />
                                    </div>
                                    <div class="col-4"></div>
                                </div>
							<?php		
							}
						?>
                        </div>
                        <div class="col-md-6">
                        </div>                        
					</div>
				</div>
			</div>
            
            <?php } ?>
            
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


function edit_data() {
	global $conn;
	
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}

	$menu_arr = cek_priuser($_REQUEST['fl']);
	
	if ($_SESSION['km_profil']==1) {
		$where_unit = "";
		$where_organisasi = "";
	} else {
		$user_profil_arr = get_profiluser();
		$where_unit = " and id_unit = '".$user_profil_arr['unit']."' ";
		$where_organisasi = " where a.id_organisasi = '".$user_profil_arr['organisasi']."'";
	}
			
	
	if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['organisasi']) and !empty($_REQUEST['tahun'])  and !empty($_REQUEST['status'])) {
		$qry = mysqli_query($conn,"select realisasi from kpi_realisasi where id_kpi = '".$_REQUEST['kpi']."'
									and id_periode = '".$_REQUEST['periode']."' and id_organisasi = '".$_REQUEST['organisasi']."'
									and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."'");
		$dt = mysqli_fetch_array($qry);
		$realisasi = number_format($dt['realisasi'],2,",",".");							
	}
	?>
	<script>
		  $(document).ready(function(){
		  	
			$(".angka").keydown(function (e) {
					
					// Allow: backspace, delete, tab, escape, enter and .
					/*if ($.inArray(e.keyCode, [44, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
						 // Allow: Ctrl+A
						//(e.keyCode == 65 && e.ctrlKey === true) ||  
						 // Allow: home, end, left, right
						(e.keyCode >= 35 && e.keyCode <= 39)) {
							 // let it happen, don't do anything
							 return;
					}*/
					// Allow: backspace, delete, tab, escape, enter and . , 
					if ($.inArray(e.keyCode, [44, 46, 8, 9, 27, 13, 110, 190, 188]) !== -1) {
							 // let it happen, don't do anything
							 return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});
				
		  	$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			$("#tombol").hide();
			
			$('#tahun, #unit, #kpi, #status, #organisasi, #periode').on('change', function() {
                this.form.submit();
            });
			
			$("#simpan").click(function(){
				//var datane = 'tp=input&nama='+nama+'&urutan='+urutan+'&parent='+parent+'&perspective='+perspective+'&terbalik='+terbalik+'&satuan='+satuan;
				//alert('asdsa');
				$.ajax({
					type	: "POST",
					url		: "kpirealisasidata.php",
					data	: $( "#frm" ).serialize(),
					success	: function(data){
						alert(data);
						window.location.href='?fl=kpirealisasi';
					}
				});
			});
			
		});
		
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Edit KPI Realisasi</h6>
		</div>
		<div class="card-body"> 
		   <form method="post" action="" enctype="multipart/form-data" id="frm">
            <input type="hidden" name="tp" value="edit" readonly="readonly" />
          	 <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-4 form-group">
							<label>Unit</label>
							 <select name="unit" id="unit" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_unit, nama_unit 
																from p_unit where id_unit is not null ".$where_unit." order by nama_unit");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_unit']==$_REQUEST['unit']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_unit']."' $cek>".$dt_pil['nama_unit']."</option>";
									}
								?>
                            </select>
						</div>
                        <div class="col-md-4 form-group">
                        	<label>Organisasi</label>
                       		 <select name="organisasi" id="organisasi" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select a.id_organisasi, a.nama_organisasi 
																from p_organisasi a
																".$where_organisasi."
																order by a.nama_organisasi");
											while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_organisasi']==$_REQUEST['organisasi']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_organisasi']."' $cek>".$dt_pil['nama_organisasi']." </option>";
									}
								?>
                            </select>
                        </div>
						<div class="col-md-2 form-group">
							<label>Periode</label>
							<select name="periode" id="periode" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_periode, nama_periode
																from p_periode");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_periode']==$_REQUEST['periode']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_periode']."' $cek>".$dt_pil['nama_periode']."</option>";
									}
								?>
                            </select>
						</div>
                        <div class="col-md-2 form-group">
                        <label>&nbsp;</label>
							 <input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
						</div>  
					</div>
				</div>
			</div>
            
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center"> 
						<div class="col-md-4 form-group">
							<label>Status</label>
							 <select name="status" id="status" class="form-control">
                             	<option value="">Pilih</option>
                             	<?php 
									$qry_pil = mysqli_query($conn,"select id_status, nama_status 
																from p_status order by nama_status");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_status']==$_REQUEST['status']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_status']."' $cek>".$dt_pil['nama_status']."</option>";
									}
								?>
                            </select>
						</div>
						<div class="col-md-4 form-group">
							<label>KPI</label>
							 <select name="kpi" id="kpi" class="form-control">
                             	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_kpi, nama_kpi, satuan
																from kpi 
																where id_kpi in (
																	select id_kpi from kpi_unit
																	where id_unit = '".$_REQUEST['unit']."'
																)
																order by nama_kpi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										if ($dt_pil['id_kpi']==$_REQUEST['kpi']) {
											$cek = "selected";
										} else {
											$cek = "";
										}
										echo "<option value='".$dt_pil['id_kpi']."' $cek>".$dt_pil['nama_kpi']." (".$dt_pil['satuan'].")</option>";
									}
								?>
                            </select>
						</div>                       
                        <div class="col-md-4 form-group">
                        	<label style="color:#FF0000">misal 1.234,56 (2 digit dibelakang koma)</label>
							 <input type="text" class="form-control angka" id="realisasi" placeholder="realisasi" maxlength="10" name="realisasi"  value="<?=$realisasi?>">
						</div> 
					</div>
				</div>
			</div>
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-4 form-group"><label>&nbsp;</label><br />
							<button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                            <button type="button" class="btn btn-danger mb-2" name="batal" onclick="window.location.href='?fl=kpirealisasi';">Batal</button>
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

?>