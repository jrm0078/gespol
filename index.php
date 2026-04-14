<!DOCTYPE html>
<html>
<?php include("inc/seguridad.php"); ?>

<head>

<script>
	var user_codigo = '<?php echo  $_SESSION["user_codigo"];?>';
	var user_descripcion = '<?php echo  $_SESSION["user_descripcion"];?>';
	var user_rol = '<?php echo  $_SESSION["user_rol"];?>';
	var user_email = '<?php echo  $_SESSION["user_email"];?>';
</script>

	<!-- PARA EVITAR LA CACHÉ -->
    <meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Catalweb">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Administración</title>
	<!-- DataTables CSS -->
	<link href="libs/DataTables_OLD/DataTables-1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">
	<!-- DataTables BUTTONS CSS -->
	<link href="libs/DataTables_OLD/Buttons-1.6.5/css/buttons.dataTables.min.css" rel="stylesheet">	
	
	<!-- CSS sweetalerts-->	
	<link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">	
	<!-- CSS Select2-->	
    <link rel="stylesheet" type="text/css" href="libs/select2/dist/css/select2.min.css">
    
	<!-- bootstrap-->	
	<link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">	
	
	<!-- FONT AWESOME -->
    <link href="libs/fontawesome-free-5.15.1-web/css/all.css" rel="stylesheet">	
	
	<!-- Material Design Icons -->
    <link href="libs/MaterialDesign-Webfont-master/css/materialdesignicons.min.css" rel="stylesheet">
	


	<!-- Custom CSS -->
    <link href="css/style.min.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	
	<!-- CSS Plantillas -->
	<link href="css/plantillas.css" rel="stylesheet">
	
	<!-- TinyMCE CSS -->
	<link rel="stylesheet" href="libs/tinymce/skins/content/default/content.css">
	

	<!-- REMOVIDAS: Referencias CDN para IE8 (obsoleto) - Todas las librerías ahora son locales por seguridad -->
</head>


