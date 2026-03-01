<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<style>
	.filitas { margin-top: 10px; }
	.cat-color-preview { display: inline-block; width: 24px; height: 24px; border-radius: 4px; border: 1px solid #ccc; vertical-align: middle; }
	.cat-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; color: #fff; font-size: 12px; font-weight: 600; }
	.panel-gasto { border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
	.panel-gasto .panel-heading-custom { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 12px 15px; border-bottom: 1px solid #ddd; border-radius: 6px 6px 0 0; }
	.panel-gasto .panel-heading-custom h4 { margin: 0; font-size: 14px; font-weight: 700; color: #495057; }
	.panel-gasto .panel-heading-custom i { margin-right: 8px; }
	.panel-gasto .panel-body-custom { padding: 15px; }
</style>

<section class="content">

	<div class="row filitas">
		<div class="col-sm-12">
			<a href="<?= base_url('gastos') ?>" class="btn btn-warning">
				<i class="fa fa-arrow-left"></i> Volver a Gastos
			</a>
		</div>
	</div>

	<!-- Formulario nueva/editar categoria -->
	<div class="panel-gasto" style="margin-top:15px;">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-tags"></i> <span id="form_titulo">Nueva Categoria</span></h4>
		</div>
		<div class="panel-body-custom">
			<form id="form_categoria" onsubmit="guardarCategoria(); return false;">
				<input type="hidden" id="cat_id" value="0">
				<div class="row">
					<div class="col-sm-4">
						<label>Nombre</label>
						<input type="text" id="cat_nombre" class="form-control" placeholder="Ej: Alquiler" required>
					</div>
					<div class="col-sm-2">
						<label>Color</label>
						<input type="color" id="cat_color" class="form-control" value="#6c757d" style="height:34px;padding:2px;">
					</div>
					<div class="col-sm-2">
						<label>Orden</label>
						<input type="number" id="cat_orden" class="form-control" value="0" min="0">
					</div>
					<div class="col-sm-3" style="margin-top:24px;">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-save"></i> Guardar
						</button>
						<button type="button" class="btn btn-default" onclick="limpiarForm()" style="margin-left:5px;">
							<i class="fa fa-eraser"></i> Limpiar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Tabla de categorias -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-list"></i> Categorias de Gasto</h4>
		</div>
		<div class="panel-body-custom">
			<table class="table table-striped table-bordered" style="font-size:13px;" id="tabla_categorias">
				<thead>
					<tr>
						<th style="width:5%">#</th>
						<th style="width:10%">Color</th>
						<th style="width:35%">Nombre</th>
						<th style="width:10%">Orden</th>
						<th style="width:10%">Estado</th>
						<th style="width:15%">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php if(isset($categorias) && count($categorias) > 0): ?>
						<?php foreach($categorias as $cat): ?>
						<tr id="row_cat_<?= $cat->id ?>">
							<td><?= $cat->id ?></td>
							<td><span class="cat-badge" style="background-color:<?= $cat->color ?>;"><?= htmlspecialchars($cat->nombre) ?></span></td>
							<td><?= htmlspecialchars($cat->nombre) ?></td>
							<td class="text-center"><?= $cat->orden ?></td>
							<td class="text-center">
								<?php if($cat->activo == '1'): ?>
									<span class="badge" style="background-color:#28a745;color:#fff;padding:3px 8px;">Activo</span>
								<?php else: ?>
									<span class="badge" style="background-color:#dc3545;color:#fff;padding:3px 8px;">Inactivo</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<a href="#" onclick="editarCategoria(<?= $cat->id ?>, '<?= addslashes($cat->nombre) ?>', '<?= $cat->color ?>', <?= $cat->orden ?>); return false;" title="Editar">
									<i class="fa fa-edit" style="font-size:15px;color:#f0ad4e;"></i>
								</a>&nbsp;&nbsp;
								<?php if($cat->activo == '1'): ?>
								<a href="#" onclick="eliminarCategoria(<?= $cat->id ?>, '<?= addslashes($cat->nombre) ?>'); return false;" title="Desactivar">
									<i class="fa fa-trash" style="font-size:15px;color:#d9534f;"></i>
								</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr><td colspan="6" class="text-center text-muted">No hay categorias registradas</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

</section>

<script type="text/javascript">

	function guardarCategoria() {
		var nombre = document.getElementById("cat_nombre").value.trim();
		var color = document.getElementById("cat_color").value;
		var orden = document.getElementById("cat_orden").value;
		var id = document.getElementById("cat_id").value;

		if (nombre.length == 0) {
			alert("Debe ingresar un nombre");
			return;
		}

		$.ajax({
			url: '<?= base_url("gastos/guardar_categoria") ?>',
			type: 'POST',
			data: { id: id, nombre: nombre, color: color, orden: orden },
			dataType: 'json',
			success: function(res) {
				if (res.rpta_msg == 'success') {
					alert("Categoria guardada correctamente");
					location.reload();
				} else {
					alert("Error al guardar la categoria");
				}
			},
			error: function() {
				alert("Error de conexion");
			}
		});
	}

	function editarCategoria(id, nombre, color, orden) {
		document.getElementById("cat_id").value = id;
		document.getElementById("cat_nombre").value = nombre;
		document.getElementById("cat_color").value = color;
		document.getElementById("cat_orden").value = orden;
		document.getElementById("form_titulo").textContent = "Editar Categoria #" + id;
		window.scrollTo(0, 0);
	}

	function eliminarCategoria(id, nombre) {
		if (confirm("Desea desactivar la categoria '" + nombre + "'?")) {
			$.ajax({
				url: '<?= base_url("gastos/eliminar_categoria") ?>',
				type: 'POST',
				data: { id: id },
				dataType: 'json',
				success: function(res) {
					if (res.rpta_msg == 'success') {
						alert("Categoria desactivada correctamente");
						location.reload();
					} else {
						alert("Error al desactivar la categoria");
					}
				}
			});
		}
	}

	function limpiarForm() {
		document.getElementById("cat_id").value = 0;
		document.getElementById("cat_nombre").value = '';
		document.getElementById("cat_color").value = '#6c757d';
		document.getElementById("cat_orden").value = 0;
		document.getElementById("form_titulo").textContent = "Nueva Categoria";
	}
</script>
