<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="images/favicon.ico" type="image/ico" />

		<title>JFK System | <?php echo isset($page_title) ? $page_title : ''; ?></title>

		<!-- Bootstrap -->
		<link href="<?= base_url() ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- VERSION 4.3 -->
		<!-- Font Awesome -->
		<link href="<?= base_url() ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">



		<!-- iCheck -->
		<link href="<?= base_url() ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
		<!-- bootstrap-progressbar -->
		<link href="<?= base_url() ?>vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
		<!-- JQVMap -->
		<link href="<?= base_url() ?>vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
		<!-- bootstrap-daterangepicker -->
		<link href="<?= base_url() ?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

		<!-- Custom Theme Style -->
		<link href="<?= base_url() ?>build/css/custom.css" rel="stylesheet">

		<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
		<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/w3.css') ?>">

		<script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>

		<link href="<?= base_url('assets/plugins/font-awesome/css/font-awesome.css') ?>" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?= base_url('assets/js/funciones.js') ?>"></script>

		<style type="text/css">
			body{
					/*background-color: rgb(250,250,200);  amarillo perla */
					color: rgb(0,50,150);
			}
			.mariposa{
					float: left; margin: 0px 0px 0px 1px; padding: 3px 2px; width: 100px; height: 40px;
					text-align: center; border-style: none; border-color: red; border-width: 1px; font-family: 'Arial'; font-size: 11px;
			}
		</style>

	</head>

	<body class="nav-md">
		<div class="container body">
			<div class="main_container">
				<div class="col-md-3 left_col">
					<div class="left_col scroll-view">
						<div class="navbar nav_title text-center" style="border: 0;">
							<a href="index.html" class="site_title"><span>JFK System</span></a>
						</div>

						<div class="clearfix"></div>

						<br />

