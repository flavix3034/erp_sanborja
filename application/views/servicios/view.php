<div class="row filitas">
	<div class="col-sm-12">
		<h3>Detalles del Servicio: <?= $servicio->codigo ?></h3>
	</div>
</div>

<!-- Información Principal -->
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-info-sign"></i> Información del Servicio</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-2">
				<strong>Código:</strong><br>
				<span class="label label-primary"><?= $servicio->codigo ?></span>
			</div>
			<div class="col-sm-2">
				<strong>Estado:</strong><br>
				<?php 
				$color_estado = 'default';
				switch($servicio->estado) {
					case 'RECIBIDO': $color_estado = 'info'; break;
					case 'EN DIAGNOSTICO': $color_estado = 'warning'; break;
					case 'EN REPARACION': $color_estado = 'primary'; break;
					case 'ESPERA REPUESTOS': $color_estado = 'warning'; break;
					case 'REPARADO': $color_estado = 'success'; break;
					case 'ENTREGADO': $color_estado = 'success'; break;
					case 'CANCELADO': $color_estado = 'danger'; break;
				}
				?>
				<span class="label label-<?= $color_estado ?>"><?= $servicio->estado ?></span>
			</div>
			<div class="col-sm-2">
				<strong>Prioridad:</strong><br>
				<?php 
				$color_prioridad = 'default';
				switch($servicio->prioridad) {
					case 'BAJA': $color_prioridad = 'default'; break;
					case 'NORMAL': $color_prioridad = 'info'; break;
					case 'ALTA': $color_prioridad = 'warning'; break;
					case 'URGENTE': $color_prioridad = 'danger'; break;
				}
				?>
				<span class="label label-<?= $color_prioridad ?>"><?= $servicio->prioridad ?></span>
			</div>
			<div class="col-sm-3">
				<strong>Fecha Recepción:</strong><br>
				<?= date('d/m/Y H:i', strtotime($servicio->fecha_recepcion)) ?>
			</div>
			<div class="col-sm-3">
				<strong>Técnico Asignado:</strong><br>
				<?= $servicio->tecnico_nombre ?: 'Sin Asignar' ?>
			</div>
		</div>
		<div class="row filitas">
			<div class="col-sm-3">
				<strong>Fecha de Ingreso:</strong><br>
				<?= !empty($servicio->fecha_ingreso) ? date('d/m/Y H:i', strtotime($servicio->fecha_ingreso)) : '<span class="text-muted">No registrada</span>' ?>
			</div>
			<div class="col-sm-3">
				<strong>Fecha Estimada Reparaci&oacute;n:</strong><br>
				<?= !empty($servicio->fecha_estimada_reparacion) ? date('d/m/Y H:i', strtotime($servicio->fecha_estimada_reparacion)) : '<span class="text-muted">No registrada</span>' ?>
			</div>
			<div class="col-sm-3">
				<strong>Fecha de Entrega:</strong><br>
				<?= !empty($servicio->fecha_entrega) ? date('d/m/Y H:i', strtotime($servicio->fecha_entrega)) : '<span class="text-muted">Pendiente</span>' ?>
			</div>
		</div>
	</div>
</div>

<!-- Información del Cliente -->
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-user"></i> Información del Cliente</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-4">
				<strong>Nombre:</strong><br>
				<?= $servicio->cliente_nombre ?>
			</div>
			<div class="col-sm-4">
				<strong>Teléfono:</strong><br>
				<?= $servicio->cliente_telefono ?: 'No registrado' ?>
			</div>
			<div class="col-sm-4">
				<strong>Email:</strong><br>
				<?= $servicio->cliente_email ?: 'No registrado' ?>
			</div>
		</div>
	</div>
</div>

<!-- Información del Equipo -->
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-phone"></i> Información del Equipo</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-2">
				<strong>Tipo:</strong><br>
				<?= $servicio->equipo_tipo ?>
			</div>
			<div class="col-sm-2">
				<strong>Marca:</strong><br>
				<?= $servicio->marca ?: 'No especificado' ?>
			</div>
			<div class="col-sm-2">
				<strong>Modelo:</strong><br>
				<?= $servicio->modelo ?: 'No especificado' ?>
			</div>
			<div class="col-sm-3">
				<strong>N° Serie:</strong><br>
				<?= $servicio->numero_serie ?: 'No especificado' ?>
			</div>
		</div>
		<div class="row filitas">
			<div class="col-sm-12">
				<strong>Descripción:</strong><br>
				<?= $servicio->equipo_descripcion ?: 'No especificado' ?>
			</div>
		</div>
	</div>
</div>

<!-- Información del Problema -->
<div class="panel panel-warning">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-warning-sign"></i> Problema Reportado</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-12">
				<p style="background-color: #fcf8e3; padding: 10px; border-radius: 4px;">
					<?= nl2br($servicio->problema_reportado) ?>
				</p>
			</div>
		</div>
	</div>
