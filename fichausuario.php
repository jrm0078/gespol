<?php include("inc/seguridad.php"); ?> 	

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Catalweb">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Ficha de Usuario</title>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
		<!-- ============================================================== -->
		<!-- Preloader - style you can find in spinners.css -->
		<!-- ============================================================== -->
		<div class="preloader2">
			<div class="lds-ripple">
				<div class="lds-pos"></div>
				<div class="lds-pos"></div>
			</div>
		</div>

        <div class="container-fluid">
			<div class="card">
				<form class="form-horizontal">	
				
					<div class="card-body">

						<div class="form-group row">
							<label for="txtid" class="col-sm-3 text-right control-label col-form-label">Código Usuario</label>
							<div class="col-sm-6">
								<input id="txtid" type="number" class="form-control" maxlength="" placeholder="(Auto)" disabled/>
							</div>
						</div>
					
						<div class="form-group row">
							<label for="txtnombre" class="col-sm-3 text-right control-label col-form-label">Nombre</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="txtnombre" placeholder="" maxlength="50" required="">
							</div>
						</div>

						<div class="form-group row">
							<label for="txtemail" class="col-sm-3 text-right control-label col-form-label">e-mail</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="txtemail" placeholder="" maxlength="320" required="">
							</div>
						</div>

						<div class="form-group row">
							<label for="txtcontrasenia" class="col-sm-3 text-right control-label col-form-label">Contraseña</label>
							<div class="col-sm-6">
								<input type="password" class="form-control" id="txtcontrasenia" placeholder="" maxlength="" style="display: block;">
							</div>
						</div>		

						<div class="form-group row">
							<label class="col-sm-3 text-right control-label col-form-label">Rol</label>
							<div class="col-md-6">
								<select id="cmbrol" class="select2 form-control custom-select">
									<option disabled>Seleccionar Rol</option>                                                                                                                                                              
								</select>
							</div>
                        </div>	
						
						
						
					</div>
					<div class="border-top">
						<div class="card-body">
							<button type="button" id="btnActualizar" class="btn btn-success btn-rounded"><i class="fa fa-check"></i> Actualizar</button>
							<button type="button" id="btnEliminar" class="btn btn-danger btn-rounded"><i class="fa fa-recycle"></i> Eliminar</button>
						</div>
					</div>
				</form>
			</div>
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->


	
	<script src="js/fichausuario.js"></script>
	
</body>

</html>