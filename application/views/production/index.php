<?php
$grupo_id = $_SESSION["group_id"];
// Solo submódulos (hijos) — parent_id IS NOT NULL
$ar_permitidos = $this->db->query("select trim(lower(b.modulo)) modulo from tec_grupo_modulos a
	inner join tec_modulos b on a.modulo_id = b.id
	where a.grupo_id = $grupo_id and b.parent_id is not null")->result_array();
?>
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

		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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


		<!-- iCheck -->
		<link href="<?= base_url() ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
		<!-- bootstrap-progressbar -->
		<link href="<?= base_url() ?>vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
		<!-- JQVMap -->
		<link href="<?= base_url() ?>vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
		<!-- bootstrap-daterangepicker -->
		<link href="<?= base_url() ?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

		<!-- Boxicons (Minible-style icons) -->
		<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
		<!-- Google Fonts: Inter -->
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

		<!-- Custom Theme Style -->
		<link href="<?= base_url() ?>build/css/custom.css" rel="stylesheet">
		<!-- Modern Sidebar -->
		<link href="<?= base_url() ?>assets/css/sidebar-modern.css" rel="stylesheet">

		
		<style type="text/css">
			body{
					/*background-color: rgb(250,250,200);  amarillo perla */
					color: rgb(0,35,80);
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
						<div class="navbar nav_title" style="border: 0;">
							<a href="<?= base_url('welcome/home') ?>" class="site_title">
								<i class='bx bx-cube-alt' style="font-size:26px;color:var(--sb-accent)"></i>
								<span>JFK System</span>
							</a>
						</div>

						<div class="clearfix"></div>

<?php
if (!isset($_SESSION["usuario"])){
		echo "<div class=\"alert alert-danger\">Acceso Denegado  <a href=\"" . base_url() . "\"><br>(click aqu&iacute; para iniciar)</a></div>";
}else{
?>

						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

							<!-- ===== SECCION: MENU ===== -->
							<?php if (busca_en(['welcome','caja','sales','compras','gastos','cajachica'], $ar_permitidos)): ?>
							<div class="menu_section">
								<h3>Menu</h3>
								<ul class="nav side-menu">
									<?php if (busca_en(['welcome','caja'], $ar_permitidos)): ?>
									<li><a><i class='bx bx-home-circle'></i> Dashboard <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("welcome/home",$ar_permitidos,"Inicio") ?>
											<?= opcion("caja/index",$ar_permitidos,"Caja de Ventas") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('sales', $ar_permitidos)): ?>
									<li><a><i class='bx bx-receipt'></i> Ventas <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("sales/index",$ar_permitidos,"Listar") ?>
											<?= opcion("sales/add",$ar_permitidos,"Agregar") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('compras', $ar_permitidos)): ?>
									<li><a><i class='bx bx-cart'></i> Compras <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("compras/index",$ar_permitidos,"Listar") ?>
											<?= opcion("compras/add",$ar_permitidos,"Agregar") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('gastos', $ar_permitidos)): ?>
									<li><a><i class='bx bx-wallet'></i> Gastos <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("gastos/index",$ar_permitidos,"Listar") ?>
											<?= opcion("gastos/add",$ar_permitidos,"Agregar") ?>
											<?= opcion("gastos/categorias",$ar_permitidos,"Categorias de Gasto") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('cajachica', $ar_permitidos)): ?>
									<li><a><i class='bx bx-money'></i> Caja Chica <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("cajachica/index",$ar_permitidos,"Administrar") ?>
										</ul>
									</li>
									<?php endif; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- ===== SECCION: OPERACIONES ===== -->
							<?php if (busca_en(['servicios','products','categorias','atributos','inventarios','mediospagos'], $ar_permitidos)): ?>
							<div class="menu_section">
								<h3>Operaciones</h3>
								<ul class="nav side-menu">
									<?php if (busca_en('servicios', $ar_permitidos)): ?>
									<li><a><i class='bx bx-wrench'></i> Servicio T&eacute;cnico <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("servicios/index",$ar_permitidos,"Listar Servicios") ?>
											<?= opcion("servicios/add",$ar_permitidos,"Nuevo Servicio") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en(['products','categorias','atributos','inventarios'], $ar_permitidos)): ?>
									<li><a><i class='bx bx-package'></i> Productos <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("products/index",$ar_permitidos,"Listar") ?>
											<?= opcion("products/add",$ar_permitidos,"Agregar Productos") ?>
											<?= opcion("products/add_servicio",$ar_permitidos,"Agregar Servicios") ?>
											<?= opcion("inventarios/stock_productos",$ar_permitidos,"Stock Avanzado") ?>
											<?= opcion("products/print_compra",$ar_permitidos,"C&oacute;digo de Barras") ?>
											<?php if (busca_en('categorias', $ar_permitidos)): ?>
											<li><a><i class='bx bx-purchase-tag'></i> Categorias <span class="fa fa-chevron-down"></span></a>
												<ul class="nav child_menu">
													<?= opcion("categorias/index",$ar_permitidos,"Listar") ?>
													<?= opcion("categorias/add",$ar_permitidos,"Agregar") ?>
												</ul>
											</li>
											<?php endif; ?>
											<?= opcion("atributos/index",$ar_permitidos,"<i class='bx bx-purchase-tag'></i> Atributos") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (tiene_rutas(['inventarios/index','inventarios/add','inventarios/registrar_productos','inventarios/kardex','inventarios/listar_stock','inventarios/actualizar_stock','inventarios/ver_movimientos','inventarios/add_traslados','inventarios/add_movimientos'], $ar_permitidos)): ?>
									<li><a><i class='bx bx-box'></i> Inventarios <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("inventarios/index",$ar_permitidos,"Ver") ?>
											<?= opcion("inventarios/add",$ar_permitidos,"Agregar") ?>
											<?= opcion("inventarios/registrar_productos",$ar_permitidos,"Registrar") ?>
											<?= opcion("inventarios/kardex",$ar_permitidos,"Kardex de Producto") ?>
											<?= opcion("inventarios/listar_stock",$ar_permitidos,"Stock de Productos") ?>
											<?= opcion("inventarios/actualizar_stock",$ar_permitidos,"Actualizar Stock") ?>
											<?= opcion("inventarios/ver_movimientos",$ar_permitidos,"Ver movimientos") ?>
											<?= opcion("inventarios/add_traslados",$ar_permitidos,"Movim. Traslado") ?>
											<?= opcion("inventarios/add_movimientos",$ar_permitidos,"Movim. otros") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('mediospagos', $ar_permitidos)): ?>
									<li><a><i class='bx bx-credit-card'></i> Medios de Pago <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("mediospagos/index",$ar_permitidos,"Ver medios") ?>
											<?= opcion("mediospagos/add",$ar_permitidos,"Agregar medios") ?>
										</ul>
									</li>
									<?php endif; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- ===== SECCION: RRHH ===== -->
							<?php if (busca_en('empleados', $ar_permitidos)): ?>
							<div class="menu_section">
								<h3>RRHH</h3>
								<ul class="nav side-menu">
									<li><a><i class='bx bx-id-card'></i> Empleados <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("empleados/index",$ar_permitidos,"Listar") ?>
											<?= opcion("empleados/add",$ar_permitidos,"Agregar") ?>
										</ul>
									</li>
								</ul>
							</div>
							<?php endif; ?>

							<!-- ===== SECCION: REPORTES ===== -->
							<?php if (busca_en('reportes', $ar_permitidos)): ?>
							<div class="menu_section">
								<h3>Reportes</h3>
								<ul class="nav side-menu">
									<?php if (busca_en('reportes', $ar_permitidos)): ?>
									<li><a><i class='bx bx-bar-chart-alt-2'></i> Reportes <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("reportes/contabilidad",$ar_permitidos,"Contabilidad") ?>
											<?= opcion("reportes/ventas_detalles_prod",$ar_permitidos,"Ventas x Producto") ?>
											<?= opcion("reportes/ventas_por_forma_pago",$ar_permitidos,"Ventas x Forma de Pago") ?>
											<?= opcion("reportes/ganancias",$ar_permitidos,"Ganancias Diarias") ?>
											<?= opcion("reportes/ganancias_detallado",$ar_permitidos,"Ganancia Detalles") ?>
											<?= opcion("reportes/top_productos",$ar_permitidos,"Top Productos") ?>
											<?= opcion("reportes/productos_sin_compra",$ar_permitidos,"Productos sin compra") ?>
											<?= opcion("reportes/analisis",$ar_permitidos,"Reportes para An&aacute;lisis") ?>
											<?= opcion("reportes/gastos_cajachica",$ar_permitidos,"Gastos Caja Chica") ?>
											<?= opcion("reportes/cierre_diario",$ar_permitidos,"Cierre Diario") ?>
										</ul>
									</li>
									<?php endif; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- ===== SECCION: CONFIGURACION ===== -->
							<?php if (busca_en(['usuarios','ajustes','clientes','proveedores','roles'], $ar_permitidos)): ?>
							<div class="menu_section">
								<h3>Configuraci&oacute;n</h3>
								<ul class="nav side-menu">
									<?php if (busca_en('usuarios', $ar_permitidos)): ?>
									<li><a><i class='bx bx-user'></i> Usuarios <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("usuarios/ver_usuarios",$ar_permitidos,"Ver Usuarios") ?>
											<?= opcion("usuarios/add",$ar_permitidos,"Agregar Usuarios") ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en(['ajustes','clientes','proveedores'], $ar_permitidos)): ?>
									<li><a><i class='bx bx-cog'></i> Ajustes <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("ajustes/index",$ar_permitidos,"Ajustes") ?>
											<?php if (busca_en('clientes', $ar_permitidos)): ?>
											<li><a><i class='bx bx-group'></i> Clientes <span class="fa fa-chevron-down"></span></a>
												<ul class="nav child_menu">
													<?= opcion("clientes/index",$ar_permitidos,"Listar") ?>
													<?= opcion("clientes/add",$ar_permitidos,"Agregar") ?>
												</ul>
											</li>
											<?php endif; ?>
											<?php if (busca_en('proveedores', $ar_permitidos)): ?>
											<li><a><i class='bx bx-store'></i> Proveedores <span class="fa fa-chevron-down"></span></a>
												<ul class="nav child_menu">
													<?= opcion("proveedores/index",$ar_permitidos,"Listar") ?>
													<?= opcion("proveedores/add",$ar_permitidos,"Agregar") ?>
												</ul>
											</li>
											<?php endif; ?>
										</ul>
									</li>
									<?php endif; ?>
									<?php if (busca_en('roles', $ar_permitidos)): ?>
									<li><a><i class='bx bx-shield'></i> Roles <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<?= opcion("roles/index",$ar_permitidos,"Gestionar Roles") ?>
											<?= opcion("roles/add",$ar_permitidos,"Nuevo Rol") ?>
										</ul>
									</li>
									<?php endif; ?>
								</ul>
							</div>
							<?php endif; ?>

							<!-- Cerrar Sesión siempre visible -->
							<div class="menu_section">
								<ul class="nav side-menu">
									<li>
										<a href="<?= site_url('welcome/cierra_sesion'); ?>">
											<i class='bx bx-power-off'></i> Cerrar Sesi&oacute;n
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
						<div class="topnav-left">
							<a id="menu_toggle" class="topnav-toggle"><i class='bx bx-menu'></i></a>
							<div class="topnav-search">
								<i class='bx bx-search'></i>
								<input type="text" id="topnav-search-input" placeholder="Buscar..." autocomplete="off">
							</div>
						</div>
						<div class="topnav-right">
							<a href="#" class="topnav-icon" title="Pantalla completa" onclick="toggleFullScreen()">
								<i class='bx bx-fullscreen'></i>
							</a>
							<a href="#" class="topnav-icon topnav-notify" title="Notificaciones">
								<i class='bx bx-bell'></i>
							</a>
							<div class="topnav-user">
								<div class="topnav-avatar">
									<i class='bx bx-user-circle'></i>
								</div>
								<span class="topnav-username"><?php echo isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : ''; ?></span>
							</div>
							<a href="<?= base_url('ajustes/index'); ?>" class="topnav-icon" title="Ajustes">
								<i class='bx bx-cog'></i>
							</a>
						</div>
					</div>
				</div>
				<!-- /top navigation -->
				<script>
				function toggleFullScreen(){
					if(!document.fullscreenElement){document.documentElement.requestFullscreen();}
					else{if(document.exitFullscreen){document.exitFullscreen();}}
				}
				</script>

				<div class="right_col" role="main">

					<?php if(isset($page_title) && $page_title != ''){ ?>
					<div class="page-title-box">
						<h4 class="page-title-text"><?= $page_title ?></h4>
					</div>
					<?php } ?>

					<?php if(isset($msg)){ ?>
					<div class="row">
						<div class="col-12 col-sm-7" style="margin-top:0;">
							<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'success' ?>" style="margin-bottom:12px;"> <?= $msg ?></div>
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
<?php } 
/**
 * Renderiza un <li> de menú solo si la ruta exacta está en los permisos del grupo.
 */
