<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
	$id_gasto           = isset($id) ? $id : "";
	$modo               = isset($modo) ? $modo : "I";
	$modo_edicion       = ($modo == 'U') ? "1" : "0";

	// Valores para edicion
	$fecha_val          = "";
	$nroDoc_val         = "";
	$tipoDoc_val        = "";
	$proveedor_id_val   = "";
	$redondeo_val       = "";
	$estado_pago_val    = "PAGADO";
	$fecha_vencimiento_val = "";
	$observaciones_val  = "";
	$por_igv_val        = 18;

	$proveedor_nombre_edit = "";
	$proveedor_ruc_edit = "";
	$proveedor_direccion_edit = "";
	$proveedor_correo_edit = "";
	$proveedor_phone_edit = "";
?>
<script type="text/javascript">
	var ar_items = [];
	var categorias_lista = <?= json_encode($categorias) ?>;
</script>
<?php
	// CARGAR DATOS AL EDITAR
	if(strlen($id_gasto."") > 0){
		$gasto = $this->db->query("SELECT a.*, p.nombre prov_nombre, p.ruc prov_ruc, p.direccion prov_direccion, p.correo prov_correo, p.phone prov_phone
			FROM tec_gastos a LEFT JOIN tec_proveedores p ON a.proveedor_id = p.id WHERE a.id = ?", array($id_gasto))->row();

		if($gasto){
			$fecha_val     = $gasto->fecha;
			$nroDoc_val    = $gasto->nroDoc;
			$tipoDoc_val   = $gasto->tipoDoc;
			$proveedor_id_val = $gasto->proveedor_id;
			$redondeo_val  = $gasto->redondeo;
			$estado_pago_val = $gasto->estado_pago;
			$fecha_vencimiento_val = $gasto->fecha_vencimiento;
			$observaciones_val = $gasto->observaciones;
			$por_igv_val   = $gasto->por_igv;

			$proveedor_nombre_edit = $gasto->prov_nombre;
			$proveedor_ruc_edit = $gasto->prov_ruc;
			$proveedor_direccion_edit = $gasto->prov_direccion;
			$proveedor_correo_edit = $gasto->prov_correo;
			$proveedor_phone_edit = $gasto->prov_phone;

			// Cargar items
			$items = $this->db->query("SELECT gi.*, gc.nombre cat_nombre, gc.color cat_color
				FROM tec_gastos_items gi LEFT JOIN tec_gastos_categorias gc ON gi.categoria_id = gc.id
				WHERE gi.gasto_id = ? ORDER BY gi.id", array($id_gasto))->result();

			if(count($items) > 0){
				echo "<script>\n";
				foreach($items as $item){
					echo "ar_items.push({categoria_id:" . intval($item->categoria_id) . ",
						categoria_nombre:'" . addslashes($item->cat_nombre) . "',
						categoria_color:'" . $item->cat_color . "',
						descripcion:'" . addslashes($item->descripcion) . "',
						cantidad:" . ($item->cantidad * 1) . ",
						precio:" . ($item->precio_unitario * 1) . ",
						subtotal:" . ($item->subtotal * 1) . "});\n";
				}
				echo "window.addEventListener('load', function() { cargar_items(); });\n";
				echo "</script>\n";
			}
		}
	}
?>

