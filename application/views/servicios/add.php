<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
	$id_servicio = isset($id) ? $id : "";
	$modo_val    = isset($modo) ? $modo : "insert";
	$modo_edicion = ($modo_val == 'update') ? "1" : "0";
?>

<style type="text/css">
	.panel-gasto { border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
	.panel-gasto .panel-heading-custom { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 12px 15px; border-bottom: 1px solid #ddd; border-radius: 6px 6px 0 0; }
	.panel-gasto .panel-heading-custom h4 { margin: 0; font-size: 14px; font-weight: 700; color: #495057; }
	.panel-gasto .panel-heading-custom i { margin-right: 8px; }
	.panel-gasto .panel-body-custom { padding: 15px; }

	.filitas { margin-top: 10px; }
	.form-control { text-transform: uppercase; }
	textarea.form-control { text-transform: uppercase; resize: vertical; }
	input[type="datetime-local"].form-control { text-transform: none; }
	input[readonly].form-control { background-color: #f5f5f5; }

	/* Autocomplete busqueda */
	.item-search-wrap { position: relative; }
	#resultados_busqueda {
		position: absolute; z-index: 1000; width: 100%; max-height: 250px;
		overflow-y: auto; background: #fff; border: 1px solid #ccc; border-top: none;
		box-shadow: 0 4px 8px rgba(0,0,0,0.15); border-radius: 0 0 4px 4px; display: none;
	}
	#resultados_busqueda .list-group { margin: 0; }
	#resultados_busqueda .list-group-item:hover { background-color: #e8f4fd; }
	#buscar_item { text-transform: uppercase; }
	#tabla_items_servicio { font-size: 12px; }
</style>

<section class="content">

	<?php if(isset($msg) && strlen($msg)>0): ?>
	<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'info' ?>" style="margin-top:10px;">
		<?= $msg ?>
	</div>
	<?php endif; ?>

	<form method="post" action="<?= base_url('servicios/save') ?>" id="form_servicio">
		<input type="hidden" name="modo" value="<?= $modo_val ?>">
		<?php if(isset($id)): ?>
		<input type="hidden" name="id" value="<?= $id ?>">
		<?php endif; ?>

		<!-- ==================== PANEL 1: INFORMACION DEL CLIENTE ==================== -->
		<div class="panel-gasto">
			<div class="panel-heading-custom">
				<h4><i class="fa fa-user"></i> Información del Cliente</h4>
			</div>
			<div class="panel-body-custom">
				<div class="row">
					<div class="col-sm-4">
						<label>Nombre del Cliente *</label>
						<input type="text" name="cliente_nombre" id="cliente_nombre" class="form-control"
							   value="<?= isset($row->cliente_nombre) ? $row->cliente_nombre : set_value('cliente_nombre') ?>" required>
					</div>
					<div class="col-sm-4">
						<label>Teléfono</label>
						<input type="text" name="cliente_telefono" id="cliente_telefono" class="form-control"
							   value="<?= isset($row->cliente_telefono) ? $row->cliente_telefono : set_value('cliente_telefono') ?>">
					</div>
					<div class="col-sm-4">
						<label>Email</label>
						<input type="email" name="cliente_email" id="cliente_email" class="form-control"
							   value="<?= isset($row->cliente_email) ? $row->cliente_email : set_value('cliente_email') ?>">
					</div>
				</div>
			</div>
		</div>

		<!-- ==================== PANEL 2: INFORMACION DEL EQUIPO ==================== -->
		<div class="panel-gasto">
			<div class="panel-heading-custom">
				<h4><i class="fa fa-mobile"></i> Información del Equipo</h4>
			</div>
			<div class="panel-body-custom">
				<div class="row">
					<div class="col-sm-3">
						<label>Tipo de Equipo</label>
						<?php echo form_dropdown('equipo_tipo', $equipos_tipo, isset($row->equipo_tipo) ? $row->equipo_tipo : 'Otro', 'class="form-control"'); ?>
					</div>
					<div class="col-sm-3">
						<label>Marca</label>
						<input type="text" name="marca" id="marca" class="form-control"
							   value="<?= isset($row->marca) ? $row->marca : set_value('marca') ?>">
					</div>
					<div class="col-sm-3">
						<label>Modelo</label>
						<input type="text" name="modelo" id="modelo" class="form-control"
							   value="<?= isset($row->modelo) ? $row->modelo : set_value('modelo') ?>">
					</div>
					<div class="col-sm-3">
						<label>Número de Serie</label>
						<input type="text" name="numero_serie" id="numero_serie" class="form-control"
							   value="<?= isset($row->numero_serie) ? $row->numero_serie : set_value('numero_serie') ?>">
					</div>
				</div>
				<div class="row" style="margin-top:10px;">
					<div class="col-sm-12">
						<label>Descripción del Equipo</label>
						<textarea name="equipo_descripcion" id="equipo_descripcion" class="form-control" rows="2"><?= isset($row->equipo_descripcion) ? $row->equipo_descripcion : set_value('equipo_descripcion') ?></textarea>
					</div>
				</div>
			</div>
		</div>

		<!-- ==================== PANEL 3: PROBLEMA REPORTADO ==================== -->
		<div class="panel-gasto">
			<div class="panel-heading-custom">
				<h4><i class="fa fa-exclamation-triangle"></i> Problema Reportado</h4>
			</div>
			<div class="panel-body-custom">
				<div class="row">
					<div class="col-sm-12">
						<label>Descripción del Problema *</label>
						<textarea name="problema_reportado" id="problema_reportado" class="form-control" rows="3" required><?= isset($row->problema_reportado) ? $row->problema_reportado : set_value('problema_reportado') ?></textarea>
					</div>
				</div>
			</div>
		</div>

		<!-- ==================== PANEL 4: INFORMACION DEL SERVICIO ==================== -->
		<div class="panel-gasto">
			<div class="panel-heading-custom">
				<h4><i class="fa fa-wrench"></i> Información del Servicio</h4>
			</div>
			<div class="panel-body-custom">
				<div class="row">
					<div class="col-sm-3">
						<label>Estado</label>
						<?php echo form_dropdown('estado', $estados, isset($row->estado) ? $row->estado : 'RECIBIDO', 'class="form-control"'); ?>
					</div>
					<div class="col-sm-3">
						<label>Prioridad</label>
						<?php echo form_dropdown('prioridad', $prioridades, isset($row->prioridad) ? $row->prioridad : 'NORMAL', 'class="form-control"'); ?>
					</div>
					<div class="col-sm-3">
						<label>Técnico Asignado</label>
						<?php
						$ar_tecnicos = array('' => '-- Sin Asignar --');
						foreach($tecnicos as $tec) {
							$ar_tecnicos[$tec->id] = $tec->nombre . ' - ' . $tec->especialidad;
						}
						$selected_tecnico = isset($row->tecnico_asignado) ? $row->tecnico_asignado : '';
						echo form_dropdown('tecnico_asignado', $ar_tecnicos, $selected_tecnico, 'class="form-control"');
						?>
					</div>
				</div>
				<div class="row" style="margin-top:10px;">
					<div class="col-sm-3">
						<label>Fecha de Ingreso *</label>
						<input type="datetime-local" name="fecha_ingreso" id="fecha_ingreso" class="form-control"
							   value="<?= isset($row->fecha_ingreso) ? date('Y-m-d\TH:i', strtotime($row->fecha_ingreso)) : date('Y-m-d\TH:i') ?>" required>
					</div>
					<div class="col-sm-3">
						<label>Fecha Estimada de Reparación</label>
						<input type="datetime-local" name="fecha_estimada_reparacion" id="fecha_estimada_reparacion" class="form-control"
							   value="<?= isset($row->fecha_estimada_reparacion) && $row->fecha_estimada_reparacion ? date('Y-m-d\TH:i', strtotime($row->fecha_estimada_reparacion)) : '' ?>">
					</div>
					<div class="col-sm-3">
						<label>Fecha de Entrega</label>
						<?php
							$estado_actual = isset($row->estado) ? $row->estado : 'RECIBIDO';
							$entrega_disabled = ($estado_actual != 'ENTREGADO') ? 'disabled' : '';
						?>
						<input type="datetime-local" name="fecha_entrega" id="fecha_entrega" class="form-control"
							   value="<?= isset($row->fecha_entrega) && $row->fecha_entrega ? date('Y-m-d\TH:i', strtotime($row->fecha_entrega)) : '' ?>" <?= $entrega_disabled ?>>
						<?php if($estado_actual != 'ENTREGADO'): ?>
						<small class="text-muted" id="fecha_entrega_hint">Se habilita cuando el estado sea ENTREGADO</small>
						<?php endif; ?>
					</div>
				</div>
				<div class="row" style="margin-top:10px;">
					<div class="col-sm-6">
						<label>Diagnóstico</label>
						<textarea name="diagnostico" id="diagnostico" class="form-control" rows="3"><?= isset($row->diagnostico) ? $row->diagnostico : set_value('diagnostico') ?></textarea>
					</div>
					<div class="col-sm-6">
						<label>Observaciones</label>
						<textarea name="observaciones" id="observaciones" class="form-control" rows="3"><?= isset($row->observaciones) ? $row->observaciones : set_value('observaciones') ?></textarea>
					</div>
				</div>
				<div class="row" style="margin-top:10px;">
					<div class="col-sm-3">
						<label>Costo Presupuesto</label>
						<input type="number" name="costo_presupuesto" id="costo_presupuesto" class="form-control"
							   step="0.01" min="0" value="<?= isset($row->costo_presupuesto) ? $row->costo_presupuesto : set_value('costo_presupuesto', 0) ?>" readonly>
					</div>
					<div class="col-sm-3">
						<label>Costo Final</label>
						<input type="number" name="costo_final" id="costo_final" class="form-control"
							   step="0.01" min="0" value="<?= isset($row->costo_final) ? $row->costo_final : set_value('costo_final', 0) ?>" readonly>
					</div>
				</div>
			</div>
		</div>

		<!-- ==================== PANEL 5: PRODUCTOS Y SERVICIOS ==================== -->
		<div class="panel-gasto">
			<div class="panel-heading-custom">
				<h4><i class="fa fa-list-ul"></i> Productos y Servicios</h4>
			</div>
			<div class="panel-body-custom">
				<div class="row">
					<div class="col-sm-5">
						<label>Buscar Producto/Servicio</label>
						<div class="item-search-wrap">
							<input type="text" id="buscar_item" class="form-control" placeholder="Escriba para buscar..." autocomplete="off">
							<div id="resultados_busqueda"></div>
						</div>
					</div>
					<div class="col-sm-2">
						<label>Precio Unit.</label>
						<input type="number" id="item_precio" class="form-control" step="0.01" min="0" value="0">
					</div>
					<div class="col-sm-1">
						<label>Cant.</label>
						<input type="number" id="item_cantidad" class="form-control" step="1" min="1" value="1">
					</div>
					<div class="col-sm-2">
						<label>Obs.</label>
						<input type="text" id="item_obs" class="form-control" placeholder="">
					</div>
					<div class="col-sm-2">
						<label>&nbsp;</label><br>
						<button type="button" class="btn btn-success" onclick="agregarItemServicio()" style="border-radius:4px;">
							<i class="fa fa-plus"></i> Agregar
						</button>
					</div>
				</div>
				<!-- Hidden fields for selected item -->
				<input type="hidden" id="item_id_selected" value="">
				<input type="hidden" id="item_variant_id_selected" value="0">
				<input type="hidden" id="item_name_selected" value="">
				<input type="hidden" id="item_impuesto_selected" value="18">
				<input type="hidden" id="item_prod_serv_selected" value="S">

				<div class="row" style="margin-top:10px;">
					<div class="col-sm-12">
						<table class="table table-bordered table-striped table-condensed" id="tabla_items_servicio">
							<thead>
								<tr style="background-color:#f5f5f5;">
									<th style="width:60px;">Tipo</th>
									<th>Producto / Servicio</th>
									<th style="width:70px;">Cant.</th>
									<th style="width:100px;">P.Unit.</th>
									<th style="width:90px;">Imp.%</th>
									<th style="width:110px;">SubTotal</th>
									<th>Obs.</th>
									<th style="width:50px;"></th>
								</tr>
							</thead>
							<tbody id="tbody_items_servicio">
							</tbody>
							<tfoot>
								<tr style="font-weight:bold; background-color:#f9f9f9;">
									<td colspan="5" class="text-right">TOTAL:</td>
									<td id="total_items_servicio">0.00</td>
									<td colspan="2"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Botones -->
		<div class="row" style="margin-top:10px; margin-bottom:30px;">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary" style="font-size:14px; padding:8px 25px;">
					<i class="fa fa-save"></i> Guardar Servicio
				</button>
				<a href="<?= base_url('servicios') ?>" class="btn btn-danger" style="margin-left:8px;">
					<i class="fa fa-arrow-left"></i> Cancelar
				</a>
				<?php if(isset($id)): ?>
				<a href="<?= base_url('servicios/view') ?>/<?= $id ?>" class="btn btn-info" style="margin-left:8px;">
					<i class="fa fa-eye"></i> Ver Detalles
				</a>
				<?php endif; ?>
			</div>
		</div>
	</form>

</section>

<script>
var ar_serv_items = [];
var busqueda_timeout = null;

$(document).ready(function() {
	// Cargar items existentes (edicion)
	<?php if(isset($items) && !empty($items)): ?>
	<?php foreach($items as $item): ?>
	ar_serv_items.push({
		id: '<?= $item->product_id ?>',
		variant_id: <?= isset($item->variant_id) ? $item->variant_id : 0 ?>,
		name: '<?= addslashes($item->product_name) ?>',
		quantity: <?= $item->quantity ?>,
		price: <?= $item->unit_price ?>,
		impuesto: <?= $item->impuesto ?>,
		prod_serv: '<?= $item->prod_serv ?>',
		obs: '<?= addslashes($item->observaciones) ?>'
	});
	<?php endforeach; ?>
	renderItemsServicio();
	<?php endif; ?>

	// Control de fecha_entrega segun estado
	$('select[name="estado"]').on('change', function() {
		var estado = $(this).val();
		if(estado == 'ENTREGADO') {
			$('#fecha_entrega').prop('disabled', false).prop('required', true);
			if($('#fecha_entrega').val() == '') {
				var ahora = new Date();
				var y = ahora.getFullYear();
				var m = String(ahora.getMonth()+1).padStart(2,'0');
				var d = String(ahora.getDate()).padStart(2,'0');
				var h = String(ahora.getHours()).padStart(2,'0');
				var mi = String(ahora.getMinutes()).padStart(2,'0');
				$('#fecha_entrega').val(y+'-'+m+'-'+d+'T'+h+':'+mi);
			}
			$('#fecha_entrega_hint').hide();
		} else {
			$('#fecha_entrega').prop('disabled', true).prop('required', false).val('');
			$('#fecha_entrega_hint').show();
		}
	});

	// Validacion de formulario
	$('#form_servicio').submit(function() {
		var cliente_nombre = $('#cliente_nombre').val().trim();
		var problema_reportado = $('#problema_reportado').val().trim();
		var fecha_ingreso = $('#fecha_ingreso').val();
		var fecha_estimada = $('#fecha_estimada_reparacion').val();
		var estado = $('select[name="estado"]').val();

		if(cliente_nombre == '') {
			alert('El nombre del cliente es obligatorio');
			$('#cliente_nombre').focus();
			return false;
		}
		if(problema_reportado == '') {
			alert('La descripcion del problema es obligatoria');
			$('#problema_reportado').focus();
			return false;
		}
		if(fecha_ingreso == '') {
			alert('La fecha de ingreso es obligatoria');
			$('#fecha_ingreso').focus();
			return false;
		}
		if(estado == 'ENTREGADO' && $('#fecha_entrega').val() == '') {
			alert('La fecha de entrega es obligatoria cuando el estado es ENTREGADO');
			$('#fecha_entrega').focus();
			return false;
		}
		// Habilitar fecha_entrega antes de enviar para que se incluya en POST
		if(estado == 'ENTREGADO') {
			$('#fecha_entrega').prop('disabled', false);
		}
		return true;
	});

	// Formatear texto a mayusculas automaticamente
	$('#cliente_nombre, #equipo_descripcion, #marca, #modelo, #problema_reportado').on('input', function() {
		this.value = this.value.toUpperCase();
	});

	// Autocomplete busqueda de producto
	$('#buscar_item').on('keyup', function() {
		var term = $(this).val().trim();
		if(term.length < 2) {
			$('#resultados_busqueda').hide().html('');
			return;
		}
		clearTimeout(busqueda_timeout);
		busqueda_timeout = setTimeout(function(){
			$.get('<?= base_url("servicios/buscar_producto") ?>', {term: term}, function(data) {
				var html = '';
				if(data.length > 0) {
					html = '<ul class="list-group" style="margin:0;">';
					for(var i = 0; i < data.length; i++) {
						var r = data[i];
						var tipo = r.prod_serv == 'P' ? '<span class="label label-info">P</span>' : '<span class="label label-success">S</span>';
						var stock_txt = r.prod_serv == 'P' ? ' | Stock: ' + r.stock : '';
						html += '<li class="list-group-item item-resultado" style="cursor:pointer; padding:5px 10px; font-size:12px;" '
							+ 'data-id="' + r.id + '" '
							+ 'data-variant_id="' + (r.variant_id || 0) + '" '
							+ 'data-name="' + r.nombres.replace(/"/g,'&quot;') + '" '
							+ 'data-price="' + r.price + '" '
							+ 'data-impuesto="' + r.impuesto + '" '
							+ 'data-prod_serv="' + r.prod_serv + '" '
							+ 'data-stock="' + r.stock + '">'
							+ tipo + ' ' + r.nombres + ' <small class="text-muted">(' + r.categoria + stock_txt + ')</small>'
							+ '</li>';
					}
					html += '</ul>';
				} else {
					html = '<ul class="list-group" style="margin:0;"><li class="list-group-item" style="padding:5px 10px; font-size:12px;">Sin resultados</li></ul>';
				}
				$('#resultados_busqueda').html(html).show();
			}, 'json');
		}, 300);
	});

	// Seleccionar item del dropdown
	$(document).on('click', '.item-resultado', function() {
		$('#item_id_selected').val($(this).data('id'));
		$('#item_variant_id_selected').val($(this).data('variant_id') || 0);
		$('#item_name_selected').val($(this).data('name'));
		$('#item_impuesto_selected').val($(this).data('impuesto'));
		$('#item_prod_serv_selected').val($(this).data('prod_serv'));
		$('#buscar_item').val($(this).data('name'));
		$('#item_precio').val($(this).data('price'));
		$('#item_cantidad').val(1);
		$('#resultados_busqueda').hide().html('');
	});

	// Cerrar dropdown al click fuera
	$(document).on('click', function(e) {
		if(!$(e.target).closest('#buscar_item, #resultados_busqueda').length) {
			$('#resultados_busqueda').hide().html('');
		}
	});
});

function agregarItemServicio() {
	var id = $('#item_id_selected').val();
	var name = $('#item_name_selected').val();
	var price = parseFloat($('#item_precio').val());
	var quantity = parseFloat($('#item_cantidad').val());
	var impuesto = parseFloat($('#item_impuesto_selected').val());
	var prod_serv = $('#item_prod_serv_selected').val();
	var obs = $('#item_obs').val().trim();

	if(!id || id == '') {
		alert('Seleccione un producto/servicio de la lista');
		$('#buscar_item').focus();
		return;
	}
	if(isNaN(price) || price <= 0) {
		alert('Ingrese un precio valido');
		$('#item_precio').focus();
		return;
	}
	if(isNaN(quantity) || quantity <= 0) {
		alert('Ingrese una cantidad valida');
		$('#item_cantidad').focus();
		return;
	}

	var variant_id = parseInt($('#item_variant_id_selected').val()) || 0;

	ar_serv_items.push({
		id: id, variant_id: variant_id, name: name, quantity: quantity, price: price,
		impuesto: impuesto, prod_serv: prod_serv, obs: obs
	});

	renderItemsServicio();

	$('#buscar_item').val('');
	$('#item_id_selected').val('');
	$('#item_variant_id_selected').val(0);
	$('#item_name_selected').val('');
	$('#item_precio').val(0);
	$('#item_cantidad').val(1);
	$('#item_obs').val('');
	$('#item_impuesto_selected').val(18);
	$('#item_prod_serv_selected').val('S');
	$('#buscar_item').focus();
}

function quitarItemServicio(index) {
	ar_serv_items.splice(index, 1);
	renderItemsServicio();
}

function renderItemsServicio() {
	var html = '';
	var total = 0;

	for(var i = 0; i < ar_serv_items.length; i++) {
		var item = ar_serv_items[i];
		var subtotal = item.price * item.quantity;
		total += subtotal;
		var tipo_label = item.prod_serv == 'P'
			? '<span class="label label-info">Prod</span>'
			: '<span class="label label-success">Serv</span>';

		html += '<tr>';
		html += '<td class="text-center">' + tipo_label + '</td>';
		html += '<td>' + item.name + '</td>';
		html += '<td class="text-center">' + item.quantity + '</td>';
		html += '<td class="text-right">' + item.price.toFixed(2) + '</td>';
		html += '<td class="text-center">' + item.impuesto + '%</td>';
		html += '<td class="text-right">' + subtotal.toFixed(2) + '</td>';
		html += '<td>' + (item.obs || '') + '</td>';
		html += '<td class="text-center"><button type="button" class="btn btn-xs btn-danger" onclick="quitarItemServicio(' + i + ')"><i class="fa fa-trash"></i></button></td>';
		html += '</tr>';

		html += '<input type="hidden" name="item[]" value="' + item.id + '">';
		html += '<input type="hidden" name="variant_id[]" value="' + (item.variant_id || 0) + '">';
		html += '<input type="hidden" name="descripo[]" value="' + item.name.replace(/"/g,'&quot;') + '">';
		html += '<input type="hidden" name="quantity[]" value="' + item.quantity + '">';
		html += '<input type="hidden" name="cost[]" value="' + item.price + '">';
		html += '<input type="hidden" name="impuestos[]" value="' + item.impuesto + '">';
		html += '<input type="hidden" name="obs[]" value="' + (item.obs || '') + '">';
		html += '<input type="hidden" name="prod_serv_arr[]" value="' + item.prod_serv + '">';
	}

	$('#tbody_items_servicio').html(html);
	$('#total_items_servicio').text(total.toFixed(2));
	$('#costo_presupuesto').val(total.toFixed(2));
	$('#costo_final').val(total.toFixed(2));
}
</script>
