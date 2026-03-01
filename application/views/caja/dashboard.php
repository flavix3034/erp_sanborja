<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

<style>
	.caja-card {
		background: #fff;
		border-radius: 10px;
		box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		padding: 20px;
		margin-bottom: 16px;
	}
	.caja-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-bottom: 20px;
	}
	.caja-header h4 {
		margin: 0;
		font-weight: 700;
		color: #1a1a2e;
		font-size: 18px;
	}
	.badge-estado {
		padding: 6px 16px;
		border-radius: 20px;
		font-size: 13px;
		font-weight: 600;
		color: #fff;
	}
	.badge-abierta { background: #28a745; }
	.badge-cerrada { background: #dc3545; }

	/* KPI Cards */
	.kpi-row { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 16px; }
	.kpi-card {
		flex: 1;
		min-width: 140px;
		background: #f8f9fa;
		border-radius: 8px;
		padding: 14px 16px;
		text-align: center;
		border: 1px solid #e9ecef;
	}
	.kpi-card .kpi-label {
		font-size: 11px;
		text-transform: uppercase;
		color: #6c757d;
		font-weight: 600;
		letter-spacing: 0.3px;
		margin-bottom: 4px;
	}
	.kpi-card .kpi-value {
		font-size: 20px;
		font-weight: 700;
		color: #333;
	}
	.kpi-card.kpi-green .kpi-value { color: #28a745; }
	.kpi-card.kpi-red .kpi-value { color: #dc3545; }

	/* Saldo Teórico destacado */
	.saldo-teorico-card {
		background: linear-gradient(135deg, #4e73df, #224abe);
		border-radius: 10px;
		padding: 20px 24px;
		text-align: center;
		margin-bottom: 20px;
		color: #fff;
	}
	.saldo-teorico-card .st-label {
		font-size: 12px;
		text-transform: uppercase;
		letter-spacing: 1px;
		opacity: 0.85;
		margin-bottom: 4px;
	}
	.saldo-teorico-card .st-value {
		font-size: 32px;
		font-weight: 700;
	}

	/* Apertura form */
	.apertura-form {
		background: #f0f4ff;
		border: 2px dashed #4e73df;
		border-radius: 10px;
		padding: 30px;
		text-align: center;
		max-width: 500px;
		margin: 40px auto;
	}
	.apertura-form h5 {
		color: #4e73df;
		font-weight: 700;
		margin-bottom: 20px;
	}
	.apertura-form .form-group { margin-bottom: 16px; text-align: left; }
	.apertura-form label { font-size: 12px; color: #555; font-weight: 600; text-transform: uppercase; }
	.apertura-form input {
		height: 42px;
		font-size: 16px;
		border-radius: 8px;
		border: 1px solid #ced4da;
		text-align: center;
	}
	.apertura-form input:focus { border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78,115,223,0.15); }

	/* Tabs */
	.nav-tabs { border-bottom: 2px solid #dee2e6; }
	.nav-tabs .nav-link {
		font-size: 13px;
		font-weight: 600;
		color: #6c757d;
		border: none;
		padding: 10px 18px;
		cursor: pointer;
	}
	.nav-tabs .nav-link.active {
		color: #4e73df;
		border-bottom: 3px solid #4e73df;
		background: transparent;
	}
	.tab-content { padding-top: 16px; }

	/* Arqueo table */
	.arqueo-table { width: 100%; max-width: 450px; border-collapse: collapse; }
	.arqueo-table th {
		font-size: 11px;
		text-transform: uppercase;
		color: #6c757d;
		padding: 6px 8px;
		border-bottom: 2px solid #dee2e6;
	}
	.arqueo-table td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
	.arqueo-table input[type="number"] {
		width: 70px;
		height: 32px;
		text-align: center;
		border: 1px solid #ced4da;
		border-radius: 5px;
		font-size: 13px;
	}
	.arqueo-table input[type="number"]:focus { border-color: #4e73df; outline: none; }
	.arqueo-table .subtotal-cell { text-align: right; font-weight: 600; color: #333; }
	.arqueo-separator td { font-weight: 700; background: #f8f9fa; }

	/* Diferencia */
	.diferencia-sobrante { color: #28a745; font-weight: 700; }
	.diferencia-faltante { color: #dc3545; font-weight: 700; }
	.diferencia-cero { color: #6c757d; font-weight: 700; }

	/* Detalle modal */
	.detalle-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
	.detalle-label { color: #6c757d; }
	.detalle-value { font-weight: 600; color: #333; }

	/* Responsive */
	@media (max-width: 768px) {
		.kpi-card { min-width: 120px; }
		.kpi-card .kpi-value { font-size: 16px; }
		.saldo-teorico-card .st-value { font-size: 24px; }
	}

	/* Loading overlay */
	.caja-loading {
		position: absolute; top: 0; left: 0; right: 0; bottom: 0;
		background: rgba(255,255,255,0.8);
		display: flex; align-items: center; justify-content: center;
		border-radius: 10px; z-index: 10;
	}
</style>

<section class="content" style="padding: 15px;">

	<!-- Header -->
	<div class="caja-header">
		<h4><i class="fas fa-cash-register" style="color:#4e73df; margin-right:8px;"></i> Caja de Ventas</h4>
		<span id="badge_estado" class="badge-estado badge-cerrada">CERRADA</span>
	</div>

	<!-- Contenedor principal -->
	<div id="caja_container" style="position:relative; min-height:200px;">

		<!-- Loading -->
		<div id="caja_loading" class="caja-loading">
			<i class="fas fa-spinner fa-spin" style="font-size:28px; color:#4e73df;"></i>
		</div>

		<!-- === ESTADO CERRADO: Formulario de Apertura === -->
		<div id="seccion_cerrada" style="display:none;">
			<div class="apertura-form">
				<i class="fas fa-lock" style="font-size:40px; color:#adb5bd; margin-bottom:12px;"></i>
				<h5>No hay caja abierta</h5>
				<p style="color:#6c757d; font-size:13px; margin-bottom:20px;">Ingrese el monto inicial (fondo de caja / sencillo) para comenzar.</p>
				<div class="form-group">
					<label>Monto Inicial (S/.)</label>
					<input type="number" id="txt_monto_inicial" class="form-control" step="0.01" min="0" placeholder="0.00">
				</div>
				<button type="button" class="btn btn-primary btn-lg" onclick="aperturarCaja()" style="border-radius:8px; padding:10px 40px; font-size:15px; font-weight:600;">
					<i class="fas fa-unlock"></i> Abrir Caja
				</button>
			</div>
		</div>

		<!-- === ESTADO ABIERTO: Dashboard === -->
		<div id="seccion_abierta" style="display:none;">

			<!-- Info de apertura -->
			<div style="font-size:12px; color:#6c757d; margin-bottom:12px;">
				<i class="fas fa-calendar"></i> <span id="info_fecha"></span>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<i class="fas fa-clock"></i> <span id="info_hora"></span>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<i class="fas fa-user"></i> <span id="info_responsable"></span>
			</div>

			<!-- KPI Cards -->
			<div class="kpi-row">
				<div class="kpi-card">
					<div class="kpi-label">Fondo Inicial</div>
					<div class="kpi-value" id="kpi_fondo">S/. 0.00</div>
				</div>
				<div class="kpi-card kpi-green">
					<div class="kpi-label">Ventas Efectivo</div>
					<div class="kpi-value" id="kpi_ventas">S/. 0.00</div>
				</div>
				<div class="kpi-card kpi-green">
					<div class="kpi-label">Ingresos Manuales</div>
					<div class="kpi-value" id="kpi_ingresos">S/. 0.00</div>
				</div>
				<div class="kpi-card kpi-red">
					<div class="kpi-label">Egresos Manuales</div>
					<div class="kpi-value" id="kpi_egresos">S/. 0.00</div>
				</div>
			</div>

			<!-- Saldo Teórico -->
			<div class="saldo-teorico-card">
				<div class="st-label">Saldo Te&oacute;rico en Caja</div>
				<div class="st-value" id="saldo_teorico">S/. 0.00</div>
			</div>

			<!-- Otros medios de pago (informativo, no afecta caja) -->
			<div id="otros_medios_container" style="display:none; margin-bottom:16px;">
				<div style="background:#f8f9fa; border-radius:8px; padding:12px 18px; border:1px solid #e9ecef;">
					<div style="font-size:11px; text-transform:uppercase; color:#6c757d; font-weight:600; margin-bottom:8px;">
						<i class="fas fa-info-circle"></i> Otros ingresos del d&iacute;a (no afectan caja f&iacute;sica)
					</div>
					<div style="display:flex; gap:20px; flex-wrap:wrap; font-size:13px;">
						<div>
							<i class="fas fa-mobile-alt" style="color:#6f2da8;"></i>
							<span style="color:#555;">Yape/Plin:</span>
							<strong id="otros_yape_plin" style="color:#6f2da8;">S/. 0.00</strong>
						</div>
						<div>
							<i class="fas fa-credit-card" style="color:#e67e22;"></i>
							<span style="color:#555;">Tarjeta:</span>
							<strong id="otros_tarjeta" style="color:#e67e22;">S/. 0.00</strong>
						</div>
						<div>
							<i class="fas fa-university" style="color:#2980b9;"></i>
							<span style="color:#555;">Transferencia:</span>
							<strong id="otros_transferencia" style="color:#2980b9;">S/. 0.00</strong>
						</div>
					</div>
				</div>
			</div>

			<!-- Tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab_movimientos" role="tab">
						<i class="fas fa-exchange-alt"></i> Movimientos
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_arqueo" role="tab">
						<i class="fas fa-calculator"></i> Arqueo / Cierre
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_historial" role="tab">
						<i class="fas fa-history"></i> Historial
					</a>
				</li>
			</ul>

			<div class="tab-content">

				<!-- TAB: Movimientos -->
				<div class="tab-pane fade show active" id="tab_movimientos" role="tabpanel">
					<div style="margin-bottom:12px;">
						<button class="btn btn-success btn-sm" onclick="abrirModalMovimiento('INGRESO')" style="border-radius:6px;">
							<i class="fas fa-plus-circle"></i> Ingreso
						</button>
						<button class="btn btn-danger btn-sm" onclick="abrirModalMovimiento('EGRESO')" style="border-radius:6px; margin-left:6px;">
							<i class="fas fa-minus-circle"></i> Egreso
						</button>
					</div>
					<div class="caja-card" style="padding:12px;">
						<table id="tbl_movimientos" class="display" style="width:100%; font-size:12px;" data-page-length="15">
							<thead>
								<tr>
									<th>ID</th>
									<th>Hora</th>
									<th>Tipo</th>
									<th>Descripci&oacute;n</th>
									<th>Referencia</th>
									<th>Monto</th>
									<th>.</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>

				<!-- TAB: Arqueo / Cierre -->
				<div class="tab-pane fade" id="tab_arqueo" role="tabpanel">
					<div class="caja-card">
						<h5 style="font-weight:700; color:#495057; margin-bottom:16px;">
							<i class="fas fa-coins" style="color:#f0ad4e;"></i> Conteo de Efectivo
						</h5>

						<div class="row">
							<div class="col-md-6">
								<table class="arqueo-table">
									<thead>
										<tr>
											<th>Denominaci&oacute;n</th>
											<th>Cantidad</th>
											<th style="text-align:right;">Subtotal</th>
										</tr>
									</thead>
									<tbody>
										<tr class="arqueo-separator"><td colspan="3">Billetes</td></tr>
										<tr><td>S/. 200</td><td><input type="number" class="arq-input" data-valor="200" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_200">0.00</td></tr>
										<tr><td>S/. 100</td><td><input type="number" class="arq-input" data-valor="100" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_100">0.00</td></tr>
										<tr><td>S/. 50</td><td><input type="number" class="arq-input" data-valor="50" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_50">0.00</td></tr>
										<tr><td>S/. 20</td><td><input type="number" class="arq-input" data-valor="20" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_20">0.00</td></tr>
										<tr><td>S/. 10</td><td><input type="number" class="arq-input" data-valor="10" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_10">0.00</td></tr>
										<tr class="arqueo-separator"><td colspan="3">Monedas</td></tr>
										<tr><td>S/. 5</td><td><input type="number" class="arq-input" data-valor="5" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_5">0.00</td></tr>
										<tr><td>S/. 2</td><td><input type="number" class="arq-input" data-valor="2" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_2">0.00</td></tr>
										<tr><td>S/. 1</td><td><input type="number" class="arq-input" data-valor="1" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_1">0.00</td></tr>
										<tr><td>S/. 0.50</td><td><input type="number" class="arq-input" data-valor="0.50" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_050">0.00</td></tr>
										<tr><td>S/. 0.20</td><td><input type="number" class="arq-input" data-valor="0.20" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_020">0.00</td></tr>
										<tr><td>S/. 0.10</td><td><input type="number" class="arq-input" data-valor="0.10" min="0" value="0" onchange="calcularArqueo()" onkeyup="calcularArqueo()"></td><td class="subtotal-cell" id="arq_010">0.00</td></tr>
									</tbody>
								</table>
							</div>

							<div class="col-md-6">
								<div style="margin-top:20px;">
									<div class="detalle-row" style="font-size:16px;">
										<span class="detalle-label">Total Contado (Real):</span>
										<span class="detalle-value" id="arqueo_total_real" style="font-size:20px; color:#4e73df;">S/. 0.00</span>
									</div>
									<div class="detalle-row" style="font-size:16px;">
										<span class="detalle-label">Saldo Te&oacute;rico:</span>
										<span class="detalle-value" id="arqueo_saldo_teorico" style="font-size:20px;">S/. 0.00</span>
									</div>
									<hr>
									<div class="detalle-row" style="font-size:18px;">
										<span class="detalle-label">Diferencia:</span>
										<span id="arqueo_diferencia" style="font-size:22px;" class="diferencia-cero">S/. 0.00</span>
									</div>
									<div id="arqueo_diferencia_texto" style="font-size:12px; color:#6c757d; margin-top:4px;"></div>

									<div class="form-group" style="margin-top:20px;">
										<label style="font-size:12px; color:#555; font-weight:600;">Observaciones</label>
										<textarea id="txt_observaciones_cierre" class="form-control" rows="3" style="border-radius:6px; font-size:13px;" placeholder="Notas del cierre (opcional)..."></textarea>
									</div>

									<button type="button" class="btn btn-danger btn-lg" onclick="cerrarCaja()" style="border-radius:8px; width:100%; margin-top:12px; font-weight:600;">
										<i class="fas fa-lock"></i> Cerrar Caja
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- TAB: Historial -->
				<div class="tab-pane fade" id="tab_historial" role="tabpanel">
					<div class="caja-card" style="padding:12px;">
						<table id="tbl_historial" class="display" style="width:100%; font-size:12px;" data-page-length="15">
							<thead>
								<tr>
									<th>ID</th>
									<th>Fecha</th>
									<th>Responsable</th>
									<th>Inicial</th>
									<th>Ventas</th>
									<th>Mov +</th>
									<th>Mov -</th>
									<th>Calculado</th>
									<th>Real</th>
									<th>Diferencia</th>
									<th>Estado</th>
									<th>.</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>

			</div>
		</div>

	</div>

</section>

<!-- ==================== MODAL: Registrar Movimiento ==================== -->
<div id="modal_movimiento" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content" style="border-radius:10px;">
			<div class="modal-header" style="border-bottom:1px solid #e9ecef; padding:14px 20px;">
				<h5 class="modal-title" id="modal_mov_titulo" style="font-weight:700; font-size:15px;">Registrar Movimiento</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
			</div>
			<div class="modal-body" style="padding:20px;">
				<input type="hidden" id="mov_tipo">
				<div class="form-group">
					<label style="font-size:12px; color:#555; font-weight:600;">Monto (S/.)</label>
					<input type="number" id="mov_monto" class="form-control" step="0.01" min="0.01" placeholder="0.00" style="height:40px; font-size:15px; text-align:center; border-radius:6px;">
				</div>
				<div class="form-group">
					<label style="font-size:12px; color:#555; font-weight:600;">Descripci&oacute;n *</label>
					<input type="text" id="mov_descripcion" class="form-control" placeholder="Ej: Pago proveedor urgente" style="height:38px; font-size:13px; border-radius:6px;">
				</div>
				<div class="form-group">
					<label style="font-size:12px; color:#555; font-weight:600;">Referencia <small>(opcional)</small></label>
					<input type="text" id="mov_referencia" class="form-control" placeholder="Nro. recibo, boleta, etc." style="height:38px; font-size:13px; border-radius:6px;">
				</div>
			</div>
			<div class="modal-footer" style="border-top:1px solid #e9ecef; padding:12px 20px;">
				<button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:6px;">Cancelar</button>
				<button type="button" class="btn btn-primary" onclick="guardarMovimiento()" style="border-radius:6px; font-weight:600;">Guardar</button>
			</div>
		</div>
	</div>
</div>

<!-- ==================== MODAL: Detalle Caja Cerrada ==================== -->
<div id="modal_detalle" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" style="border-radius:10px;">
			<div class="modal-header" style="border-bottom:1px solid #e9ecef; padding:14px 20px;">
				<h5 class="modal-title" style="font-weight:700; font-size:15px;"><i class="fas fa-receipt"></i> Detalle de Caja</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
			</div>
			<div class="modal-body" id="detalle_body" style="padding:20px;">
				<!-- Populated by JS -->
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

	var saldoTeoricoGlobal = 0;
	var dtMovimientos = null;
	var dtHistorial = null;

	$(document).ready(function(){
		cargarDashboard();

		// Inicializar DataTable movimientos
		dtMovimientos = $('#tbl_movimientos').DataTable({
			dom: "frtip",
			order: [[0, 'desc']],
			pageLength: 15,
			language: { emptyTable: "No hay movimientos registrados" },
			ajax: "<?= base_url('caja/get_movimientos') ?>",
			columnDefs: [
				{ visible: false, targets: [0] },
				{ className: "text-right", targets: [5] }
			]
		});

		// Inicializar DataTable historial
		dtHistorial = $('#tbl_historial').DataTable({
			dom: "Bfrtip",
			order: [[0, 'desc']],
			pageLength: 15,
			buttons: [
				{ extend: 'excelHtml5', title: 'Historial_Caja' },
				{ extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', title: 'Historial de Caja' }
			],
			ajax: "<?= base_url('caja/get_historial') ?>",
			columnDefs: [
				{ visible: false, targets: [0] },
				{ className: "text-right", targets: [3,4,5,6,7,8] }
			]
		});
	});

	function fmtMoney(n){
		return 'S/. ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function cargarDashboard(){
		$.getJSON('<?= base_url("caja/get_dashboard_data") ?>', function(data){
			$('#caja_loading').hide();

			if(!data.abierta){
				$('#seccion_cerrada').show();
				$('#seccion_abierta').hide();
				$('#badge_estado').text('CERRADA').removeClass('badge-abierta').addClass('badge-cerrada');
			} else {
				$('#seccion_cerrada').hide();
				$('#seccion_abierta').show();
				$('#badge_estado').text('ABIERTA').removeClass('badge-cerrada').addClass('badge-abierta');

				// Info
				$('#info_fecha').text(data.fecha);
				$('#info_hora').text(data.hora_apertura || '--:--');
				$('#info_responsable').text(data.responsable);

				// KPIs
				$('#kpi_fondo').text(fmtMoney(data.monto_ini));
				$('#kpi_ventas').text(fmtMoney(data.ventas_cash));
				$('#kpi_ingresos').text(fmtMoney(data.mov_ingreso));
				$('#kpi_egresos').text(fmtMoney(data.mov_egreso));

				// Saldo teórico
				saldoTeoricoGlobal = data.saldo_teorico;
				$('#saldo_teorico').text(fmtMoney(data.saldo_teorico));
				$('#arqueo_saldo_teorico').text(fmtMoney(data.saldo_teorico));

				// Otros medios de pago (informativo)
				var tieneOtros = (data.otros_yape_plin > 0 || data.otros_tarjeta > 0 || data.otros_transferencia > 0);
				if(tieneOtros){
					$('#otros_medios_container').show();
					$('#otros_yape_plin').text(fmtMoney(data.otros_yape_plin));
					$('#otros_tarjeta').text(fmtMoney(data.otros_tarjeta));
					$('#otros_transferencia').text(fmtMoney(data.otros_transferencia));
				} else {
					$('#otros_medios_container').hide();
				}

				calcularArqueo();
			}
		}).fail(function(){
			$('#caja_loading').hide();
			$('#seccion_cerrada').show();
		});
	}

	// ========================
	// APERTURA
	// ========================
	function aperturarCaja(){
		var monto = parseFloat($('#txt_monto_inicial').val());
		if(isNaN(monto) || monto < 0){
			alert('Ingrese un monto inicial v\u00e1lido.');
			return;
		}
		if(!confirm('\u00bfDesea abrir la caja con S/. ' + monto.toFixed(2) + ' como fondo inicial?')){
			return;
		}
		$.post('<?= base_url("caja/aperturar") ?>', { monto_inicial: monto }, function(data){
			if(data.rpta == 'success'){
				location.reload();
			} else {
				alert(data.msg);
			}
		}, 'json').fail(function(){ alert('Error de conexi\u00f3n.'); });
	}

	// ========================
	// MOVIMIENTOS
	// ========================
	function abrirModalMovimiento(tipo){
		$('#mov_tipo').val(tipo);
		$('#mov_monto').val('');
		$('#mov_descripcion').val('');
		$('#mov_referencia').val('');

		var color = tipo == 'INGRESO' ? '#28a745' : '#dc3545';
		var icon  = tipo == 'INGRESO' ? 'fa-plus-circle' : 'fa-minus-circle';
		$('#modal_mov_titulo').html('<i class="fas '+icon+'" style="color:'+color+';"></i> Registrar '+tipo.charAt(0)+tipo.slice(1).toLowerCase());

		$('#modal_movimiento').modal('show');
		setTimeout(function(){ $('#mov_monto').focus(); }, 300);
	}

	function guardarMovimiento(){
		var tipo = $('#mov_tipo').val();
		var monto = parseFloat($('#mov_monto').val());
		var descripcion = $.trim($('#mov_descripcion').val());
		var referencia = $.trim($('#mov_referencia').val());

		if(isNaN(monto) || monto <= 0){
			alert('Ingrese un monto v\u00e1lido mayor a cero.');
			return;
		}
		if(descripcion.length == 0){
			alert('La descripci\u00f3n es obligatoria.');
			return;
		}

		$.post('<?= base_url("caja/registrar_movimiento") ?>', {
			tipo: tipo,
			monto: monto,
			descripcion: descripcion,
			referencia: referencia
		}, function(data){
			if(data.rpta == 'success'){
				$('#modal_movimiento').modal('hide');
				dtMovimientos.ajax.reload();
				cargarDashboard();
			} else {
				alert(data.msg);
			}
		}, 'json').fail(function(){ alert('Error de conexi\u00f3n.'); });
	}

	function eliminarMovimiento(id){
		if(!confirm('\u00bfEliminar este movimiento?')) return;

		$.post('<?= base_url("caja/eliminar_movimiento") ?>', { id: id }, function(data){
			if(data.rpta == 'success'){
				dtMovimientos.ajax.reload();
				cargarDashboard();
			} else {
				alert(data.msg);
			}
		}, 'json').fail(function(){ alert('Error de conexi\u00f3n.'); });
	}

	// ========================
	// ARQUEO
	// ========================
	function calcularArqueo(){
		var total = 0;
		var arqueo = { billetes: {}, monedas: {} };
		var denominacionesBilletes = [200, 100, 50, 20, 10];

		$('.arq-input').each(function(){
			var valor = parseFloat($(this).data('valor'));
			var cantidad = parseInt($(this).val()) || 0;
			var subtotal = valor * cantidad;
			total += subtotal;

			// ID del span subtotal
			var idKey = 'arq_' + (valor + '').replace('.', '');
			$('#' + idKey).text(subtotal.toFixed(2));

			if(denominacionesBilletes.indexOf(valor) >= 0){
				arqueo.billetes[valor] = cantidad;
			} else {
				arqueo.monedas[valor] = cantidad;
			}
		});

		arqueo.total = parseFloat(total.toFixed(2));
		$('#arqueo_total_real').text(fmtMoney(total));

		var diferencia = total - saldoTeoricoGlobal;
		$('#arqueo_diferencia').text(fmtMoney(diferencia));

		if(diferencia > 0.009){
			$('#arqueo_diferencia').attr('class', 'diferencia-sobrante');
			$('#arqueo_diferencia_texto').text('SOBRANTE: Hay m\u00e1s dinero del esperado').css('color', '#28a745');
		} else if(diferencia < -0.009){
			$('#arqueo_diferencia').attr('class', 'diferencia-faltante');
			$('#arqueo_diferencia_texto').text('FALTANTE: Hay menos dinero del esperado').css('color', '#dc3545');
		} else {
			$('#arqueo_diferencia').attr('class', 'diferencia-cero');
			$('#arqueo_diferencia_texto').text('Cuadre exacto').css('color', '#28a745');
		}

		// Guardar JSON en variable global para enviar al cerrar
		window.arqueoJSON = JSON.stringify(arqueo);
	}

	// ========================
	// CIERRE DE CAJA
	// ========================
	function cerrarCaja(){
		var totalReal = 0;
		$('.arq-input').each(function(){
			var valor = parseFloat($(this).data('valor'));
			var cantidad = parseInt($(this).val()) || 0;
			totalReal += valor * cantidad;
		});

		var diferencia = totalReal - saldoTeoricoGlobal;
		var difTexto = diferencia > 0 ? 'SOBRANTE' : (diferencia < 0 ? 'FALTANTE' : 'EXACTO');

		var msg = 'Resumen del cierre:\n\n';
		msg += 'Total Contado: S/. ' + totalReal.toFixed(2) + '\n';
		msg += 'Saldo Te\u00f3rico: S/. ' + saldoTeoricoGlobal.toFixed(2) + '\n';
		msg += 'Diferencia: S/. ' + diferencia.toFixed(2) + ' (' + difTexto + ')\n\n';
		msg += '\u00bfDesea cerrar la caja?';

		if(!confirm(msg)) return;

		var observaciones = $.trim($('#txt_observaciones_cierre').val());

		$.post('<?= base_url("caja/cerrar") ?>', {
			monto_real: totalReal,
			arqueo_json: window.arqueoJSON || '{}',
			observaciones: observaciones
		}, function(data){
			if(data.rpta == 'success'){
				alert('Caja cerrada correctamente.\n\nDiferencia: S/. ' + parseFloat(data.diferencia).toFixed(2));
				location.reload();
			} else {
				alert(data.msg);
			}
		}, 'json').fail(function(){ alert('Error de conexi\u00f3n.'); });
	}

	// ========================
	// HISTORIAL - DETALLE
	// ========================
	function verDetalle(id){
		$.getJSON('<?= base_url("caja/ver_detalle/") ?>' + id, function(data){
			if(!data.caja){
				alert('No se encontr\u00f3 la caja.');
				return;
			}
			var c = data.caja;
			var html = '';

			// Datos generales
			html += '<div class="row"><div class="col-md-6">';
			html += '<h6 style="font-weight:700; margin-bottom:12px;">Resumen</h6>';
			html += '<div class="detalle-row"><span class="detalle-label">Fecha:</span><span class="detalle-value">' + c.fecha + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Responsable:</span><span class="detalle-value">' + (c.responsable || '-') + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Hora Apertura:</span><span class="detalle-value">' + (c.hora_apertura || '-') + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Hora Cierre:</span><span class="detalle-value">' + (c.hora_cierre || '-') + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Monto Inicial:</span><span class="detalle-value">' + fmtMoney(c.monto_ini) + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Ventas Efectivo:</span><span class="detalle-value">' + fmtMoney(c.ventas) + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Mov. Ingreso:</span><span class="detalle-value" style="color:#28a745;">' + fmtMoney(c.movimientos_ingreso) + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Mov. Egreso:</span><span class="detalle-value" style="color:#dc3545;">' + fmtMoney(c.movimientos_egreso) + '</span></div>';
			html += '<hr>';
			html += '<div class="detalle-row"><span class="detalle-label">Saldo Te\u00f3rico:</span><span class="detalle-value" style="font-size:16px;">' + fmtMoney(c.monto_calculado) + '</span></div>';
			html += '<div class="detalle-row"><span class="detalle-label">Saldo Real:</span><span class="detalle-value" style="font-size:16px;">' + fmtMoney(c.monto_fin) + '</span></div>';

			var dif = parseFloat(c.diferencia || 0);
			var difColor = dif == 0 ? '#6c757d' : (dif > 0 ? '#28a745' : '#dc3545');
			html += '<div class="detalle-row"><span class="detalle-label">Diferencia:</span><span class="detalle-value" style="font-size:16px; color:'+difColor+';">' + fmtMoney(dif) + '</span></div>';

			if(c.observaciones_cierre){
				html += '<div style="margin-top:10px; font-size:12px; color:#6c757d;"><b>Observaciones:</b> ' + c.observaciones_cierre + '</div>';
			}
			html += '</div>';

			// Arqueo JSON
			html += '<div class="col-md-6">';
			if(c.arqueo_json && c.arqueo_json != '{}'){
				try {
					var arq = JSON.parse(c.arqueo_json);
					html += '<h6 style="font-weight:700; margin-bottom:12px;">Arqueo</h6>';
					html += '<table style="width:100%; font-size:12px; border-collapse:collapse;">';
					html += '<tr style="border-bottom:2px solid #dee2e6;"><th style="padding:4px;">Denom.</th><th style="padding:4px;">Cant.</th><th style="padding:4px; text-align:right;">Subtotal</th></tr>';

					if(arq.billetes){
						html += '<tr style="background:#f8f9fa;"><td colspan="3" style="padding:4px; font-weight:600;">Billetes</td></tr>';
						for(var k in arq.billetes){
							var sub = parseFloat(k) * arq.billetes[k];
							html += '<tr><td style="padding:3px 4px;">S/. '+k+'</td><td style="padding:3px 4px;">'+arq.billetes[k]+'</td><td style="padding:3px 4px; text-align:right;">'+sub.toFixed(2)+'</td></tr>';
						}
					}
					if(arq.monedas){
						html += '<tr style="background:#f8f9fa;"><td colspan="3" style="padding:4px; font-weight:600;">Monedas</td></tr>';
						for(var k in arq.monedas){
							var sub = parseFloat(k) * arq.monedas[k];
							html += '<tr><td style="padding:3px 4px;">S/. '+k+'</td><td style="padding:3px 4px;">'+arq.monedas[k]+'</td><td style="padding:3px 4px; text-align:right;">'+sub.toFixed(2)+'</td></tr>';
						}
					}
					html += '<tr style="border-top:2px solid #333;"><td colspan="2" style="padding:4px; font-weight:700;">TOTAL</td><td style="padding:4px; text-align:right; font-weight:700;">'+(arq.total ? arq.total.toFixed(2) : '0.00')+'</td></tr>';
					html += '</table>';
				} catch(e){}
			}

			// Movimientos
			if(data.movimientos && data.movimientos.length > 0){
				html += '<h6 style="font-weight:700; margin:16px 0 8px;">Movimientos</h6>';
				html += '<table style="width:100%; font-size:11px; border-collapse:collapse;">';
				html += '<tr style="border-bottom:1px solid #dee2e6;"><th style="padding:3px;">Hora</th><th>Tipo</th><th>Descripci\u00f3n</th><th style="text-align:right;">Monto</th></tr>';
				for(var i=0; i<data.movimientos.length; i++){
					var m = data.movimientos[i];
					var mc = m.tipo == 'INGRESO' ? '#28a745' : '#dc3545';
					html += '<tr style="border-bottom:1px solid #f0f0f0;">';
					html += '<td style="padding:3px;">'+(m.fecha_hora || '').substring(11,16)+'</td>';
					html += '<td style="padding:3px; color:'+mc+'; font-weight:600;">'+m.tipo+'</td>';
					html += '<td style="padding:3px;">'+m.descripcion+'</td>';
					html += '<td style="padding:3px; text-align:right;">'+parseFloat(m.monto).toFixed(2)+'</td>';
					html += '</tr>';
				}
				html += '</table>';
			}
			html += '</div></div>';

			$('#detalle_body').html(html);
			$('#modal_detalle').modal('show');
		});
	}

</script>
