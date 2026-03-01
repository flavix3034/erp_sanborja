<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Reporte Caja Chica - Periodo #<?= $periodo->id ?></title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #333; padding: 20px; background: #f5f5f5; }

		.reporte-container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

		/* Header */
		.header-reporte { border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
		.header-reporte h1 { font-size: 20px; margin-bottom: 3px; }
		.header-reporte h2 { font-size: 14px; font-weight: normal; color: #666; }
		.empresa-info { font-size: 11px; color: #555; margin-top: 5px; }

		/* Info periodo */
		.info-periodo { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 12px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; }
		.info-periodo .col { flex: 1; }
		.info-periodo label { font-weight: bold; font-size: 10px; color: #888; text-transform: uppercase; display: block; margin-bottom: 2px; }
		.info-periodo span { font-size: 13px; font-weight: bold; }

		/* Resumen */
		.seccion-titulo { font-size: 14px; font-weight: bold; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 2px solid #337ab7; color: #337ab7; }

		table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
		table th { background: #f0f0f0; font-size: 11px; text-transform: uppercase; padding: 6px 8px; border: 1px solid #ddd; text-align: left; }
		table td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; }
		table .text-right { text-align: right; }
		table .text-center { text-align: center; }
		table tfoot td { font-weight: bold; background: #f7f7f7; }

		.badge-cat { display: inline-block; padding: 2px 8px; border-radius: 3px; color: #fff; font-size: 10px; font-weight: bold; }

		/* Totales */
		.resumen-totales { display: flex; justify-content: flex-end; margin-bottom: 20px; }
		.resumen-totales table { width: 300px; }
		.resumen-totales td { font-size: 12px; padding: 6px 10px; }

		/* Firmas */
		.firmas { display: flex; justify-content: space-between; margin-top: 50px; padding-top: 10px; }
		.firma-box { text-align: center; width: 40%; }
		.firma-linea { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; font-size: 11px; }
		.firma-cargo { font-size: 10px; color: #666; }

		/* Botones (no se imprimen) */
		.acciones-reporte { text-align: center; margin-bottom: 20px; }
		.acciones-reporte button { padding: 10px 24px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; margin: 0 5px; }
		.btn-pdf { background: #dc3545; color: #fff; }
		.btn-pdf:hover { background: #c82333; }
		.btn-imprimir { background: #337ab7; color: #fff; }
		.btn-imprimir:hover { background: #286090; }
		.btn-volver { background: #6c757d; color: #fff; }
		.btn-volver:hover { background: #5a6268; }

		@media print {
			body { background: #fff; padding: 0; }
			.reporte-container { box-shadow: none; padding: 10px; }
			.acciones-reporte { display: none !important; }
			.firmas { page-break-inside: avoid; }
		}
	</style>
</head>
<body>

<?php
	$monto_ini = floatval($periodo->monto_inicial);
	$saldo_final = floatval($periodo->saldo_actual);
	$total_gastado = $monto_ini - $saldo_final;
	$nombre_empresa = isset($store->nombre_empresa) ? $store->nombre_empresa : '';
	$direccion = isset($store->address1) ? $store->address1 : '';
	$ruc = isset($store->ruc) ? $store->ruc : '';
	$nombre_comercial = isset($store->name) ? $store->name : '';
?>

<!-- Botones -->
<div class="acciones-reporte">
	<button class="btn-pdf" onclick="generarPDF()"><i class="fa fa-file-pdf-o"></i> Descargar PDF</button>
	<button class="btn-imprimir" onclick="window.print()">Imprimir</button>
	<button class="btn-volver" onclick="window.close()">Cerrar</button>
</div>

<div class="reporte-container" id="reporte">

	<!-- Header -->
	<div class="header-reporte">
		<div style="display:flex;justify-content:space-between;align-items:flex-start;">
			<div>
				<h1><?= $nombre_empresa ?></h1>
				<div class="empresa-info">
					<?php if($ruc): ?>RUC: <?= $ruc ?><br><?php endif; ?>
					<?php if($direccion): ?><?= $direccion ?><?php endif; ?>
				</div>
			</div>
			<div style="text-align:right;">
				<h2>REPORTE DE CAJA CHICA</h2>
				<span style="font-size:13px;font-weight:bold;">Periodo #<?= $periodo->id ?></span><br>
				<span style="font-size:10px;color:#888;">Generado: <?= date('d/m/Y H:i') ?></span>
			</div>
		</div>
	</div>

	<!-- Info del Periodo -->
	<div class="info-periodo">
		<div class="col">
			<label>Fecha Apertura</label>
			<span><?= date('d/m/Y H:i', strtotime($periodo->fecha_apertura)) ?></span>
		</div>
		<div class="col">
			<label>Fecha Cierre</label>
			<span><?= $periodo->fecha_cierre ? date('d/m/Y H:i', strtotime($periodo->fecha_cierre)) : 'Abierto' ?></span>
		</div>
		<div class="col">
			<label>Responsable</label>
			<span><?= $periodo->usuario_apertura_nombre ?></span>
		</div>
		<div class="col">
			<label>Tienda</label>
			<span><?= $nombre_comercial ?></span>
		</div>
	</div>

	<!-- Resumen Financiero -->
	<div class="resumen-totales">
		<table>
			<tr>
				<td>Fondo Inicial:</td>
				<td class="text-right">S/. <?= number_format($monto_ini, 2) ?></td>
			</tr>
			<tr style="color:#dc3545;">
				<td>Total Gastado:</td>
				<td class="text-right">S/. <?= number_format($total_gastado, 2) ?></td>
			</tr>
			<tr style="border-top:2px solid #333;font-size:14px;">
				<td><strong>Saldo Teórico:</strong></td>
				<td class="text-right"><strong>S/. <?= number_format($saldo_final, 2) ?></strong></td>
			</tr>
		</table>
	</div>

	<!-- Resumen por Categoría -->
	<div class="seccion-titulo">Resumen por Categoría</div>
	<table>
		<thead>
			<tr>
				<th>Categoría</th>
				<th class="text-center" style="width:80px;">Nro. Gastos</th>
				<th class="text-right" style="width:110px;">Total</th>
				<th class="text-right" style="width:70px;">%</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($resumen as $r):
				$pct = $total_gastado > 0 ? (floatval($r['total']) / $total_gastado) * 100 : 0;
			?>
			<tr>
				<td>
					<span class="badge-cat" style="background-color:<?= $r['color'] ?>;"><?= $r['nombre'] ?></span>
				</td>
				<td class="text-center"><?= $r['num_gastos'] ?></td>
				<td class="text-right">S/. <?= number_format($r['total'], 2) ?></td>
				<td class="text-right"><?= number_format($pct, 1) ?>%</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td>TOTAL</td>
				<td></td>
				<td class="text-right">S/. <?= number_format($total_gastado, 2) ?></td>
				<td class="text-right">100%</td>
			</tr>
		</tfoot>
	</table>

	<!-- Detalle de Gastos -->
	<div class="seccion-titulo">Detalle de Gastos</div>
	<table>
		<thead>
			<tr>
				<th style="width:30px;">#</th>
				<th style="width:110px;">Fecha</th>
				<th>Categoría</th>
				<th>Descripción</th>
				<th>Beneficiario</th>
				<th class="text-right" style="width:90px;">Monto</th>
				<th class="text-center" style="width:50px;">Comp.</th>
			</tr>
		</thead>
		<tbody>
			<?php $n = 0; foreach($gastos as $g): $n++; ?>
			<tr>
				<td class="text-center"><?= $n ?></td>
				<td><?= date('d/m/Y H:i', strtotime($g['fecha_gasto'])) ?></td>
				<td>
					<span class="badge-cat" style="background-color:<?= $g['categoria_color'] ?>;"><?= $g['categoria'] ?></span>
				</td>
				<td><?= $g['descripcion'] ?></td>
				<td><?= $g['beneficiario'] ?></td>
				<td class="text-right">S/. <?= number_format($g['monto'], 2) ?></td>
				<td class="text-center"><?= !empty($g['comprobante']) ? 'Si' : '-' ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" class="text-right">TOTAL GASTOS:</td>
				<td class="text-right">S/. <?= number_format($total_gastado, 2) ?></td>
				<td></td>
			</tr>
		</tfoot>
	</table>

	<!-- Vales Provisionales -->
	<?php if(!empty($vales)): ?>
	<div class="seccion-titulo">Vales Provisionales</div>
	<table>
		<thead>
			<tr>
				<th style="width:30px;">#</th>
				<th style="width:110px;">Fecha</th>
				<th>Beneficiario</th>
				<th>Motivo</th>
				<th class="text-right" style="width:90px;">Monto</th>
				<th class="text-center" style="width:80px;">Estado</th>
				<th class="text-right" style="width:80px;">Gastado</th>
				<th class="text-right" style="width:80px;">Devuelto</th>
			</tr>
		</thead>
		<tbody>
			<?php $nv = 0; foreach($vales as $v): $nv++;
				$ec = array('PENDIENTE'=>'#ff8c00','LIQUIDADO'=>'#5cb85c','ANULADO'=>'#d9534f');
			?>
			<tr>
				<td class="text-center"><?= $nv ?></td>
				<td><?= date('d/m/Y H:i', strtotime($v['fecha_entrega'])) ?></td>
				<td><?= htmlspecialchars($v['beneficiario']) ?></td>
				<td><?= htmlspecialchars($v['motivo']) ?></td>
				<td class="text-right">S/. <?= number_format($v['monto'], 2) ?></td>
				<td class="text-center">
					<span class="badge-cat" style="background-color:<?= $ec[$v['estado']] ?>;"><?= $v['estado'] ?></span>
				</td>
				<td class="text-right">S/. <?= number_format($v['monto_gastado'], 2) ?></td>
				<td class="text-right">S/. <?= number_format($v['monto_devuelto'], 2) ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

	<?php if(!empty($periodo->observaciones)): ?>
	<div style="margin-top:15px;padding:10px;background:#f9f9f9;border:1px solid #e0e0e0;border-radius:4px;">
		<strong>Observaciones:</strong> <?= $periodo->observaciones ?>
	</div>
	<?php endif; ?>

	<!-- Firmas -->
	<div class="firmas">
		<div class="firma-box">
			<div class="firma-linea">Responsable de Caja Chica</div>
			<div class="firma-cargo"><?= $periodo->usuario_apertura_nombre ?></div>
		</div>
		<div class="firma-box">
			<div class="firma-linea">Aprobado por</div>
			<div class="firma-cargo">Encargado de Reposición</div>
		</div>
	</div>

</div><!-- /reporte-container -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
function generarPDF() {
	// Datos del reporte inyectados desde PHP
	var empresa = <?= json_encode($nombre_empresa) ?>;
	var ruc_emp = <?= json_encode($ruc) ?>;
	var direccion_emp = <?= json_encode($direccion) ?>;
	var periodo_id = <?= $periodo->id ?>;
	var fecha_apertura = <?= json_encode(date('d/m/Y H:i', strtotime($periodo->fecha_apertura))) ?>;
	var fecha_cierre = <?= json_encode($periodo->fecha_cierre ? date('d/m/Y H:i', strtotime($periodo->fecha_cierre)) : 'Abierto') ?>;
	var responsable = <?= json_encode($periodo->usuario_apertura_nombre) ?>;
	var tienda = <?= json_encode($nombre_comercial) ?>;
	var monto_ini = <?= $monto_ini ?>;
	var total_gast = <?= $total_gastado ?>;
	var saldo_fin = <?= $saldo_final ?>;

	// Resumen por categoría
	var resumen = <?= json_encode($resumen) ?>;
	// Detalle de gastos
	var gastos = <?= json_encode($gastos) ?>;
	// Vales provisionales
	var vales = <?= json_encode(isset($vales) ? $vales : array()) ?>;

	// === Construir tabla resumen ===
	var resumenBody = [
		[
			{ text: 'Categoría', style: 'tableHeader' },
			{ text: 'Nro. Gastos', style: 'tableHeader', alignment: 'center' },
			{ text: 'Total', style: 'tableHeader', alignment: 'right' },
			{ text: '%', style: 'tableHeader', alignment: 'right' }
		]
	];

	for (var i = 0; i < resumen.length; i++) {
		var r = resumen[i];
		var pct = total_gast > 0 ? (parseFloat(r.total) / total_gast * 100) : 0;
		resumenBody.push([
			{ text: r.nombre, fontSize: 9 },
			{ text: r.num_gastos, alignment: 'center', fontSize: 9 },
			{ text: 'S/. ' + parseFloat(r.total).toFixed(2), alignment: 'right', fontSize: 9 },
			{ text: pct.toFixed(1) + '%', alignment: 'right', fontSize: 9 }
		]);
	}
	resumenBody.push([
		{ text: 'TOTAL', bold: true, fontSize: 9 },
		{ text: '', fontSize: 9 },
		{ text: 'S/. ' + total_gast.toFixed(2), bold: true, alignment: 'right', fontSize: 9 },
		{ text: '100%', bold: true, alignment: 'right', fontSize: 9 }
	]);

	// === Construir tabla detalle ===
	var detalleBody = [
		[
			{ text: '#', style: 'tableHeader', alignment: 'center' },
			{ text: 'Fecha', style: 'tableHeader' },
			{ text: 'Categoría', style: 'tableHeader' },
			{ text: 'Descripción', style: 'tableHeader' },
			{ text: 'Beneficiario', style: 'tableHeader' },
			{ text: 'Monto', style: 'tableHeader', alignment: 'right' },
			{ text: 'Comp.', style: 'tableHeader', alignment: 'center' }
		]
	];

	for (var j = 0; j < gastos.length; j++) {
		var g = gastos[j];
		var fecha_g = g.fecha_gasto.substring(0, 10).split('-');
		var fecha_fmt = fecha_g[2] + '/' + fecha_g[1] + '/' + fecha_g[0];
		detalleBody.push([
			{ text: (j + 1).toString(), alignment: 'center', fontSize: 8 },
			{ text: fecha_fmt, fontSize: 8 },
			{ text: g.categoria, fontSize: 8 },
			{ text: g.descripcion, fontSize: 8 },
			{ text: g.beneficiario || '', fontSize: 8 },
			{ text: 'S/. ' + parseFloat(g.monto).toFixed(2), alignment: 'right', fontSize: 8 },
			{ text: g.comprobante ? 'Si' : '-', alignment: 'center', fontSize: 8 }
		]);
	}
	detalleBody.push([
		{ text: '', fontSize: 8 },
		{ text: '', fontSize: 8 },
		{ text: '', fontSize: 8 },
		{ text: '', fontSize: 8 },
		{ text: 'TOTAL:', bold: true, alignment: 'right', fontSize: 9 },
		{ text: 'S/. ' + total_gast.toFixed(2), bold: true, alignment: 'right', fontSize: 9 },
		{ text: '', fontSize: 8 }
	]);

	// === Construir tabla vales ===
	var valesContent = [];
	if (vales.length > 0) {
		var valesBody = [
			[
				{ text: '#', style: 'tableHeader', alignment: 'center' },
				{ text: 'Fecha', style: 'tableHeader' },
				{ text: 'Beneficiario', style: 'tableHeader' },
				{ text: 'Motivo', style: 'tableHeader' },
				{ text: 'Monto', style: 'tableHeader', alignment: 'right' },
				{ text: 'Estado', style: 'tableHeader', alignment: 'center' },
				{ text: 'Gastado', style: 'tableHeader', alignment: 'right' },
				{ text: 'Devuelto', style: 'tableHeader', alignment: 'right' }
			]
		];
		for (var k = 0; k < vales.length; k++) {
			var vl = vales[k];
			var fv = vl.fecha_entrega.substring(0, 10).split('-');
			valesBody.push([
				{ text: (k + 1).toString(), alignment: 'center', fontSize: 8 },
				{ text: fv[2] + '/' + fv[1] + '/' + fv[0], fontSize: 8 },
				{ text: vl.beneficiario || '', fontSize: 8 },
				{ text: vl.motivo || '', fontSize: 8 },
				{ text: 'S/. ' + parseFloat(vl.monto).toFixed(2), alignment: 'right', fontSize: 8 },
				{ text: vl.estado, alignment: 'center', fontSize: 8 },
				{ text: 'S/. ' + parseFloat(vl.monto_gastado || 0).toFixed(2), alignment: 'right', fontSize: 8 },
				{ text: 'S/. ' + parseFloat(vl.monto_devuelto || 0).toFixed(2), alignment: 'right', fontSize: 8 }
			]);
		}
		valesContent = [
			{ text: 'VALES PROVISIONALES', style: 'sectionTitle' },
			{
				table: {
					headerRows: 1,
					widths: [20, 55, 70, '*', 55, 55, 55, 55],
					body: valesBody
				},
				layout: 'lightHorizontalLines',
				margin: [0, 0, 0, 10]
			}
		];
	}

	// === Definición del documento ===
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
							{ text: (ruc_emp ? 'RUC: ' + ruc_emp : ''), fontSize: 9, color: '#666', margin: [0, 2, 0, 0] },
							{ text: direccion_emp || '', fontSize: 9, color: '#666' }
						]
					},
					{
						width: 'auto',
						alignment: 'right',
						stack: [
							{ text: 'REPORTE DE CAJA CHICA', fontSize: 13, bold: true, color: '#337ab7' },
							{ text: 'Periodo #' + periodo_id, fontSize: 11, bold: true, margin: [0, 3, 0, 0] },
							{ text: 'Generado: ' + new Date().toLocaleDateString('es-PE') + ' ' + new Date().toLocaleTimeString('es-PE', {hour:'2-digit',minute:'2-digit'}), fontSize: 8, color: '#999' }
						]
					}
				]
			},
			{ canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 2, lineColor: '#333' }], margin: [0, 5, 0, 10] },

			// Info del periodo
			{
				table: {
					widths: ['*', '*', '*', '*'],
					body: [[
						{ stack: [{ text: 'FECHA APERTURA', fontSize: 7, color: '#888', bold: true }, { text: fecha_apertura, fontSize: 10, bold: true }], border: [false, false, false, false] },
						{ stack: [{ text: 'FECHA CIERRE', fontSize: 7, color: '#888', bold: true }, { text: fecha_cierre, fontSize: 10, bold: true }], border: [false, false, false, false] },
						{ stack: [{ text: 'RESPONSABLE', fontSize: 7, color: '#888', bold: true }, { text: responsable, fontSize: 10, bold: true }], border: [false, false, false, false] },
						{ stack: [{ text: 'TIENDA', fontSize: 7, color: '#888', bold: true }, { text: tienda, fontSize: 10, bold: true }], border: [false, false, false, false] }
					]]
				},
				layout: { fillColor: '#f5f5f5', hLineWidth: function() { return 0; }, vLineWidth: function() { return 0; }, paddingTop: function() { return 8; }, paddingBottom: function() { return 8; } },
				margin: [0, 0, 0, 15]
			},

			// Resumen financiero
			{
				columns: [
					{ width: '*', text: '' },
					{
						width: 250,
						table: {
							widths: ['*', 100],
							body: [
								[{ text: 'Fondo Inicial:', fontSize: 10 }, { text: 'S/. ' + monto_ini.toFixed(2), alignment: 'right', fontSize: 10 }],
								[{ text: 'Total Gastado:', fontSize: 10, color: '#dc3545' }, { text: 'S/. ' + total_gast.toFixed(2), alignment: 'right', fontSize: 10, color: '#dc3545' }],
								[{ text: 'Saldo Teórico:', fontSize: 12, bold: true }, { text: 'S/. ' + saldo_fin.toFixed(2), alignment: 'right', fontSize: 12, bold: true }]
							]
						},
						layout: { hLineWidth: function(i, node) { return i === node.table.body.length - 1 ? 2 : 0; }, vLineWidth: function() { return 0; }, hLineColor: function() { return '#333'; }, paddingTop: function() { return 4; }, paddingBottom: function() { return 4; } }
					}
				],
				margin: [0, 0, 0, 15]
			},

			// Resumen por categoría
			{ text: 'RESUMEN POR CATEGORÍA', style: 'sectionTitle' },
			{
				table: {
					headerRows: 1,
					widths: ['*', 70, 90, 50],
					body: resumenBody
				},
				layout: 'lightHorizontalLines',
				margin: [0, 0, 0, 15]
			},

			// Detalle de gastos
			{ text: 'DETALLE DE GASTOS', style: 'sectionTitle' },
			{
				table: {
					headerRows: 1,
					widths: [20, 65, 80, '*', 70, 60, 30],
					body: detalleBody
				},
				layout: 'lightHorizontalLines',
				margin: [0, 0, 0, 10]
			},

			// Observaciones
			<?php if(!empty($periodo->observaciones)): ?>
			{
				text: [
					{ text: 'Observaciones: ', bold: true, fontSize: 9 },
					{ text: <?= json_encode($periodo->observaciones) ?>, fontSize: 9 }
				],
				margin: [0, 5, 0, 0],
				padding: [8, 8, 8, 8],
				background: '#f9f9f9'
			},
			<?php endif; ?>

			// Firmas
			{
				columns: [
					{
						width: '40%',
						alignment: 'center',
						stack: [
							{ text: '', margin: [0, 60, 0, 0] },
							{ canvas: [{ type: 'line', x1: 0, y1: 0, x2: 180, y2: 0, lineWidth: 1 }] },
							{ text: 'Responsable de Caja Chica', fontSize: 9, margin: [0, 5, 0, 0] },
							{ text: responsable, fontSize: 8, color: '#666' }
						]
					},
					{ width: '20%', text: '' },
					{
						width: '40%',
						alignment: 'center',
						stack: [
							{ text: '', margin: [0, 60, 0, 0] },
							{ canvas: [{ type: 'line', x1: 0, y1: 0, x2: 180, y2: 0, lineWidth: 1 }] },
							{ text: 'Aprobado por', fontSize: 9, margin: [0, 5, 0, 0] },
							{ text: 'Encargado de Reposición', fontSize: 8, color: '#666' }
						]
					}
				],
				margin: [0, 30, 0, 0]
			}
		],
		styles: {
			sectionTitle: { fontSize: 11, bold: true, color: '#337ab7', margin: [0, 5, 0, 8], decoration: 'underline' },
			tableHeader: { fontSize: 9, bold: true, fillColor: '#e9ecef', color: '#333' }
		},
		footer: function(currentPage, pageCount) {
			return {
				columns: [
					{ text: empresa + ' - Reporte Caja Chica', fontSize: 7, color: '#aaa', margin: [40, 0, 0, 0] },
					{ text: 'Página ' + currentPage + ' de ' + pageCount, fontSize: 7, color: '#aaa', alignment: 'right', margin: [0, 0, 40, 0] }
				]
			};
		}
	};

	// Insertar vales antes de las firmas (penúltimo elemento)
	if (valesContent.length > 0) {
		var firmasIdx = docDefinition.content.length - 1; // Firmas es el último
		for (var vi = 0; vi < valesContent.length; vi++) {
			docDefinition.content.splice(firmasIdx + vi, 0, valesContent[vi]);
		}
	}

	var fileName = 'CajaChica_Periodo_' + periodo_id + '_' + new Date().toISOString().substring(0, 10) + '.pdf';
	pdfMake.createPdf(docDefinition).download(fileName);
}
</script>

</body>
</html>
