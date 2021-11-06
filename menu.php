

        <nav class="navbar navbar-expand-xl navbar-light fixed-top hk-navbar hk-navbar-alt">
            <a class="navbar-toggle-btn nav-link-hover navbar-toggler" href="javascript:void(0);" data-toggle="collapse" data-target="#navbarCollapseAlt" aria-controls="navbarCollapseAlt" aria-expanded="false" aria-label="Toggle navigation"><span class="feather-icon"><i data-feather="menu"></i></span></a>
            <div class="collapse navbar-collapse" id="navbarCollapseAlt">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    
                    <?php 
						$qry_menu = mysqli_query($conn,"select a.id_menu, b.nama_menu, b.link,
												(select count(*) from user_menu x where x.parent = a.id_menu) jml
												from user_profil_menu a
												inner join user_menu b on a.id_menu = b.id_menu
												where a.id_profil = '".$_SESSION['km_profil']."' and b.is_active = '1' and parent = 0
												order by b.urutan");
						while ($dt_menu = mysqli_fetch_array($qry_menu)) {
							if ($dt_menu['jml']>0) {
						?>
                            	 <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $dt_menu['nama_menu'];?></a>
                                    <div class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
        							<?php
										menu_det($dt_menu['id_menu']);
									?>
                                    </div>                                    
                                </li>
                        <?php
							} else {
						?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo $dt_menu['link'];?>"><?php echo $dt_menu['nama_menu'];?></a>
                                </li>
                    	<?php   
							}
						}
					?>
                </ul>		
            </div>
            <ul class="navbar-nav hk-navbar-content">
              
                <li class="nav-item dropdown dropdown-authentication">
                    <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <div class="media-body">
                                <span><?=$_SESSION['km_nama']?><i class="zmdi zmdi-chevron-down"></i></span></div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                        <a class="dropdown-item" href="logout.php"><i class="dropdown-icon zmdi zmdi-power"></i><span>Log out</span></a>                    </div>
                </li>
            </ul>
        </nav>
        
        
<?php

function menu_det($id){
	global $conn;
	
		$qry_menu = mysqli_query($conn,"select a.id_menu, b.nama_menu, b.link
							from user_profil_menu a
							inner join user_menu b on a.id_menu = b.id_menu
							where a.id_profil = '".$_SESSION['km_profil']."' and b.is_active = '1' and parent = '".$id."'
							order by b.urutan");
	while ($dt_menu = mysqli_fetch_array($qry_menu)) { ?>
    	<a class="dropdown-item" href="<?php echo $dt_menu['link'];?>"><?php echo $dt_menu['nama_menu'];?></a>      
    <?php 
	}
	
}
?>        