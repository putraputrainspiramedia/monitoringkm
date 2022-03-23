<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
	default :
		list_data();
		break;
}

function list_data() {
	global $conn;

	//$menu_arr = cek_priuser($_REQUEST['fl']);
?>
	<script language="javascript">
		
		/*tinymce.init({
		  selector: 'textarea#rumus',
		  menubar: false,
		  toolbar: 'undo redo Target Bobot Real Kali Bagi Kurang Tambah %',
		  content_css: [
			'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
			'//www.tiny.cloud/css/codepen.min.css'
		  ],
		  setup: function (editor) {
		  		editor.ui.registry.addButton('%', {
				  text: '%',
				  onAction: function (_) {
					editor.insertContent(' {%} ');
				  }
				});
				editor.ui.registry.addButton('Target', {
				  text: 'Target',
				  onAction: function (_) {
					editor.insertContent(' {target} ');
				  }
				});
				editor.ui.registry.addButton('Bobot', {
				  text: 'Bobot',
				  onAction: function (_) {
					editor.insertContent(' {bobot} ');
				  }
				});
				editor.ui.registry.addButton('Real', {
				  text: 'Real',
				  onAction: function (_) {
					editor.insertContent(' {real} ');
				  }
				});
				editor.ui.registry.addButton('Kali', {
				  text: 'X',
				  onAction: function (_) {
					editor.insertContent(' {X} ');
				  }
				});
				editor.ui.registry.addButton('Bagi', {
				  text: '/',
				  onAction: function (_) {
					editor.insertContent(' {/} ');
				  }
				});
				editor.ui.registry.addButton('Kurang', {
				  text: '-',
				  onAction: function (_) {
					editor.insertContent(' {-} ');
				  }
				});
				editor.ui.registry.addButton('Tambah', {
				  text: '+',
				  onAction: function (_) {
					editor.insertContent(' {+} ');
				  }
				});
			},
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
		});
		tinymce.triggerSave();*/
		 <?php if ($_SESSION['km_user_input']==1) { ?>
		function inputdata(idbc, idparent){				
			$("#hapus2").hide();
			$("#simpan").show();
			
			$('#perspective option').each(function() {
				if($(this).val() == idbc) {
					$('#perspective').prop("selected", true);
					$("#perspective").val(idbc);
				}
			});
			
			$("#parent").hide();
			var perspective = $("#perspective").val();
			var dataString = 'tp=kpi&perspective='+ perspective;
			$("#flash").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
			$.ajax({
				type		: "POST",
				url			: "./kpidata.php",
				data		: dataString,
				cache		: false,
				success		: function(html)	{	
					//alert(html);			
					$("#parent").show();
					$("#parent").html(html);
					$('#flash').fadeOut('fast');
					$('#parent option').each(function() {
						if($(this).val() == idparent) {
							$('#parent').prop("selected", true);
							$("#parent").val(idparent);
						}
					});
				}
			});
			$('.modal-title').html('Input KPI');
			$('#InputModalForms').modal('show');
				
		}		 
		<?php } ?>
		 <?php if ($_SESSION['km_user_delete']==1) { ?>   		
		function hapusdata(id,idbc, idparent,  nama, terbalik, urutan, satuan, realisasi, score, status){
			$("#hapus2").show();
			$("#simpan").hide();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#urutan").val(urutan);
			$("#satuan").val(satuan);
			//tinymce.activeEditor.setContent(rumus);
		
			$('#terbalik option').each(function() {
				if($(this).val() == terbalik) {
					$('#terbalik').prop("selected", true);
					$("#terbalik").val(terbalik);
				}
			});
			
			$('#perspective option').each(function() {
				if($(this).val() == idbc) {
					$('#perspective').prop("perspective", true);
					$("#perspective").val(idbc);
				}
			});
			
			$('#realisasi option').each(function() {
				if($(this).val() == realisasi) {
					$('#realisasi').prop("selected", true);
					$("#realisasi").val(realisasi);
				}
			});
			$('#score option').each(function() {
				if($(this).val() == score) {
					$('#score').prop("selected", true);
					$("#score").val(score);
				}
			});
			$('#status option').each(function() {
				if($(this).val() == status) {
					$('#status').prop("selected", true);
					$("#status").val(status);
				}
			});
			
			$("#parent").hide();
			var perspective = $("#perspective").val();
			var dataString = 'tp=kpi&perspective='+ perspective;
			$("#flash").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
			$.ajax({
				type		: "POST",
				url			: "./kpidata.php",
				data		: dataString,
				cache		: false,
				success		: function(html)	{	
					//alert(html);			
					$("#parent").show();
					$("#parent").html(html);
					$('#flash').fadeOut('fast');
					$('#parent option').each(function() {
						if($(this).val() == idparent) {
							$('#parent').prop("selected", true);
							$("#parent").val(idparent);
						}
					});
				}
			});
			
			$('.modal-title').html('Hapus KPI');
			$('#InputModalForms').modal('show');
			
		}
		<?php } ?>
		
		 <?php if ($_SESSION['km_user_edit']==1) { ?>
   		function editdata(id, idbc, idparent,  nama, terbalik, urutan,satuan, realisasi, score, status){		
			$("#hapus2").hide();
			$("#simpan").show();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#urutan").val(urutan);
			$("#satuan").val(satuan);
		
			//tinymce.activeEditor.setContent(rumus);
			
			$('#terbalik option').each(function() {
				if($(this).val() == terbalik) {
					$('#terbalik').prop("selected", true);
					$("#terbalik").val(terbalik);
				}
			});
			
			$('#perspective option').each(function() {
				if($(this).val() == idbc) {
					$('#perspective').prop("perspective", true);
					$("#perspective").val(idbc);
				}
			});
			
			$('#realisasi option').each(function() {
				if($(this).val() == realisasi) {
					$('#realisasi').prop("selected", true);
					$("#realisasi").val(realisasi);
				}
			});
			$('#score option').each(function() {
				if($(this).val() == score) {
					$('#score').prop("selected", true);
					$("#score").val(score);
				}
			});
			$('#status option').each(function() {
				if($(this).val() == status) {
					$('#status').prop("selected", true);
					$("#status").val(status);
				}
			});
			
			$("#parent").hide();
			var perspective = $("#perspective").val();
			var dataString = 'tp=kpi&perspective='+ perspective;
			$("#flash").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
			$.ajax({
				type		: "POST",
				url			: "./kpidata.php",
				data		: dataString,
				cache		: false,
				success		: function(html)	{	
					//alert(html);			
					$("#parent").show();
					$("#parent").html(html);
					$('#flash').fadeOut('fast');
					$('#parent option').each(function() {
						if($(this).val() == idparent) {
							$('#parent').prop("selected", true);
							$("#parent").val(idparent);
						}
					});
				}
			});
			
			$('.modal-title').html('Edit KPI');
			$('#InputModalForms').modal('show');
				
		}		 
		<?php } ?>
		
		//var table = $('#datable_1').DataTable();
		$(document).ready(function(){
			$('#loading_dealer').hide();
			$('#hasil').html('');
			
			 $.ajax({
				type		: "POST",
				url			: "./kpidata.php",
				data:{
					tp : 'view',
					fl : '<?php echo $_REQUEST['fl'];?>'
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
			
			$("#hapus2").hide();
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			 <?php if ($_SESSION['km_user_input']==1) { ?>
			$("#simpan").click(function () {
				
				//tinymce.triggerSave();
				// tinymce.get('rumus').getContent();
		
				var id = $("#id").val();
				var nama =  $("#nama").val();
				var urutan =  $("#urutan").val();
				var rumus =  $("#rumus").val();
				var satuan =  $("#satuan").val();
				var terbalik =  $('#terbalik option:selected').val();
				var parent =  $('#parent option:selected').val();
				var perspective =  $('#perspective option:selected').val();
				var realisasi =  $('#realisasi option:selected').val();
				var score =  $('#score option:selected').val();
				var status =  $('#status option:selected').val();
				
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				
				if (id!='') {
					var datane = 'tp=edit&nama='+nama+'&id='+id+'&urutan='+urutan+'&parent='+parent+'&perspective='+perspective+'&terbalik='+terbalik+'&satuan='+satuan+'&realisasi='+realisasi+'&score='+score+'&status='+status;
				} else {
					var datane = 'tp=input&nama='+nama+'&urutan='+urutan+'&parent='+parent+'&perspective='+perspective+'&terbalik='+terbalik+'&satuan='+satuan+'&realisasi='+realisasi+'&score='+score+'&status='+status;
				}
				$.ajax({
					type	: "POST",
					url		: "kpidata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						$.ajax({
							type		: "POST",
							url			: "./kpidata.php",
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
			
						$('#nama').val('');
						$('#id').val('');
						$("#urutan").val('');
						$("#parent").val('');
						$("#satuan").val('');
						$("#perspective").val('');
						$("#realisasi").val('');
						$("#score").val('');
						$("#status").val('');
						//tinymce.activeEditor.setContent('');
			
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
					}
				});
				
       		});
			<?php } ?>
			
			 <?php if ($_SESSION['km_user_delete']==11) { ?>   
			$("#hapus2").click(function () {
				var id = $("#id").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=delete&id='+id;
				$.ajax({
					type	: "POST",
					url		: "kpidata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						$.ajax({
							type		: "POST",
							url			: "./kpidata.php",
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
						$('#nama').val('');
						$('#id').val('');
						$("#urutan").val('');
						$("#parent").val('');
						$("#satuan").val('');
						$("#perspective").val('');
						$("#realisasi").val('');
						$("#score").val('');
						$("#status").val('');
						//tinymce.activeEditor.setContent('');
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
			<h6>Data Master KPI</h6>
		</div>
		<div class="card-body"> 
	
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
                         	<label>KPI Parent</label> 
                            <select name="parent" id="parent" class="form-control" disabled="disabled">
                            </select>
                         </div>
                         <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
                       </div>
                        <div class="form-group">
                            <label>Rumus Terbalik</label>
                            <select name="terbalik" id="terbalik" class="form-control">
                            	<option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                       </div>
                       <!--
                        <div class="form-group">
                            <label>Rumus</label>
                            <textarea class="form-control mt-15" rows="2" placeholder="rumus" name="rumus" id="rumus"></textarea>
                       </div>-->
                         <div class="form-group">
                            <label>Satuan</label>
                            <input type="text" class="form-control mb-6" id="satuan" placeholder="satuan" name="satuan" maxlength="12">
                       </div>
                        <div class="form-group">
                            <label>Realisasi</label>
                            <select name="realisasi" id="realisasi" class="form-control">
                            	<option value="0">Input</option>
                                <option value="1">Rumus (Score/Bobot)</option>
                                <option value="2">Rata2 dibawahnya</option>
                                <option value="3">Penjumlahan dibawahnya</option>
                            </select>
                       </div>
                        <div class="form-group">
                            <label>Score</label>
                            <select name="score" id="score" class="form-control">
                            	<option value="0">Rumus</option>
                                <option value="1">Penjumlahan dibawahnya</option>
                            </select>
                       </div>
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="text" class="form-control mb-6" id="urutan" placeholder="urutan" name="urutan" maxlength="3">
                       </div>
                         <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                            	<option value="0">Tidak Aktif</option>
                                <option value="1">Aktif</option>
                            </select>
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
    
    
<?php 
		}
		
		
	} 

?>