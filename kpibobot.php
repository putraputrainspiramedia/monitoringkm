<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
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
				url			: "./kpibobotdata.php",
				data:{
					tp : 'view',
					organisasi : $("#organisasi").val(),
					tahun : $("#tahun").val(),
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
					url			: "./kpibobotdata.php",
					data:{
						tp : 'view',
						organisasi : $("#organisasi").val(),
						tahun : $("#tahun").val(),
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
				$("#id").val('');
				$("#tahun2").val($("#tahun").val());
				$("#group").val('');
				$("#organisasi2").val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input KPI Bobot');
           		$('#InputModalForms').modal('show');
       		});
			<?php } ?>
			
			<?php if ($_SESSION['km_user_delete']==1) { ?>			
			$("#reset").click(function () {
				var organisasi = $("#organisasi").val();				
				var tahun = $("#tahun").val();
						
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=reset&organisasi='+organisasi+'&tahun='+tahun;
				$.ajax({
					type	: "POST",
					url		: "kpibobotdata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						$.ajax({
							type		: "POST",
							url			: "./kpibobotdata.php",
							data:{
								tp : 'view',
								organisasi : $("#organisasi").val(),
								tahun : $("#tahun").val(),
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
           		window.location.href='index.php?fl=kpibobot&tp=edit';
       		});
			<?php } ?>
			
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			$("#excel").click(function () {
				window.location.href='kpibobotexcel.php?tahun='+$("#tahun").val()+'&organisasi='+$("#organisasi").val();
           	});
			
			/*$("#simpan").click(function () {
				var id = $("#id").val();
				var nama =  $("#nama").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				
				if (id!='') {
					var datane = 'tp=edit&nama='+nama+'&id='+id;
				} else {
					var datane = 'tp=input&nama='+nama;
				}
				$.ajax({
					type	: "POST",
					url		: "mmodedata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						var dtTable2 = $('#datable_1').DataTable();		
						dtTable2.destroy();
						
						$('#datable_1 tbody').empty(); // empty in case the columns change
						
						dtTable = $('#datable_1').DataTable({
							// "order": [[ 2, "asc" ]],
							 "bLengthChange": false,
							"paging": true,
							"scrollY": "300px",
							//"scrollY": true,
							"scrollX": true,
							"processing": true,
							"displayLength": 10,
							"serverSide": true,
							"ajax": {
								"url" : "mmodedata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_mode"},
								{"data": "nama"},
								{"data": "tgl_input"},
								{"data": "action"},
							],
							"createdRow": function( nRow, aData, iDataIndex ) {
								$(nRow).attr('id', aData['id']);
							},
							"initComplete": function(settings, json) {
								//HideLoading();
							},
							"drawCallback": function( settings ) {
								//HideLoading();  
							},
							"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
								$('td:eq(0)', nRow).attr("id", aData['id']);
								$('td:eq(1)', nRow).attr("id", aData['id']);
								$('td:eq(2)', nRow).attr("id", aData['id']);
							//	$('td:eq(1)', nRow).attr("id", aData['id']);
								//$('td:eq(2)', nRow).attr("id", aData['id']);		
							}, 
							"columnDefs": [
								{
									"targets": -1,
									"className": 'dt-body-center'
								}
							  ]
						});
			
						$('#nama').val('');
						$('#id').val('');
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
					}
				});
			
       		});*/
			
			$("#group").change(function(){			
				$("#organisasi1").hide();
						
				var group = $("#group").val();
				var dataString = 'tp=organisasi&group='+ group;
				$("#flash_dept").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				$.ajax({
					type		: "POST",
					url			: "./variabel.php",
					data		: dataString,
					cache		: false,
					success		: function(html)	{
						$("#organisasi2").show();
						$("#organisasi2").fadeIn();
						$("#organisasi2").html(html);
						$('#flash_dept').fadeOut('fast');
					}
				});
			});
				
		});
					
    </script>

<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Data KPI Bobot</h6>
		</div>
		<div class="card-body"> 
			
            <div class="row">
            	<div class="col-sm">
					<form method="post" action="">
						<div class="form-row align-items-center">
                        	 <div class="col-md-2 form-group">
                            	<input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                             </div>   
							 <div class="col-md-4 form-group">
                            <select name="organisasi" id="organisasi" class="form-control">
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi 
																from p_organisasi order by nama_organisasi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										$pos = strpos(str_replace(" ","",strtoupper($dt_pil['nama_organisasi'])),"REG4");
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
							<div class="col-md-6 form-group">
								<button type="button" class="btn btn-info" id="kirim" name="kirim">Cari Data</button>
                                <?php if ($_SESSION['km_user_input']==1) { ?>
                            	 <button type="button" class="btn btn-success" id="tambah">Upload Data</button>
                            	 <?php } ?> 
								 <?php if ($_SESSION['km_user_edit']==1) { ?>
                            	 <button type="button" class="btn btn-warning" id="edit">Ubah Data</button>
                                 <?php } ?>  
                                 <?php if ($_SESSION['km_user_delete']==1) { ?>
                                 <button type="button" class="btn btn-danger" id="reset">Reset Data</button><!--
									<?php } ?>
                                <button type="button" class="btn btn-success mb-2" id="excel">Export Excel</button>-->
							</div>
						</div>
					</form>
				</div>
			</div>
            
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
                	<form method="post" enctype="multipart/form-data" action="kpibobotdata.php">
            			<input type="hidden" name="tp" id="tp" value="input" readonly="readonly" />
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                         <div class="form-group">
                            <label>Tahun</label>
                           <input type="text" class="form-control mb-2 date-picker" data-date-format="yyyy" id="tahun2" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                       </div>
                       <div class="form-group">
                            <label>Group Organisasi</label>
                           <select name="group" id="group" class="form-control">
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_group, nama_group 
																from p_group order by nama_group");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										$pos = strpos(str_replace(" ","",strtoupper($dt_pil['nama_group'])),"EVP TReg");
										if ($pos===false) {
											$select = "";
										} else {
											$select = "selected";
										}
										echo "<option value='".$dt_pil['id_group']."' $select>".$dt_pil['nama_group']."</option>";
									}
								?>
                            </select>
                       </div>
                       <div class="form-group">
                            <label>Organisasi</label>
                           <select name="organisasi2" id="organisasi2" class="form-control">
                            	
                            </select>
                            <span id="flash_dept"></span>
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
                           <?php } ?>
                             <?php if ($_SESSION['km_user_delete']==1) { ?>
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


function input_data2() {
	global $conn;
	
		
	if (empty($_REQUEST['tahun'])) {
		$tahun = date("Y");
	} else {
		$tahun = $_REQUEST['tahun'];
	}

	?>
	<script>
		  $(document).ready(function(){
		  
		  	$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			$("#tombol").hide();
			 $.ajax({
				type		: "POST",
				url			: "./kpibobotdata.php",
				data:{
					tp : 'inputlist',
					organisasi : $("#organisasi").val(),
					tahun : $("#tahun").val(),
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
			$("#organisasi, #tahun").change(function(){    // 2n
				$('#hasil').hide();
				$('#loading').show();
						
				$.ajax({
					type		: "POST",
					url			: "./kpibobotdata.php",
					data:{
						tp : 'inputlist',
						organisasi : $("#organisasi").val(),
						tahun : $("#tahun").val(),
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
			
		});
		
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Input KPI Bobot</h6>
		</div>
		<div class="card-body">   
		
			<form method="post" action="kpibobotdata.php" enctype="multipart/form-data">
            <input type="hidden" name="tp" value="input" readonly="readonly" />
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
                    	<div class="col-md-2 form-group">
                            <input type="text" class="form-control mb-2 date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                         </div>   
						<div class="col-md-4 form-group">
							 <select name="organisasi" id="organisasi" class="form-control">
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi 
																from p_organisasi order by nama_organisasi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										echo "<option value='".$dt_pil['id_organisasi']."'>".$dt_pil['nama_organisasi']."</option>";
									}
								?>
                            </select>
						</div>
                        <div class="col-md-6 form-group">&nbsp;</div>
					</div>
				</div>
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
            
			 <div class="row" id="tombol">
                <div class="col-sm">					
                    <div class="form-row align-items-center">                       
                        <div class="col-md-10 form-group">
                            
                        </div> 
                        <div class="col-md-2 form-group"><label>&nbsp;</label><br />
                            <button type="submit" class="btn btn-primary mb-2" name="simpan">Simpan</button>
                            <button type="button" class="btn btn-danger mb-2" name="batal" onclick="window.history.back();">Batal</button>
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
	} else {
		$user_profil_arr = get_profiluser();
		$where_unit = " and id_unit = '".$user_profil_arr['unit']."' ";
	}
	
	if (!empty($_REQUEST['kpi']) and !empty($_REQUEST['periode']) and !empty($_REQUEST['tahun']) and !empty($_REQUEST['organisasi']) and !empty($_REQUEST['tahun'])  and !empty($_REQUEST['status'])) {
		$qry = mysqli_query($conn,"select bobot from kpi_targetbobot where id_kpi = '".$_REQUEST['kpi']."'
									and id_periode = '".$_REQUEST['periode']."' and id_organisasi = '".$_REQUEST['organisasi']."'
									and tahun = '".$_REQUEST['tahun']."' and id_status = '".$_REQUEST['status']."'");
		$dt = mysqli_fetch_array($qry);		
		$bobot = number_format($dt['bobot'],2,",",".");					
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
					url		: "kpibobotdata.php",
					data	: $( "#frm" ).serialize(),
					success	: function(data){
						alert(data);
						window.location.href='?fl=kpibobot';
					}
				});
			});
			
		});
		
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Edit KPI Bobot</h6>
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
							 <input type="text" class="form-control angka" id="bobot" placeholder="bobot" name="bobot"  maxlength="10"   value="<?=$bobot;?>">
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
?>