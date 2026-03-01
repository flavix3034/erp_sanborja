<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
	.filitas { margin-top: 10px; }
	.panel-attr {
		border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px;
		background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);
	}
	.panel-attr .panel-heading-custom {
		background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
		padding: 12px 15px; border-bottom: 1px solid #ddd;
		border-radius: 6px 6px 0 0;
	}
	.panel-attr .panel-heading-custom h4 {
		margin: 0; font-size: 14px; font-weight: 700; color: #495057;
	}
	.panel-attr .panel-body-custom { padding: 15px; }
	.attr-card {
		border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 12px;
		background: #fff; overflow: hidden;
	}
	.attr-card-header {
		background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
		color: #fff; padding: 10px 15px; font-weight: 700; font-size: 14px;
		display: flex; justify-content: space-between; align-items: center;
	}
	.attr-card-header .attr-actions a { color: #fff; margin-left: 10px; opacity: 0.8; }
	.attr-card-header .attr-actions a:hover { opacity: 1; }
	.attr-card-body { padding: 12px 15px; }
	.valor-tag {
		display: inline-block; padding: 4px 12px; margin: 3px 4px;
		background: #e9ecef; border-radius: 20px; font-size: 12px;
		font-weight: 500; color: #495057; border: 1px solid #dee2e6;
	}
	.valor-tag .remove-val {
		margin-left: 6px; color: #dc3545; cursor: pointer; font-weight: 700;
	}
	.valor-tag .remove-val:hover { color: #a71d2a; }
	.attr-inactive { opacity: 0.5; }
	.attr-inactive .attr-card-header { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
	.add-valor-inline {
		display: inline-flex; align-items: center; gap: 6px; margin-top: 6px;
	}
	.add-valor-inline input {
		width: 150px; height: 28px; font-size: 12px;
		border: 1px solid #ced4da; border-radius: 4px; padding: 2px 8px;
	}
	.add-valor-inline button {
		height: 28px; font-size: 11px; padding: 0 10px;
	}
	.badge-count {
		background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 10px;
		font-size: 11px; font-weight: 400;
	}
</style>

<section class="content">

	<div class="row filitas">
		<div class="col-sm-6">
			<a href="<?= base_url('products') ?>" class="btn btn-warning">
				<i class="fa fa-arrow-left"></i> Volver a Productos
			</a>
		</div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-primary" onclick="mostrarFormAtributo()">
				<i class="fa fa-plus"></i> Nuevo Atributo
			</button>
		</div>
	</div>

	<!-- Formulario nuevo/editar atributo -->
	<div class="panel-attr" style="margin-top:15px; display:none;" id="form_panel">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-tag"></i> <span id="form_titulo">Nuevo Atributo</span></h4>
		</div>
		<div class="panel-body-custom">
			<form id="form_atributo" onsubmit="guardarAtributo(); return false;">
				<input type="hidden" id="attr_id" value="0">
				<div class="row">
					<div class="col-sm-4">
						<label>Nombre del Atributo</label>
						<input type="text" id="attr_nombre" class="form-control" placeholder="Ej: Color, Capacidad, Calidad" required>
					</div>
					<div class="col-sm-2">
						<label>Orden</label>
						<input type="number" id="attr_orden" class="form-control" value="0" min="0">
					</div>
					<div class="col-sm-4" style="margin-top:24px;">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-save"></i> Guardar
						</button>
						<button type="button" class="btn btn-default" onclick="cancelarForm()" style="margin-left:5px;">
							<i class="fa fa-times"></i> Cancelar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Lista de atributos -->
	<?php if (isset($atributos) && count($atributos) > 0): ?>
		<?php foreach ($atributos as $attr): ?>
		<div class="attr-card <?= $attr->activo != '1' ? 'attr-inactive' : '' ?>" id="attr_card_<?= $attr->id ?>">
			<div class="attr-card-header">
				<span>
					<i class="fa fa-tag"></i> <?= htmlspecialchars($attr->nombre) ?>
					<span class="badge-count"><?= count($attr->valores) ?> valores</span>
					<?php if ($attr->activo != '1'): ?>
						<span class="badge-count" style="background:rgba(255,0,0,0.3);">INACTIVO</span>
					<?php endif; ?>
				</span>
				<span class="attr-actions">
					<a href="#" onclick="editarAtributo(<?= $attr->id ?>, '<?= addslashes($attr->nombre) ?>', <?= $attr->orden ?>); return false;" title="Editar nombre">
						<i class="fa fa-pencil"></i>
					</a>
					<?php if ($attr->activo == '1'): ?>
					<a href="#" onclick="toggleAtributo(<?= $attr->id ?>, '1'); return false;" title="Desactivar">
						<i class="fa fa-eye-slash"></i>
					</a>
					<?php else: ?>
					<a href="#" onclick="toggleAtributo(<?= $attr->id ?>, ''); return false;" title="Activar">
						<i class="fa fa-eye"></i>
					</a>
					<?php endif; ?>
				</span>
			</div>
			<div class="attr-card-body">
				<div id="valores_<?= $attr->id ?>">
					<?php if (count($attr->valores) > 0): ?>
						<?php foreach ($attr->valores as $v): ?>
						<span class="valor-tag" id="val_<?= $v->id ?>">
							<?= htmlspecialchars($v->valor) ?>
							<span class="remove-val" onclick="eliminarValor(<?= $v->id ?>, '<?= addslashes($v->valor) ?>')" title="Eliminar">&times;</span>
						</span>
						<?php endforeach; ?>
					<?php else: ?>
						<span class="text-muted" style="font-size:12px;">Sin valores definidos</span>
					<?php endif; ?>
				</div>
				<div class="add-valor-inline">
					<input type="text" id="nuevo_valor_<?= $attr->id ?>" placeholder="Nuevo valor..."
						   onkeydown="if(event.key==='Enter'){agregarValor(<?= $attr->id ?>);event.preventDefault();}">
					<button class="btn btn-xs btn-success" onclick="agregarValor(<?= $attr->id ?>)">
						<i class="fa fa-plus"></i> Agregar
					</button>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="panel-attr" style="margin-top:15px;">
			<div class="panel-body-custom text-center text-muted" style="padding:40px;">
				<i class="fa fa-tags" style="font-size:40px;color:#ccc;"></i>
				<p style="margin-top:10px;">No hay atributos creados. Haz clic en "Nuevo Atributo" para comenzar.</p>
			</div>
		</div>
	<?php endif; ?>

</section>

<script type="text/javascript">
	var baseUrl = '<?= base_url() ?>';

	function mostrarFormAtributo() {
		document.getElementById('form_panel').style.display = 'block';
		document.getElementById('attr_id').value = 0;
		document.getElementById('attr_nombre').value = '';
		document.getElementById('attr_orden').value = 0;
		document.getElementById('form_titulo').textContent = 'Nuevo Atributo';
		document.getElementById('attr_nombre').focus();
	}

	function cancelarForm() {
		document.getElementById('form_panel').style.display = 'none';
	}

	function editarAtributo(id, nombre, orden) {
		document.getElementById('form_panel').style.display = 'block';
		document.getElementById('attr_id').value = id;
		document.getElementById('attr_nombre').value = nombre;
		document.getElementById('attr_orden').value = orden;
		document.getElementById('form_titulo').textContent = 'Editar Atributo #' + id;
		document.getElementById('attr_nombre').focus();
		window.scrollTo(0, 0);
	}

	function guardarAtributo() {
		var nombre = document.getElementById('attr_nombre').value.trim();
		if (nombre.length === 0) { alert('Debe ingresar un nombre'); return; }

		$.ajax({
			url: baseUrl + 'atributos/guardar_atributo',
			type: 'POST',
			data: {
				id: document.getElementById('attr_id').value,
				nombre: nombre,
				orden: document.getElementById('attr_orden').value
			},
			dataType: 'json',
			success: function(res) {
				if (res.rpta === 'success') {
					location.reload();
				} else {
					alert(res.msg || 'Error al guardar');
				}
			},
			error: function() { alert('Error de conexion'); }
		});
	}

	function toggleAtributo(id, activo) {
		var accion = activo === '1' ? 'desactivar' : 'activar';
		if (!confirm('Desea ' + accion + ' este atributo?')) return;

		$.ajax({
			url: baseUrl + 'atributos/toggle_atributo',
			type: 'POST',
			data: { id: id, activo: activo },
			dataType: 'json',
			success: function(res) {
				if (res.rpta === 'success') location.reload();
			}
		});
	}

	function agregarValor(atributo_id) {
		var input = document.getElementById('nuevo_valor_' + atributo_id);
		var valor = input.value.trim();
		if (valor.length === 0) { input.focus(); return; }

		$.ajax({
			url: baseUrl + 'atributos/guardar_valor',
			type: 'POST',
			data: { id: 0, atributo_id: atributo_id, valor: valor, orden: 0 },
			dataType: 'json',
			success: function(res) {
				if (res.rpta === 'success') {
					location.reload();
				} else {
					alert(res.msg || 'Error al guardar valor');
				}
			},
			error: function() { alert('Error de conexion'); }
		});
	}

	function eliminarValor(id, nombre) {
		if (!confirm('Eliminar el valor "' + nombre + '"?')) return;

		$.ajax({
			url: baseUrl + 'atributos/eliminar_valor',
			type: 'POST',
			data: { id: id },
			dataType: 'json',
			success: function(res) {
				if (res.rpta === 'success') {
					var el = document.getElementById('val_' + id);
					if (el) el.remove();
				} else {
					alert(res.msg || 'Error al eliminar');
				}
			},
			error: function() { alert('Error de conexion'); }
		});
	}
</script>
