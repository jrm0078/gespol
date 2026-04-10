
<?php
session_start();
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Catalweb">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Login</title>
    <!-- Custom CSS -->
    <link href="css/style.min.css" rel="stylesheet">
	<!-- CSS sweetalerts-->
	<link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
	
	<link href="css/custom.css" rel="stylesheet">	
	
	
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center" style="background:url(images/lanzadera-login.jpg) no-repeat center center;">
            <div class="auth-box">
                <div id="loginform">
                    <div class="logo">
                        <span class="db"><img src="images/logo-icon.png" alt="logo" /></span>
						<p></p>
                        <h5 class="font-medium mb-3">Conectar</h5>
                    </div>
                    <!-- Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-horizontal mt-3" id="loginform">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" id="usuario" name="usuario" class="form-control form-control-lg" placeholder="Usuario" aria-label="Usuario" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
                                </div>
                                <div class="form-group text-center">
                                    <div class="col-xs-12 pb-3">
                                        <button id="btnconectar" class="btn btn-block btn-lg btn-info">Conectar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

				<footer class="footer text-center">
					<p>&copy;<?php echo date("Y"); ?> cdisistemas</p>
				</footer>

            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script src="libs/jquery/dist/jquery.min.js?v=1"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="libs/popper.js/dist/umd/popper.min.js?v=1"></script>
    <script src="libs/bootstrap/dist/js/bootstrap.min.js?v=1"></script>
	<!-- swettalert2 para alert tipo msgbox -->	
	<script src="libs/sweetalert2/dist/sweetalert2.all.min.js?v=1"></script>	
    <!-- ============================================================== -->
    <!-- js de la página y genericas cdi-->
    <!-- ============================================================== -->
	<script src="js/genericas.js?v=1"></script>
	<script src="js/login.js?v=1"></script>

	
</body>

</html>