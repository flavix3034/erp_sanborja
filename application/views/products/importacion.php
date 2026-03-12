<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<style type="text/css">
	.panel-gasto { border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
	.panel-gasto .panel-heading-custom { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 12px 15px; border-bottom: 1px solid #ddd; border-radius: 6px 6px 0 0; }
	.panel-gasto .panel-heading-custom h4 { margin: 0; font-size: 14px; font-weight: 700; color: #495057; }
	.panel-gasto .panel-heading-custom i { margin-right: 8px; }
	.panel-gasto .panel-body-custom { padding: 15px; }
	.ref-table { font-size: 12px; }
	.ref-table th { background: #f5f5f5; padding: 4px 8px !important; }
	.ref-table td { padding: 4px 8px !important; }
	.tipo-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-weight: 700; font-size: 12px; color: #fff; }
	.tipo-p { background: #17a2b8; }
	.tipo-pv { background: #6f42c1; }
	.tipo-v { background: #28a745; }
</style>

<section class="content">

	<?= form_open_multipart(base_url("products/leer_csv"), 'id="form_importacion"'); ?>

	<!-- ==================== PANEL 1: SUBIR ARCHIVO ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-upload"></i> Subir Archivo CSV</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<div class="col-sm-4">
					<label>Archivo CSV</label>
					<input type="file" name="fichero1" class="form-control" accept=".csv,.txt" required>
				</div>
				<div class="col-sm-4">
					<label>Formato del CSV</label>
					<select name="opciones_csv" class="form-control" required>
						<option value="">-- Seleccione --</option>
						<option value="1">CSV (separador ; y datos entrecomillados)</option>
						<option value="2">CSV (solo separador ;)</option>
					</select>
				</div>
				<div class="col-sm-4">
					<label>&nbsp;</label><br>
					<button type="submit" class="btn btn-primary" style="border-radius:4px;">
						<i class="fa fa-cloud-upload"></i> Importar
					</button>
					<a href="<?= base_url('downloads/formato_importacion_productos.csv') ?>" class="btn btn-success" style="border-radius:4px; margin-left:8px;">
						<i class="fa fa-download"></i> Plantilla
					</a>
				</div>
			</div>
		</div>
	</div>

	<?= form_close(); ?>

	<!-- ==================== PANEL 2: RESULTADO ==================== -->
	<?php if(isset($respuesta) && strlen($respuesta) > 0): ?>
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-list-alt"></i> Resultado de Importación</h4>
		</div>
		<div class="panel-body-custom">
			<?= $respuesta ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- ==================== PANEL 3: REFERENCIA ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-book"></i> Datos de Referencia</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<!-- Categorías -->
				<div class="col-sm-3">
					<strong>Categorías</strong>
					<table class="table table-bordered table-condensed ref-table" style="margin-top:6px;">
						<thead><tr><th>Cód</th><th>Categoría</th></tr></thead>
						<tbody>
						<?php
							$cats = $this->db->select('id, name')->order_by('name')->get("tec_categories")->result();
							foreach($cats as $c):
						?>
						<tr><td><?= $c->id ?></td><td><?= $c->name ?></td></tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<!-- Unidades -->
				<div class="col-sm-3">
					<strong>Unidades</strong>
					<table class="table table-bordered table-condensed ref-table" style="margin-top:6px;">
						<thead><tr><th>Cód</th><th>Unidad</th></tr></thead>
						<tbody>
						<?php
							$unidades = $this->db->select('id, descrip')->order_by('descrip')->get("tec_unidades")->result();
							foreach($unidades as $u):
						?>
						<tr><td><?= $u->id ?></td><td><?= $u->descrip ?></td></tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<!-- Atributos y Valores -->
				<div class="col-sm-6">
					<strong>Atributos y Valores (para variantes)</strong>
					<table class="table table-bordered table-condensed ref-table" style="margin-top:6px;">
						<thead><tr><th>Atributo (col. header)</th><th>Valores disponibles</th></tr></thead>
						<tbody>
						<?php
							$atributos = $this->db->query("SELECT * FROM tec_atributos WHERE activo='1' ORDER BY orden, nombre")->result();
							foreach($atributos as $at):
								$valores = $this->db->query("SELECT valor FROM tec_atributo_valores WHERE atributo_id=? ORDER BY orden, valor", array($at->id))->result();
								$vals_txt = array();
								foreach($valores as $v) $vals_txt[] = $v->valor;
						?>
						<tr>
							<td><strong><?= htmlspecialchars($at->nombre) ?></strong></td>
							<td><?= htmlspecialchars(implode(', ', $vals_txt)) ?></td>
						</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- ==================== PANEL 4: INSTRUCCIONES ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-info-circle"></i> Instrucciones del Formato CSV</h4>
		</div>
		<div class="panel-body-custom" style="font-size:13px;">
			<p>El CSV debe usar <strong>punto y coma (;)</strong> como separador. La primera columna indica el <strong>tipo de fila</strong>:</p>

			<table class="table table-bordered" style="font-size:12px; max-width:700px;">
				<thead><tr><th style="width:80px;">Tipo</th><th>Descripción</th></tr></thead>
				<tbody>
					<tr>
						<td><span class="tipo-badge tipo-p">P</span></td>
						<td>Producto simple (sin variantes). Todos los campos son requeridos.</td>
					</tr>
					<tr>
						<td><span class="tipo-badge tipo-pv">PV</span></td>
						<td>Producto padre con variantes. El <b>código se autogenera</b> (dejar vacío). Los precios sirven como default.</td>
					</tr>
					<tr>
						<td><span class="tipo-badge tipo-v">V</span></td>
						<td>Variante del producto PV anterior. Solo llena precios (opcionales) y valores de atributos.</td>
					</tr>
				</tbody>
			</table>

			<p><strong>Cabecera del CSV:</strong></p>
			<code style="font-size:11px;">tipo;codigo;nombre;marca;categoria;unidad;precio_x_menor;precio_x_mayor;alerta_cantidad;<?php
				$attr_names = array();
				foreach($atributos as $at) $attr_names[] = $at->nombre;
				echo implode(';', $attr_names);
			?></code>

			<p style="margin-top:12px;"><strong>Ejemplo:</strong></p>
<pre style="font-size:11px; background:#f8f9fa; padding:10px; border-radius:4px; overflow-x:auto;">tipo;codigo;nombre;marca;categoria;unidad;precio_x_menor;precio_x_mayor;alerta_cantidad;<?= implode(';', $attr_names) . "\n" ?>P;CAB001;CABLE USB;SAMSUNG;<?= isset($cats[0]) ? $cats[0]->id : '1' ?>;<?= isset($unidades[0]) ? $unidades[0]->id : '1' ?>;15.00;12.00;10;<?= str_repeat(';', count($attr_names)-1) ?>

PV;;AUDIFONO BT;UGREEN;<?= isset($cats[0]) ? $cats[0]->id : '1' ?>;<?= isset($unidades[0]) ? $unidades[0]->id : '1' ?>;50.00;40.00;5;<?= str_repeat(';', count($attr_names)-1) ?>

V;;;;;;;;60.00;50.00;<?php
	// Ejemplo con primer valor de cada atributo
	$ejemplo_vals = array();
	foreach($atributos as $at){
		$primer_val = $this->db->query("SELECT valor FROM tec_atributo_valores WHERE atributo_id=? ORDER BY id LIMIT 1", array($at->id))->row();
		$ejemplo_vals[] = $primer_val ? $primer_val->valor : '';
	}
	echo implode(';', $ejemplo_vals);
?>

V;;;;;;;;55.00;45.00;<?php
	$ejemplo_vals2 = array();
	foreach($atributos as $at){
		$seg_val = $this->db->query("SELECT valor FROM tec_atributo_valores WHERE atributo_id=? ORDER BY id LIMIT 1,1", array($at->id))->row();
		$ejemplo_vals2[] = $seg_val ? $seg_val->valor : '';
	}
	echo implode(';', $ejemplo_vals2);
?></pre>

			<p class="text-muted" style="font-size:11px; margin-top:8px;">
				<i class="fa fa-lightbulb-o"></i> <strong>Nota:</strong> También se acepta el formato anterior (sin columna "tipo") para importar solo productos simples.
			</p>
		</div>
	</div>

</section>
