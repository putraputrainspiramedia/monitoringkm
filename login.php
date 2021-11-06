<?php
session_start();
error_reporting(0);	
ini_set("magic_quotes_sybase",'On');
ini_set("magic_quotes_runtime",'On');


require_once ("./inc/conn.php");
require_once ("./inc/fungsi.php");

if (isset($_POST['login'])) {  
    global $conn;

	$user 	= antisqlinjection($_POST['username']);
	$pass	= md5(md5(antisqlinjection($_POST['pass'])));
	//  --
	$qry2 = "select id_user, username, pass, nama, id_profil
			from user_app 
			where username =  '".$user."' and pass = '".$pass."' and is_active = '1' ";
						
	$qry = mysqli_query($conn,$qry2);
	$cek =  mysqli_num_rows($qry);
	$dt = mysqli_fetch_array($qry);
	
	if ($cek!=0 and !empty($cek)) {	
	
		$km_nama = $dt['nama'];
		$km_user = $dt['id_user'];		
		$km_timeout = time();
		$km_profil = $dt['id_profil'];
				
		#session_register("km_user");			
		#session_register("km_timeout");
		#session_register("km_nama");
		
		$_SESSION['km_user'] = $km_user;
		$_SESSION['km_timeout'] = $km_timeout;
		$_SESSION['km_nama'] = $km_nama;
		$_SESSION['km_profil'] = $km_profil;
		
		setcookie("cookie_kmtelkom", "", time()+3600); 
		
		echo "<script>alert('Selamat datang ".$km_nama."');window.location.href='index.php';</script>";
		
					
	} else {
		session_destroy();	
		//session_unset($_SESSION['km_user']);
		//session_unset($_SESSION['km_timeout']);
		
		$_SESSION['km_user'] = "";
		$_SESSION['km_timeout'] = "";
		$_SESSION['km_nama'] = "";
		$_SESSION['km_profil'] = "";
		
		echo "<script>alert('Gagal Login !!');window.location.href='index.php';</script>";
		
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title>KM TR4 | Login</title>
   		 <meta name="description" content="bagus km plastik">
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="img/favicon.ico">
		<link rel="icon" href="img/favicon.ico" type="image/x-icon">
		
		<!-- Toggles CSS -->
		<link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
		<link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
		
		<!-- Custom CSS -->
		<link href="dist/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<!-- Preloader -->
		<div class="preloader-it">
			<div class="loader-pendulums"></div>
		</div>
		<!-- /Preloader -->
		
		<!-- HK Wrapper -->
		<div class="hk-wrapper">
			
			<!-- Main Content -->
            <div class="hk-pg-wrapper hk-auth-wrapper">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xl-12 pa-0">
							<div class="auth-form-wrap pt-xl-0 pt-70">
								<div class="auth-form w-xl-30 w-lg-55 w-sm-75 w-100">
									<center><img src="img/logo.png" width="50%" class="brand-img" /></center>
									<form method="post" enctype="multipart/form-data" action="">
										<div class="form-group">
											<input class="form-control" placeholder="Username" type="text" name="username">
										</div>
										<div class="form-group">
											<input class="form-control" placeholder="Password" type="password" name="pass">
										</div>
                                        <div class="form-row">
                                        	<div class="col-sm-12 mb-20">
												<button class="btn btn-primary btn-block" type="submit" name="login">Login</button>										
											</div>
                                        </div>
                                    </form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>            
			<!-- /Main Content -->
		
		</div>
		<!-- /HK Wrapper -->
		
		<!-- JavaScript -->
		
		<!-- jQuery -->
		<script src="vendors/jquery/dist/jquery.min.js"></script>
		
		<!-- Bootstrap Core JavaScript -->
		<script src="vendors/popper.js/dist/umd/popper.min.js"></script>
		<script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JavaScript -->
		<script src="dist/js/jquery.slimscroll.js"></script>
	
		<!-- Fancy Dropdown JS -->
		<script src="dist/js/dropdown-bootstrap-extended.js"></script>
		
		<!-- FeatherIcons JavaScript -->
		<script src="dist/js/feather.min.js"></script>
		
		<!-- Init JavaScript -->
		<script src="dist/js/init.js"></script>
		
		
	</body>
</html>