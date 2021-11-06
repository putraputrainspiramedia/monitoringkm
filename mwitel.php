<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
	default :
		list_data();
		break;
}

function list_data() {
	global $conn;

?>
	<script language="javascript">

		<?php if ($_SESSION['km_user_delete']==1) { ?>
		function hapusdata(id,nama, idreg){
			$("#hapus2").show();
			$("#simpan").hide();
			$("#id").val(id);
			$("#nama").val(nama);
			$('#regional option').each(function() {
				if($(this).val() == idreg) {
					$('#regional').prop("selected", true);
					$("#regional").val(idreg);
				}
			});
			$('.modal-title').html('Hapus Master Witel');
			$('#InputModalForms').modal('show');
			
		}
		<?php } ?>
		
		<?php if ($_SESSION['km_user_edit']==1) { ?>
		function editdata(id,nama, idreg){				
			$("#hapus2").hide();
			$("#simpan").show();
			$("#id").val(id);
			$("#nama").val(nama);
			//$("#regional").val(idreg);
			//$('#regional option[value='+idreg+']').attr('selected','selected');
			
			$('#regional option').each(function() {
				if($(this).val() == idreg) {
					$('#regional').prop("selected", true);
					$("#regional").val(idreg);
				}
			});

			
			$('.modal-title').html('Edit Master Witel');
			$('#InputModalForms').modal('show');
				
		}	
		<?php } ?>
			 
		//var table = $('#datable_1').DataTable();
		$(document).ready(function(){
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
					"url" : "mwiteldata.php",
					"type" : "post",
					"cache" : true,
					//"data" : {"sales": kdsales},
				},
				"deferRender": true,
				"columns" : [
					{"data": "nama_regional"},
					{"data": "nama_witel"},
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
			
			$("#hapus2").hide();
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			<?php if ($_SESSION['km_user_input']==1) { ?>
			$("#tambah").click(function () {
				$("#id").val('');
				$("#nama").val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input Master Witel');
           		$('#InputModalForms').modal('show');
       		});
			<?php } 
			
			if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
			$("#simpan").click(function () {
				var id = $("#id").val();
				var nama =  $("#nama").val();
				var regional =  $('#regional option:selected').val();
				
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				
				if (id!='') {
					var datane = 'tp=edit&nama='+nama+'&id='+id+'&regional='+regional;
				} else {
					var datane = 'tp=input&nama='+nama+'&regional='+regional;
				}
				$.ajax({
					type	: "POST",
					url		: "mwiteldata.php",
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
								"url" : "mwiteldata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_regional"},
								{"data": "nama_witel"},
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
			
       		});
			<?php } 
			
			if ($_SESSION['km_user_delete']==1) { ?>
			$("#hapus2").click(function () {
				var id = $("#id").val();
				var nama =  $("#nama").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=delete&id='+id;
				$.ajax({
					type	: "POST",
					url		: "mwiteldata.php",
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
								"url" : "mwiteldata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_regional"},
								{"data": "nama_witel"},
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
			
       		});
			<?php } ?>
		});
	
</script>


<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Data Master Witel</h6>
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
									<th>Regional</th>
									<th>Nama</th>
									<th>User Input</th>
									<th>Tgl Input</th>
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
                	<form method="post" enctype="multipart/form-data">
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                        
                         <div class="form-group">
                            <label>Regional</label>
                                <select name="regional" id="regional" class="form-control">
                                    <option value="0">Pilih regional</option>
                                    <?php 
                                        $qry_pil = mysqli_query($conn,"select id_regional, nama_regional from p_regional order by nama_regional");
                                        while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<option value='".$dt_pil['id_regional']."'>".$dt_pil['nama_regional']."</option>";
                                        }
                                    ?>
                                </select>
                         </div>
                         <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
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