<?php
if (!isset($_SESSION["usuario"])){
		echo "<div class=\"alert alert-danger\">Acceso Denegado  <a href=\"" . base_url() . "\"><br>(click aqu&iacute; para iniciar)</a></div>";
}else{
?>

						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
							<div class="menu_section">
								<h3>General</h3>
								<ul class="nav side-menu">
									<li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('welcome/home'); ?>">Inicio</a></li>
											<li><a href="<?= base_url('caja/ver_cajas'); ?>">Apertura/Cierre Cajas</a></li>
											<li><a href="<?= base_url('caja/analisis_mensual'); ?>">An&aacute;lisis Mensual</a></li>
										</ul>
									</li>
									<li><a><i class="fa fa-clone" style="font-size:20px;color:red"></i>&nbsp;&nbsp;Ventas <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('sales/index'); ?>">Listar</a></li>
											<li><a href="<?= base_url('sales/add'); ?>">Agregar</a></li>
										</ul>
									</li>

									<li><a><i class="fa fa-desktop" style="color:orange"></i> Compras <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('compras/index') ?>">Listar</a></li>
											<li><a href="<?= base_url('compras/add') ?>">Agregar</a></li>
										</ul>
									</li>
									
									<li><a><i class="fa fa-bicycle" style="color:orange"></i> Gastos <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('gastos/index') ?>">Listar</a></li>
											<li><a href="<?= base_url('gastos/add') ?>">Agregar</a></li>
										</ul>
									</li>

									<li><a><i class="fa fa-table" style="color:skyblue"></i> Clientes <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('clientes/index') ?>">Listar</a></li>
											<li><a href="<?= base_url('clientes/add') ?>">Agregar</a></li>
										</ul>
									</li>
									<li><a><i class="fa fa-bar-chart-o" style="color:rgb(50,230,50)"></i> Productos <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('products/index'); ?>">Listar</a></li>
											<li><a href="<?= base_url('products/add'); ?>">Agregar Productos</a></li>
											<li><a href="<?= base_url('products/add_servicio'); ?>">Agregar Servicios</a></li>
											<li><a href="<?= base_url('inventarios/stock_productos'); ?>">STOCKS</a></li>
											<li>
												<a href="<?= base_url('products/print_inicial'); ?>">
													<span>Codigo de Barras</span>
											</a>
											<li>
												<a href="<?= base_url('products/importacion'); ?>">
													<span>Importaci&oacute;n</span>
											</a>
								</li>

										</ul>
									</li>
									<li><a><i class="glyphicon glyphicon-tags" style="font-size:16px;color:lime"></i>&nbsp;&nbsp; Categorias <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('categorias/index'); ?>">Listar</a></li>
											<li><a href="<?= base_url('categorias/add'); ?>">Agregar</a></li>
										</ul>
									</li>

									<li><a><i class="glyphicon glyphicon-user" style="font-size:18px;color:rgb(100,200,200)"></i> &nbsp;Proveedores <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('proveedores/index'); ?>">Listar</a></li>
											<li><a href="<?= base_url('proveedores/add'); ?>">Agregar</a></li>
										</ul>
									</li>

									<li><a><i class="fa fa-table" style="font-size:20px;color:rgb(200,255,150)"></i> &nbsp;Inventarios <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('inventarios/index'); ?>">Ver</a></li>
											<li><a href="<?= base_url('inventarios/add'); ?>">Agregar</a></li>
											<li><a href="<?= base_url('inventarios/registrar_productos'); ?>">Registrar</a></li>
											<li><a href="<?= base_url('inventarios/kardex'); ?>">kardex de Producto</a></li>
											<li><a href="<?= base_url('inventarios/listar_stock'); ?>">Ver Stocks</a></li>
											<li><a href="<?= base_url('inventarios/actualizar_stock'); ?>">Actualizar Stock</a></li>
											<li><a href="<?= base_url('inventarios/ver_movimientos'); ?>">Ver movimientos</a></li>
											<li><a href="<?= base_url('inventarios/add_traslados'); ?>">Movim. Traslado</a></li>
											<li><a href="<?= base_url('inventarios/add_movimientos'); ?>">Movim. otros</a></li>
										</ul>
									</li>

									<li><a><i class="fa fa-table" style="color:skyblue"></i> Ctas por Cobrar<span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
												<li><a href="<?= base_url('cuentas_cobrar/listar'); ?>">Listar Ctas x Cobrar</a></li>
												<li><a href="<?= base_url('cuentas_cobrar/saldar'); ?>">Saldar Cuentas</a></li>
										</ul>
									</li>

									<li><a><i class="glyphicon glyphicon-tag" style="font-size:18px;color:rgb(240,100,100)"></i> &nbsp;Medios de Pago <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('mediospagos/index'); ?>">Ver medios</a></li>
											<li><a href="<?= base_url('mediospagos/add'); ?>">Agregar medios</a></li>
										</ul>
									</li>

									<li><a><i class="glyphicon glyphicon-user" style="font-size:18px;color:skyblue"></i> &nbsp;RR.HH <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('recursos/ver_personal'); ?>">Ver Personal</a></li>
											<li><a href="<?= base_url('recursos/agregar_personal'); ?>">Agregar Personal</a></li>
											<li><a href="<?= base_url('recursos/ver_contratos'); ?>">Ver Contratos</a></li>
											<li><a href="<?= base_url('recursos/agregar_contratos'); ?>">Agregar Contratos</a></li>
										</ul>
									</li>

									<li><a><i class="glyphicon glyphicon-signal" style="font-size:18px;color:rgb(200,150,0)"></i> &nbsp;Reportes <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('reportes/ventas_detalles_prod'); ?>">Ventas x Producto</a></li>
											<li><a href="<?= base_url('reportes/ventas_por_forma_pago'); ?>">Ventas x Forma de Pago</a></li>
											<li><a href="<?= base_url('reportes/grafico_mensual_ventas'); ?>">Grafico mensual Ventas</a></li>
											<li><a href="<?= base_url('reportes/ganancias'); ?>">Ganancias Diarias</a></li>
											<li><a href="<?= base_url('reportes/ganancias_detallado'); ?>">Ganancia Detalles</a></li>
											<li><a href="<?= base_url('reportes/productos_sin_compra'); ?>">Productos sin compra</a></li>
											<li><a href="<?= base_url('reportes/analisis'); ?>">Reportes para Analisis</a></li>
										</ul>
									</li>

									<li><a><i class="glyphicon glyphicon-user" style="font-size:20px;color:rgb(0,100,255)"></i> &nbsp;Usuarios <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?= base_url('usuarios/ver_usuarios'); ?>">Ver Usuarios</a></li>
											<li><a href="<?= base_url('usuarios/add'); ?>">Agregar Usuarios</a></li>
										</ul>
									</li>

									<li>
										<a href="<?= site_url('welcome/cierra_sesion'); ?>">
											<i class="glyphicon glyphicon-off" style="font-size:20px;color:red"></i> &nbsp;Cerrar <span class="fa fa-chevron-down"></span>
										</a>
									</li>
								</ul>
							</div>


						</div>
						<!-- /sidebar menu -->

						<!-- /menu footer buttons -->
						<div class="sidebar-footer hidden-small">
							<a data-toggle="tooltip" data-placement="top" title="Settings">
								<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="FullScreen">
								<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="Lock">
								<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
								<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
							</a>
						</div>
						<!-- /menu footer buttons -->
					</div>
				</div>

				<!-- top navigation -->
				<div class="top_nav">
					<div class="nav_menu">
							<div class="nav toggle">
								<a id="menu_toggle"><i class="fa fa-bars" style="font-size:18px"></i></a>
							</div>
							<nav class="nav navbar-nav">
								<ul class=" navbar-right">
							</ul>
						</nav>
					</div>
				</div>
				<!-- /top navigation -->

				<div class="right_col" role="main">
				<!--<div class="col-xs-12 col-sm-11 col-md-11 col-lg-11" style="border-style:none; border-color: gray; margin-left:0px">-->
					<section class="content">
						<div class="row" style="">
							<div class="col-sm-9 left_col" style="padding: 5px 0px 5px 15px; height:70px; 
							background-color: rgb(46, 134, 193);
							color:white;border-radius:8px 0px 0px 0px;">
								<h3>
								<?php 
										if(isset($page_title)){
												echo $page_title;
										}else{
												echo "";
										}
								?>
								</h3>
							</div>
							<div class="col-sm-3 left_col" style="padding: 0px 0px 5px 15px; background-size: cover; background-position: center; background-image:url(<?= base_url('/assets/images/rueda.jpg') ?>); height:70px; border-radius:0px 8px 0px 0px; border-style: solid; border-color:rgb(46,134,193)"><!-- rgb(86, 174, 233) -->
									<table border="0" style="">
										<tr>
											<td style="margin:0px; padding:0px; font-weight: bold;">Fecha:</td>
											<td style="padding-left:8px; height: 4px;"><?php echo date("d-m-Y"); ?> <?php echo date("H:i:s"); ?></td>
										</tr>
										<tr>
											<td style="font-weight: bold;">Usuario:</td>
											<td style="padding-left:8px;"><?php echo $_SESSION["usuario"]; ?></td>
										</tr>
										<tr>
											<td style="font-weight: bold;">Tienda:</td>
											<td style="padding-left:8px;"><?php echo $_SESSION["nombre_tienda"]; ?></td>
										</tr>
									</table>
							</div>

						</div>

					</section>

					<?php if(isset($msg)){ ?>
					<div class="row">
						<div class="col-xs-12 col-sm-7 left_col" style="margin-top:7px;background-color: white;">
							<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'success' ?>"> <?= $msg ?> </div>
						</div>
					</div>
					<?php } ?>

					<?= $contents ?>      

				<!--</div>-->
				</div>


				<!-- footer content -->
				<footer>
					<div class="pull-right">
						JFK System
					</div>
					<div class="clearfix"></div>
				</footer>
				<!-- /footer content -->
			</div>
		</div>

		<!-- jQuery -->
		<!--<script src="../vendors/jquery/dist/jquery.min.js"></script>-->
		<!-- Bootstrap -->
		<script src="<?= base_url() ?>vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
		<!-- FastClick -->
		<script src="<?= base_url() ?>vendors/fastclick/lib/fastclick.js"></script>
		<!-- NProgress -->
		<script src="<?= base_url() ?>vendors/nprogress/nprogress.js"></script>
		<!-- Chart.js -->
		<script src="<?= base_url() ?>vendors/Chart.js/dist/Chart.min.js"></script>
		<!-- gauge.js -->
		<script src="<?= base_url() ?>vendors/gauge.js/dist/gauge.min.js"></script>
		<!-- bootstrap-progressbar -->
		<script src="<?= base_url() ?>vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
		<!-- iCheck -->
		<script src="<?= base_url() ?>vendors/iCheck/icheck.min.js"></script>
		<!-- Skycons -->
		<script src="<?= base_url() ?>vendors/skycons/skycons.js"></script>
		<!-- Flot -->
		<script src="<?= base_url() ?>vendors/Flot/jquery.flot.js"></script>
		<script src="<?= base_url() ?>vendors/Flot/jquery.flot.pie.js"></script>
		<script src="<?= base_url() ?>vendors/Flot/jquery.flot.time.js"></script>
		<script src="<?= base_url() ?>vendors/Flot/jquery.flot.stack.js"></script>
		<script src="<?= base_url() ?>vendors/Flot/jquery.flot.resize.js"></script>
		<!-- Flot plugins -->
		<script src="<?= base_url() ?>vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
		<script src="<?= base_url() ?>vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
		<script src="<?= base_url() ?>vendors/flot.curvedlines/curvedLines.js"></script>
		<!-- DateJS -->
		<script src="<?= base_url() ?>vendors/DateJS/build/date.js"></script>
		<!-- JQVMap -->
		<script src="<?= base_url() ?>vendors/jqvmap/dist/jquery.vmap.js"></script>
		<script src="<?= base_url() ?>vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
		<script src="<?= base_url() ?>vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
		<!-- bootstrap-daterangepicker -->
		<script src="<?= base_url() ?>vendors/moment/min/moment.min.js"></script>
		<script src="<?= base_url() ?>vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

		<!-- Custom Theme Scripts -->
		<script src="<?= base_url() ?>build/js/custom.min.js"></script>
	
	</body>
</html>
<?php } ?>