<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
	
	<audio id="sonidonotificacion" class="audio">
		<source src="sounds/notifica.mp3" type="audio/mpeg">
	</audio>	

	<audio id="alerta" class="audio">
		<source src="sounds/alerta.mp3" type="audio/mpeg">
	</audio>		
	
    <div id="main-wrapper" data-layout="vertical" data-sidebar-position="fixed" data-sidebartype="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand d-flex align-items-center" href="javascript:void(0);" onclick="document.getElementById('titulopagina').innerHTML='<i class=\'mdi mdi-home\'></i> Inicio'; document.getElementById('panelcentral').innerHTML='';" style="padding: 10px 0; cursor: pointer; overflow: hidden; width: 100%;">
                        <!-- Logo icon - siempre visible -->
                        <b class="logo-icon" style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; min-width: 40px; flex-shrink: 0;">
                            <i class="fas fa-cloud" style="color: white;"></i>
                        </b>
                        <!-- Logo text - se oculta en mini-sidebar via style.css -->
                        <span class="logo-text" style="color: white; font-weight: bold; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px; white-space: nowrap; margin-left: 10px;">
                            CDiCloud
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    
					
					<ul class="navbar-nav float-left mr-auto">
						<li class="nav-item"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0);"><i class="mdi mdi-menu font-24"></i></a></li>
						<li class="nav-item d-flex align-items-center"><span style="color: white; font-size: 1.4rem; font-weight: 600; padding-left: 10px; letter-spacing: 0.5px;">Panel de Administración</span></li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->

					
					
                    <ul class="navbar-nav float-right">

						<div>
								<a class="nav-link dropdown-toggle waves-effect waves-dark" href="">
									<li style="list-style:none;" id="notificacion" class="nav-item dropdown"><i href="" class="mdi mdi-bell-off font-24" style="color:white"></i></li>
								</a>
								<a class="nav-link dropdown-toggle waves-effect waves-dark" href="index.php?pagina=consmensajeria.php">
									<li style="list-style: outside none none; display: none;" id="notificacion2" class="nav-item dropdown"><i class="mdi mdi-bell-ring font-24" style="color:red"></i></li>
								</a>
						</div>					


                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" style="height: 38px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="images/user1.jpg" id="imgusuario1" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>

								<a class="dropdown-item" ><?php echo $_SESSION["user_descripcion"];?></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="inc/sesion.php/cerrarsesion"><i class="fa fa-power-off mr-1 ml-1"></i> Cerrar Sesión</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">

						<!-- Ejemplo menú desplegable
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Opciones </span></a>
                            <ul id="menuopciones" aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="index.php?pagina=opcion1.php" class="sidebar-link"><i class="far fa-folder-open"></i><span class="hide-menu"> Opcion 1 </span></a></li>
                                <li class="sidebar-item"><a href="index.php?pagina=opcion2.php"  class="sidebar-link"><i class="fas fa-user-circle"></i><span class="hide-menu"> Opcion 2 </span></a></li>
                            </ul>
                        </li>
						-->

					<?php 
						$menu = "";
						$rol = isset($_SESSION["user_rol"]) ? $_SESSION["user_rol"] : "SIN_ROL";
						$menu = $menu . "<!-- DEBUG: Rol es: " . htmlspecialchars($rol) . " -->";

						// Menús solo Admin - TEMPORAL: comentado por debugging
						// if ($rol=='Superadmin' || $rol=='Administrador'){
						// Mostrar menús para todos mientras debuggeamos
						{
							$menu= $menu . '<li class="sidebar-item" id="mnuusuarios"> <a href="javascript:void(0)" class="sidebar-link"><i class="far fa-user"></i><span class="hide-menu">Usuarios</span></a></li>';
							$menu= $menu . '<script>';
								$menu= $menu . 'document.getElementById("mnuusuarios").addEventListener("click", function(){CargarPagina("consusuarios.php","Usuarios","far fa-user");}, false);';
							$menu= $menu . '</script>';
							
							// MENÚ PLANTILLAS (Admin)
							$menu= $menu . '<li class="sidebar-item" id="mnuplantillas"> <a href="javascript:void(0)" class="sidebar-link"><i class="mdi mdi-file-document"></i><span class="hide-menu">Plantillas</span></a></li>';
							$menu= $menu . '<script>';
								$menu= $menu . 'document.getElementById("mnuplantillas").addEventListener("click", function(){CargarPagina("admin_plantillas.php","Plantillas","mdi mdi-file-document");}, false);';
							$menu= $menu . '</script>';
						}

						// MENÚ INFORMES (Todos los usuarios autenticados)
						$menu= $menu . '<li class="sidebar-item" id="mnuinformes"> <a href="javascript:void(0)" class="sidebar-link"><i class="mdi mdi-file-chart"></i><span class="hide-menu">Informes</span></a></li>';
						$menu= $menu . '<script>';
							$menu= $menu . 'document.getElementById("mnuinformes").addEventListener("click", function(){CargarPagina("informes.php","Informes","mdi mdi-file-chart");}, false);';
						$menu= $menu . '</script>';

						echo $menu;										
					?>


                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->

        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 id="titulopagina" class="page-title">Inicio</h4>
                    </div>

                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div id="panelcentral" class="container-fluid">
				
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
				<p>&copy; <?php echo date("Y"); ?> cdi sistemas</p>
			</footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->


    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script src="libs/jquery/dist/jquery.min.js?v=1"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="libs/popper.js/dist/umd/popper.min.js?v=1"></script>
    <script src="libs/bootstrap/dist/js/bootstrap.min.js?v=1"></script>
	<!-- swettalert2 para alert tipo msgbox -->
	<script src="libs/sweetalert2/dist/sweetalert2.all.min.js?v=1"></script>	
	
	<!-- TinyMCE 6.8.2 - Editor WYSIWYG GLOBAL -->
	<script src="libs/tinymce/tinymce.min.js"></script>
	<script src="libs/tinymce/langs/es.js"></script>
	<!-- ============================================================== -->
	
    <!-- ============================================================== -->
	<!-- PANEL DE CONTROL -->
	<!-- ============================================================== -->
    <script src="js/dist/app.min.js?v=1"></script>
    <script src="js/dist/app.init.js?v=1"></script>
    <script src="js/dist/app-style-switcher.js?v=1"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js?v=1"></script>
    <script src="libs/sparkline/sparkline.js?v=1"></script>
    <!--Wave Effects -->
    <script src="js/dist/waves.js?v=1"></script>
    <!--Menu sidebar -->
    <script src="js/dist/sidebarmenu.js?v=1"></script>
	<!--DataTables -->	
	<script src="libs/DataTables_OLD/DataTables-1.10.22/js/jquery.dataTables.min.js?v=1"></script>
	<!--Buttons DataTables -->	
	<script src="libs/DataTables_OLD/Buttons-1.6.5/js/dataTables.buttons.min.js?v=1"></script>
	<script src="libs/DataTables_OLD/Buttons-1.6.5/js/buttons.flash.min.js?v=1"></script>
	<script src="libs/DataTables_OLD/JSZip-2.5.0/jszip.min.js?v=1"></script>
	<script src="libs/DataTables_OLD/pdfmake-0.1.36/pdfmake.min.js?v=1"></script>
	<script src="libs/DataTables_OLD/pdfmake-0.1.36/vfs_fonts.js?v=1"></script>
	<script src="libs/DataTables_OLD/Buttons-1.6.5/js/buttons.html5.min.js?v=1"></script>
	<script src="libs/DataTables_OLD/Buttons-1.6.5/js/buttons.print.min.js?v=1"></script>		
	<!--Select2 -->	
	<script src="libs/select2/dist/js/select2.full.min.js?v=1"></script>
    <script src="libs/select2/dist/js/select2.min.js?v=1"></script>

	
    <!--Custom JavaScript -->
    <script src="js/dist/custom.min.js?v=1"></script>	
	<!-- ============================================================== -->
	
	
    <!-- ============================================================== -->
    <!-- js de la página y genericas cdi-->
    <!-- ============================================================== -->
	<script src="js/genericas.js?v=1"></script>
	<script src="js/func_aplicacion.js?v=1"></script>
	<script src="js/index.js?v=1"></script>
	<script>
	// Toggle del sidebar - reemplaza el handler de app.min.js para que funcione correctamente
	$(document).ready(function() {
		setTimeout(function() {
			$('.sidebartoggler').off('click').on('click', function(e) {
				e.preventDefault();
				var $w = $('#main-wrapper');
				if ($w.hasClass('mini-sidebar')) {
					$w.removeClass('mini-sidebar').attr('data-sidebartype', 'full');
				} else {
					$w.addClass('mini-sidebar').attr('data-sidebartype', 'mini-sidebar');
				}
			});
		}, 300);
	});
	</script>

</body>


</html>