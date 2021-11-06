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
		function hapusdata(id,nama,nilai,cap1,cap2){
			$("#hapus2").show();
			$("#simpan").hide();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#nilai").val(nilai);
			$("#cap1").val(cap1);
			$("#cap2").val(cap2);
			$('.modal-title').html('Hapus Master BSC Perspective');
			$('#InputModalForms').modal('show');
			
		}
		<?php } 
		
		if ($_SESSION['km_user_edit']==1) { ?>
		function editdata(id,nama, nilai, cap1, cap2){				
			$("#hapus2").hide();
			$("#simpan").show();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#nilai").val(nilai);
			$("#cap1").val(cap1);
			$("#cap2").val(cap2);
			$('.modal-title').html('Edit Master BSC Perspective');
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
					"url" : "mbsc_perspectivedata.php",
					"type" : "post",
					"cache" : true,
					//"data" : {"sales": kdsales},
				},
				"deferRender": true,
				"columns" : [
					{"data": "nama_bsc_perspective"},
					{"data": "nilai"},
					{"data": "cap"},
					{"data": "cap2"},
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
			
			/*$('body').on('click','img',function(){
				$("#hapus2").hide();
				$("#simpan").show();
				$("#nama").val($("#idx").val());
				$('.modal-title').html('Edit Master Regional');
           		$('#InputModalForms').modal('show');
			});*/
			
			$("#hapus2").hide();
			
			<?php if ($_SESSION['km_user_input']==1) { ?>
			$("#tambah").click(function () {
				$("#id").val('');
				$("#nama").val('');
				$("#nilai").val('');
				$('#cap1').val('');
				$('#cap2').val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input Master BSC Perspective');
           		$('#InputModalForms').modal('show');
       		});
			<?php } ?>
			
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			<?php if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
			$("#simpan").click(function () {
				var id = $("#id").val();
				var nama =  $("#nama").val();
				var nilai =  $("#nilai").val();
				var cap1 =  $("#cap1").val();
				var cap2 =  $("#cap2").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				
				if (id!='') {
					var datane = 'tp=edit&nama='+nama+'&id='+id+'&nilai='+nilai+'&cap1='+cap1+'&cap2='+cap2;
				} else {
					var datane = 'tp=input&nama='+nama+'&nilai='+nilai+'&cap1='+cap1+'&cap2='+cap2;
				}
				$.ajax({
					type	: "POST",
					url		: "mbsc_perspectivedata.php",
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
								"url" : "mbsc_perspectivedata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_bsc_perspective"},
								{"data": "nilai"},
								{"data": "cap"},
								{"data": "cap2"},
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
						$('#nilai').val('');
						$('#cap1').val('');
						$('#cap2').val('');
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
					url		: "mbsc_perspectivedata.php",
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
								"url" : "mbsc_perspectivedata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_bsc_perspective"},
								{"data": "nilai"},
								{"data": "cap"},
								{"data": "cap2"},
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
						$('#nilai').val('');
						$('#cap1').val('');
						$('#cap2').val('');
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
					}
				});
			
       		});
			<?php } ?>
			
			$(".angka").keydown(function (e) {
					// Allow: backspace, delete, tab, escape, enter and .
					if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
						 // Allow: Ctrl+A
						(e.keyCode == 65 && e.ctrlKey === true) || 
						 // Allow: home, end, left, right
						(e.keyCode >= 35 && e.keyCode <= 39)) {
							 // let it happen, don't do anything
							 return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});
					
		});
	
</script>


<div class="col-md-12">
	<div class="card">
		<div class="card-header card-header-action">
			<h6>Data Master Bsc Perspective</h6>
		</div>
		<div class="card-body"> 
	
    		<div class="row">
				<div class="col-sm">
					<div class="table-wrap">
                    	<?php if ($_SESSION['km_user_input']==1) { ?>
							 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#InputModalForms" id="tambah">Tambah Data</button>
						<?php } 
						
						if ($_SESSION['km_user_view']==1) {?>
                        <table id="datable_1" class="table table-bordered table-striped table-sm w-100">
							 <thead class="thead-primary">
								<tr align="center">
									<th>Nama</th>
									<th>Nilai Bobot</th>
									<th>CAP Min</th>
									<th>CAP Max</th>
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
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
                       </div> 
                       <div class="form-group">
                            <label>Bobot</label>
                            <input type="text" class="form-control mb-6 angka" id="nilai" placeholder="nilai bobot" name="nilai" maxlength="10">
                       </div> 
                       <div class="form-group">
                            <label>CAP Min</label>
                            <input type="text" class="form-control mb-6 angka" id="cap1" placeholder="CAP minimal" name="cap1" maxlength="3">
                       </div>
                       <div class="form-group">
                            <label>CAP Max</label>
                            <input type="text" class="form-control mb-6 angka" id="cap2" placeholder="CAP maximal" name="cap2" maxlength="3">
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