</div>

<!-- Diagnóstico -->
<div class="panel panel-info">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-search"></i> Diagnóstico</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-12">
				<?php if($servicio->diagnostico): ?>
					<p style="background-color: #d9edf7; padding: 10px; border-radius: 4px;">
						<?= nl2br($servicio->diagnostico) ?>
					</p>
				<?php else: ?>
					<p class="text-muted">Sin diagnóstico registrado</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Costos -->
<div class="panel panel-success">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-usd"></i> Información de Costos</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-4">
				<strong>Costo Presupuesto:</strong><br>
				S/. <?= number_format($servicio->costo_presupuesto, 2) ?>
			</div>
			<div class="col-sm-4">
				<strong>Costo Final:</strong><br>
				S/. <?= number_format($servicio->costo_final, 2) ?>
			</div>
		</div>
	</div>
</div>

<!-- Productos y Servicios -->
<?php if(!empty($items)): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-list-alt"></i> Productos y Servicios</h4>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-condensed" style="font-size:12px;">
			<thead>
				<tr style="background-color:#f5f5f5;">
					<th style="width:60px;">Tipo</th>
					<th>Producto / Servicio</th>
					<th style="width:70px;" class="text-center">Cant.</th>
					<th style="width:100px;" class="text-right">P.Unit.</th>
					<th style="width:80px;" class="text-center">Imp.%</th>
					<th style="width:110px;" class="text-right">SubTotal</th>
					<th>Obs.</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_items = 0;
				foreach($items as $item):
					$subtotal_item = $item->unit_price * $item->quantity;
					$total_items += $subtotal_item;
					$tipo_label = $item->prod_serv == 'P'
						? '<span class="label label-info">Prod</span>'
						: '<span class="label label-success">Serv</span>';
				?>
				<tr>
					<td class="text-center"><?= $tipo_label ?></td>
					<td><?= $item->product_name ?></td>
					<td class="text-center"><?= $item->quantity + 0 ?></td>
					<td class="text-right"><?= number_format($item->unit_price, 2) ?></td>
					<td class="text-center"><?= $item->impuesto ?>%</td>
					<td class="text-right"><?= number_format($subtotal_item, 2) ?></td>
					<td><?= $item->observaciones ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr style="font-weight:bold; background-color:#f9f9f9;">
					<td colspan="5" class="text-right">TOTAL:</td>
					<td class="text-right">S/. <?= number_format($total_items, 2) ?></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php endif; ?>

<!-- Observaciones -->
<?php if($servicio->observaciones): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><i class="glyphicon glyphicon-comment"></i> Observaciones</h4>
	</div>
	<div class="panel-body">
		<div class="row filitas">
			<div class="col-sm-12">
				<p style="background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
					<?= nl2br($servicio->observaciones) ?>
				</p>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Acciones -->
<div class="row filitas">
	<div class="col-sm-12">
		<a href="<?= base_url('servicios/add') ?>/<?= $servicio->id ?>" class="btn btn-primary">
			<i class="glyphicon glyphicon-edit"></i> Editar Servicio
		</a>
		<a href="<?= base_url('servicios') ?>" class="btn btn-default">
			<i class="glyphicon glyphicon-arrow-left"></i> Volver al Listado
		</a>
		
		<a href="javascript:void(0)" onclick="window.open('<?= base_url('servicios/print_etiqueta/'.$servicio->id) ?>', 'etiqueta', 'width=500,height=300')" class="btn btn-default">
			<i class="glyphicon glyphicon-print"></i> Etiqueta
		</a>

		<!-- Cambio de Estado Rápido -->
		<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalCambiarEstado">
			<i class="glyphicon glyphicon-refresh"></i> Cambiar Estado
		</button>

		<?php if(!empty($items) && in_array($servicio->estado, array('REPARADO','ENTREGADO')) && empty($servicio->sale_id)): ?>
		<!-- Generar Venta -->
		<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarVenta">
			<i class="glyphicon glyphicon-shopping-cart"></i> Generar Venta
		</button>
		<?php endif; ?>

		<?php if(!empty($servicio->sale_id)): ?>
		<!-- Ver Venta -->
		<button type="button" class="btn btn-success" onclick="ver_venta(<?= $servicio->sale_id ?>)">
			<i class="glyphicon glyphicon-eye-open"></i> Ver Venta #<?= $servicio->sale_id ?>
		</button>
		<?php endif; ?>
	</div>
</div>

