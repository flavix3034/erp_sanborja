<?php
	$monto_ini = floatval($periodo->monto_inicial);
	$saldo_final = floatval($periodo->saldo_actual);
	$total_gastado = $monto_ini - $saldo_final;
?>

<div class="row filitas">
	<div class="col-sm-12">
		<h3><i class="fa fa-archive"></i> <?= $page_title ?></h3>
	</div>
</div>

<!-- Tarjetas Resumen -->
<div class="row filitas">
	<div class="col-sm-3">
		<div class="card text-center" style="border-top:3px solid #337ab7;">
			<div class="card-body">
				<small class="text-muted">Monto Inicial</small>
				<h3 style="margin:5px 0;color:#337ab7;">S/. <?= number_format($monto_ini, 2) ?></h3>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card text-center" style="border-top:3px solid #dc3545;">
			<div class="card-body">
				<small class="text-muted">Total Gastado</small>
				<h3 style="margin:5px 0;color:#dc3545;">S/. <?= number_format($total_gastado, 2) ?></h3>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card text-center" style="border-top:3px solid #28a745;">
			<div class="card-body">
				<small class="text-muted">Saldo Final</small>
				<h3 style="margin:5px 0;color:#28a745;">S/. <?= number_format($saldo_final, 2) ?></h3>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card" style="border-top:3px solid #6c757d;">
			<div class="card-body" style="font-size:12px;">
				<small class="text-muted">Información del Periodo</small><br>
				<strong>Apertura:</strong> <?= date('d/m/Y H:i', strtotime($periodo->fecha_apertura)) ?><br>
				<strong>Cierre:</strong> <?= $periodo->fecha_cierre ? date('d/m/Y H:i', strtotime($periodo->fecha_cierre)) : '-' ?><br>
				<strong>Responsable:</strong> <?= $periodo->usuario_apertura_nombre ?><br>
				<?php if(!empty($periodo->observaciones)): ?>
				<strong>Obs:</strong> <?= $periodo->observaciones ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Resumen por Categoría -->
<?php if(!empty($resumen)): ?>
<div class="row filitas">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="fa fa-pie-chart"></i> Resumen por Categoría</h4></div>
			<div class="panel-body">
				<table class="table table-bordered" style="font-size:13px;">
					<thead>
						<tr style="background-color:#f7f7f7;">
							<th>Categoría</th>
							<th class="text-center" style="width:80px;">Gastos</th>
							<th class="text-right" style="width:120px;">Total</th>
							<th class="text-right" style="width:80px;">%</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($resumen as $r):
							$pct = $total_gastado > 0 ? (floatval($r['total']) / $total_gastado) * 100 : 0;
						?>
						<tr style="border-left:4px solid <?= $r['color'] ?>;">
							<td>
								<span class="badge" style="background-color:<?= $r['color'] ?>;color:#fff;padding:3px 8px;"><?= $r['nombre'] ?></span>
							</td>
							<td class="text-center"><?= $r['num_gastos'] ?></td>
							<td class="text-right">S/. <?= number_format($r['total'], 2) ?></td>
							<td class="text-right"><?= number_format($pct, 1) ?>%</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;background-color:#f0f0f0;">
							<td>TOTAL</td>
							<td></td>
							<td class="text-right">S/. <?= number_format($total_gastado, 2) ?></td>
							<td class="text-right">100%</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	<!-- Barras visuales -->
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="fa fa-bar-chart"></i> Distribución</h4></div>
			<div class="panel-body">
				<?php foreach($resumen as $r):
					$pct = $total_gastado > 0 ? (floatval($r['total']) / $total_gastado) * 100 : 0;
				?>
				<div style="margin-bottom:10px;">
					<div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:2px;">
						<span><?= $r['nombre'] ?></span>
						<span>S/. <?= number_format($r['total'], 2) ?></span>
					</div>
					<div class="progress" style="height:18px;margin-bottom:0;">
						<div class="progress-bar" style="width:<?= $pct ?>%;background-color:<?= $r['color'] ?>;font-size:11px;line-height:18px;">
							<?= number_format($pct, 1) ?>%
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Tabla detalle de gastos -->
<div class="row filitas">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="fa fa-list"></i> Detalle de Gastos</h4></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table id="tabla_detalle" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Fecha</th>
								<th>Categoría</th>
								<th>Descripción</th>
								<th>Beneficiario</th>
								<th>Monto</th>
								<th>Comp.</th>
							</tr>
						</thead>
						<tbody>
							<?php $n = 0; foreach($gastos as $g): $n++; ?>
							<tr>
								<td class="text-center"><?= $n ?></td>
								<td><?= date('d/m/Y H:i', strtotime($g['fecha_gasto'])) ?></td>
								<td>
									<span class="badge" style="background-color:<?= $g['categoria_color'] ?>;color:#fff;padding:3px 8px;font-size:11px;"><?= $g['categoria'] ?></span>
								</td>
								<td><?= $g['descripcion'] ?></td>
								<td><?= $g['beneficiario'] ?></td>
								<td class="text-right">S/. <?= number_format($g['monto'], 2) ?></td>
								<td class="text-center">
									<?php if(!empty($g['comprobante'])): ?>
									<a href="<?= base_url('cajachica/ver_comprobante/' . $g['id']) ?>" target="_blank" title="Ver comprobante">
										<i class="fa fa-file-image-o" style="font-size:16px;color:#337ab7"></i>
									</a>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Vales Provisionales -->