function opcion($ruta, $ar_permitidos, $etiqueta) {
	foreach ($ar_permitidos as $item) {
		if (trim(strtolower($item['modulo'])) === trim(strtolower($ruta))) {
			return '<li><a href="' . base_url($ruta) . '">' . $etiqueta . '</a></li>';
		}
	}
	return '';
}

/**
 * Devuelve true si algún permiso del grupo pertenece a los controladores indicados.
 * $controladores puede ser string o array de strings (nombres de controlador).
 * Ejemplo: busca_en('sales', $ar)  o  busca_en(['welcome','caja'], $ar)
 */
function busca_en($controladores, $ar) {
	if (!is_array($controladores)) {
		$controladores = [$controladores];
	}
	foreach ($ar as $item) {
		$partes = explode('/', trim(strtolower($item['modulo'])));
		if (in_array($partes[0], $controladores)) {
			return true;
		}
	}
	return false;
}

/**
 * Devuelve true si alguna de las rutas EXACTAS está en los permisos del grupo.
 * Útil para secciones cuyos submódulos pertenecen a distintos controladores.
 */
function tiene_rutas($rutas_exactas, $ar) {
	foreach ($ar as $item) {
		if (in_array(trim(strtolower($item['modulo'])), $rutas_exactas)) {
			return true;
		}
	}
	return false;
}
?>