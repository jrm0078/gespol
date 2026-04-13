<?php include("inc/seguridad.php"); ?> 

<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader2">
	<div class="lds-ripple">
		<div class="lds-pos"></div>
		<div class="lds-pos"></div>
	</div>
</div>

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="container-fluid">
	<div class="row mb-3">
		<div class="col-12">
			<button type="button" id="btnCrear" class="btn btn-success btn-rounded">
				<i class="fa fa-star"></i> Crear Usuario
			</button>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="zero_config" class='display' style='width:100%'>
							<thead>
								<tr>
									<th style="width: 50px;"></th>
									<th>Cod. Usuario</th>
									<th>Nombre</th>
									<th>Email</th>
									<th>Rol</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid -->
<!-- ============================================================== -->

<!-- this page js -->
<script src="js/consusuarios.js"></script>