<!-- Modal Cambiar Estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Cambiar Estado del Servicio</h4>
			</div>
			<div class="modal-body">
				<form id="formCambiarEstado">
					<input type="hidden" name="servicio_id" value="<?= $servicio->id ?>">
					<div class="form-group">
						<label>Nuevo Estado:</label>
						<select name="estado_nuevo" id="select_estado_nuevo" class="form-control" required onchange="toggleFechaEntrega()">
							<option value="">-- Seleccione --</option>
							<option value="EN DIAGNOSTICO">EN DIAGNOSTICO</option>
							<option value="EN REPARACION">EN REPARACION</option>
							<option value="ESPERA REPUESTOS">ESPERA REPUESTOS</option>
							<option value="REPARADO">REPARADO</option>
							<option value="ENTREGADO">ENTREGADO</option>
							<option value="CANCELADO">CANCELADO</option>
						</select>
					</div>
					<div class="form-group" id="grupo_fecha_entrega" style="display:none;">
						<label>Fecha de Entrega: *</label>
						<input type="datetime-local" name="fecha_entrega" id="fecha_entrega_modal" class="form-control">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-warning" onclick="guardarCambioEstado()">Guardar Cambio</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Generar Venta -->
<?php if(!empty($items) && in_array($servicio->estado, array('REPARADO','ENTREGADO')) && empty($servicio->sale_id)): ?>
<div class="modal fade" id="modalGenerarVenta" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><i class="glyphicon glyphicon-shopping-cart"></i> Generar Venta desde Servicio <?= $servicio->codigo ?></h4>
			</div>
			<div class="modal-body">
				<!-- Resumen de items -->
				<h5><strong>Resumen de Items:</strong></h5>
				<table class="table table-condensed table-bordered" style="font-size:12px;">
					<thead>
						<tr style="background-color:#f5f5f5;">
							<th>Tipo</th>
							<th>Producto / Servicio</th>
							<th class="text-center">Cant.</th>
							<th class="text-right">P.Unit.</th>
							<th class="text-right">SubTotal</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total_venta = 0;
						foreach($items as $item):
							$st = $item->unit_price * $item->quantity;
							$total_venta += $st;
							$tipo = $item->prod_serv == 'P' ? '<span class="label label-info">P</span>' : '<span class="label label-success">S</span>';
						?>
						<tr>
							<td class="text-center"><?= $tipo ?></td>
							<td><?= $item->product_name ?></td>
							<td class="text-center"><?= $item->quantity + 0 ?></td>
							<td class="text-right"><?= number_format($item->unit_price, 2) ?></td>
							<td class="text-right"><?= number_format($st, 2) ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td colspan="4" class="text-right">TOTAL:</td>
							<td class="text-right">S/. <?= number_format($total_venta, 2) ?></td>
						</tr>
					</tfoot>
				</table>

	
				<p class="text-muted" style="margin-top:10px;">Al confirmar, será redirigido a la pantalla de ventas con estos items precargados.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success" onclick="confirmarGenerarVenta()">
					<i class="glyphicon glyphicon-ok"></i> Confirmar e Ir a Ventas
				</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<script>
function toggleFechaEntrega() {
	var estado = $('#select_estado_nuevo').val();
	if(estado == 'ENTREGADO') {
		$('#grupo_fecha_entrega').show();
		// Pre-llenar con fecha/hora actual
		var ahora = new Date();
		var y = ahora.getFullYear();
		var m = String(ahora.getMonth()+1).padStart(2,'0');
		var d = String(ahora.getDate()).padStart(2,'0');
		var h = String(ahora.getHours()).padStart(2,'0');
		var mi = String(ahora.getMinutes()).padStart(2,'0');
		$('#fecha_entrega_modal').val(y+'-'+m+'-'+d+'T'+h+':'+mi);
	} else {
		$('#grupo_fecha_entrega').hide();
		$('#fecha_entrega_modal').val('');
	}
}

function guardarCambioEstado() {
	var form = $('#formCambiarEstado');
	var estado_nuevo = form.find('select[name="estado_nuevo"]').val();

	if(estado_nuevo == '') {
		alert('Seleccione un nuevo estado');
		return;
	}

	if(estado_nuevo == 'ENTREGADO' && $('#fecha_entrega_modal').val() == '') {
		alert('Debe ingresar la fecha de entrega');
		return;
	}

	$.post('<?= base_url('servicios/cambiar_estado_servicio') ?>', form.serialize(), function(response) {
		if(response.rpta == 'success') {
			alert('Estado cambiado correctamente');
			location.reload();
		} else {
			alert('Error: ' + response.msg);
		}
	}, 'json');
}

function confirmarGenerarVenta() {
	window.location.href = '<?= base_url("sales/add") ?>?servicio_id=<?= $servicio->id ?>';
}

function ver_venta(sale_id) {
	window.open('<?= base_url('sales/view_popup') ?>/' + sale_id, 'venta_' + sale_id, 'width=900,height=700,scrollbars=yes');
}
</script>

<style>
.filitas {
    margin-bottom: 15px;
}

.panel-heading h4 {
    margin: 0;
    color: #3c8dbc;
}

.label {
    font-size: 11px;
    padding: 4px 8px;
}

.panel {
    margin-bottom: 20px;
}

.panel-body p {
    margin: 0;
}
</style>