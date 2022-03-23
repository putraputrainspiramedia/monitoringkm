<?php
require_once("./cek_priv.php");


switch($_REQUEST['tp']) {
	case 'input':
		if ($_SESSION['km_user_input']==1) {
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

?>
	<script language="javascript">
		
		<?php if ($_SESSION['km_user_edit']==1) { ?>
		function editdata(id,nama, idorganisasi, namaorganisasi, tahun){		
			$("#hapus2").hide();
			$("#simpan").show();
			$('#nama').val(nama);
			$('#id').val(id);
			$("#idorganisasi").val(idorganisasi);
			$('#namaorganisasi').val(namaorganisasi);
			$("#tahun2").val(tahun);
			
			$('.modal-title').html('Edit KPI');
			$('#InputModalForms').modal('show');
				
		}	
		<?php } ?>
		
		<?php if ($_SESSION['km_user_delete']==1) { ?>
		function hapusdata(id, nama, idorganisasi, namaorganisasi, tahun){
			$("#hapus2").show();
			$("#simpan").hide();
			$('#nama').val(nama);
			$('#namaorganisasi').val(namaorganisasi);
			$('#idorganisasi').val(idorganisasi);
			$('#id').val(id);
			$("#tahun2").val(tahun);
			
			//alert(organisasi + nama + tahun);
			$('.modal-title').html('Hapus KPI Organisasi');
			$('#InputModalForms').modal('show');
			
		}
		<?php } ?>
		
		//var table = $('#datable_1').DataTable();
		$(document).ready(function(){
			$('#loading_dealer').hide();
			$('#hasil').html('');
			$("#hapus2").hide();
			
			$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
			 $.ajax({
				type		: "POST",
				url			: "./kpiorganisasidata.php",
				data:{
					tp : 'view',
					organisasi : $("#organisasi").val(),
				//	unit : $("#unit").val(),
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
					url			: "./kpiorganisasidata.php",
					data:{
						tp : 'view',
						organisasi : $("#organisasi").val(),
						tahun : $("#tahun").val(),
						//unit : $("#unit").val(),
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
           		window.location.href='index.php?fl=kpiorganisasi&tp=input';
       		});
			<?php } ?>
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			<?php if ($_SESSION['km_user_delete']==1) { ?>
			$("#hapus2").click(function () {
				var id = $("#id").val();
				//var unit2 = $("#unit2").val();
				var idorganisasi = $("#idorganisasi").val();
				var tahun2 =  $("#tahun2").val();
				
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=delete&id='+id+'&organisasi='+idorganisasi+'&tahun='+tahun2;
				$.ajax({
					type	: "POST",
					url		: "kpiorganisasidata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						$.ajax({
							type		: "POST",
							url			: "./kpiorganisasidata.php",
							data:{
								tp : 'view',
								organisasi : $("#organisasi").val(),
								//unit : $("#unit").val(),
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
						$('#nama').val('');
						$('#namaorganisasi').val('');
						$('#idorganisasi').val('');
						$('#id').val('');
						//$("#unit2").val('');
						$("#tahun2").val('');
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
					}
				});
			
       		});
			<?php } ?>
		});
			
		
</script>

<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Data KPI per Organisasi</h6>
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
                            	<option value="">Pilih</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi 
																from p_organisasi order by nama_organisasi");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										echo "<option value='".$dt_pil['id_organisasi']."'>".$dt_pil['nama_organisasi']."</option>";
									}
								?>
                            </select>
                            </div><!--
                             <div class="col-md-2 form-group">
                            <select name="unit" id="unit" class="form-control">
                            	<option value="">Pilih</option>
								<?php 
									$qry_pil = mysqli_query($conn,"select id_unit, nama_unit
																from p_unit order by nama_unit");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										echo "<option value='".$dt_pil['id_unit']."'>".$dt_pil['nama_unit']."</option>";
									}
								?>
                            </select>
                             </div>-->
							<div class="col-md-6 form-group">
								<button type="button" class="btn btn-info" id="kirim" name="kirim">Cari Data</button>
                                	<?php if ($_SESSION['km_user_input']==1) { ?>
                            	 <button type="button" class="btn btn-primary" id="tambah">Tambah Data</button><!--
								<button type="button" class="btn btn-success mb-2" id="excel">Export Excel</button>-->
                                <?php } ?>
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
                	<form method="post" enctype="multipart/form-data">
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
            			<input type="hidden" name="idorganisasi" id="idorganisasi" value="" readonly="readonly" />
                        
                         <div class="form-group">
                            <label>KPI</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100" readonly="readonly">
                       </div>
                        <div class="form-group">
                            <label>Organisasi</label>
                            <input type="text" class="form-control mb-6" id="namaorganisasi" placeholder="organisasi" name="namaorganisasi" maxlength="100" readonly="readonly">
                       </div>
                        <div class="form-group">
                            <label>Tahun</label>
                            <input type="text" class="form-control mb-6" id="tahun2" placeholder="tahun" name="tahun2" maxlength="100" readonly="readonly">
                       </div>
                       <div class="form-group">
                       		<label>&nbsp;</label><br />
							 <?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
                              <button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                              <?php } ?>
                            
							<?php if ($_SESSION['km_user_delete']==1) { ?>
                             <button type="button" class="btn btn-info mb-2" name="hapus" id="hapus2">Hapus</button>
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
	?>
	<script>
		  $(document).ready(function(){
		  	$('.date-picker').datepicker({
				 format: "yyyy",
					viewMode: "years", 
					minViewMode: "years"
			});
			
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
			
			$("#checkall").click(function(){
				$('input:checkbox').not(this).prop('checked', this.checked);
			});
			
			$('#tahun, #group').on('change', function() {
                this.form.submit();
            });
			
			$("#simpan").click(function(){
				//var datane = 'tp=input&nama='+nama+'&urutan='+urutan+'&parent='+parent+'&perspective='+perspective+'&terbalik='+terbalik+'&satuan='+satuan;
				//alert('asdsa');
				$.ajax({
					type	: "POST",
					url		: "kpiorganisasidata.php",
					data	: $( "#frm" ).serialize(),
					success	: function(data){
						alert(data);
						window.location.href='?fl=kpiorganisasi';
					}
				});
			});
			
		});
		
	</script>
<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Input KPI per Organisasi</h6>
		</div>
		<div class="card-body"> 
		
			<form method="post" action="" enctype="multipart/form-data" id="frm">
            <input type="hidden" name="tp" value="input" readonly="readonly" />
            <div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
                    	 <div class="col-md-2 form-group">
                            <input type="text" class="form-control date-picker" data-date-format="yyyy" id="tahun" placeholder="tahun" data-provide="datepicker" name="tahun"  value="<?=$tahun;?>">
                         </div> 
                         
						<div class="col-md-4 form-group">
							<select name="group" id="group" class="form-control">
                             	<option value="">Pilih</option>
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_group, nama_group 
                                                                    from p_group order by nama_group");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
											if ($dt_pil['id_group']==$_REQUEST['group']) {
												$cek = "selected";
											} else {
												$cek = "";
											}
											echo "<option value='".$dt_pil['id_group']."' $cek>".$dt_pil['nama_group']."</option>";
										}
                                    ?>
                                </select>
						</div>
                        <div class="col-md-6 form-group">
                         <input type="checkbox" id="checkall" name="checkall" value="1">&nbsp;CHeck All KPI
                        </div>       
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm">					
					<div class="form-row align-items-center">
						<div class="col-md-12 form-group">
							<table id="datable_1" class="table table-bordered table-striped table-sm w-100">
							<thead class="thead-primary">
                                <tr align="center">
                                    <th><b>No</b></th>
                                    <th><b>Nama KPI</b></th>
                                    <th><b>Satuan</b></th>
                                    <th><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
								//$id_organisasi = $_REQUEST['organisasi'];
								$id_unit = $_REQUEST['unit'];
								$id_group = $_REQUEST['group'];
								
                                $stmt_bsc = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective
                                                                from p_bsc_perspective ");
                                $i = 1;
                                while ($row_bsc = mysqli_fetch_array($stmt_bsc)) {			
                                    
                                    ?>
                                    <tr>
                                        <td><b><?php echo $i; ?></b></td>
                                        <td><b><?php echo $row_bsc['nama_bsc_perspective']; ?></b></td>
                                        <td></td>
                                        <td align="center"></td>
                                    </tr>
                                            
                                   <?php
                                   
                                   $stmt = "select distinct a.id_kpi, a.nama_kpi, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan
                                            from kpi a
                                                where a.id_kpi is not null and a.id_bsc_perspective = '".$row_bsc['id_bsc_perspective']."' and a.parent = '0'
												and a.status = '1'
                                                order by a.parent, a.urutan";
                                   // echo "<pre>$stmt</pre>";
                                    $query = mysqli_query($conn,$stmt);
                                    
                                    $j = 1;
                                    while ($row = mysqli_fetch_array($query)){
									
										$cek = cek($row['id_kpi'], $id_group, $tahun); 
										
										if ($cek>0) {
											$ceked = "checked";
										} else {
											$ceked = "";
										}
										
										
                                        ?>
                                        <tr>
                                            <td><?php echo $i.".".$j; ?></td>
                                            <td><?php echo $row['nama_kpi']; ?></td>
                                            <td align="center"><?php echo $row['satuan']; ?></td>
                                            <td align="center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $row['id_kpi'];?>" name="kpi_<?php echo $row['id_kpi'];?>" value="1" <?php echo $ceked;?>>
                                                <label class="custom-control-label" for="customCheck<?php echo $row['id_kpi'];?>">&nbsp;</label>
                                            </div>
                                            </td>
                                        </tr>
                                    <?php
                                        child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_group, $tahun);
                                            
                                        $j++;
                                    }
                                   
                                   
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
							
						</div>
                        <div class="col-md-4 form-group">
                        
                        </div>
                        <div class="col-md-4 form-group" align="right">
                        	  <button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                            <button type="button" class="btn btn-danger mb-2" name="batal" onclick="window.history.back();">Batal</button>
						</div>
					</div>
				</div>
			</div>
			</form>
				
		</div> 
	</div> 
	</div> 
	
     
   <!-- <div class="modal fade" id="InputModalForms" tabindex="-1" role="dialog" aria-labelledby="InputModalForms" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                	<form method="post" enctype="multipart/form-data">
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                        
                         <div class="form-group">
                            <label>BSC Perspective</label>
                             <select name="perspective" id="perspective" class="form-control" disabled="disabled">
                            	<option value="">Pilih BSC Perspective</option>
                            	<?php 
									$qry_pil = mysqli_query($conn,"select id_bsc_perspective, nama_bsc_perspective 
																from p_bsc_perspective order by nama_bsc_perspective");
									while ($dt_pil = mysqli_fetch_array($qry_pil)) {
										echo "<option value='".$dt_pil['id_bsc_perspective']."'>".$dt_pil['nama_bsc_perspective']."</option>";
									}
								?>
                            </select>
                         </div>
                         <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
                       </div>
                        <div class="form-group">
                            <label>Rumus</label>
                            <textarea class="form-control mt-15" rows="2" placeholder="rumus" name="rumus" id="rumus"></textarea>
                       </div>
                       <div class="form-group">
                       		<label>&nbsp;</label><br />
                             <button type="button" class="btn btn-primary mb-2" name="hapus" id="hapus2">Hapus</button>
                          <button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                          <button type="button" class="btn btn-danger mb-2" name="batal" id="batal">Batal</button>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
    -->
<?php
} 



function child($id_bsc_perspective, $parent, $i,  $id_group,$tahun){
	global $conn;
	
	$stmt = "select distinct a.id_kpi, a.nama_kpi, IFNULL(a.parent,0) parent, a.urutan, a.id_bsc_perspective, a.satuan
			from kpi a
			left join p_bsc_perspective c on a.id_bsc_perspective = c.id_bsc_perspective
				where a.id_kpi is not null and a.id_bsc_perspective = '".$id_bsc_perspective."' and a.parent = '".$parent."'
				order by a.parent, a.urutan";
	//echo "<pre>$stmt</pre>";
	$query = mysqli_query($conn,$stmt);
	
	$j = 1;
	while ($row = mysqli_fetch_array($query)){
		
		$cek = cek($row['id_kpi'], $id_group, $tahun); 
										
		
		if ($cek>0) {
			$ceked = "checked";
		} else {
			$ceked = "";
		}
		?>
		<tr>
			<td><?php echo $i.".".$j; ?></td>
			<td><?php echo $row['nama_kpi']; ?></td>
			<td align="center"><?php echo $row['satuan']; ?></td>
			<td align="center">
             <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $row['id_kpi'];?>" name="kpi_<?php echo $row['id_kpi'];?>" value="1"  <?php echo $ceked;?>>
                <label class="custom-control-label" for="customCheck<?php echo $row['id_kpi'];?>">&nbsp;</label>
            </div>
            </td>
		</tr>           
	<?php
		child($row['id_bsc_perspective'], $row['id_kpi'], $i.".".$j, $id_organisasi, $tahun);
		$j++;
	}
	
	
}

function cek($id_kpi, $id_group, $tahun){
	global $conn;
	
	$qry = mysqli_query($conn,"select distinct a.id_kpi 
								from kpi_organisasi  a
								inner join p_organisasi b on a.id_organisasi = b.id_organisasi
								where b.id_group = '".$id_group."' 
								and a.id_kpi = '".$id_kpi."' and a.tahun = '".$tahun."'");
	$dt = mysqli_fetch_array($qry);
	$jml = mysqli_num_rows($qry);
	
	return $jml;
}

?>