<?php if(!empty($vales)): ?>
<div class="row filitas">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="fa fa-ticket"></i> Vales Provisionales</h4></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered" style="font-size:12px;">
						<thead>
							<tr style="background-color:#f7f7f7;">
								<th>#</th>
								<th>Fecha Entrega</th>
								<th>Beneficiario</th>
								<th>Motivo</th>
								<th class="text-right">Monto</th>
								<th class="text-center">Estado</th>
								<th class="text-right">Gastado</th>
								<th class="text-right">Devuelto</th>
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
									<span class="badge" style="background-color:<?= $ec[$v['estado']] ?>;color:#fff;padding:3px 8px;"><?= $v['estado'] ?></span>
								</td>
								<td class="text-right">S/. <?= number_format($v['monto_gastado'], 2) ?></td>
								<td class="text-right">S/. <?= number_format($v['monto_devuelto'], 2) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Botones -->
<div class="row filitas">
	<div class="col-sm-12">
		<a href="<?= base_url('cajachica/index') ?>" class="btn btn-default">
			<i class="fa fa-arrow-left"></i> Volver al Listado
		</a>
		<button type="button" class="btn btn-danger" onclick="abrirReporte(<?= $periodo->id ?>)">
			<i class="fa fa-file-pdf-o"></i> Generar Reporte PDF
		</button>
	</div>
</div>

<script>
$(document).ready(function() {
	$('#tabla_detalle').DataTable({
		"order": [[ 0, "asc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
		},
		"dom": 'Bfrtip',
		"buttons": [
			'copy', 'csv', 'excel',
			{ extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', title: 'Caja Chica - Periodo #<?= $periodo->id ?>' },
			'print'
		],
		"pageLength": 50
	});
});

function abrirReporte(id) {
	window.open('<?= base_url("cajachica/reporte_periodo") ?>/' + id, 'reporte_caja_' + id, 'width=960,height=700,scrollbars=yes');
}
</script>

<style>
.filitas { margin-bottom: 15px; }
.card { border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 15px; }
.card-body { padding: 15px; }
.panel-heading h4 { margin: 0; font-size: 15px; font-weight: bold; }
.progress { background-color: #e9ecef; border-radius: 4px; }

#tabla_detalle th, #tabla_detalle td {
	font-size: 12px;
	vertical-align: middle;
	white-space: nowrap;
}
#tabla_detalle thead th {
	background-color: #f7f7f7;
	font-weight: bold;
}
</style>
