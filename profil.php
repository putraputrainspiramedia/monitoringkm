<?php
require_once("./cek_priv.php");

switch($_REQUEST['tp']) {
	default :
		list_data();
		break;
}

function list_data() {
	global $conn;

	/*$_SESSION['km_user_input'] = $data_cek['is_input'];
	$_SESSION['km_user_edit'] = $data_cek['is_edit'];
	$_SESSION['km_user_delete'] = $data_cek['is_delete'];
	$_SESSION['km_user_view']
	*/
?>

	<script language="javascript">

		<?php if ($_SESSION['km_user_delete']==1) { ?>
		function hapusdata(id, nama, menu_id){
			$("#hapus2").show();
			$("#simpan").hide();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#tp").val('delete');
			$('.modal-title').html('Hapus Data Profil');
			$('#InputModalForms').modal('show');	
			$('.cek').prop('checked', false);	
			const myArr = menu_id.split(",");
			const menu = ["x"];
			
			for (var i =0; i<myArr.length; i++){ 
				menu.push(myArr[i]); 
			}
			
			<?php
			 $qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu where is_active = '1' 
											order by urutan");
			$i=0;
			while ($dt_pil = mysqli_fetch_array($qry_pil)) {
				?>
				if (menu.includes("<?=$dt_pil['id_menu'];?>")) {
					$('#menu_<?=$dt_pil['id_menu'];?>').prop('checked', true);
				}
				<?php
			}										
			?>	
		}
		<?php } ?>
		
		<?php if ($_SESSION['km_user_edit']==1) { ?>
		function editdata(id, nama, menu_id){				
			$("#hapus2").hide();
			$("#simpan").show();
			$("#id").val(id);
			$("#nama").val(nama);
			$("#tp").val('edit');
			$('.modal-title').html('Edit Data Profil');
			$('#InputModalForms').modal('show');
			$('.cek').prop('checked', false);
			const myArr = menu_id.split(",");
			const menu = ["x"];
			
			for (var i =0; i<myArr.length; i++){ 
				menu.push(myArr[i]); 
			}
			
			<?php
			 $qry_pil = mysqli_query($conn,"select id_menu, nama_menu from user_menu where is_active = '1' 
											order by urutan");
			$i=0;
			while ($dt_pil = mysqli_fetch_array($qry_pil)) {
				?>
				if (menu.includes("<?=$dt_pil['id_menu'];?>")) {
					$('#menu_<?=$dt_pil['id_menu'];?>').prop('checked', true);
				}
				<?php
			}										
			?>
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
					"url" : "profildata.php",
					"type" : "post",
					"cache" : true,
					//"data" : {"sales": kdsales},
				},
				"deferRender": true,
				"columns" : [
					{"data": "nama_profil"},
					{"data": "menu"},
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
			$("#batal").click(function () {
           		$('#InputModalForms').modal('hide');
       		});
			
			<?php if ($_SESSION['km_user_input']==1) { ?>
			$("#tambah").click(function () {
				$("#tp").val('input');
				$("#id").val('');
				$("#nama").val('');
				$("#hapus2").hide();
				$("#simpan").show();
				$('.modal-title').html('Input Profil');
           		$('#InputModalForms').modal('show');
				$(".ket").hide();
       		});
			<?php } 
			
			if ($_SESSION['km_user_input']==1 or $_SESSION['km_user_edit']==1) { ?>
			$("#simpan").click(function () {
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
					url		: "profildata.php",
					data	: $("#frm").serialize(),
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
								"url" : "profildata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_profil"},
								{"data": "menu"},
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
						
						$('.cek').prop('checked', false);
						$('#nama').val('');
						$('#id').val('');
						$('#InputModalForms').modal('hide');
						$("#load2").fadeOut('fast');
					}
				});			
       		});
			<?php } 
			
			if ($_SESSION['km_user_view']==1) { ?>
			$("#hapus2").click(function () {
				var id = $("#id").val();
				$("#load2").fadeIn(400).html('<img src="./img/loading.gif" align="absmiddle"> <span class="loading">Loading ...</span>');
				var datane = 'tp=delete&id='+id;
				$.ajax({
					type	: "POST",
					url		: "profildata.php",
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
								"url" : "profildata.php",
								"type" : "post",
								"cache" : true,
								//"data" : {"sales": kdsales},
							},
							"deferRender": true,
							"columns" : [
								{"data": "nama_profil"},
								{"data": "menu"},
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
						
						$('.cek').prop('checked', false);
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
			<h6>Profil User</h6>
		</div>
		<div class="card-body"> 
	
    		<div class="row">
				<div class="col-sm">
					<div class="table-wrap">
                    <?php if ($_SESSION['km_user_input']==1) { ?>
						 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#InputModalForms" id="tambah">Tambah Data</button>
					<?php } 
					if ($_SESSION['km_user_view']==1) { ?>
                    	<table id="datable_1"  class="table table-bordered table-striped w-100">
							 <thead class="thead-primary">
								<tr align="center">
									<th>Nama</th>
									<th>Menu</th>
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
            			<input type="hidden" name="tp" id="tp" value="" readonly="readonly" />
            			<input type="hidden" name="id" id="id" value="" readonly="readonly" />
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control mb-6" id="nama" placeholder="nama" name="nama" maxlength="100">
                       </div>
                        <div class="form-group">
                            <label>Menu</label>
                            <br />
                                   <?php 
                                        $qry_pil = mysqli_query($conn,"select a.id_menu, a.nama_menu, 
																	(select count(*) from user_menu b where b.parent = a.id_menu) jml
																from user_menu a
																where a.is_active = '1' and a.parent = '0' 
																order by a.urutan");
                                        $i=0;
										while ($dt_pil = mysqli_fetch_array($qry_pil)) {
                                            echo "<input id='menu_".$dt_pil['id_menu']."' name='menu_".$dt_pil['id_menu']."' type='checkbox' value='".$dt_pil['id_menu']."' class='cek' $cek>&nbsp;".$dt_pil['nama_menu']."<br />";
											if ($dt_pil['jml']==0) {
												echo "--<input id='menuinput_".$dt_pil['id_menu']."' name='menuinput_".$dt_pil['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Input - <input id='menuedit_".$dt_pil['id_menu']."' name='menuedit_".$dt_pil['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Edit - <input id='menudelete_".$dt_pil['id_menu']."' name='menudelete_".$dt_pil['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Delete - <input id='menuview_".$dt_pil['id_menu']."' name='menuview_".$dt_pil['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;View<br />";
											} else { 
												menu_detail($dt_pil['id_menu'],$i);
                                        	}
										}
                                    ?>
                         </div>
                       <div class="form-group">
                       		<label>&nbsp;</label><br />
                            <?php if ($_SESSION['km_user_delete']==1) { ?>
                             <button type="button" class="btn btn-primary mb-2" name="hapus" id="hapus2">Hapus</button>
                          <?php } 
						  
						  if ($_SESSION['km_user_input']==1  ) {?>
                          <button type="button" class="btn btn-primary mb-2" name="simpan" id="simpan">Simpan</button>
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



function menu_detail($id,$i){
	global $conn;
	
	$i++;
	$qry_menu = mysqli_query($conn,"select id_menu, nama_menu
						from user_menu b
						where is_active = '1' and parent = '".$id."'
							order by urutan");
	while ($dt_menu = mysqli_fetch_array($qry_menu)) { 
		echo "|---<input id='menu_".$dt_menu['id_menu']."' name='menu_".$dt_menu['id_menu']."' type='checkbox' value='".$dt_menu['id_menu']."'  class='cek' $cek>&nbsp;".$dt_menu['nama_menu']."<br />";
		echo "|-----<input id='menuinput_".$dt_menu['id_menu']."' name='menuinput_".$dt_menu['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Input - <input id='menuedit_".$dt_menu['id_menu']."' name='menuedit_".$dt_menu['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Edit - <input id='menudelete_".$dt_menu['id_menu']."' name='menudelete_".$dt_menu['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;Delete - <input id='menuview_".$dt_menu['id_menu']."' name='menuview_".$dt_menu['id_menu']."' type='checkbox' value='1' class='cek' $cek>&nbsp;View<br />";
		$i++;
	}
}



?>