<style type="text/css">
	.filitas { margin-top: 10px; border-style: none; border-width: 1px; }
	.table th { height: 35px; padding: 4px !important; }
	.panel-gasto { border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
	.panel-gasto .panel-heading-custom { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 12px 15px; border-bottom: 1px solid #ddd; border-radius: 6px 6px 0 0; }
	.panel-gasto .panel-heading-custom h4 { margin: 0; font-size: 14px; font-weight: 700; color: #495057; }
	.panel-gasto .panel-heading-custom i { margin-right: 8px; }
	.panel-gasto .panel-body-custom { padding: 15px; }

	/* Autocomplete proveedor */
	.proveedor-search-wrap { position: relative; }
	.proveedor-results { position: absolute; z-index: 1000; background: #fff; border: 1px solid #ccc; border-top: none; width: 100%; max-height: 250px; overflow-y: auto; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.15); border-radius: 0 0 4px 4px; }
	.proveedor-results .prov-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
	.proveedor-results .prov-item:hover { background-color: #e8f4fd; }
	.proveedor-results .prov-item .prov-nombre { font-weight: 600; color: #333; }
	.proveedor-results .prov-item .prov-ruc { color: #888; font-size: 12px; margin-left: 8px; }

	/* Tarjeta proveedor */
	.prov-card { background: #f0f7ff; border: 1px solid #b8d4f0; border-radius: 6px; padding: 12px 15px; margin-top: 10px; display: none; }
	.prov-card .prov-card-title { font-weight: 700; font-size: 14px; color: #2c5282; margin-bottom: 6px; }
	.prov-card .prov-card-detail { font-size: 12px; color: #555; line-height: 1.6; }
	.prov-card .prov-card-detail strong { color: #333; }
	.prov-card .btn-limpiar-prov { position: absolute; top: 8px; right: 15px; cursor: pointer; color: #999; font-size: 16px; }
	.prov-card .btn-limpiar-prov:hover { color: #d9534f; }

	/* Calculadora impuestos */
	.calc-impuestos { background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 6px; padding: 12px 15px; margin-top: 10px; }
	.calc-impuestos .calc-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
	.calc-impuestos .calc-row.calc-total { border-top: 2px solid #495057; padding-top: 8px; margin-top: 4px; font-size: 15px; font-weight: 700; color: #28a745; }
	.calc-impuestos .calc-label { color: #555; }
	.calc-impuestos .calc-value { font-weight: 600; color: #333; }

	/* File preview */
	.file-preview { background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; margin-top: 8px; display: none; font-size: 12px; }
	.file-preview i { color: #337ab7; margin-right: 6px; }
	.file-preview .btn-remove-file { cursor: pointer; color: #d9534f; margin-left: 10px; }

	/* Categoria badge en select */
	.cat-badge { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 6px; vertical-align: middle; }
</style>

<section class="content">

	<?php
		if(isset($msg)){
			echo '<div class="alert alert-'.$rpta_msg.'" style="margin-top:10px;">'.$msg.'</div>';
		}
	?>

	<?php echo form_open_multipart(base_url("gastos/save"), 'onsubmit="return validar_gral()" class="validation" id="form_gasto"'); ?>

	<!-- ==================== PANEL 1: DATOS DEL DOCUMENTO ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-file-text-o"></i> Datos del Documento</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<div class="col-sm-4 col-md-3">
					<label>Fecha</label>
					<input type="datetime-local" name="date" id="date" value="<?= $fecha_val ?>" class="form-control" required="required">
				</div>
				<div class="col-sm-3 col-md-2">
					<label>Tipo Doc</label>
					<?php
						$cSql = "select id, descrip from tec_tipos_doc order by id";
						$result = $this->db->query($cSql)->result_array();
						$ar_p = array();
						$ar_p[""] = "--- Seleccione ---";
						foreach($result as $r){
							$ar_p[ $r["id"] ] = $r["descrip"];
						}
						echo form_dropdown('tipoDoc', $ar_p, $tipoDoc_val, 'class="form-control tip" id="tipoDoc" required');
					?>
				</div>
				<div class="col-sm-3 col-md-2">
					<label>Nro. Doc</label>
					<input type="text" name="nroDoc" id="nroDoc" value="<?= $nroDoc_val ?>" class="form-control" required>
				</div>
			</div>
		</div>
	</div>

	<!-- ==================== PANEL 2: DATOS DEL PROVEEDOR ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-building-o"></i> Datos del Proveedor</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<div class="col-sm-6 col-md-5">
					<label>Buscar Proveedor <small class="text-muted">(nombre o RUC)</small></label>
					<div class="proveedor-search-wrap">
						<input type="text" id="prov_search" class="form-control" placeholder="Escriba nombre o RUC del proveedor..." autocomplete="off"
							value="<?= htmlspecialchars($proveedor_nombre_edit) ?>">
						<input type="hidden" name="proveedor_id" id="proveedor_id" value="<?= $proveedor_id_val ?>">
						<div id="prov_results" class="proveedor-results"></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8 col-md-7" style="position:relative;">
					<div id="prov_card" class="prov-card" <?= !empty($proveedor_id_val) ? 'style="display:block;"' : '' ?>>
						<span class="btn-limpiar-prov" onclick="limpiarProveedor()" title="Quitar proveedor">&times;</span>
						<div class="prov-card-title" id="prov_card_nombre"><?= htmlspecialchars($proveedor_nombre_edit) ?></div>
						<div class="prov-card-detail">
							<div class="row">
								<div class="col-sm-6"><strong>RUC:</strong> <span id="prov_card_ruc"><?= htmlspecialchars($proveedor_ruc_edit) ?></span></div>
								<div class="col-sm-6"><strong>Telefono:</strong> <span id="prov_card_phone"><?= htmlspecialchars($proveedor_phone_edit) ?></span></div>
							</div>
							<div class="row">
								<div class="col-sm-6"><strong>Direccion:</strong> <span id="prov_card_direccion"><?= htmlspecialchars($proveedor_direccion_edit) ?></span></div>
								<div class="col-sm-6"><strong>Email:</strong> <span id="prov_card_correo"><?= htmlspecialchars($proveedor_correo_edit) ?></span></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- ==================== PANEL 3: ITEMS DEL GASTO ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-list-ul"></i> Items del Gasto</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<div class="col-sm-3 col-md-2">
					<label>Categoria</label>
					<select id="inp_categoria" class="form-control">
						<option value="">-- Seleccione --</option>
						<?php foreach($categorias as $cat): ?>
						<option value="<?= $cat->id ?>" data-color="<?= $cat->color ?>" data-nombre="<?= htmlspecialchars($cat->nombre) ?>"><?= htmlspecialchars($cat->nombre) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-sm-4 col-md-3">
					<label>Descripcion / Concepto</label>
					<input type="text" id="inp_descripcion" class="form-control" placeholder="Ej: Alquiler local enero 2026">
				</div>

				<div class="col-sm-2 col-md-1">
					<label>Cantidad</label>
					<input type="number" id="inp_cantidad" class="form-control" value="1" min="0.01" step="0.01">
				</div>

				<div class="col-sm-2 col-md-2">
					<label>Precio Unit.</label>
					<input type="number" id="inp_precio" class="form-control" value="0" min="0" step="0.01">
				</div>

				<div class="col-sm-1">
					<div style="margin-top:24px;">
						<button type="button" class="btn btn-success" onclick="agregar()" style="font-size:18px;font-weight:bold;" title="Agregar item">+</button>
					</div>
				</div>
			</div>

			<div class="row" style="margin-top:8px;">
				<div class="col-sm-1" style="margin-top:4px;">
					<label style="font-size:12px;">
						<input type="checkbox" id="chk_igv" name="chk_igv" checked> IGV
					</label>
				</div>
				<div class="col-sm-2">
					<label style="font-size:11px;">% IGV</label>
					<input type="number" id="igv_pct" name="por_igv" value="<?= $por_igv_val ?>" min="0" max="100" step="1" class="form-control input-sm" style="width:70px;" onchange="recalcularIGV()">
				</div>
			</div>

			<!-- Tabla items -->
			<div class="row" style="margin-top:10px;">
				<div class="col-sm-12" id="items_container">
					<p class="text-muted" style="font-style:italic;font-size:12px;">No hay items agregados. Use el formulario de arriba para agregar conceptos.</p>
				</div>
			</div>

			<!-- Redondeo -->
			<div class="row" style="margin-top:5px;">
				<div class="col-sm-2">
					<label>Redondeo</label>
					<input type="text" name="redondeo" id="redondeo" value="<?= $redondeo_val ?>" class="form-control" onchange="cargar_items()">
				</div>
			</div>

			<!-- Calculadora de Impuestos -->
			<div class="row" style="margin-top:10px;">
				<div class="col-sm-5 col-md-4">
					<div class="calc-impuestos">
						<div class="calc-row">
							<span class="calc-label">Subtotal (sin IGV):</span>
							<span class="calc-value">S/. <span id="calc_subtotal">0.00</span></span>
						</div>
						<div class="calc-row">
							<span class="calc-label">IGV (<span id="calc_igv_pct"><?= $por_igv_val ?></span>%):</span>
							<span class="calc-value">S/. <span id="calc_igv">0.00</span></span>
						</div>
						<div class="calc-row calc-total">
							<span class="calc-label">TOTAL:</span>
							<span class="calc-value">S/. <span id="calc_total">0.00</span></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- ==================== PANEL 4: INFORMACION DE PAGO ==================== -->
	<div class="panel-gasto">
		<div class="panel-heading-custom">
			<h4><i class="fa fa-credit-card"></i> Informacion de Pago</h4>
		</div>
		<div class="panel-body-custom">
			<div class="row">
				<div class="col-sm-4">
					<label>Estado de Pago</label><br>
					<label class="radio-inline" style="font-weight:normal;">
						<input type="radio" name="estado_pago" value="PAGADO" <?= $estado_pago_val == 'PAGADO' ? 'checked' : '' ?> onchange="toggleFechaVencimiento()">
						<span style="color:#28a745;font-weight:600;">Pagado</span>
					</label>
					<label class="radio-inline" style="font-weight:normal;margin-left:15px;">
						<input type="radio" name="estado_pago" value="PENDIENTE" <?= $estado_pago_val == 'PENDIENTE' ? 'checked' : '' ?> onchange="toggleFechaVencimiento()">
						<span style="color:#dc3545;font-weight:600;">Pendiente</span>
					</label>
				</div>
				<div class="col-sm-3" id="div_fecha_vencimiento" style="<?= $estado_pago_val == 'PENDIENTE' ? '' : 'display:none;' ?>">
					<label>Fecha de Vencimiento</label>
					<input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="<?= $fecha_vencimiento_val ?>" class="form-control">
				</div>
			</div>
			<div class="row" style="margin-top:12px;">
				<div class="col-sm-5">
					<label>Comprobante <small class="text-muted">(foto o PDF de factura/boleta - max 5MB)</small></label>
					<input type="file" name="comprobante_file" id="comprobante_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" onchange="previewComprobante(this)">
					<div class="file-preview" id="file_preview">
						<i class="fa fa-paperclip"></i>
						<span id="file_preview_name"></span>
						<span class="btn-remove-file" onclick="quitarComprobante()" title="Quitar archivo">&times;</span>
					</div>
				</div>
			</div>
			<div class="row" style="margin-top:12px;">
				<div class="col-sm-8">
					<label>Observaciones</label>
					<textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Notas adicionales (opcional)"><?= htmlspecialchars($observaciones_val) ?></textarea>
				</div>
			</div>
		</div>
	</div>

	<!-- Hidden fields -->
	<input type="hidden" name="id_gasto" id="id_gasto" value="<?= $id_gasto ?>">
	<input type="hidden" name="modo_edicion" id="modo_edicion" value="<?= $modo_edicion ?>">

	<!-- Botones -->
	<div class="row" style="margin-top:10px;margin-bottom:30px;">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary" style="font-size:14px;padding:8px 25px;">
				<i class="fa fa-save"></i> Guardar Gasto
			</button>
			<button type="button" class="btn btn-danger" style="margin-left:8px;" onclick="limpiarFormulario()">
				<i class="fa fa-eraser"></i> Limpiar
			</button>
			<a href="<?= base_url('gastos') ?>" class="btn btn-warning" style="margin-left:8px;">
				<i class="fa fa-arrow-left"></i> Regresar
			</a>
		</div>
	</div>

	<?php echo form_close(); ?>

</section>

<script type="text/javascript">
	var gIgv = <?= $por_igv_val ?>;
	var provSearchTimer = null;

	// ======================== PROVEEDOR AUTOCOMPLETE ========================
	document.getElementById("prov_search").addEventListener("keyup", function() {
		clearTimeout(provSearchTimer);
		var q = this.value.trim();
		if (q.length < 2) {
			document.getElementById("prov_results").style.display = 'none';
			return;
		}
		provSearchTimer = setTimeout(function() {
			$.ajax({
				url: '<?= base_url("gastos/buscar_proveedor") ?>',
				data: { q: q },
				type: 'get',
				dataType: 'json',
				success: function(data) {
					var container = document.getElementById("prov_results");
					if (data.length == 0) {
						container.innerHTML = '<div style="padding:10px;color:#999;font-size:12px;">No se encontraron proveedores</div>';
						container.style.display = 'block';
						return;
					}
					var html = '';
					for (var i = 0; i < data.length; i++) {
						var p = data[i];
						html += '<div class="prov-item" onclick=\'seleccionarProveedor(' + JSON.stringify(p) + ')\'>';
						html += '<span class="prov-nombre">' + p.nombre + '</span>';
						html += '<span class="prov-ruc">' + (p.ruc || '') + '</span>';
						html += '</div>';
					}
					container.innerHTML = html;
					container.style.display = 'block';
				}
			});
		}, 300);
	});

	document.addEventListener("click", function(e) {
		if (!e.target.closest('.proveedor-search-wrap')) {
			document.getElementById("prov_results").style.display = 'none';
		}
	});

	function seleccionarProveedor(p) {
		document.getElementById("proveedor_id").value = p.id;
		document.getElementById("prov_search").value = p.nombre;
		document.getElementById("prov_results").style.display = 'none';
		document.getElementById("prov_card_nombre").textContent = p.nombre;
		document.getElementById("prov_card_ruc").textContent = p.ruc || '-';
		document.getElementById("prov_card_phone").textContent = p.phone || '-';
		document.getElementById("prov_card_direccion").textContent = p.direccion || '-';
		document.getElementById("prov_card_correo").textContent = p.correo || '-';
		document.getElementById("prov_card").style.display = 'block';
	}

	function limpiarProveedor() {
		document.getElementById("proveedor_id").value = '';
		document.getElementById("prov_search").value = '';
		document.getElementById("prov_card").style.display = 'none';
	}

	// ======================== ESTADO DE PAGO ========================
	function toggleFechaVencimiento() {
		var pendiente = document.querySelector('input[name="estado_pago"][value="PENDIENTE"]').checked;
		document.getElementById("div_fecha_vencimiento").style.display = pendiente ? '' : 'none';
		if (!pendiente) {
			document.getElementById("fecha_vencimiento").value = '';
		}
	}

	// ======================== COMPROBANTE FILE ========================
	function previewComprobante(input) {
		if (input.files && input.files[0]) {
			var fileSize = input.files[0].size;
			if (fileSize > 5242880) {
				alert("El archivo excede el tamano maximo de 5MB.");
				input.value = '';
				return;
			}
			document.getElementById("file_preview_name").textContent = input.files[0].name;
			document.getElementById("file_preview").style.display = 'inline-block';
		}
	}

	function quitarComprobante() {
		document.getElementById("comprobante_file").value = '';
		document.getElementById("file_preview").style.display = 'none';
	}

	// ======================== IGV RECALCULO ========================
	function recalcularIGV() {
		gIgv = parseFloat(document.getElementById("igv_pct").value) || 0;
		document.getElementById("calc_igv_pct").textContent = gIgv;
		if (ar_items.length > 0) {
			cargar_items();
		}
	}

	// ======================== VALIDACIONES ========================
	function validar_gral() {
		if (ar_items.length == 0) {
			alert("Debe ingresar al menos un item");
			return false;
		}
		return true;
	}

	// ======================== ITEMS MANAGEMENT ========================
	function agregar() {
		var cat_select = document.getElementById("inp_categoria");
		var cat_id = cat_select.value;
		var descripcion = document.getElementById("inp_descripcion").value.trim();
		var cantidad = parseFloat(document.getElementById("inp_cantidad").value);
		var precio = parseFloat(document.getElementById("inp_precio").value);

		if (!cat_id) {
			alert("Debe seleccionar una categoria");
			document.getElementById("inp_categoria").focus();
			return;
		}
		if (descripcion.length == 0) {
			alert("Debe ingresar una descripcion");
			document.getElementById("inp_descripcion").focus();
			return;
		}
		if (isNaN(cantidad) || cantidad <= 0) {
			alert("La cantidad debe ser mayor a 0");
			document.getElementById("inp_cantidad").focus();
			return;
		}
		if (isNaN(precio) || precio <= 0) {
			alert("El precio debe ser mayor a 0");
			document.getElementById("inp_precio").focus();
			return;
		}

		var cat_option = cat_select.options[cat_select.selectedIndex];
		var cat_nombre = cat_option.getAttribute("data-nombre");
		var cat_color = cat_option.getAttribute("data-color");

		// Si IGV esta marcado, el precio ingresado incluye IGV -> calcular precio sin IGV
		var precio_base = precio;
		if (document.getElementById("tipoDoc").value != 'G') {
			if (document.getElementById("chk_igv").checked) {
				precio_base = precio / (1 + (gIgv / 100));
				precio_base = parseFloat(precio_base.toFixed(4));
			}
		}

		var subtotal = parseFloat((cantidad * precio_base).toFixed(2));

		ar_items.push({
			categoria_id: parseInt(cat_id),
			categoria_nombre: cat_nombre,
			categoria_color: cat_color,
			descripcion: descripcion,
			cantidad: cantidad,
			precio: precio_base,
			subtotal: subtotal
		});

		cargar_items();

		// Limpiar inputs
		document.getElementById("inp_descripcion").value = '';
		document.getElementById("inp_cantidad").value = 1;
		document.getElementById("inp_precio").value = 0;
		document.getElementById("inp_descripcion").focus();
	}

	function cargar_items() {
		var container = document.getElementById("items_container");
		if (ar_items.length == 0) {
			container.innerHTML = '<p class="text-muted" style="font-style:italic;font-size:12px;">No hay items agregados. Use el formulario de arriba para agregar conceptos.</p>';
			document.getElementById("calc_subtotal").textContent = "0.00";
			document.getElementById("calc_igv").textContent = "0.00";
			document.getElementById("calc_total").textContent = "0.00";
			return;
		}

		var gSubtotal = 0;
		var cad = '';

		cad += '<table class="table table-striped table-bordered" style="font-size:12px;">';
		cad += '<thead><tr class="active">';
		cad += '<th style="width:5%">#</th>';
		cad += '<th style="width:18%">Categoria</th>';
		cad += '<th style="width:32%">Descripcion</th>';
		cad += '<th style="width:10%;text-align:center">Cantidad</th>';
		cad += '<th style="width:13%;text-align:right">P.U. (sin IGV)</th>';
		cad += '<th style="width:14%;text-align:right">Subtotal</th>';
		cad += '<th style="width:8%;text-align:center">Accion</th>';
		cad += '</tr></thead><tbody>';

		for (var i = 0; i < ar_items.length; i++) {
			var item = ar_items[i];
			gSubtotal += item.subtotal;

			cad += '<tr>';
			cad += '<td>' + (i + 1) + '</td>';
			cad += '<td><span class="badge" style="background-color:' + item.categoria_color + ';color:#fff;padding:3px 8px;">' + item.categoria_nombre + '</span>';
			cad += '<input type="hidden" name="categoria_id[]" value="' + item.categoria_id + '"></td>';
			cad += '<td>' + escapeHtml(item.descripcion);
			cad += '<input type="hidden" name="descripcion[]" value="' + escapeHtml(item.descripcion) + '"></td>';
			cad += '<td class="text-center">' + item.cantidad.toFixed(2);
			cad += '<input type="hidden" name="cantidad[]" value="' + item.cantidad + '"></td>';
			cad += '<td class="text-right">' + item.precio.toFixed(4);
			cad += '<input type="hidden" name="precio[]" value="' + item.precio + '"></td>';
			cad += '<td class="text-right">' + item.subtotal.toFixed(2) + '</td>';
			cad += '<td class="text-center"><a href="#" onclick="quitar_item(' + i + '); return false;"><i class="fa fa-trash-o" style="color:#d9534f;font-size:14px;"></i></a></td>';
			cad += '</tr>';
		}

		cad += '</tbody></table>';
		container.innerHTML = cad;

		// Calcular totales
		var redondeo = parseFloat(document.getElementById("redondeo").value) || 0;
		var nIgv = 0;
		var gTotal = 0;

		if (document.getElementById("tipoDoc").value != 'G') {
			nIgv = gSubtotal * (gIgv / 100);
			gTotal = gSubtotal + nIgv + redondeo;
		} else {
			gTotal = gSubtotal + redondeo;
			nIgv = 0;
		}

		document.getElementById("calc_subtotal").textContent = gSubtotal.toFixed(2);
		document.getElementById("calc_igv").textContent = nIgv.toFixed(2);
		document.getElementById("calc_total").textContent = gTotal.toFixed(2);
		document.getElementById("calc_igv_pct").textContent = gIgv;
	}

	function quitar_item(index) {
		ar_items.splice(index, 1);
		cargar_items();
	}

	function escapeHtml(text) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(text));
		return div.innerHTML;
	}

	function limpiarFormulario() {
		ar_items = [];
		cargar_items();
		document.getElementById("form_gasto").reset();
		limpiarProveedor();
		document.getElementById("div_fecha_vencimiento").style.display = 'none';
		document.getElementById("file_preview").style.display = 'none';
	}
</script>
