<?php
session_start(); 
//error_reporting(0);

ini_set("magic_quotes_gpc","On");
ini_set("magic_quotes_runtime","On");
ini_set("magic_quotes_sybase","On");
ini_set("max_execution_time",720);

require_once("inc/conn.php");
require_once("inc/fungsi.php");

if (empty($_SESSION['km_user'])) {
	header("Location:login.php");
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>.: Monitoring KM TR4 :.</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
	
	<!-- Toggles CSS -->
   <!-- <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
-->
    <!-- ION CSS -->
   <!-- <link href="vendors/ion-rangeslider/css/ion.rangeSlider.css" rel="stylesheet" type="text/css">
    <link href="vendors/ion-rangeslider/css/ion.rangeSlider.skinHTML5.css" rel="stylesheet" type="text/css">
	-->
    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
       

    <!-- Pickr CSS -->
    <!--<link href="vendors/pickr-widget/dist/pickr.min.css" rel="stylesheet" type="text/css" />
-->
    <!-- Daterangepicker CSS -->
    <link href="vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
    
    <link href="dist/css/stylenew.css" rel="stylesheet" type="text/css" />
	
    <!-- Tinymec 
    <link href="vendors/tinymce/style.css" rel="stylesheet" type="text/css">
	-->
	<script type="text/javascript">
		
		
	<!--
		var weekdaystxt=["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"]
	
		function showLocalTime(container, servermode, offsetMinutes, displayversion){
			if (!document.getElementById || !document.getElementById(container)) return
				this.container=document.getElementById(container)
			this.displayversion=displayversion
			var servertimestring=(servermode=="server-php")? '<?php echo date("F d, Y H:i:s", time())?>' : (servermode=="server-ssi") 
			this.localtime=this.serverdate=new Date(servertimestring)
			this.localtime.setTime(this.serverdate.getTime()+offsetMinutes*60*1000) //add user offset to server time
			this.updateTime()
			this.updateContainer()
		}
	
		showLocalTime.prototype.updateTime=function(){
			var thisobj=this
			this.localtime.setSeconds(this.localtime.getSeconds()+1)
			setTimeout(function(){thisobj.updateTime()}, 1000) //update time every second
		}
	
		showLocalTime.prototype.updateContainer=function(){
			var thisobj=this
			if (this.displayversion=="long")
				this.container.innerHTML=this.localtime.toLocaleString()
			else{
				var hour=this.localtime.getHours()
				var minutes=this.localtime.getMinutes()
				var seconds=this.localtime.getSeconds()
				var ampm=(hour>=12)? "PM" : "AM"
				var dayofweek=weekdaystxt[this.localtime.getDay()]
				this.container.innerHTML=formatField(hour, 1)+":"+formatField(minutes)+":"+formatField(seconds)+" "+ampm+" "
			}
			setTimeout(function(){thisobj.updateContainer()}, 1000) //update container every second
		}
	
		function formatField(num, isHour){
			if (typeof isHour!="undefined"){ //if this is the hour field
				var hour=(num>12)? num-12 : num
				return (hour==0)? 12 : hour
			}
			return (num<=9)? "0"+num : num//if this is minute or sec field
		}
	//-->
	</script>
</head>

<body>

	    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Jasny-bootstrap  JavaScript -->
    <script src="vendors/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Ion JavaScript -->
   <!-- <script src="vendors/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
    <script src="dist/js/rangeslider-data.js"></script>
-->
    <!-- Toggles JavaScript -->
    <!--<script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>
-->
    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>
    
    <!-- Bootstrap Tagsinput JavaScript -->
    <!--<script src="vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>-->

    <!-- Bootstrap Input spinner JavaScript -->
    <script src="vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js"></script>
    <script src="dist/js/inputspinner-data.js"></script>

    <!-- Pickr JavaScript --><!--
    <script src="vendors/pickr-widget/dist/pickr.min.js"></script>
    <script src="dist/js/pickr-data.js"></script>-->

    <!-- Daterangepicker JavaScript -->
   <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/daterangepicker/daterangepicker.js"></script>
    <script src="dist/js/daterangepicker-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="dist/js/feather.min.js"></script>

    <!-- Toggles JavaScript -->
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
	
	<!--
	 <link href="vendors/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
    <script src="vendors/datepicker/js/bootstrap-datepicker.js"></script>
    -->
     <link href="vendors/datepicker/css/datepicker2.css" rel="stylesheet" type="text/css" />
    <script src="vendors/datepicker/js/bootstrap-datepicker2.js"></script>
	
    <!-- Tablesaw JavaScript --><!--
    <link href="vendors/tablesaw/dist/tablesaw.css" rel="stylesheet" type="text/css" />
    <script src="vendors/tablesaw/dist/tablesaw.jquery.js"></script>
    <script src="dist/js/tablesaw-data.js"></script>
-->
	
	 <!-- Data Table JavaScript -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net/css/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css" />
    
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script><!--
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="dist/js/dataTables-data.js"></script>
	-->
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables.net/js/dataTables.fixedColumns.min.js"></script>

	<!-- tinymce -->
    <!--<script src="vendors/tinymce/jquery.tinymce.min.js"></script>
    <script src="vendors/tinymce/tinymce.min.js"></script>-->
	
    <!-- Preloader -->
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>
    <!-- /Preloader -->
	
	<!-- HK Wrapper -->
	<div class="hk-wrapper hk-alt-nav hk-icon-nav">

        <!-- Top Navbar -->
		<?php include("menu.php"); ?>
        <!-- /Top Navbar -->
        

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
			<!-- Container -->
            <div class="container-fluid mt-5">
                <!-- Row -->
				 <div class="row">
				 	<div class="col-12">
					<?php 
						$hari =	date("l");
						$bulan= date("F");
						$tanggal= date("d");
						$tahun= date("Y");
						echo $hari.", ".$tanggal." ".$bulan."  ".$tahun."";
						?> 
						&nbsp;<span id="timecontainer"></span>
						<script type="text/javascript">
						<!--
							new showLocalTime("timecontainer", "server-php", 0, "short")
						//-->
						</script>&nbsp;&nbsp;
					</div>
				</div> 
                <div class="row">
                    <div class="col-xl-12">
						<div class="hk-row">
						<?php
						if (!empty($_REQUEST['fl'])) {
							if (is_file("./".$_REQUEST['fl'].".php")) {
								include("./".$_REQUEST['fl'].".php");
							} else {
								echo "File not found";	
							}
						}else{
							include("./home.php");
						}
						?>
						</div>	
					</div>
                </div>
                <!-- /Row -->
			</div>
            <!-- /Container -->
			<!-- Footer -->
            <div class="hk-footer-wrap container-fluid">
                <footer class="footer">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
							<?php
								$thn = "";
								$tahun = date("Y");
								if ($tahun=="2021") {
									$thn = "2021";
								} else {
									$thn = "2021-".$tahun;
								}
							?>
                            <p>&copy; <b>PT.Telkom Indonesia, tbk</b> <?php echo $thn;?></p>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->
    </div>
    <!-- /HK Wrapper -->

 
</body>

</html>