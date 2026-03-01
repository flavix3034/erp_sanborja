<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
	.dash-card {
		border: none;
		border-radius: 10px;
		box-shadow: 0 2px 12px rgba(0,0,0,0.08);
		transition: transform 0.2s, box-shadow 0.2s;
		overflow: hidden;
	}
	.dash-card:hover {
		transform: translateY(-3px);
		box-shadow: 0 6px 20px rgba(0,0,0,0.12);
	}
	.dash-card .card-body {
		padding: 20px;
	}
	.kpi-icon {
		width: 52px;
		height: 52px;
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 26px;
		color: #fff;
	}
	.kpi-value {
		font-size: 24px;
		font-weight: 700;
		color: #333;
		line-height: 1.1;
	}
	.kpi-label {
		font-size: 12px;
		color: #888;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-top: 2px;
	}
	.section-title {
		font-size: 14px;
		font-weight: 700;
		color: #555;
		text-transform: uppercase;
		letter-spacing: 0.8px;
		margin-bottom: 15px;
		padding-bottom: 8px;
		border-bottom: 2px solid #f0f0f0;
	}
	.mini-stat {
		text-align: center;
		padding: 10px 5px;
		border-radius: 8px;
		background: #f8f9fa;
		transition: background 0.2s;
	}
	.mini-stat:hover {
		background: #e9ecef;
	}
	.mini-stat .stat-count {
		font-size: 22px;
		font-weight: 700;
		display: block;
		line-height: 1.2;
	}
	.mini-stat .stat-label {
		font-size: 10px;
		color: #666;
		text-transform: uppercase;
		letter-spacing: 0.3px;
		display: block;
		margin-top: 3px;
	}
	.alert-item {
		display: flex;
		align-items: center;
		padding: 10px 14px;
		border-radius: 8px;
		margin-bottom: 8px;
		background: #f8f9fa;
		transition: background 0.2s;
	}
	.alert-item:hover {
		background: #e9ecef;
	}
	.alert-item i {
		font-size: 20px;
		margin-right: 12px;
		width: 24px;
		text-align: center;
	}
	.alert-item .alert-label {
		flex: 1;
		font-size: 13px;
		color: #444;
	}
	.alert-badge {
		font-size: 13px;
		font-weight: 700;
		min-width: 28px;
		height: 28px;
		line-height: 28px;
		text-align: center;
		border-radius: 50%;
		color: #fff;
	}
	.badge-ok { background: #28a745; }
	.badge-warn { background: #ffc107; color: #333; }
	.badge-danger { background: #dc3545; }
	.chart-container {
		position: relative;
		width: 100%;
	}
	.rendimiento-card {
		text-align: center;
		padding: 18px 10px;
	}
	.rendimiento-card .rend-value {
		font-size: 22px;
		font-weight: 700;
		color: #333;
	}
	.rendimiento-card .rend-label {
		font-size: 11px;
		color: #888;
		text-transform: uppercase;
		margin-top: 4px;
	}
	.loading-overlay {
		text-align: center;
		padding: 60px 20px;
		color: #aaa;
	}
	.loading-overlay i {
		font-size: 40px;
		animation: spin 1s linear infinite;
	}
	@keyframes spin {
		from { transform: rotate(0deg); }
		to { transform: rotate(360deg); }
	}
</style>

<section class="content" style="padding: 20px;">

	<!-- Loading -->
	<div id="dash_loading" class="loading-overlay">
		<i class='bx bx-loader-alt'></i>
		<p style="margin-top:12px; font-size:14px;">Cargando dashboard...</p>
	</div>

	<!-- Dashboard content (hidden until loaded) -->
	<div id="dash_content" style="display:none;">

		<!-- ===== ROW 1: KPIs Estado General ===== -->
		<div class="section-title"><i class='bx bx-bar-chart' style="font-size:16px;vertical-align:middle;"></i> Estado General del Negocio</div>
		<div class="row" style="margin-bottom:25px;">
			<div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
				<div class="card dash-card">
					<div class="card-body" style="display:flex;align-items:center;">
						<div class="kpi-icon" style="background:linear-gradient(135deg,#4e73df,#224abe);">
							<i class='bx bx-cart'></i>
						</div>
						<div style="margin-left:15px;flex:1;">
							<div class="kpi-value" id="kpi_ventas_dia">--</div>
							<div class="kpi-label">Ventas del D&iacute;a</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
				<div class="card dash-card">
					<div class="card-body" style="display:flex;align-items:center;">
						<div class="kpi-icon" style="background:linear-gradient(135deg,#1cc88a,#13855c);">
							<i class='bx bx-trending-up'></i>
						</div>
						<div style="margin-left:15px;flex:1;">
							<div class="kpi-value" id="kpi_ganancias_dia">--</div>
							<div class="kpi-label">Ganancias del D&iacute;a</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
				<div class="card dash-card">
					<div class="card-body" style="display:flex;align-items:center;">
						<div class="kpi-icon" style="background:linear-gradient(135deg,#f6c23e,#dda20a);">
							<i class='bx bx-wallet'></i>
						</div>
						<div style="margin-left:15px;flex:1;">
							<div class="kpi-value" id="kpi_caja">--</div>
							<div class="kpi-label" id="kpi_caja_label">Caja Chica</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6" style="margin-bottom:12px;">
				<div class="card dash-card">
					<div class="card-body" style="display:flex;align-items:center;">
						<div class="kpi-icon" style="background:linear-gradient(135deg,#e74a3b,#be2617);">
							<i class='bx bx-wrench'></i>
						</div>
						<div style="margin-left:15px;flex:1;">
							<div class="kpi-value" id="kpi_servicios">--</div>
							<div class="kpi-label">Servicios Activos</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ===== ROW 2: Servicios + Grafico Ventas ===== -->
		<div class="row" style="margin-bottom:25px;">
			<!-- Servicio Tecnico -->
			<div class="col-md-5" style="margin-bottom:12px;">
				<div class="card dash-card" style="height:100%;">
					<div class="card-body">
						<div class="section-title" style="margin-bottom:12px;"><i class='bx bx-wrench' style="font-size:16px;vertical-align:middle;"></i> Servicio T&eacute;cnico</div>
						<div class="row" style="margin-bottom:15px;">
							<div class="col" style="padding:0 4px;">
								<div class="mini-stat">
									<span class="stat-count" style="color:#6c757d;" id="st_recibido">0</span>
									<span class="stat-label">Recibidos</span>
								</div>
							</div>
							<div class="col" style="padding:0 4px;">
								<div class="mini-stat">
									<span class="stat-count" style="color:#17a2b8;" id="st_diagnostico">0</span>
									<span class="stat-label">Diagn&oacute;stico</span>
								</div>
							</div>
							<div class="col" style="padding:0 4px;">
								<div class="mini-stat">
									<span class="stat-count" style="color:#ffc107;" id="st_reparacion">0</span>
									<span class="stat-label">Reparaci&oacute;n</span>
								</div>
							</div>
							<div class="col" style="padding:0 4px;">
								<div class="mini-stat">
									<span class="stat-count" style="color:#fd7e14;" id="st_espera">0</span>
									<span class="stat-label">Espera Rep.</span>
								</div>
							</div>
							<div class="col" style="padding:0 4px;">
								<div class="mini-stat">
									<span class="stat-count" style="color:#28a745;" id="st_reparado">0</span>
									<span class="stat-label">Reparados</span>
								</div>
							</div>
						</div>
						<div class="chart-container" style="height:220px;">
							<canvas id="chartServicios"></canvas>
						</div>
					</div>
				</div>
			</div>

			<!-- Grafico Ventas 15 dias -->
			<div class="col-md-7" style="margin-bottom:12px;">
				<div class="card dash-card" style="height:100%;">
					<div class="card-body">
						<div class="section-title" style="margin-bottom:12px;"><i class='bx bx-line-chart' style="font-size:16px;vertical-align:middle;"></i> Ventas &Uacute;ltimos 15 D&iacute;as</div>
						<div class="chart-container" style="height:280px;">
							<canvas id="chartVentas15d"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ===== ROW 3: Rendimiento + Control ===== -->
		<div class="row" style="margin-bottom:25px;">
			<!-- Rendimiento del Mes -->
			<div class="col-md-5" style="margin-bottom:12px;">
				<div class="card dash-card" style="height:100%;">
					<div class="card-body">
						<div class="section-title" style="margin-bottom:12px;"><i class='bx bx-rocket' style="font-size:16px;vertical-align:middle;"></i> Rendimiento del Mes</div>
						<div class="row">
							<div class="col-4">
								<div class="rendimiento-card">
									<div class="rend-value" id="rend_ingresos">--</div>
									<div class="rend-label">Ingresos</div>
								</div>
							</div>
							<div class="col-4">
								<div class="rendimiento-card">
									<div class="rend-value" id="rend_ticket">--</div>
									<div class="rend-label">Ticket Prom.</div>
								</div>
							</div>
							<div class="col-4">
								<div class="rendimiento-card">
									<div class="rend-value" id="rend_margen">--</div>
									<div class="rend-label">Margen Prom.</div>
								</div>
							</div>
						</div>
						<hr style="margin:12px 0;">
						<div style="text-align:center;color:#999;font-size:11px;">
							<span id="rend_num_ventas">0</span> ventas en el mes actual
						</div>
					</div>
				</div>
			</div>

			<!-- Control del Sistema -->
			<div class="col-md-7" style="margin-bottom:12px;">
				<div class="card dash-card" style="height:100%;">
					<div class="card-body">
						<div class="section-title" style="margin-bottom:12px;"><i class='bx bx-shield-quarter' style="font-size:16px;vertical-align:middle;"></i> Control del Sistema</div>

						<div class="alert-item">
							<i class='bx bx-task' style="color:#6c757d;"></i>
							<span class="alert-label">&Oacute;rdenes incompletas</span>
							<span class="alert-badge" id="ctrl_incompletas">0</span>
						</div>
						<div class="alert-item">
							<i class='bx bx-user-x' style="color:#e74a3b;"></i>
							<span class="alert-label">Sin t&eacute;cnico asignado</span>
							<span class="alert-badge" id="ctrl_sin_tecnico">0</span>
						</div>
						<div class="alert-item">
							<i class='bx bx-search-alt' style="color:#f6c23e;"></i>
							<span class="alert-label">Sin diagn&oacute;stico</span>
							<span class="alert-badge" id="ctrl_sin_diagnostico">0</span>
						</div>
						<div class="alert-item">
							<i class='bx bx-money' style="color:#1cc88a;"></i>
							<span class="alert-label">Sin cierre financiero</span>
							<span class="alert-badge" id="ctrl_sin_cierre">0</span>
						</div>
						<div class="alert-item" style="margin-bottom:0;">
							<i class='bx bx-time-five' style="color:#fd7e14;"></i>
							<span class="alert-label">&Oacute;rdenes estancadas (&gt;7 d&iacute;as)</span>
							<span class="alert-badge" id="ctrl_estancadas">0</span>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</section>

<script>
/* Chart.js v2.x API */
var chartServicios = null;
var chartVentas = null;

$(document).ready(function(){
	loadDashboard();
});

function loadDashboard(){
	$.getJSON('<?= base_url("welcome/get_dashboard_data") ?>', function(data){
		$('#dash_loading').hide();
		$('#dash_content').show();
		renderGeneral(data.general);
		renderServicios(data.servicios);
		renderRendimiento(data.rendimiento);
		renderControl(data.control);
		renderChartVentas(data.chart_ventas_15d);
	}).fail(function(){
		$('#dash_loading').html('<p style="color:#dc3545;">Error al cargar el dashboard.</p>');
	});
}

function fmtMoney(val){
	if(val === null || val === undefined) return '--';
	var n = parseFloat(val);
	return 'S/ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function renderGeneral(g){
	$('#kpi_ventas_dia').text(fmtMoney(g.ventas_dia));
	$('#kpi_ganancias_dia').text(fmtMoney(g.ganancias_dia));

	if(g.caja_saldo !== null){
		$('#kpi_caja').text(fmtMoney(g.caja_saldo));
		$('#kpi_caja_label').text('Caja Chica (Abierta)');
	} else {
		$('#kpi_caja').text('--');
		$('#kpi_caja_label').text('Caja Chica (Cerrada)');
	}

	$('#kpi_servicios').text(g.servicios_activos);
}

function renderServicios(s){
	$('#st_recibido').text(s['RECIBIDO'] || 0);
	$('#st_diagnostico').text(s['EN DIAGNOSTICO'] || 0);
	$('#st_reparacion').text(s['EN REPARACION'] || 0);
	$('#st_espera').text(s['ESPERA REPUESTOS'] || 0);
	$('#st_reparado').text(s['REPARADO'] || 0);

	var labels = ['Recibidos', 'Diagn\u00f3stico', 'Reparaci\u00f3n', 'Espera Rep.', 'Reparados'];
	var valores = [
		s['RECIBIDO'] || 0,
		s['EN DIAGNOSTICO'] || 0,
		s['EN REPARACION'] || 0,
		s['ESPERA REPUESTOS'] || 0,
		s['REPARADO'] || 0
	];
	var colores = ['#6c757d', '#17a2b8', '#ffc107', '#fd7e14', '#28a745'];

	var totalSrv = valores.reduce(function(a,b){ return a+b; }, 0);

	var ctx = document.getElementById('chartServicios').getContext('2d');
	if(chartServicios) chartServicios.destroy();

	if(totalSrv === 0){
		chartServicios = new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ['Sin servicios activos'],
				datasets: [{
					data: [1],
					backgroundColor: ['#e9ecef'],
					borderWidth: 0
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				cutoutPercentage: 65,
				legend: { display: false },
				tooltips: { enabled: false }
			}
		});
		return;
	}

	chartServicios = new Chart(ctx, {
		type: 'doughnut',
		data: {
			labels: labels,
			datasets: [{
				data: valores,
				backgroundColor: colores,
				borderWidth: 2,
				borderColor: '#fff'
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			cutoutPercentage: 65,
			legend: {
				position: 'bottom',
				labels: {
					boxWidth: 12,
					padding: 10,
					fontSize: 11
				}
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data){
						var label = data.labels[tooltipItem.index] || '';
						var value = data.datasets[0].data[tooltipItem.index];
						return label + ': ' + value;
					}
				}
			}
		}
	});
}

function renderRendimiento(r){
	$('#rend_ingresos').text(fmtMoney(r.ingresos_mes));
	$('#rend_ticket').text(fmtMoney(r.ticket_promedio));
	$('#rend_margen').text(r.margen_promedio + '%');
	$('#rend_num_ventas').text(r.num_ventas_mes);
}

function renderControl(c){
	setAlertBadge('#ctrl_incompletas', c.incompletas);
	setAlertBadge('#ctrl_sin_tecnico', c.sin_tecnico);
	setAlertBadge('#ctrl_sin_diagnostico', c.sin_diagnostico);
	setAlertBadge('#ctrl_sin_cierre', c.sin_cierre);
	setAlertBadge('#ctrl_estancadas', c.estancadas);
}

function setAlertBadge(selector, val){
	var $el = $(selector);
	$el.text(val);
	$el.removeClass('badge-ok badge-warn badge-danger');
	if(val == 0){
		$el.addClass('badge-ok');
	} else if(val <= 2){
		$el.addClass('badge-warn');
	} else {
		$el.addClass('badge-danger');
	}
}

function renderChartVentas(chartData){
	var labels = [];
	var valores = [];

	for(var i=0; i<chartData.length; i++){
		var parts = chartData[i].dia.split('-');
		labels.push(parts[2] + '/' + parts[1]);
		valores.push(parseFloat(chartData[i].total));
	}

	var ctx = document.getElementById('chartVentas15d').getContext('2d');
	if(chartVentas) chartVentas.destroy();

	chartVentas = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [{
				label: 'Ventas (S/)',
				data: valores,
				backgroundColor: 'rgba(78,115,223,0.7)',
				borderColor: 'rgba(78,115,223,1)',
				borderWidth: 1
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			legend: { display: false },
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data){
						return 'S/ ' + parseFloat(tooltipItem.yLabel).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
					}
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						callback: function(value){
							return 'S/ ' + value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
						}
					},
					gridLines: { color: 'rgba(0,0,0,0.05)' }
				}],
				xAxes: [{
					ticks: { fontSize: 11 },
					gridLines: { display: false }
				}]
			}
		}
	});
}
</script>
