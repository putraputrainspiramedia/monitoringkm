<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
	case 'input':
		input_data();
		break;
	case 'edit':
		edit_data();
		break;
	case 'delete':
		delete_data();
		break;	
	default :
		list_data();
		break;
}

function list_data() {
	global $conn;

?>


	<script language="javascript">

		<?php if ($_SESSION['km_user_delete']==1) { ?>
		function hapusdata(id, username, nama, idprofil, idunit, idorganisasi){
			
			//$("#frm").find('select').prop('selectedIndex', -1); 
			//$("#organisasi").multiselect('refresh'); 
						
			$("#hapus2").show();
			$("#simpan").hide();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#username").val(username);
			$(".ket").hide();
			
			$('#unit option').each(function() {
				if($(this).val() == idunit) {
					$('#unit').prop("selected", true);
					$("#unit").val(idunit);
				}
			});
			$('#organisasi option').each(function() {
				if($(this).val() == idorganisasi) {
					$('#organisasi').prop("selected", true);
					$("#organisasi").val(idorganisasi);
				}
			});
			$('#profil option').each(function() {
				if($(this).val() == idprofil) {
					$('#profil').prop("selected", true);
					$("#profil").val(idprofil);
				}
			});
			$('.modal-title').html('Hapus Data User');
			$('#InputModalForms').modal('show');
			
		}
		<?php }
		
		if ($_SESSION['km_user_edit']==1) { ?>
		function editdata(id, username, nama, idprofil, idunit, idorganisasi){	
			//$("#frm").find('select').prop('selectedIndex', -1); 
			//$('#organisasi').val(null).trigger('change');
			//$(".select2").select2("val", "");
			//$("#organisasi").val('').trigger('change');
			//$('#organisasi option').each(function() {
		//			$('#organisasi').removeAttr("selected");
		//			$("#organisasi").val("");
		//	});
			
			$("#hapus2").hide();
			$("#simpan").show();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#username").val(username);
			$(".ket").show();
			//$("#regional").val(idreg);
			//$('#regional option[value='+idreg+']').attr('selected','selected');
			$('#unit option').each(function() {
				if($(this).val() == idunit) {
					$('#unit').prop("selected", true);
					$("#unit").val(idunit);
				}
			});
			//alert(idorganisasi);
			var org_arr = idorganisasi.split("_");			
			$('#organisasi option').each(function() {
				for (var i=0;i<org_arr.length;i++) {
					//alert(org_arr[i]);
					var isi = org_arr[i];
					if($(this).val() == isi) {
						$('#organisasi').prop("selected", true);
						$("#organisasi").val(isi);
					}
				}
			});
			
			$('#profil option').each(function() {
				if($(this).val() == idprofil) {
					$('#profil').prop("selected", true);
					$("#profil").val(idprofil);
				}
			});

			
			$('.modal-title').html('Edit Data User');
			$('#InputModalForms').modal('show');
				
		}		
		<?php } ?>
		 
		//var table = $('#datable_1').DataTable();
		$(document).ready(function(){
			
			$('.select2').select2();
			var dtTable = $('#datable_1').DataTable();		
			dtTable.destroy();
			
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
					"url" : "userdata.php",
					"type" : "post",
					"cache" : true,
					//"data" : {"sales": kdsales},
				},
				"deferRender": true,
				"columns" : [
					{"data": "nama"},
					{"data": "username"},
					{"data": "nama_unit"},
					{"data": "nama_organisasi"},
					{"data": "nama_profil"},
					{"data": "action"},
				],
					"fixedColumns": {
					"leftColumns": 2
				},
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
				//	$('td:eq(1)', nRow).attr("id", aData['id']);
				//	$('td:eq(2)', nRow).attr("id", aData['id']);
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
			
			$("#hapus2").hide();
			
			<?php if ($_SESSION['km_user_input']==1) { ?>
			$("#tambah").click(function () {
				$("#id").val('');
				$("#nama").val('');
				$('#organisasi').val('');
				$('#unit').val('');
				$('#organisasi').val('');
				$('#pass').val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input User');
           		$('#InputModalForms').modal('show');
				$(".ket").hide();
       		});
			<?php } ?>
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			<?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
			$("#simpan").click(function () {
				var id = $("#id").val();
				var nama =  $("#nama").val();
				var username =  $("#username").val();
				var pass =  $("#pass").val();
				var profil =  $('#profil option:selected').val();
				//var organisasi =  $('#organisasi option:selected').val();
				var munit =  $('#unit option:selected').val();
								
				var organisasi = [];
				$.each($("#organisasi option:selected"), function(){            
					organisasi.push($(this).val());
				});
				//alert("You have selected the country - " + organisasi.join(","));
				
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				
				if (id!='') {
					var datane = 'tp=edit&nama='+nama+'&id='+id+'&profil='+profil+'&username='+username+'&pass='+pass+'&organisasi='+organisasi.join(",")+'&unit='+munit;
				} else {
					var datane = 'tp=input&nama='+nama+'&profil='+profil+'&username='+username+'&pass='+pass+'&organisasi='+organisasi.join(",")+'&unit='+munit;
				}
				
				
				$.ajax({
					type	: "POST",
					url		: "userdata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						var dtTable = $('#datable_1').DataTable();		
						dtTable.destroy();
						
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
								"url" : "userdata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama"},
								{"data": "username"},
								{"data": "nama_unit"},
								{"data": "nama_organisasi"},
								{"data": "nama_profil"},
								{"data": "action"},
							],
								"fixedColumns": {
								"leftColumns": 2
							},
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
							//	$('td:eq(1)', nRow).attr("id", aData['id']);
							//	$('td:eq(2)', nRow).attr("id", aData['id']);
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
						
						$('#username').val('');
						$('#pass').val('');
						$('#nama').val('');
						$('#id').val('');
						$('#organisasi').val('');
						$('#unit').val('');
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
						//$("#frm").find('select').prop('selectedIndex', -1); 
						//$("#organisasi").multiselect('refresh'); 
					}
				});
				
       		});
			<?php
			}
			
			if ($_SESSION['km_user_delete']==1) { ?>
			$("#hapus2").click(function () {
				var id = $("#id").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=delete&id='+id;
				$.ajax({
					type	: "POST",
					url		: "userdata.php",
					data	: datane,
					success	: function(data){
						alert(data);
						
						var dtTable = $('#datable_1').DataTable();		
						dtTable.destroy();
						
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
								"url" : "userdata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama"},
								{"data": "username"},
								{"data": "nama_unit"},
								{"data": "nama_organisasi"},
								{"data": "nama_profil"},
								{"data": "action"},
							],
								"fixedColumns": {
								"leftColumns": 2
							},
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
							//	$('td:eq(1)', nRow).attr("id", aData['id']);
							//	$('td:eq(2)', nRow).attr("id", aData['id']);
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
						
						$('#username').val('');
						$('#pass').val('');
						$('#nama').val('');
						$('#id').val('');
						$('#organisasi').val('');
						$('#unit').val('');
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
			<h6>Data User</h6>
		</div>
		<div class="card-body"> 
	
    		<div class="row">
				<div class="col-sm">
					<div class="table-wrap">
                    	<?php if ($_SESSION['km_user_input']==1) { ?>
						 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#InputModalForms" id="tambah">Tambah Data</button>
						<?php } 
						
						if ($_SESSION['km_user_view']==1) { ?>
						<table id="datable_1"  class="table table-bordered table-striped table-sm w-100">
							 <thead class="thead-primary">
								<tr align="center">
									<th>Nama</th>
									<th>Username</th>
									<th>Unit</th>
									<th>Organisasi</th>
									<th>Profil</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
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
                	<form method="post" enctype="multipart/form-data" id="frm">
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                        
                         <div class="form-group">
                            <label>Profil</label>
                                <select name="profil" id="profil" class="form-control">
                                    <option value="">Pilih profil</option>
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_profil, nama_profil from user_profil order by nama_profil");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_profil']."'>".$dt_pil['nama_profil']."</option>";
                                        }
                                    ?>
                                </select>
                         </div>
                         <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control mb-6" id="username" placeholder="username" name="username" maxlength="10">
                       </div>
                       <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control mb-6" id="pass" placeholder="password" name="pass" maxlength="15">
                            <label class="ket"> *) Jika tidak diubah, mohon dikosongi</label>
                       </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
                       </div>
                        <div class="form-group">
                            <label>Organisasi</label>
                                 <select class="select2 select2-multiple" multiple="multiple" data-placeholder="Choose" id="organisasi" name="organisasi[]" required>
                                     <?php 
                                        $qry_pil = mysqli_query($conn,"select id_organisasi, nama_organisasi from p_organisasi order by nama_organisasi");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_organisasi']."'>".$dt_pil['nama_organisasi']."</option>";
                                        }
                                    ?>
                                </select>
                         </div>
                          <div class="form-group">
                            <label>Unit</label>
                                <select name="unit" id="unit" class="form-control">
                                    <option value="">Pilih unit</option>
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_unit, nama_unit from p_unit order by nama_unit");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_unit']."'>".$dt_pil['nama_unit']."</option>";
                                        }
                                    ?>
                                </select>
                         </div>
                       <div class="form-group">
                       		<label>&nbsp;</label><br />
                            <?php if ($_SESSION['km_user_delete']==1) { ?>
                             <button type="button" class="btn btn-primary mb-2" name="hapus" id="hapus2">Hapus</button>
                          <?php } 
						  if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
                          <button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
                          <?php } ?>
                          <button type="button" class="btn btn-danger mb-2" name="batal" id="batal">Batal</button>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
	
<?php } 
}
?>