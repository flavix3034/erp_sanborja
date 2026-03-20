<?php defined('BASEPATH') OR exit('No direct script access allowed');
$store_id_val = isset($store_id) ? $store_id : $_SESSION['store_id'];
$fecha_val = isset($fecha) ? $fecha : date('Y-m-d');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

<style>
	.cd-card {
		background: #fff;
		border-radius: 10px;
		box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		padding: 20px;
		margin-bottom: 16px;
	}
	.cd-card h5 {
		font-weight: 700;
		color: #1a1a2e;
		font-size: 15px;
		margin-bottom: 14px;
		border-bottom: 2px solid #f0f0f0;
		padding-bottom: 8px;
	}

	/* KPI Cards */
	.cd-kpi-row { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 16px; }
	.cd-kpi {
		flex: 1;
		min-width: 160px;
		background: #fff;
		border-radius: 10px;
		padding: 16px;
		text-align: center;
		box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		border-left: 4px solid #4e73df;
	}
	.cd-kpi .kpi-label {
		font-size: 11px;
		text-transform: uppercase;
		color: #6c757d;
		font-weight: 600;
		letter-spacing: 0.3px;
		margin-bottom: 4px;
	}
	.cd-kpi .kpi-value {
		font-size: 22px;
		font-weight: 700;
		color: #333;
	}
	.cd-kpi.kpi-ventas { border-left-color: #4e73df; }
	.cd-kpi.kpi-ventas .kpi-value { color: #4e73df; }
	.cd-kpi.kpi-ganancia-bruta { border-left-color: #1cc88a; }
	.cd-kpi.kpi-ganancia-bruta .kpi-value { color: #1cc88a; }
	.cd-kpi.kpi-gastos { border-left-color: #e74a3b; }
	.cd-kpi.kpi-gastos .kpi-value { color: #e74a3b; }
	/* Caja card special */
	.caja-resumen-card {
		background: linear-gradient(135deg, #4e73df, #224abe);
		border-radius: 10px;
		padding: 18px 22px;
		color: #fff;
		margin-bottom: 16px;
	}
	.caja-resumen-card .caja-titulo {
		font-size: 13px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		opacity: 0.85;
		margin-bottom: 12px;
		font-weight: 600;
	}
	.caja-resumen-card .caja-fila {
		display: flex;
		justify-content: space-between;
		padding: 3px 0;
		font-size: 13px;
	}
	.caja-resumen-card .caja-fila .caja-lbl { opacity: 0.8; }
	.caja-resumen-card .caja-fila .caja-val { font-weight: 600; }
	.caja-resumen-card .caja-total {
		border-top: 1px solid rgba(255,255,255,0.3);
		margin-top: 8px;
		padding-top: 8px;
		font-size: 18px;
		font-weight: 700;
	}
	.caja-no-abierta {
		background: #f8f9fa;
		border: 2px dashed #dee2e6;
		border-radius: 10px;
		padding: 20px;
		text-align: center;
		color: #6c757d;
		margin-bottom: 16px;
	}

	/* Tables */
	.cd-table { width: 100%; border-collapse: collapse; font-size: 13px; }
	.cd-table th {
		font-size: 11px;
		text-transform: uppercase;
		color: #6c757d;
		padding: 8px 10px;
		border-bottom: 2px solid #dee2e6;
		font-weight: 600;
	}
	.cd-table td {
		padding: 6px 10px;
		border-bottom: 1px solid #f0f0f0;
		color: #333;
	}
	.cd-table tr:last-child td { border-bottom: none; }
	.cd-table .fila-total {
		background: #f8f9fa;
		font-weight: 700;
	}
	.cd-table .text-right { text-align: right; }

	/* Validaciones */
	.validacion-item {
		display: flex;
		align-items: center;
		padding: 8px 12px;
		border-radius: 6px;
		margin-bottom: 8px;
		font-size: 13px;
		font-weight: 500;
	}
	.validacion-item i { margin-right: 10px; font-size: 16px; }
	.val-ok { background: #d4edda; color: #155724; }
	.val-warn { background: #fff3cd; color: #856404; }
	.val-error { background: #f8d7da; color: #721c24; }
	.val-info { background: #d1ecf1; color: #0c5460; }

	/* Loading */
	.cd-loading {
		text-align: center;
		padding: 60px;
		color: #6c757d;
	}
	.cd-loading i { font-size: 28px; color: #4e73df; }

	/* Print */
	@media print {
		.no-print { display: none !important; }
		.cd-card { box-shadow: none; border: 1px solid #ddd; page-break-inside: avoid; }
		.cd-kpi { box-shadow: none; border: 1px solid #ddd; }
	}

	@media (max-width: 768px) {
		.cd-kpi .kpi-value { font-size: 16px; }
		.cd-kpi { min-width: 120px; }
	}
</style>

<section class="content" style="padding: 15px;">

	<!-- Filtros -->
	<div class="row no-print" style="display:flex; align-items: flex-end; margin-bottom: 16px;">
		<div class="col-sm-3 col-md-2">
			<div class="form-group" style="margin-bottom:0;">
				<label for="fecha" style="font-size:12px; font-weight:600; color:#555;">Fecha:</label>
				<input type="date" name="fecha" id="fecha" class="form-control" value="<?= $fecha_val ?>">
			</div>
		</div>
		<div class="col-sm-3 col-md-2">
			<div class="form-group" style="margin-bottom:0;">
				<label for="store_id" style="font-size:12px; font-weight:600; color:#555;">Tienda:</label>
				<?php
					$group_id = $_SESSION["group_id"];
					$q = $this->db->get('tec_stores');
					$ar = array();
					if ($group_id == '1') {
						foreach ($q->result() as $r) {
							$ar[$r->id] = $r->name;
						}
					} else {
						foreach ($q->result() as $r) {
							if ($r->id == $_SESSION["store_id"]) {
								$ar[$r->id] = $r->name;
							}
						}
					}
					echo form_dropdown('store_id', $ar, $store_id_val, 'class="form-control" id="store_id"');
				?>
			</div>
		</div>
		<div class="col-sm-2 col-md-1" style="margin-bottom:0;">
			<a href="#" onclick="activo1()" class="btn btn-primary" style="margin-bottom:0;"><b>Consultar</b></a>
		</div>
		<div class="col-sm-2 col-md-2" style="margin-bottom:0;">
			<a href="#" onclick="generarPDF()" class="btn btn-danger" style="margin-bottom:0;"><i class="fas fa-file-pdf"></i> Descargar PDF</a>
		</div>
		<div id="refresco" class="col-sm-1"></div>
	</div>

	<!-- Encabezado del reporte -->
	<div style="margin-bottom:16px;">
		<h4 style="font-weight:700; color:#1a1a2e; margin:0;">
			<i class="fas fa-clipboard-check" style="color:#4e73df;"></i>
			Cierre Diario - <span id="rep_fecha"><?= $fecha_val ?></span>
		</h4>
		<span id="rep_tienda" style="font-size:13px; color:#6c757d;"></span>
	</div>

	<!-- Loading -->
	<div id="cd_loading" class="cd-loading">
		<i class="fas fa-spinner fa-spin"></i>
		<p style="margin-top:10px;">Cargando datos del d&iacute;a...</p>
	</div>

	<!-- Contenido principal (oculto hasta cargar) -->
	<div id="cd_contenido" style="display:none;">

		<!-- KPIs principales -->
		<div class="cd-kpi-row">
			<div class="cd-kpi kpi-ventas">
				<div class="kpi-label">Total Ventas</div>
				<div class="kpi-value" id="kpi_total_ventas">S/. 0.00</div>
				<div style="font-size:11px; color:#6c757d;" id="kpi_cant_ventas">0 ventas</div>
			</div>
			<div class="cd-kpi kpi-ganancia-bruta">
				<div class="kpi-label">Ganancia Bruta</div>
				<div class="kpi-value" id="kpi_ganancia_bruta">S/. 0.00</div>
				<div style="font-size:11px; color:#6c757d;">Sin IGV</div>
			</div>
			<div class="cd-kpi kpi-gastos">
				<div class="kpi-label">Total Gastos</div>
				<div class="kpi-value" id="kpi_total_gastos">S/. 0.00</div>
				<div style="font-size:11px; color:#6c757d;" id="kpi_detalle_gastos">-</div>
			</div>
		</div>

		<!-- Fila: Ventas por Forma de Pago + Ventas por Documento -->
		<div class="row">
			<!-- Ventas por Forma de Pago -->
			<div class="col-md-6">
				<div class="cd-card">
					<h5><i class="fas fa-credit-card" style="color:#4e73df;"></i> Ventas por Forma de Pago</h5>
					<table class="cd-table" id="tbl_forma_pago">
						<thead>
							<tr>
								<th>Forma de Pago</th>
								<th class="text-right">Cant.</th>
								<th class="text-right">Total</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>

			<!-- Ventas por Tipo Documento -->
			<div class="col-md-6">
				<div class="cd-card">
					<h5><i class="fas fa-file-invoice" style="color:#1cc88a;"></i> Ventas por Documento</h5>
					<table class="cd-table" id="tbl_documento">
						<thead>
							<tr>
								<th>Tipo</th>
								<th class="text-right">Cant.</th>
								<th class="text-right">Total</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Fila: Rentabilidad + Gastos -->
		<div class="row">
			<!-- Rentabilidad -->
			<div class="col-md-5">
				<div class="cd-card">
					<h5><i class="fas fa-chart-line" style="color:#f6c23e;"></i> Rentabilidad del D&iacute;a</h5>
					<table class="cd-table" id="tbl_rentabilidad">
						<tbody></tbody>
					</table>
				</div>
			</div>

			<!-- Gastos del Día -->
			<div class="col-md-7">
				<div class="cd-card">
					<h5><i class="fas fa-receipt" style="color:#e74a3b;"></i> Gastos del D&iacute;a</h5>
					<table class="cd-table" id="tbl_gastos">
						<thead>
							<tr>
								<th>Origen</th>
								<th>Categor&iacute;a</th>
								<th>Descripci&oacute;n</th>
								<th class="text-right">Monto</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					<div id="sin_gastos" style="display:none; text-align:center; padding:12px; color:#6c757d; font-size:13px;">
						<i class="fas fa-check-circle" style="color:#28a745;"></i> Sin gastos registrados este d&iacute;a
					</div>
				</div>
			</div>
		</div>

		<!-- Validaciones -->
		<div class="cd-card">
			<h5><i class="fas fa-shield-alt" style="color:#36b9cc;"></i> Validaciones de Control</h5>
			<div class="row">
				<div class="col-md-3" id="val_sunat"></div>
				<div class="col-md-3" id="val_anuladas"></div>
				<div class="col-md-3" id="val_stock"></div>
				<div class="col-md-3" id="val_caja"></div>
			</div>
		</div>

		<!-- Resumen de Cajas -->
		<h5 style="margin-top:15px; margin-bottom:10px;"><i class="fas fa-cash-register" style="color:#6f42c1;"></i> Resumen de Cajas</h5>
		<div class="row" id="caja_contenido"></div>

	</div>

</section>

<script type="text/javascript">

	function fmtMoney(n) {
		return 'S/. ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function fmtPago(paidBy) {
		var mapa = {
			'cash': 'Efectivo',
			'Transf BCP': 'Transf. BCP',
			'Yape': 'Yape',
			'Plin': 'Plin',
			'IZIPAY': 'IZIPAY',
			'Transf Scotiabank': 'Transf. Scotiabank',
			'Vendemas': 'Vendemas',
			'Transf Bbva': 'Transf. BBVA',
			'Transf Interbank': 'Transf. Interbank'
		};
		return mapa[paidBy] || paidBy;
	}

	var dataCierre = null; // Variable global para PDF

	$(document).ready(function() {
		cargarCierreDiario();
	});

	function cargarCierreDiario() {
		var fecha = '<?= $fecha_val ?>';
		var store_id = '<?= $store_id_val ?>';

		$.getJSON('<?= base_url("reportes/get_cierre_diario/") ?>' + fecha + '/' + store_id, function(data) {
			dataCierre = data; // Guardar para PDF
			$('#cd_loading').hide();
			$('#cd_contenido').show();

			$('#rep_tienda').text(data.tienda);

			// KPIs
			$('#kpi_total_ventas').text(fmtMoney(data.totales.total_ventas));
			$('#kpi_cant_ventas').text(data.totales.cantidad_ventas + ' ventas');
			$('#kpi_ganancia_bruta').text(fmtMoney(data.rentabilidad.ganancia_bruta));
			$('#kpi_total_gastos').text(fmtMoney(data.totales.total_gastos_dia));
			$('#kpi_detalle_gastos').text('Op: ' + fmtMoney(data.totales.total_gastos) + ' | CC: ' + fmtMoney(data.totales.total_cajachica));
			// === CAJA ===
			renderCajas(data.cajas);

			// === VENTAS POR FORMA DE PAGO ===
			renderFormasPago(data.ventas_forma_pago);

			// === VENTAS POR DOCUMENTO ===
			renderDocumentos(data.ventas_documento);

			// === RENTABILIDAD ===
			renderRentabilidad(data.rentabilidad);

			// === GASTOS ===
			renderGastos(data.gastos, data.gastos_cajachica);

			// === VALIDACIONES ===
			renderValidaciones(data.validaciones);

		}).fail(function() {
			$('#cd_loading').html('<i class="fas fa-exclamation-triangle" style="color:#e74a3b; font-size:28px;"></i><p style="margin-top:10px; color:#e74a3b;">Error al cargar los datos</p>');
		});
	}

	function renderCajas(cajas) {
		var html = '';
		if (!cajas || cajas.length === 0) {
			html = '<div class="col-md-12"><div class="caja-no-abierta">';
			html += '<i class="fas fa-info-circle" style="font-size:24px; color:#adb5bd;"></i>';
			html += '<p style="margin:8px 0 0; font-size:13px;">Sin apertura de caja registrada este d&iacute;a</p>';
			html += '</div></div>';
		} else {
			for (var idx = 0; idx < cajas.length; idx++) {
				var caja = cajas[idx];
				var titulo = 'Caja #' + (idx + 1);
				html += '<div class="col-md-6" style="margin-bottom:10px;">';
				html += '<div class="caja-resumen-card" style="height:100%;">';
				html += '<div class="caja-titulo"><i class="fas fa-cash-register"></i> ' + titulo + '</div>';
				html += '<div class="caja-fila"><span class="caja-lbl">Fondo Inicial</span><span class="caja-val">' + fmtMoney(caja.fondo_ini) + '</span></div>';
				html += '<div class="caja-fila"><span class="caja-lbl">(+) Ventas Efectivo</span><span class="caja-val">' + fmtMoney(caja.ventas_efectivo) + '</span></div>';
				html += '<div class="caja-fila"><span class="caja-lbl">(+) Ingresos Manuales</span><span class="caja-val">' + fmtMoney(caja.ingresos) + '</span></div>';
				html += '<div class="caja-fila"><span class="caja-lbl">(-) Egresos Manuales</span><span class="caja-val">' + fmtMoney(caja.egresos) + '</span></div>';
				html += '<div class="caja-fila caja-total"><span>Saldo Te&oacute;rico</span><span>' + fmtMoney(caja.saldo_teorico) + '</span></div>';

				if (caja.estado == 'CERRADA') {
					html += '<div style="margin-top:10px; border-top:1px solid rgba(255,255,255,0.2); padding-top:8px;">';
					html += '<div class="caja-fila"><span class="caja-lbl">Conteo Real</span><span class="caja-val">' + fmtMoney(caja.monto_real) + '</span></div>';

					var dif = parseFloat(caja.diferencia);
					var difColor = '#fff';
					var difTexto = '';
					if (dif > 0.009) {
						difColor = '#ffc107';
						difTexto = 'SOBRANTE';
					} else if (dif < -0.009) {
						difColor = '#ff6b6b';
						difTexto = 'FALTANTE';
					} else {
						difColor = '#69f0ae';
						difTexto = 'EXACTO';
					}
					html += '<div class="caja-fila"><span class="caja-lbl">Diferencia</span><span class="caja-val" style="color:' + difColor + ';">' + fmtMoney(dif) + ' (' + difTexto + ')</span></div>';
					html += '<div style="text-align:right; margin-top:4px;"><span style="background:rgba(255,255,255,0.2); padding:2px 10px; border-radius:10px; font-size:11px;">CERRADA</span></div>';
					html += '</div>';
				} else {
					html += '<div style="text-align:right; margin-top:6px;"><span style="background:rgba(40,167,69,0.8); padding:2px 10px; border-radius:10px; font-size:11px;">ABIERTA</span></div>';
				}
				html += '</div></div>';
			}
		}
		$('#caja_contenido').html(html);
	}

	function renderFormasPago(formas) {
		var tbody = '';
		var totalCant = 0, totalMonto = 0;
		for (var i = 0; i < formas.length; i++) {
			var f = formas[i];
			totalCant += parseInt(f.cantidad);
			totalMonto += parseFloat(f.total);
			tbody += '<tr>';
			tbody += '<td>' + fmtPago(f.forma_pago) + '</td>';
			tbody += '<td class="text-right">' + f.cantidad + '</td>';
			tbody += '<td class="text-right">' + fmtMoney(f.total) + '</td>';
			tbody += '</tr>';
		}
		tbody += '<tr class="fila-total">';
		tbody += '<td>TOTAL</td>';
		tbody += '<td class="text-right">' + totalCant + '</td>';
		tbody += '<td class="text-right">' + fmtMoney(totalMonto) + '</td>';
		tbody += '</tr>';
		$('#tbl_forma_pago tbody').html(tbody);
	}

	function renderDocumentos(docs) {
		var tbody = '';
		var totalCant = 0, totalMonto = 0;
		for (var i = 0; i < docs.length; i++) {
			var d = docs[i];
			totalCant += parseInt(d.cantidad);
			totalMonto += parseFloat(d.total);
			tbody += '<tr>';
			tbody += '<td>' + d.tipo + '</td>';
			tbody += '<td class="text-right">' + d.cantidad + '</td>';
			tbody += '<td class="text-right">' + fmtMoney(d.total) + '</td>';
			tbody += '</tr>';
		}
		tbody += '<tr class="fila-total">';
		tbody += '<td>TOTAL</td>';
		tbody += '<td class="text-right">' + totalCant + '</td>';
		tbody += '<td class="text-right">' + fmtMoney(totalMonto) + '</td>';
		tbody += '</tr>';
		$('#tbl_documento tbody').html(tbody);
	}

	function renderRentabilidad(rent) {
		var html = '';
		html += '<tr><td>Ventas Netas (sin IGV)</td><td class="text-right">' + fmtMoney(rent.ventas_netas) + '</td></tr>';
		html += '<tr><td>(-) Costo de Mercader&iacute;a</td><td class="text-right" style="color:#e74a3b;">' + fmtMoney(rent.costos) + '</td></tr>';
		html += '<tr class="fila-total"><td>= Ganancia Bruta</td><td class="text-right" style="color:#1cc88a;">' + fmtMoney(rent.ganancia_bruta) + '</td></tr>';
		$('#tbl_rentabilidad tbody').html(html);
	}

	function renderGastos(gastos, cajachica) {
		var tbody = '';
		var totalGeneral = 0;
		var hayGastos = false;

		for (var i = 0; i < gastos.length; i++) {
			hayGastos = true;
			var g = gastos[i];
			totalGeneral += parseFloat(g.monto);
			tbody += '<tr>';
			tbody += '<td><span style="background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:4px; font-size:11px;">Gasto</span></td>';
			tbody += '<td>' + (g.categoria || '-') + '</td>';
			tbody += '<td>' + (g.descripcion || '-') + '</td>';
			tbody += '<td class="text-right">' + fmtMoney(g.monto) + '</td>';
			tbody += '</tr>';
		}

		for (var i = 0; i < cajachica.length; i++) {
			hayGastos = true;
			var c = cajachica[i];
			totalGeneral += parseFloat(c.monto);
			tbody += '<tr>';
			tbody += '<td><span style="background:#fce4ec; color:#c62828; padding:2px 8px; border-radius:4px; font-size:11px;">Caja Chica</span></td>';
			tbody += '<td>' + (c.categoria || '-') + '</td>';
			tbody += '<td>' + (c.descripcion || '-') + '</td>';
			tbody += '<td class="text-right">' + fmtMoney(c.monto) + '</td>';
			tbody += '</tr>';
		}

		if (hayGastos) {
			tbody += '<tr class="fila-total">';
			tbody += '<td colspan="3">TOTAL GASTOS</td>';
			tbody += '<td class="text-right">' + fmtMoney(totalGeneral) + '</td>';
			tbody += '</tr>';
			$('#tbl_gastos').show();
			$('#sin_gastos').hide();
		} else {
			$('#tbl_gastos').hide();
			$('#sin_gastos').show();
		}
		$('#tbl_gastos tbody').html(tbody);
	}

	function renderValidaciones(val) {
		// SUNAT
		var sunatOk = (val.sunat_total == 0) || (val.sunat_enviados == val.sunat_total);
		var sunatClass = sunatOk ? 'val-ok' : 'val-warn';
		var sunatIcon = sunatOk ? 'fa-check-circle' : 'fa-exclamation-triangle';
		$('#val_sunat').html(
			'<div class="validacion-item ' + sunatClass + '">' +
			'<i class="fas ' + sunatIcon + '"></i>' +
			'<div><div style="font-weight:600;">SUNAT</div><div style="font-size:11px;">' + val.sunat_enviados + ' / ' + val.sunat_total + ' enviados</div></div>' +
			'</div>'
		);

		// Anuladas
		var anulClass = val.anuladas == 0 ? 'val-ok' : 'val-warn';
		var anulIcon = val.anuladas == 0 ? 'fa-check-circle' : 'fa-exclamation-triangle';
		$('#val_anuladas').html(
			'<div class="validacion-item ' + anulClass + '">' +
			'<i class="fas ' + anulIcon + '"></i>' +
			'<div><div style="font-weight:600;">Anulaciones</div><div style="font-size:11px;">' + val.anuladas + ' ventas anuladas</div></div>' +
			'</div>'
		);

		// Stock negativo
		var stockClass = val.stock_negativo == 0 ? 'val-ok' : 'val-error';
		var stockIcon = val.stock_negativo == 0 ? 'fa-check-circle' : 'fa-times-circle';
		$('#val_stock').html(
			'<div class="validacion-item ' + stockClass + '">' +
			'<i class="fas ' + stockIcon + '"></i>' +
			'<div><div style="font-weight:600;">Stock</div><div style="font-size:11px;">' + val.stock_negativo + ' productos con stock negativo</div></div>' +
			'</div>'
		);

		// Caja
		var cajaHtml = '';
		var dif = parseFloat(val.diferencia_caja);
		if (dif == 0) {
			cajaHtml = '<div class="validacion-item val-ok"><i class="fas fa-check-circle"></i><div><div style="font-weight:600;">Caja</div><div style="font-size:11px;">Cuadre correcto</div></div></div>';
		} else if (Math.abs(dif) > 0 && Math.abs(dif) <= 5) {
			cajaHtml = '<div class="validacion-item val-warn"><i class="fas fa-exclamation-triangle"></i><div><div style="font-weight:600;">Caja</div><div style="font-size:11px;">Diferencia: ' + fmtMoney(dif) + '</div></div></div>';
		} else if (Math.abs(dif) > 5) {
			cajaHtml = '<div class="validacion-item val-error"><i class="fas fa-times-circle"></i><div><div style="font-weight:600;">Caja</div><div style="font-size:11px;">Diferencia: ' + fmtMoney(dif) + '</div></div></div>';
		} else {
			cajaHtml = '<div class="validacion-item val-info"><i class="fas fa-info-circle"></i><div><div style="font-weight:600;">Caja</div><div style="font-size:11px;">Sin cierre registrado</div></div></div>';
		}
		$('#val_caja').html(cajaHtml);
	}

	function activo1() {
		var fecha = document.getElementById("fecha").value;
		var store_id = document.getElementById("store_id").value;
		if (fecha.length == 0) fecha = 'null';
		if (store_id.length == 0) store_id = 'null';

		document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>reportes/cierre_diario/' + fecha + '/' + store_id + '" id="enlace_cierre_diario">Ejecutar</a>';
		document.getElementById('enlace_cierre_diario').click();
	}

	// ========================
	// GENERAR PDF
	// ========================
	function generarPDF() {
		if (!dataCierre) {
			alert('Los datos aun no se han cargado.');
			return;
		}

		var d = dataCierre;
		var empresa = d.empresa || d.tienda || '';
		var ruc = d.ruc || '';
		var direccion = d.direccion || '';
		var fechaFmt = d.fecha.split('-');
		fechaFmt = fechaFmt[2] + '/' + fechaFmt[1] + '/' + fechaFmt[0];

		var fm = function(n) { return 'S/. ' + parseFloat(n).toFixed(2); };

		// === 1. TABLA KPIs ===
		var kpiBody = [[
			{ text: 'Total Ventas', style: 'kpiLabel' },
			{ text: 'Ganancia Bruta', style: 'kpiLabel' },
			{ text: 'Total Gastos', style: 'kpiLabel' }
		],[
			{ text: fm(d.totales.total_ventas), style: 'kpiValue', color: '#4e73df' },
			{ text: fm(d.rentabilidad.ganancia_bruta), style: 'kpiValue', color: '#1cc88a' },
			{ text: fm(d.totales.total_gastos_dia), style: 'kpiValue', color: '#e74a3b' }
		],[
			{ text: d.totales.cantidad_ventas + ' ventas', fontSize: 7, color: '#888', alignment: 'center' },
			{ text: 'Sin IGV', fontSize: 7, color: '#888', alignment: 'center' },
			{ text: 'Op + Caja Chica', fontSize: 7, color: '#888', alignment: 'center' }
		]];

		// === 2. TABLA RESUMEN DE CAJAS ===
		var cajaContent = [];
		if (d.cajas && d.cajas.length > 0) {
			for (var ci = 0; ci < d.cajas.length; ci++) {
				var cj = d.cajas[ci];
				var cajaTitle = d.cajas.length > 1 ? 'CAJA #' + (ci + 1) : 'RESUMEN DE CAJA';
				cajaTitle += (cj.estado == 'CERRADA' ? ' (CERRADA)' : ' (ABIERTA)');
				var cajaBody = [
					[{ text: 'Fondo Inicial', fontSize: 9 }, { text: fm(cj.fondo_ini), alignment: 'right', fontSize: 9 }],
					[{ text: '(+) Ventas Efectivo', fontSize: 9 }, { text: fm(cj.ventas_efectivo), alignment: 'right', fontSize: 9 }],
					[{ text: '(+) Ingresos Manuales', fontSize: 9 }, { text: fm(cj.ingresos), alignment: 'right', fontSize: 9 }],
					[{ text: '(-) Egresos Manuales', fontSize: 9 }, { text: fm(cj.egresos), alignment: 'right', fontSize: 9 }],
					[{ text: 'Saldo Teorico', fontSize: 10, bold: true }, { text: fm(cj.saldo_teorico), alignment: 'right', fontSize: 10, bold: true }]
				];
				if (cj.estado == 'CERRADA') {
					cajaBody.push([{ text: 'Conteo Real', fontSize: 9 }, { text: fm(cj.monto_real), alignment: 'right', fontSize: 9 }]);
					var difCaja = parseFloat(cj.diferencia);
					var difTxt = difCaja > 0.009 ? 'SOBRANTE' : (difCaja < -0.009 ? 'FALTANTE' : 'EXACTO');
					var difCol = difCaja > 0.009 ? '#f6c23e' : (difCaja < -0.009 ? '#e74a3b' : '#1cc88a');
					cajaBody.push([{ text: 'Diferencia (' + difTxt + ')', fontSize: 9, color: difCol, bold: true }, { text: fm(difCaja), alignment: 'right', fontSize: 9, color: difCol, bold: true }]);
				}
				cajaContent.push({ text: cajaTitle, style: 'sectionTitle' });
				cajaContent.push({
					table: { headerRows: 0, widths: ['*', 100], body: cajaBody },
					layout: { hLineWidth: function(i, node) { return i === 5 ? 1.5 : 0.5; }, vLineWidth: function() { return 0; }, hLineColor: function() { return '#ddd'; } },
					margin: [0, 0, 0, 12]
				});
			}
		} else {
			cajaContent = [
				{ text: 'RESUMEN DE CAJA', style: 'sectionTitle' },
				{ text: 'Sin apertura de caja registrada este dia', fontSize: 9, color: '#888', italics: true, margin: [0, 0, 0, 12] }
			];
		}

		// === 3. TABLA VENTAS POR FORMA DE PAGO ===
		var fpBody = [[
			{ text: 'Forma de Pago', style: 'tableHeader' },
			{ text: 'Cant.', style: 'tableHeader', alignment: 'center' },
			{ text: 'Total', style: 'tableHeader', alignment: 'right' }
		]];
		var fpTotalC = 0, fpTotalM = 0;
		for (var i = 0; i < d.ventas_forma_pago.length; i++) {
			var fp = d.ventas_forma_pago[i];
			fpTotalC += parseInt(fp.cantidad);
			fpTotalM += parseFloat(fp.total);
			fpBody.push([
				{ text: fmtPago(fp.forma_pago), fontSize: 9 },
				{ text: fp.cantidad, alignment: 'center', fontSize: 9 },
				{ text: fm(fp.total), alignment: 'right', fontSize: 9 }
			]);
		}
		fpBody.push([
			{ text: 'TOTAL', bold: true, fontSize: 9 },
			{ text: fpTotalC.toString(), bold: true, alignment: 'center', fontSize: 9 },
			{ text: fm(fpTotalM), bold: true, alignment: 'right', fontSize: 9 }
		]);

		// === 4. TABLA VENTAS POR DOCUMENTO ===
		var docBody = [[
			{ text: 'Tipo', style: 'tableHeader' },
			{ text: 'Cant.', style: 'tableHeader', alignment: 'center' },
			{ text: 'Total', style: 'tableHeader', alignment: 'right' }
		]];
		var docTotalC = 0, docTotalM = 0;
		for (var i = 0; i < d.ventas_documento.length; i++) {
			var dc = d.ventas_documento[i];
			docTotalC += parseInt(dc.cantidad);
			docTotalM += parseFloat(dc.total);
			docBody.push([
				{ text: dc.tipo, fontSize: 9 },
				{ text: dc.cantidad, alignment: 'center', fontSize: 9 },
				{ text: fm(dc.total), alignment: 'right', fontSize: 9 }
			]);
		}
		docBody.push([
			{ text: 'TOTAL', bold: true, fontSize: 9 },
			{ text: docTotalC.toString(), bold: true, alignment: 'center', fontSize: 9 },
			{ text: fm(docTotalM), bold: true, alignment: 'right', fontSize: 9 }
		]);

		// === 5. TABLA RENTABILIDAD ===
		var rentBody = [
			[{ text: 'Ventas Netas (sin IGV)', fontSize: 9 }, { text: fm(d.rentabilidad.ventas_netas), alignment: 'right', fontSize: 9 }],
			[{ text: '(-) Costo de Mercaderia', fontSize: 9, color: '#e74a3b' }, { text: fm(d.rentabilidad.costos), alignment: 'right', fontSize: 9, color: '#e74a3b' }],
			[{ text: '= Ganancia Bruta', fontSize: 10, bold: true, color: '#1cc88a' }, { text: fm(d.rentabilidad.ganancia_bruta), alignment: 'right', fontSize: 10, bold: true, color: '#1cc88a' }]
		];

		// === 6. TABLA GASTOS ===
		var gastosContent = [];
		var hayGastos = (d.gastos.length > 0 || d.gastos_cajachica.length > 0);
		if (hayGastos) {
			var gastosBody = [[
				{ text: 'Origen', style: 'tableHeader' },
				{ text: 'Categoria', style: 'tableHeader' },
				{ text: 'Descripcion', style: 'tableHeader' },
				{ text: 'Monto', style: 'tableHeader', alignment: 'right' }
			]];
			var totalG = 0;
			for (var i = 0; i < d.gastos.length; i++) {
				var g = d.gastos[i];
				totalG += parseFloat(g.monto);
				gastosBody.push([
					{ text: 'Gasto', fontSize: 8, color: '#1565c0' },
					{ text: g.categoria || '-', fontSize: 8 },
					{ text: g.descripcion || '-', fontSize: 8 },
					{ text: fm(g.monto), alignment: 'right', fontSize: 8 }
				]);
			}
			for (var i = 0; i < d.gastos_cajachica.length; i++) {
				var gc = d.gastos_cajachica[i];
				totalG += parseFloat(gc.monto);
				gastosBody.push([
					{ text: 'Caja Chica', fontSize: 8, color: '#c62828' },
					{ text: gc.categoria || '-', fontSize: 8 },
					{ text: gc.descripcion || '-', fontSize: 8 },
					{ text: fm(gc.monto), alignment: 'right', fontSize: 8 }
				]);
			}
			gastosBody.push([
				{ text: 'TOTAL GASTOS', bold: true, colSpan: 3, fontSize: 9 }, {}, {},
				{ text: fm(totalG), bold: true, alignment: 'right', fontSize: 9 }
			]);
			gastosContent = [
				{ text: 'GASTOS DEL DIA', style: 'sectionTitle' },
				{ table: { headerRows: 1, widths: [55, 80, '*', 70], body: gastosBody }, layout: 'lightHorizontalLines', margin: [0, 0, 0, 12] }
			];
		} else {
			gastosContent = [
				{ text: 'GASTOS DEL DIA', style: 'sectionTitle' },
				{ text: 'Sin gastos registrados este dia', fontSize: 9, color: '#888', italics: true, margin: [0, 0, 0, 12] }
			];
		}

		// === 7. VALIDACIONES ===
		var val = d.validaciones;
		var sunatTxt = val.sunat_enviados + '/' + val.sunat_total + ' enviados';
		var sunatSt = (val.sunat_total == 0 || val.sunat_enviados == val.sunat_total) ? 'OK' : 'PENDIENTE';
		var difCajaVal = parseFloat(val.diferencia_caja);
		var cajaTxt = difCajaVal == 0 ? 'Cuadre correcto' : 'Diferencia: ' + fm(difCajaVal);

		var valBody = [[
			{ text: 'Control', style: 'tableHeader' },
			{ text: 'Estado', style: 'tableHeader', alignment: 'center' },
			{ text: 'Detalle', style: 'tableHeader' }
		],
		[{ text: 'SUNAT', fontSize: 9 }, { text: sunatSt, alignment: 'center', fontSize: 9, bold: true, color: sunatSt == 'OK' ? '#1cc88a' : '#e74a3b' }, { text: sunatTxt, fontSize: 9 }],
		[{ text: 'Anulaciones', fontSize: 9 }, { text: val.anuladas == 0 ? 'OK' : 'REVISAR', alignment: 'center', fontSize: 9, bold: true, color: val.anuladas == 0 ? '#1cc88a' : '#f6c23e' }, { text: val.anuladas + ' ventas anuladas', fontSize: 9 }],
		[{ text: 'Stock Negativo', fontSize: 9 }, { text: val.stock_negativo == 0 ? 'OK' : 'ERROR', alignment: 'center', fontSize: 9, bold: true, color: val.stock_negativo == 0 ? '#1cc88a' : '#e74a3b' }, { text: val.stock_negativo + ' productos', fontSize: 9 }],
		[{ text: 'Caja', fontSize: 9 }, { text: difCajaVal == 0 ? 'OK' : (Math.abs(difCajaVal) <= 5 ? 'REVISAR' : 'ERROR'), alignment: 'center', fontSize: 9, bold: true, color: difCajaVal == 0 ? '#1cc88a' : (Math.abs(difCajaVal) <= 5 ? '#f6c23e' : '#e74a3b') }, { text: cajaTxt, fontSize: 9 }]
		];

		// === DEFINICION DEL DOCUMENTO ===
		var docDefinition = {
			pageSize: 'A4',
			pageMargins: [40, 40, 40, 40],
			content: [
				// Header
				{
					columns: [
						{
							width: '*',
							stack: [
								{ text: empresa, fontSize: 16, bold: true },
								{ text: ruc ? 'RUC: ' + ruc : '', fontSize: 9, color: '#666', margin: [0, 2, 0, 0] },
								{ text: direccion, fontSize: 9, color: '#666' }
							]
						},
						{
							width: 'auto',
							alignment: 'right',
							stack: [
								{ text: 'CIERRE DIARIO', fontSize: 14, bold: true, color: '#4e73df' },
								{ text: fechaFmt, fontSize: 12, bold: true, margin: [0, 3, 0, 0] },
								{ text: 'Tienda: ' + (d.tienda || ''), fontSize: 9, color: '#666' },
								{ text: 'Generado: ' + new Date().toLocaleDateString('es-PE') + ' ' + new Date().toLocaleTimeString('es-PE', {hour:'2-digit',minute:'2-digit'}), fontSize: 7, color: '#999', margin: [0, 3, 0, 0] }
							]
						}
					]
				},
				{ canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 2, lineColor: '#4e73df' }], margin: [0, 5, 0, 12] },

				// KPIs
				{
					table: { widths: ['*', '*', '*'], body: kpiBody },
					layout: {
						fillColor: function(rowIndex) { return rowIndex === 0 ? '#f8f9fa' : null; },
						hLineWidth: function() { return 0.5; }, vLineWidth: function() { return 0.5; },
						hLineColor: function() { return '#e0e0e0'; }, vLineColor: function() { return '#e0e0e0'; },
						paddingTop: function() { return 5; }, paddingBottom: function() { return 5; }
					},
					margin: [0, 0, 0, 15]
				},

				// Formas de pago + Documentos (2 columnas)
				{
					columns: [
						{
							width: '50%',
							stack: [
								{ text: 'VENTAS POR FORMA DE PAGO', style: 'sectionTitle' },
								{ table: { headerRows: 1, widths: ['*', 40, 75], body: fpBody }, layout: 'lightHorizontalLines', margin: [0, 0, 0, 12] }
							]
						},
						{
							width: '50%',
							stack: [
								{ text: 'VENTAS POR DOCUMENTO', style: 'sectionTitle' },
								{ table: { headerRows: 1, widths: ['*', 40, 75], body: docBody }, layout: 'lightHorizontalLines', margin: [0, 0, 0, 12] }
							],
							margin: [10, 0, 0, 0]
						}
					]
				},

				// Rentabilidad
				{ text: 'RENTABILIDAD DEL DIA', style: 'sectionTitle' },
				{ table: { headerRows: 0, widths: ['*', 100], body: rentBody },
				  layout: { hLineWidth: function(i) { return (i === 3 || i === 5) ? 1.5 : 0.5; }, vLineWidth: function() { return 0; }, hLineColor: function(i) { return (i === 3 || i === 5) ? '#333' : '#e0e0e0'; } },
				  margin: [0, 0, 0, 12] },

				// Gastos
				gastosContent,

				// Validaciones
				{ text: 'VALIDACIONES DE CONTROL', style: 'sectionTitle' },
				{ table: { headerRows: 1, widths: [80, 55, '*'], body: valBody }, layout: 'lightHorizontalLines', margin: [0, 0, 0, 10] },

				// Resumen de Cajas (al final)
				cajaContent
			],
			styles: {
				sectionTitle: { fontSize: 11, bold: true, color: '#4e73df', margin: [0, 5, 0, 6], decoration: 'underline' },
				tableHeader: { fontSize: 9, bold: true, fillColor: '#e9ecef', color: '#333' },
				kpiLabel: { fontSize: 8, color: '#6c757d', alignment: 'center', bold: true },
				kpiValue: { fontSize: 14, bold: true, alignment: 'center', margin: [0, 3, 0, 0] }
			},
			footer: function(currentPage, pageCount) {
				return {
					columns: [
						{ text: empresa + ' - Cierre Diario ' + fechaFmt, fontSize: 7, color: '#aaa', margin: [40, 0, 0, 0] },
						{ text: 'Pagina ' + currentPage + ' de ' + pageCount, fontSize: 7, color: '#aaa', alignment: 'right', margin: [0, 0, 40, 0] }
					]
				};
			}
		};

		// Flatten arrays en content (gastosContent es un array)
		var flatContent = [];
		for (var ci = 0; ci < docDefinition.content.length; ci++) {
			var item = docDefinition.content[ci];
			if (Array.isArray(item)) {
				for (var cj = 0; cj < item.length; cj++) {
					flatContent.push(item[cj]);
				}
			} else {
				flatContent.push(item);
			}
		}
		docDefinition.content = flatContent;

		var fileName = 'CierreDiario_' + d.fecha + '_' + (d.tienda || '').replace(/\s/g, '_') + '.pdf';
		pdfMake.createPdf(docDefinition).download(fileName);
	}

</script>
