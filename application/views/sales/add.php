<?php
	$fecha = date("Y-m-d") . "T" . date("H:i");
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

<style>
	.sale-card {
		background: #fff;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		padding: 18px;
		margin-bottom: 14px;
	}
	.sale-card-header {
		background: #f8f9fa;
		border-radius: 8px;
		padding: 14px 18px;
		margin-bottom: 14px;
		border: 1px solid #e9ecef;
	}
	.sale-label {
		font-size: 11px;
		font-weight: 600;
		color: #6c757d;
		text-transform: uppercase;
		letter-spacing: 0.4px;
		margin-bottom: 4px;
	}
	.sale-section-title {
		font-size: 13px;
		font-weight: 700;
		color: #495057;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-bottom: 10px;
		padding-bottom: 6px;
		border-bottom: 2px solid #4e73df;
		display: inline-block;
	}
	.sale-input {
		border-radius: 6px;
		font-size: 13px;
		height: 38px;
		border: 1px solid #ced4da;
		box-sizing: border-box;
	}
	.sale-input:focus {
		border-color: #4e73df;
		box-shadow: 0 0 0 2px rgba(78,115,223,0.15);
	}
	.sale-input-sm {
		height: 32px;
		font-size: 12px;
		border-radius: 5px;
	}
	.sale-btn-add {
		background: #1cc88a;
		color: #fff;
		border: none;
		border-radius: 6px;
		width: 38px;
		height: 38px;
		font-size: 20px;
		font-weight: 700;
		cursor: pointer;
		transition: background 0.2s;
		display: inline-flex; align-items: center; justify-content: center;
		box-sizing: border-box;
		line-height: 1;
		margin: 0; padding: 0;
		vertical-align: bottom;
		flex-shrink: 0;
	}
	.sale-btn-add:hover { background: #17a673; }
	.sale-btn-group {
		background: #36b9cc;
		color: #fff;
		border: none;
		border-radius: 6px;
		height: 38px;
		font-size: 12px;
		padding: 0 12px;
		cursor: pointer;
		display: inline-flex; align-items: center; gap: 4px;
		box-sizing: border-box;
		white-space: nowrap;
		margin: 0;
		vertical-align: bottom;
		flex-shrink: 0;
	}
	.sale-btn-group:hover { background: #2c9faf; }

	/* Items table */
	#taxi table { width: 100%; border-collapse: collapse; font-size: 13px; }
	#taxi table th {
		background: #f8f9fa;
		color: #495057;
		font-size: 11px;
		text-transform: uppercase;
		letter-spacing: 0.3px;
		padding: 8px 6px;
		border-bottom: 2px solid #dee2e6;
		height: auto;
	}
	#taxi table td { padding: 6px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
	#taxi table tr:hover { background: #f8f9fc; }
	#taxi .celdas_totales {
		background: #e8eaf6 !important;
		font-weight: 600;
		font-size: 13px;
		color: #333;
		height: 32px !important;
		padding: 6px !important;
	}
	#taxi input.form-control {
		font-size: 12px;
		height: 30px;
		border-radius: 4px;
		padding: 2px 6px;
	}

	/* Panel derecho sticky */
	.sale-summary-panel {
		position: sticky;
		top: 80px;
	}
	.summary-row {
		display: flex;
		justify-content: space-between;
		padding: 8px 0;
		font-size: 13px;
		color: #555;
		border-bottom: 1px solid #f0f0f0;
	}
	.summary-row.total-row {
		font-size: 18px;
		font-weight: 700;
		color: #1a1a2e;
		border-bottom: none;
		padding-top: 12px;
	}
	.sale-btn-generate {
		background: #4e73df;
		color: #fff;
		border: none;
		border-radius: 8px;
		width: 100%;
		height: 44px;
		font-size: 15px;
		font-weight: 600;
		cursor: pointer;
		transition: background 0.2s;
	}
	.sale-btn-generate:hover { background: #3a5bc7; }
	.sale-btn-cancel {
		background: #fff;
		color: #6c757d;
		border: 1px solid #ced4da;
		border-radius: 8px;
		width: 100%;
		height: 44px;
		font-size: 14px;
		cursor: pointer;
	}
	.sale-btn-cancel:hover { background: #f8f9fa; }

	/* Payment section */
	.payment-row {
		display: flex;
		gap: 8px;
		align-items: center;
		margin-bottom: 8px;
	}
	.payment-row select, .payment-row input {
		font-size: 12px;
		height: 34px;
		border-radius: 5px;
	}
	.add-payment-link {
		font-size: 12px;
		color: #4e73df;
		cursor: pointer;
		text-decoration: none;
	}
	.add-payment-link:hover { text-decoration: underline; }

	/* Autocomplete */
	.search-input { position: relative; width: 100%; }
	.search-input .autocom-box {
		padding: 0; opacity: 0; pointer-events: none;
		max-height: 250px; overflow-y: auto;
		position: absolute; top: 100%; left: 0; right: 0; z-index: 999;
		background: #fff; border: 1px solid #dee2e6; border-radius: 0 0 6px 6px;
		box-shadow: 0 6px 16px rgba(0,0,0,0.12);
	}
	.search-input.active .autocom-box { padding: 6px; opacity: 1; pointer-events: auto; }
	.autocom-box li {
		list-style: none; display: none; width: 100%; cursor: pointer;
		padding: 8px 10px; border-radius: 4px; font-size: 13px;
	}
	.search-input.active .autocom-box li { display: block; }
	.autocom-box li:hover { background: #e8eaf6; }

	/* Mode dropdown - inline with search input */
	.mode-dropdown { position: relative; display: inline-block; flex-shrink: 0; }
	.mode-dropdown-btn {
		background: #e9ecef; border: 1px solid #ced4da;
		border-radius: 0 6px 6px 0; border-left: none;
		height: 38px; width: 38px; padding: 0; margin: 0;
		font-size: 14px; font-weight: 700;
		color: #495057; cursor: pointer; white-space: nowrap;
		display: flex; align-items: center; justify-content: center;
		box-sizing: border-box;
	}
	.mode-dropdown-btn:hover { background: #dee2e6; }
	.mode-dropdown-content {
		display: none; position: absolute; right: 0; top: 100%;
		background: #fff; border: 1px solid #dee2e6; border-radius: 6px;
		box-shadow: 0 4px 12px rgba(0,0,0,0.12); z-index: 100; min-width: 140px;
	}
	.mode-dropdown:hover .mode-dropdown-content { display: block; }
	.mode-dropdown-content a {
		display: block; padding: 8px 14px; font-size: 12px; color: #333; text-decoration: none;
	}
	.mode-dropdown-content a:hover { background: #f0f2ff; color: #4e73df; }

	/* Product search row — all controls aligned to same bottom line */
	.product-search-row {
		display: flex; gap: 10px; flex-wrap: wrap;
		align-items: flex-end; /* labels push items down, controls align at bottom */
	}
	.product-search-row .psr-search { flex: 1; min-width: 200px; }
	.product-search-row .psr-price { width: 100px; }
	.product-search-row .psr-qty { width: 90px; }
	.product-search-row .psr-actions {
		display: flex; gap: 6px; flex-shrink: 0;
		height: 38px; /* same height as inputs — forces bottom alignment */
	}
	.search-input-group {
		display: flex; align-items: stretch; height: 38px;
	}
	.search-input-group .sale-input {
		border-radius: 6px 0 0 6px; flex: 1;
		height: 38px; margin: 0; padding-top: 0; padding-bottom: 0;
	}
	/* search-input wrapper: no extra margins/padding that shift the input down */
	.product-search-row .search-input { margin: 0; padding: 0; }

	/* Force all product-row inputs to exact same height, no extra margin */
	.product-search-row .sale-input {
		height: 38px; margin: 0; padding-top: 0; padding-bottom: 0;
	}
	@media (max-width: 768px) {
		.product-search-row .psr-search { min-width: 100%; }
		.product-search-row .psr-price,
		.product-search-row .psr-qty { flex: 1; min-width: 80px; }
	}

	/* --- Cliente search row (flex layout) --- */
	.cliente-search-row {
		display: flex; gap: 10px; flex-wrap: wrap;
		align-items: flex-end;
	}
	.cliente-search-row .csr-tipodoc { width: 150px; flex-shrink: 0; }
	.cliente-search-row .csr-nrodoc { width: 220px; flex-shrink: 0; }
	.cliente-search-row .csr-nombre { flex: 1; min-width: 180px; }
	.cliente-search-row .csr-ruc { width: 130px; flex-shrink: 0; }
	.cliente-search-row .sale-input {
		height: 38px; margin: 0; padding-top: 0; padding-bottom: 0;
		box-sizing: border-box;
	}
	.cliente-search-row select.sale-input {
		height: 38px; padding: 0 8px; box-sizing: border-box;
	}
	@media (max-width: 768px) {
		.cliente-search-row .csr-tipodoc,
		.cliente-search-row .csr-nrodoc { flex: 1; min-width: 140px; }
		.cliente-search-row .csr-nombre { min-width: 100%; }
		.cliente-search-row .csr-ruc { flex: 1; min-width: 100px; }
	}

	/* Nuevo Cliente link */
	.new-client-link { font-size: 12px; color: #4e73df; cursor: pointer; }
	.new-client-link:hover { text-decoration: underline; }

	/* Grid productos oculto */
	.marco-producto { margin: 2px 0; height: 160px; }
</style>

<!-- Session keep-alive -->
<script>
	document.addEventListener("DOMContentLoaded", function(){
		const milisegundos = 5 * 60 * 1000;
		setInterval(function(){ fetch("<?= base_url("refrescar.php") ?>"); }, milisegundos);
	});

	function activar_zona_cliente(){
		document.getElementById('zona_cliente').style.display = 'block';
		var tipoDoc = document.getElementById('tipoDoc').value;
		var sel = document.getElementById('tipo_doc_identidad');

		// Limpiar campos
		document.getElementById('dni_cliente').value = '';
		document.getElementById('name_cliente').value = '';
		document.getElementById('txt_cf2').value = '';
		document.getElementById('txt_customer_id').value = '';

		if(tipoDoc == '1'){
			// Factura: solo RUC
			sel.innerHTML = '<option value="RUC">RUC</option>';
			sel.value = 'RUC';
			document.getElementById('col_ruc').style.display = 'none';
			document.getElementById('lbl_nombre_cliente').innerHTML = 'Raz\u00f3n Social';
			cambiarTipoDocIdentidad();
		} else {
			// Boleta o Ticket: DNI, CE, Pasaporte
			sel.innerHTML = '<option value="DNI">DNI</option><option value="CE">Carnet Extranjer\u00eda</option><option value="PAS">Pasaporte</option>';
			sel.value = 'DNI';
			document.getElementById('col_ruc').style.display = 'none';
			document.getElementById('lbl_nombre_cliente').innerHTML = 'Nombres';
			cambiarTipoDocIdentidad();
			// Default: 00000000 / VARIOS
			document.getElementById('dni_cliente').value = '00000000';
			document.getElementById('btn_buscar').click();
		}
	}

	function cambiarTipoDocIdentidad(){
		var tipo = document.getElementById('tipo_doc_identidad').value;
		var input = document.getElementById('dni_cliente');
		var lbl = document.getElementById('lbl_nro_doc');

		// Limpiar
		input.value = '';
		document.getElementById('name_cliente').value = '';
		document.getElementById('txt_cf2').value = '';
		document.getElementById('txt_customer_id').value = '';

		if(tipo == 'DNI'){
			input.maxLength = 8;
			input.placeholder = '00000000';
			lbl.innerHTML = 'N&ordm; DNI';
		} else if(tipo == 'RUC'){
			input.maxLength = 11;
			input.placeholder = '20XXXXXXXXX';
			lbl.innerHTML = 'N&ordm; RUC';
		} else if(tipo == 'CE'){
			input.maxLength = 12;
			input.placeholder = 'Carnet extranjer\u00eda';
			lbl.innerHTML = 'N&ordm; CE';
		} else if(tipo == 'PAS'){
			input.maxLength = 12;
			input.placeholder = 'Pasaporte';
			lbl.innerHTML = 'N&ordm; Pasaporte';
		}
		input.focus();
	}
</script>

<?php
if(isset($existe_apertura)){
	if($existe_apertura){
?>

<section style="padding: 15px;">
	<div class="row">
		<!-- ========== COLUMNA IZQUIERDA (formulario principal) ========== -->
		<div class="col-md-8 col-lg-9">
			<form name="form1" id="form1" action="<?= base_url("sales/save") ?>" method="POST">
				<?php if(!empty($servicio_id)): ?>
				<input type="hidden" name="servicio_id" value="<?= $servicio_id ?>">
				<?php endif; ?>
				<input type="hidden" name="subtotal" id="subtotal">
				<input type="hidden" name="igv" id="igv">
				<input type="hidden" name="total" id="total">

				<!-- === HEADER: Comprobante === -->
				<div class="sale-card-header">
					<div class="row" style="align-items:flex-end;">
						<div class="col-sm-4 col-md-3">
							<div class="sale-label">Tipo Comprobante</div>
							<?php
								$result = $this->db->where("activo","1")->get("tec_tipos_doc")->result();
								$ar = array();
								$ar[] = "-- Seleccione --";
								foreach($result as $r){
									$ar[$r->id] = $r->descrip;
								}
								echo form_dropdown('tipoDoc',$ar,'','class="form-control sale-input" id="tipoDoc" required="required" onchange="correlativo(this);activar_zona_cliente()"');
							?>
						</div>
						<div class="col-sm-2 col-md-2">
							<div class="sale-label">Nro. Recibo</div>
							<input type="text" name="txt_recibo" class="form-control sale-input" id="txt_recibo" readonly style="background:#e9ecef; font-weight:600;">
						</div>
						<div class="col-sm-3 col-md-3">
							<div class="sale-label">Fec. Emisi&oacute;n</div>
							<input type="datetime-local" name="fecha" id="fecha" value="<?= $fecha ?>" class="form-control sale-input">
						</div>
						<div class="col-sm-2 col-md-2">
							<div class="sale-label">Precio x Mayor</div>
							<div style="padding-top:6px;">
								<input type="checkbox" name="tipo_precio" id="tipo_precio" value="1" onchange="cambiar_tipo_precios()" style="transform:scale(1.2);">
								<span style="font-size:12px; color:#555; margin-left:4px;">Activar</span>
							</div>
						</div>
					</div>
				</div>

				<!-- === CLIENTE === -->
				<div id="zona_cliente" style="display:none;">
					<div class="sale-card">
						<div class="sale-section-title">Cliente</div>
						<span class="new-client-link" style="float:right; margin-top:-28px;" id="myBtn2">[+ Nuevo]</span>
						<div class="cliente-search-row">
							<div class="csr-tipodoc">
								<div class="sale-label">Tipo Doc.</div>
								<select name="tipo_doc_identidad" id="tipo_doc_identidad" class="form-control sale-input" onchange="cambiarTipoDocIdentidad()">
									<option value="DNI">DNI</option>
									<option value="CE">Carnet Extranjer&iacute;a</option>
									<option value="PAS">Pasaporte</option>
								</select>
							</div>
							<div class="csr-nrodoc">
								<div class="sale-label" id="lbl_nro_doc">N&ordm; Documento</div>
								<div style="display:flex; gap:6px; height:38px;">
									<input type="text" name="dni_cliente" id="dni_cliente" class="form-control sale-input" style="flex:1; margin:0;" maxlength="8" placeholder="00000000">
									<button id="btn_buscar" type="button" onclick="busqueda_nombre(document.getElementById('dni_cliente'))" class="btn btn-primary" style="height:38px; border-radius:6px; font-size:12px; padding:0 14px; margin:0; flex-shrink:0;">Buscar</button>
								</div>
							</div>
							<div class="csr-nombre">
								<div class="sale-label" id="lbl_nombre_cliente">Nombres</div>
								<input type="text" name="name_cliente" class="form-control sale-input" id="name_cliente" readonly style="background:#f8f9fa; margin:0;">
								<input type="hidden" name="txt_customer_id" id="txt_customer_id">
							</div>
							<div class="csr-ruc" id="col_ruc">
								<div class="sale-label">Ruc</div>
								<input type="text" name="txt_cf2" id="txt_cf2" class="form-control sale-input" readonly style="background:#f8f9fa; margin:0;">
							</div>
						</div>
					</div>
				</div>

				<!-- === BUSQUEDA PRODUCTO === -->
				<div class="sale-card">
					<div class="sale-section-title">Producto</div>
					<div class="product-search-row">
						<div class="psr-search">
							<div class="sale-label" id="lbl_busqueda">C&oacute;digo de Barras</div>
							<div class="search-input">
								<a href="" target="_blank" hidden></a>
								<div class="search-input-group">
									<input type="text" class="form-control sale-input" name="hdn_descrip" id="hdn_descrip" placeholder="Buscar producto..." onblur="ejecutar_libre()">
									<div class="mode-dropdown">
										<button type="button" class="mode-dropdown-btn" title="Modo de b&uacute;squeda">...</button>
										<div class="mode-dropdown-content">
											<a href="#" onclick="$('#hdn_codigo').val('CODIGO');$('#lbl_busqueda').html('C&oacute;digo de Barras');return false;">C&oacute;digo de Barra</a>
											<a href="#" onclick="$('#hdn_codigo').val('PRODUCTO');$('#lbl_busqueda').html('Producto');return false;">Producto</a>
											<a href="#" onclick="$('#hdn_codigo').val('LIBRE');$('#lbl_busqueda').html('Libre');return false;">Libre</a>
										</div>
									</div>
								</div>
								<div class="autocom-box"></div>
							</div>
							<input type="hidden" name="product_id" id="product_id">
							<input type="hidden" name="category_id" id="category_id">
							<input type="hidden" name="impuesto" id="impuesto">
							<input type="hidden" name="hdn_codigo" id="hdn_codigo" value="CODIGO">
						</div>
						<div class="psr-price">
							<div class="sale-label">Precio</div>
							<input type="text" name="cost" id="cost" class="form-control sale-input" style="text-align:right;">
						</div>
						<div class="psr-qty">
							<div class="sale-label">Cantidad</div>
							<input type="text" name="quantity" id="quantity" class="form-control sale-input" style="text-align:right;">
						</div>
						<div class="psr-actions">
							<button type="button" id="boton_mas" class="sale-btn-add" onclick="Agregar()" title="Agregar producto">+</button>
							<button type="button" class="sale-btn-group" onclick="crear_grupo()" title="Agrupar items seleccionados"><i class="fas fa-link"></i> Agrupar</button>
						</div>
					</div>
				</div>

				<!-- === TABLA DE ITEMS === -->
				<div class="sale-card" style="padding:12px;">
					<div id="taxi">
						<!-- Items rendered here by JS cargar_items() -->
						<div style="text-align:center; color:#aaa; padding:30px; font-size:13px;">
							<i class="fas fa-shopping-cart" style="font-size:28px; margin-bottom:8px; display:block;"></i>
							Agregue productos a la venta
						</div>
					</div>
				</div>

			</form>
		</div>

		<!-- ========== COLUMNA DERECHA (resumen + pagos) ========== -->
		<div class="col-md-4 col-lg-3">
			<div class="sale-summary-panel">

				<!-- Resumen de Totales -->
				<div class="sale-card">
					<div class="sale-section-title">Resumen</div>
					<div class="summary-row">
						<span>OP. GRAVADA</span>
						<span>S/ <span id="gsubtotal_mirror">0.00</span></span>
					</div>
					<div class="summary-row">
						<span>IGV (18%)</span>
						<span>S/ <span id="gIgv_mirror">0.00</span></span>
					</div>
					<div class="summary-row total-row">
						<span>TOTAL</span>
						<span>S/ <span id="gTotal_mirror">0.00</span></span>
					</div>
				</div>

				<!-- Forma de Pago -->
				<div class="sale-card">
					<div class="sale-section-title">Forma de Pago</div>

					<div class="payment-row">
						<div style="flex:1;">
							<div class="sale-label">M&eacute;todo</div>
							<?php
								$result = $this->db->where("activo","1")->get("tec_forma_pagos")->result();
								$ar = array();
								$ar[] = "";
								foreach($result as $r){
									$ar[$r->forma_pago] = $r->descrip;
								}
								echo form_dropdown('forma_pago',$ar,'','class="form-control sale-input-sm" id="forma_pago" required="required" form="form1"');
							?>
						</div>
						<div style="width:90px;">
							<div class="sale-label">Monto</div>
							<input type="text" name="forma_pago_monto" id="forma_pago_monto" class="form-control sale-input-sm" style="text-align:right;" form="form1">
						</div>
					</div>

					<a href="#" class="add-payment-link" onclick="document.getElementById('div-forma_pago2').style.display='flex';return false;">
						+ Agregar segundo pago
					</a>

					<div class="payment-row" id="div-forma_pago2" style="display:none; margin-top:8px;">
						<div style="flex:1;">
							<div class="sale-label">M&eacute;todo 2</div>
							<?php
								$result = $this->db->where("activo","1")->get("tec_forma_pagos")->result();
								$ar = array();
								$ar[] = "";
								foreach($result as $r){
									$ar[$r->forma_pago] = $r->descrip;
								}
								echo form_dropdown('forma_pago2',$ar,'','class="form-control sale-input-sm" id="forma_pago2" required="required" form="form1"');
							?>
						</div>
						<div style="width:90px;">
							<div class="sale-label">Monto 2</div>
							<input type="text" name="forma_pago_monto2" id="forma_pago_monto2" class="form-control sale-input-sm" style="text-align:right;" form="form1">
						</div>
					</div>
				</div>

				<!-- Botones -->
				<div style="display:flex; gap:10px; margin-top:10px;">
					<button type="button" class="sale-btn-cancel" onclick="window.location='<?= base_url("sales") ?>'">Cancelar</button>
					<button type="button" class="sale-btn-generate" onclick="grabar_venta()">Generar</button>
				</div>

			</div>
		</div>

	</div>
</section>

<!-- Grid de productos (oculto por defecto) -->
<div class="d-none" id="grid_productos">
	<?php for($nRow=1; $nRow<=4; $nRow++){ ?>
	<div class="row">
		<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-1">
			<button id="r<?= $nRow ?>-1-btn"><div style="height:90px;width:90px;padding:3px;margin:6px;" id="r<?= $nRow ?>-1-img"></div></button><br>
			<div class="mariposa" id="r<?= $nRow ?>-1-label"></div>
		</div>
		<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-2">
			<button id="r<?= $nRow ?>-2-btn"><div style="height:90px;width:90px;padding:3px;margin:6px;" id="r<?= $nRow ?>-2-img"></div></button><br>
			<div class="mariposa" id="r<?= $nRow ?>-2-label"></div>
		</div>
		<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-3">
			<button id="r<?= $nRow ?>-3-btn"><div style="height:90px;width:90px;padding:3px;margin:6px;" id="r<?= $nRow ?>-3-img"></div></button><br>
			<div class="mariposa" id="r<?= $nRow ?>-3-label"></div>
		</div>
	</div>
	<?php } ?>
</div>

<?php
	} else {
		echo '<div class="alert alert-danger" style="margin-top:20px;">INGRESE APERTURA DE CAJA &nbsp;&nbsp;&nbsp;<a href="' . base_url("caja/aperturar_caja") . '" class="btn btn-primary">Aperturar</a></div>';
	}
} else {
	echo "INGRESE APERTURA DE CAJA";
}
?>

<!-- Modal: Ver Documento -->
<span id="myBtn"></span>
<div class="modal fade" id="pizarra" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body"><p>Some text in the modal.</p></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- ==================== JAVASCRIPT (toda la logica conservada) ==================== -->
<script>

	var gIgv = 18
	function expand(obj) { obj.size = 5; }
	function unexpand(obj) { obj.size = 1; }

	function ejecutar_libre(){
		if(document.getElementById('hdn_codigo').value == 'LIBRE'){
			let texto = $("#hdn_descrip").val()
			$('#product_id').val(99999)
			$('#quantity').val(1)
			$('#cost').focus()
			$('#impuesto').val(gIgv)
		}
	}

	$(document).ready(function(){
		$("#code").focus();
	});

	$(document).ready(function(){
		$("#myBtn").click(function(){ $("#pizarra").modal(); });
		$("#myBtn2").click(function(){
			$("#pizarra2").modal();
			var dni_cliente = document.getElementById("dni_cliente").value
			if(dni_cliente.length != 11){
				$("#cf1").val($("#dni_cliente").val())
			}else{
				$("#cf2").val($("#dni_cliente").val())
			}
		});
	})

	ar_items = new Array()
	var group_counter = 0
	var selected_for_group = []

	<?php if(!empty($servicio_items)): ?>
	<?php foreach($servicio_items as $si):
		$cost_r = $si->unit_price;
		$imp = $si->impuesto;
		$net_cost = $cost_r / (1 + ($imp / 100));
		$subtotal_si = $net_cost * $si->quantity;
	?>
	ar_items.push({
		id: '<?= $si->product_id ?>',
		name: '<?= addslashes($si->product_name) ?>',
		quantity: '<?= $si->quantity + 0 ?>',
		cost: '<?= number_format($net_cost, 4, '.', '') ?>',
		cost_r: '<?= $cost_r + 0 ?>',
		subtotal: '<?= number_format($subtotal_si, 2, '.', '') ?>',
		impuesto: '<?= $imp + 0 ?>',
		group_id: null,
		group_name: null
	});
	<?php endforeach; ?>
	<?php endif; ?>

	function cambiar_tipo_precios(){
		var prod1 = document.getElementById("product_id").value
		var opcion1 = document.getElementById("tipo_precio").checked ? '1' : '0'
		$.ajax({
			data: {product_id: prod1, opcion: opcion1},
			url: '<?= base_url("sales/obtener_tipo_precios") ?>',
			type: 'get',
			success: function(res){ document.getElementById("cost").value = res }
		})
	}

	function busqueda_nombre(obj){
		var tipoDocIdentidad = document.getElementById("tipo_doc_identidad").value;
		var dni_cliente = document.getElementById("dni_cliente").value.trim();

		// Validar longitud según tipo de documento
		if(tipoDocIdentidad == 'DNI'){
			if(dni_cliente.length != 8){
				alert("El DNI debe tener 8 d\u00edgitos. Tiene " + dni_cliente.length);
				return false;
			}
		} else if(tipoDocIdentidad == 'RUC'){
			if(dni_cliente.length != 11){
				alert("El RUC debe tener 11 d\u00edgitos. Tiene " + dni_cliente.length);
				return false;
			}
		} else if(tipoDocIdentidad == 'CE'){
			if(dni_cliente.length < 6 || dni_cliente.length > 12){
				alert("El Carnet de Extranjer\u00eda debe tener entre 6 y 12 caracteres.");
				return false;
			}
		} else if(tipoDocIdentidad == 'PAS'){
			if(dni_cliente.length < 5 || dni_cliente.length > 12){
				alert("El Pasaporte debe tener entre 5 y 12 caracteres.");
				return false;
			}
		}

		if(dni_cliente.length > 0){
			$.ajax({
				data: {dato1: dni_cliente},
				url: '<?= base_url("clientes/busqueda_nombre") ?>',
				type: "get",
				success: function(response){
					var obj = JSON.parse(response);
					if(obj.rpta){
						document.getElementById("name_cliente").value = obj.name_cliente;
						document.getElementById("txt_cf2").value = obj.cf2;
						document.getElementById("txt_customer_id").value = obj.id;
					}else{
						$("#pizarra2").modal();
						if(tipoDocIdentidad == 'RUC'){
							$("#cf2").val(dni_cliente);
						} else {
							$("#cf1").val(dni_cliente);
						}
					}
				}
			});
		}
	}

	function ejecutar_codigo_barra(){
		$.ajax({
			data: {code: $("#hdn_descrip").val()},
			type: "POST",
			url: "<?= base_url("sales/buscar_codigo") ?>",
			error: function(){ alert("error petici\u00f3n ajax"); },
			success: function(data){
				var obj = JSON.parse(data)
				for(registro in obj){
					$("#product_id").val(obj[registro]["id"])
					$("#hdn_descrip").val(obj[registro]["name"] + " [" + obj[registro]["stock"] + "]")
					$("#impuesto").val(obj[registro]["impuesto"])
				}
				busqueda_precio()
			}
		})
	}

	function busqueda_precio(){
		var datin = document.getElementById("product_id").value
		$.ajax({
			data: {dato1: datin, tipo_precio: document.getElementById('tipo_precio').checked == true ? 'por_mayor' : 'por_menor'},
			url: '<?= base_url("products/busqueda_precio") ?>',
			type: "post",
			success: function(response){
				document.getElementById("cost").value = response
				document.getElementById("quantity").value = 1
				document.getElementById("quantity").focus()
				$('#boton_mas').focus()
			}
		})
	}

	function Agregar(){
		if($("#quantity").val() <= 0 && $('#category_id').val()!='SERVICIOS'){
			alert("Cantidad no puede ser 0 negativo")
			return false
		}
		if($("#cost").val() <= 0){
			alert("Costo no puede ser negativo")
			return false
		}
		if($("#product_id").val() == ''){
			alert("Debe seleccionar un producto")
			return false
		}
		agregar_item();
		cargar_items();
		$("#quantity").val(0)
		$("#cost").val(0)
		$("#code").val("")
		$("#product_id").val("")
		document.getElementById("hdn_descrip").readOnly = false;
		$('#hdn_descrip').val("")
		$("#hdn_descrip").focus()
	}

	function cargar_items(){
		var Limite = ar_items.length
		var gsubTotal = 0
		var gTotal = 0
		var nIgv_real = 0
		var cad = ""

		cad += "<div class='table-responsive'>"
		cad += '<table id="clasico" class="table">'
		cad += '<tr>'
		cad += '<th style="width:30px;"></th>'
		cad += '<th>Producto</th>'
		cad += '<th>Obs</th>'
		cad += '<th>Series</th>'
		cad += '<th>Cant.</th>'
		cad += '<th>Costo U.</th>'
		cad += '<th>Item</th>'
		cad += '<th style="width:30px;"></th>'
		cad += '</tr>'

		var rendered_groups = {}

		for(let i=0; i<Limite; i++){
			var item = ar_items[i]

			if(item.group_id != null){
				if(!rendered_groups[item.group_id]){
					rendered_groups[item.group_id] = true
					var group_sum = 0
					var group_indices = []
					for(var j=0; j<Limite; j++){
						if(ar_items[j].group_id == item.group_id){
							group_indices.push(j)
							group_sum += parseFloat(ar_items[j].quantity) * parseFloat(ar_items[j].cost_r)
						}
					}
					cad += '<tr style="background-color:#d4edda; border-left:3px solid #28a745;">'
					cad += '<td></td>'
					cad += '<td colspan="5"><b>' + item.group_name + '</b>'
					cad += ' &nbsp;<a href="#" onclick="editar_nombre_grupo(\'' + item.group_id + '\');return false;" title="Editar nombre"><i class="fas fa-pen" style="font-size:11px;"></i></a>'
					cad += ' &nbsp;<a href="#" onclick="desagrupar(\'' + item.group_id + '\');return false;" title="Desagrupar"><i class="fas fa-unlink" style="font-size:11px;color:#dc3545;"></i></a>'
					cad += '</td>'
					cad += '<td style="text-align:right"><b>' + group_sum.toFixed(2) + '</b></td>'
					cad += '<td></td>'
					cad += '</tr>'
					for(var gi=0; gi<group_indices.length; gi++){
						cad += render_item_row(group_indices[gi], true)
					}
				}
			}else{
				cad += render_item_row(i, false)
			}

			var gParcial = 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
			gTotal += gParcial
			nIgv_real += gParcial * (ar_items[i]["impuesto"]/100)
		}

		var nDscto = cDscto = 0
		if($("#descuentos").length > 0){
			nDscto = $("#descuentos").val() * 1
			nDscto = nDscto.toFixed(2)
		}

		// Subtotal
		cad += '<tr><th class="celdas_totales"></th><th class="celdas_totales"><?= lang('subtotal'); ?></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales text-right"><span id="gsubtotal">0.00</span></th><th class="celdas_totales"></th></tr>'

		// IGV
		cad += '<tr><th class="celdas_totales"></th><th class="celdas_totales"><?= lang('igv'); ?></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales text-right"><span id="gIgv">0.00</span></th><th class="celdas_totales"></th></tr>'

		// Descuento
		if(nDscto > 0){
			cad += '<tr><th class="celdas_totales"></th><th class="celdas_totales" style="font-weight:bold">Dscto.</th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales text-right">-'+nDscto+'</th><th class="celdas_totales"></th></tr>'
		}

		// Total
		cad += '<tr><th class="celdas_totales"></th><th class="celdas_totales">Total</th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales"></th><th class="celdas_totales text-right"><span id="gTotal" style="font-size:16px;font-weight:700;">0.00</span></th><th class="celdas_totales"></th></tr>'

		cad += "</table></div>"

		document.getElementById("taxi").innerHTML = cad

		var nIgv = 0
		gsubTotal = gTotal

		var cSubTotal = gsubTotal.toFixed(2) * 1;

		if(document.getElementById("tipoDoc").value != 'G'){
			nIgv = nIgv_real;
			var cIgv = nIgv.toFixed(2) * 1;
			gTotal = (gsubTotal.toFixed(2) * 1) + (nIgv.toFixed(2) * 1) - (nDscto * 1)
			var cTotal = gTotal
		}else{
			var cSubTotal = 0.00
			var cIgv = 0.00
			var cTotal = gsubTotal.toFixed(2)
			gsubTotal = 0.00
		}

		document.getElementById("gsubtotal").innerHTML = cSubTotal
		document.getElementById("gIgv").innerHTML = cIgv
		document.getElementById("gTotal").innerHTML = cTotal.toFixed(2)

		// Actualizar panel derecho (mirrors)
		if(document.getElementById("gsubtotal_mirror")){
			document.getElementById("gsubtotal_mirror").innerHTML = parseFloat(cSubTotal).toFixed(2)
			document.getElementById("gIgv_mirror").innerHTML = parseFloat(cIgv).toFixed(2)
			document.getElementById("gTotal_mirror").innerHTML = parseFloat(cTotal).toFixed(2)
		}

		// Para la forma de pago
		document.getElementById("forma_pago_monto").value = cTotal.toFixed(2)
	}

	function render_item_row(i, is_grouped){
		var cad = ""
		var indent_style = is_grouped ? "padding-left:20px;font-size:12px;" : ""
		var bg_style = is_grouped ? "background-color:#f0f9f0;" : ""
		var is_selected = selected_for_group.indexOf(i) > -1
		var checked_attr = is_selected ? "checked" : ""

		cad += '<tr style="' + bg_style + '">'

		if(!is_grouped){
			cad += '<td><input type="checkbox" ' + checked_attr + ' onchange="toggle_select_item(' + i + ')" style="transform:scale(1.1);"></td>'
		}else{
			cad += '<td></td>'
		}

		var ctrl_descrip = '<input type="text" name="descripo[]" value="' + ar_items[i]["name"] + '" class="form-control" readonly style="' + indent_style + '">'
		cad += '<td style="text-align:left">' + ctrl_descrip
		cad += '<input type="hidden" name="item[]" value="' + ar_items[i]['id'] + '">'
		cad += '<input type="hidden" name="group_id[]" value="' + (ar_items[i]['group_id'] || '') + '">'
		cad += '<input type="hidden" name="group_name[]" value="' + (ar_items[i]['group_name'] || '') + '">'
		cad += '</td>'
		cad += '<input type="hidden" name="impuestos[]" value="' + ar_items[i]['impuesto'] + '">'
		cad += '<td><input type="text" class="form-control" name="obs[]" id="obs[]"></td>'
		cad += '<td><input type="text" class="form-control" name="series[]" id="series[]"></td>'
		cad += '<td><input size="4" style="text-align:right" type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '" class="form-control" readonly></td>'
		cad += '<td><input size="9" style="text-align:right;padding:4px 6px" type="text" name="cost[]" value="' + ar_items[i]["cost_r"] + '" class="form-control" readonly></td>'

		var nSubTotalx = ar_items[i]["subtotal"] + ""
		var n_sub_total = parseFloat(ar_items[i]["quantity"]) * parseFloat(ar_items[i]["cost_r"])
		n_sub_total = n_sub_total.toFixed(2)

		cad += '<td style="padding-right:5px;"><input type="hidden" name="sub[]" value="' + nSubTotalx + '" style="text-align:right;" class="form-control" readonly>'
		cad += '<input type="text" name="sss[]" value="' + n_sub_total + '" style="text-align:right;padding:4px 6px;" class="form-control" readonly></td>'
		cad += '<td style="text-align:center;"><a href="#" onclick="quitar_item(' + i + ')" style="color:#dc3545;" title="Eliminar"><i class="fas fa-trash-alt"></i></a></td>'
		cad += "</tr>"
		return cad
	}

	function agregar_item(){
		var x1 = document.getElementById("product_id").value
		var x1_name = quitar_hasta_letra($('#hdn_descrip').val(),'[')
		var x2 = document.getElementById("quantity").value
		var x3 = document.getElementById("cost").value
		var x3_r = document.getElementById("cost").value
		var nImp = $('#impuesto').val()

		if(document.getElementById("tipoDoc").value != 'G'){
			x3 = x3 / (1+(nImp/100))
			x3 = x3.toFixed(4)
			var x4 = 1 * x2 * x3
			x4 = x4.toFixed(2)
		}else{
			x3 = x3 * 1
			x3 = x3.toFixed(4)
			var x4 = 1 * x2 * x3
			x4 = x4.toFixed(2)
		}

		ar_items.push({id:x1, name:x1_name, quantity:x2, cost:x3, cost_r:x3_r, subtotal:x4, impuesto:nImp, group_id:null, group_name:null})
	}

	function quitar_hasta_letra(inputString, character) {
		const index = inputString.indexOf(character);
		if (index !== -1) { return inputString.substring(0, index); }
		else { return inputString; }
	}

	function quitar_item(pid){
		ar_items.splice(pid,1);
		selected_for_group = []
		cargar_items()
	}

	function toggle_select_item(index){
		var pos = selected_for_group.indexOf(index)
		if(pos > -1){ selected_for_group.splice(pos, 1) }
		else{ selected_for_group.push(index) }
		cargar_items()
	}

	function crear_grupo(){
		if(selected_for_group.length < 2){
			alert("Seleccione al menos 2 items para agrupar")
			return
		}
		group_counter++
		var gid = "g" + group_counter
		var default_name = ar_items[selected_for_group[0]]["name"]
		var group_name = prompt("Nombre del grupo:", default_name)
		if(group_name == null || group_name.trim() == "") return
		for(var k = 0; k < selected_for_group.length; k++){
			var idx = selected_for_group[k]
			ar_items[idx]["group_id"] = gid
			ar_items[idx]["group_name"] = group_name.trim()
		}
		selected_for_group = []
		cargar_items()
	}

	function desagrupar(gid){
		for(var i = 0; i < ar_items.length; i++){
			if(ar_items[i]["group_id"] == gid){
				ar_items[i]["group_id"] = null
				ar_items[i]["group_name"] = null
			}
		}
		cargar_items()
	}

	function editar_nombre_grupo(gid){
		var current_name = ""
		for(var i = 0; i < ar_items.length; i++){
			if(ar_items[i]["group_id"] == gid){ current_name = ar_items[i]["group_name"]; break }
		}
		var new_name = prompt("Nuevo nombre del grupo:", current_name)
		if(new_name == null || new_name.trim() == "") return
		for(var i = 0; i < ar_items.length; i++){
			if(ar_items[i]["group_id"] == gid){ ar_items[i]["group_name"] = new_name.trim() }
		}
		cargar_items()
	}

	function grabar_venta(){
		if(validar()){
			$("#subtotal").val( $("#gsubtotal").html() )
			$("#igv").val( $("#gIgv").html())
			$("#total").val( $("#gTotal").html() )
			document.getElementById("form1").submit()
		}
	}

	function validar(){
		fecha = $("#fecha").val()
		dni_cliente = $.trim($("#dni_cliente").val())
		name_cliente = $("#name_cliente").val()
		tipoDoc = $("#tipoDoc").val()
		forma_pago = $("#forma_pago").val()
		txt_recibo = $("#txt_recibo").val()
		tipoDocId = $("#tipo_doc_identidad").val()

		if(empty(fecha)){ mensaje("Ingrese fecha"); return false; }
		if(empty(name_cliente)){ mensaje("Ingrese nombre cliente"); return false; }
		if(empty(tipoDoc)){ mensaje("Ingrese tipo de documento"); return false; }
		if(empty(forma_pago) || forma_pago=='0'){ mensaje("Ingrese forma de pago"); return false; }
		if(empty(dni_cliente)){ mensaje("Ingrese n\u00famero de documento del cliente"); return false; }

		// Validar longitud según tipo de doc identidad
		if(tipoDocId == 'DNI' && dni_cliente.length != 8 && dni_cliente != '00000000'){
			mensaje("El DNI debe tener 8 d\u00edgitos"); return false;
		}
		if(tipoDocId == 'RUC' && dni_cliente.length != 11){
			mensaje("El RUC debe tener 11 d\u00edgitos"); return false;
		}
		if(tipoDocId == 'CE' && (dni_cliente.length < 6 || dni_cliente.length > 12)){
			mensaje("El Carnet de Extranjer\u00eda debe tener entre 6 y 12 caracteres"); return false;
		}
		if(tipoDocId == 'PAS' && (dni_cliente.length < 5 || dni_cliente.length > 12)){
			mensaje("El Pasaporte debe tener entre 5 y 12 caracteres"); return false;
		}

		if(ar_items.length == 0){ mensaje("No ha ingresado alg\u00fan producto."); return false }
		if(empty(txt_recibo)){ mensaje("No ha ingresado el Numero de Documento"); return false }
		if(tipoDoc == '1'){
			if(dni_cliente.length != 11){ mensaje("Para Factura el RUC debe tener 11 d\u00edgitos."); return false }
		}

		// Validando montos forma de pago
		$nAcu = document.getElementById("forma_pago_monto").value * 1
		if(document.getElementById("div-forma_pago2").style.display != "none"){
			forma_pago2 = document.getElementById("forma_pago2").value
			if(empty(forma_pago2) || forma_pago2=='0'){ mensaje("Ingrese segunda forma de pago"); return false; }
			$nAcu += document.getElementById("forma_pago_monto2").value*1
		}
		if($nAcu != $("#gTotal").html()*1){
			mensaje("Los monto de la forma de Pago no suman el Total")
			return false
		}
		return true
	}

	function mensaje(cad){ alert(cad) }

	function correlativo(obj){
		$.ajax({
			data: {tipo:obj.value},
			type: 'get',
			url: '<?= base_url('sales/correlativo') ?>',
			success: function(res){ document.getElementById("txt_recibo").value = res }
		})
	}

	function ver_documento(id){
		$.ajax({
			url: '<?= base_url('sales/view/') ?>' + id,
			type: 'get',
			success: function(response){
				$(".modal-body").html(response)
				document.getElementById("myBtn").click()
			}
		})
	}

	function llenar_grilla(categoria){
		$.ajax({
			data: {categoria: categoria},
			type: "post",
			url: '<?= base_url('products/mostrar') ?>',
			success: function(res){
				var obj = JSON.parse(res)
				var x = 0, y = 1
				for(registro in obj){
					x++
					if(x <= 3 && y <= 4){
						document.getElementById("r"+y+"-"+x+"-img").innerHTML = "<img src=\"../imagenes/" + obj[registro]["imagen"] + "\" style=\"width:90px;height:90px\">";
						document.getElementById("r"+y+"-"+x+"-label").innerHTML = obj[registro]["name"] + " " + obj[registro]["marca"] + " " + obj[registro]["modelo"] + " " + obj[registro]["color"]
						var la_funcion = 'escoger(' + obj[registro]["id"] + ')'
						document.getElementById("r"+y+"-"+x+"-btn").setAttribute('onclick',la_funcion)
					}else{ x = 0; y++ }
				}
			}
		})
	}

	function escoger(miId){
		$("#product_id").val(miId)
		var quantity = $("#quantity").val()
		if(empty(quantity)){ $("#quantity").val(1) }
		else{ $("#quantity").val(parseFloat($("#quantity").val()) + 1) }
		busqueda_precio(document.getElementById("product_id"))
	}

	llenar_grilla('')

	// Si hay items precargados de servicio, renderizarlos
	if(ar_items.length > 0) { cargar_items(); }

	// ---- Autocomplete logic ----
	let suggestions = [];
	const searchWrapper = document.querySelector(".search-input");
	const inputBox = searchWrapper.querySelector("input");
	const suggBox = searchWrapper.querySelector(".autocom-box");

	inputBox.onkeyup = (e)=>{
		if(document.getElementById('hdn_codigo').value == 'CODIGO'){
			if(e.key == 'Enter'){ ejecutar_codigo_barra() }
		}else if(document.getElementById('hdn_codigo').value == 'PRODUCTO'){
			let userData = e.target.value;
			$.ajax({
				data: {b: userData},
				url: '<?= base_url("sales/buscar") ?>',
				type: 'post',
				success: function(res){
					if(res.length > 0){
						let obj = JSON.parse(res)
						let emptyArray = [];
						for(let i in obj){
							let cad_stock = ""
							if(obj[i]['prod_serv'] == 'P'){ cad_stock = " [" + obj[i]['stock'] + "]" }
							emptyArray.push('<li mio="' + obj[i]['id'] + '" categoria="' + obj[i]['categoria'] + '" impuesto="' + obj[i]['impuesto'] + '">' + obj[i]['name'] + cad_stock + '</li>')
						}
						searchWrapper.classList.add("active");
						$(".autocom-box").empty()
						showSuggestions(emptyArray)
						let allList = suggBox.querySelectorAll("li");
						for(let i = 0; i < allList.length; i++){
							allList[i].setAttribute("onclick", "$('#product_id').val(this.getAttribute('mio'));$('#category_id').val(this.getAttribute('categoria'));$('#impuesto').val(this.getAttribute('impuesto'));select(this);document.getElementById('hdn_descrip').readOnly=true;");
						}
						if(!userData){ searchWrapper.classList.remove("active"); }
					}
				}
			})
		}else if(document.getElementById('hdn_codigo').value == 'LIBRE'){
			if(e.key == 'Enter'){ ejecutar_libre() }
		}
	}

	function select(element){
		let selectData = element.textContent;
		busqueda_precio()
		inputBox.value = selectData;
		searchWrapper.classList.remove("active");
	}

	function showSuggestions(list){
		suggBox.innerHTML = list.join('');
	}

</script>

<!-- Modal: Agregar Cliente -->
<?php
	$name = $cf1 = $cf2 = $phone = $email = $direccion = "";
	$cerrar = isset($cerrar) ? $cerrar : "";
?>
<div class="modal fade" id="pizarra2" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="border-radius:10px;">
			<div class="modal-header" style="border-bottom:1px solid #eee; padding:16px 20px;">
				<h5 style="margin:0; font-weight:600;">Agregar Cliente</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" style="padding:20px;">
				<div class="row">
					<div class="col-sm-5">
						<div class="sale-label">Nombre</div>
						<?= form_input('name', $name, 'class="form-control sale-input" id="name"'); ?>
					</div>
					<div class="col-sm-3">
						<div class="sale-label">DNI</div>
						<?= form_input('cf1', $cf1, 'class="form-control sale-input" id="cf1"'); ?>
					</div>
					<div class="col-sm-3">
						<div class="sale-label">RUC</div>
						<?= form_input('cf2', $cf2, 'class="form-control sale-input" id="cf2"'); ?>
					</div>
				</div>
				<div class="row" style="margin-top:12px;">
					<div class="col-sm-4">
						<div class="sale-label">Tel&eacute;fono</div>
						<?= form_input('phone', $phone, 'class="form-control sale-input" id="phone"'); ?>
					</div>
					<div class="col-sm-5">
						<div class="sale-label">Email</div>
						<?= form_input('email', $email, 'class="form-control sale-input" id="email"'); ?>
					</div>
				</div>
				<div class="row" style="margin-top:12px;">
					<div class="col-sm-12">
						<div class="sale-label">Direcci&oacute;n</div>
						<?= form_input('direccion', $direccion, 'class="form-control sale-input" id="direccion"'); ?>
						<input type="hidden" name="cerrar" value="<?= $cerrar ?>">
					</div>
				</div>
				<div style="margin-top:16px;">
					<button type="button" class="btn btn-primary" onclick="guardar_cliente()" style="border-radius:6px; padding:8px 24px;">Guardar</button>
				</div>
			</div>
			<div class="modal-footer" style="border-top:1px solid #eee; padding:12px 20px;">
				<button id="cerrar_modal2" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<script>
	function guardar_cliente(){
		if(empty(document.getElementById("name").value)){ mensaje("Ingrese Nombre"); return false; }
		var cf1 = document.getElementById("cf1").value
		var cf2 = document.getElementById("cf2").value
		if(empty(cf1) && empty(cf2)){ mensaje("Falta ingresar datos en dni o Ruc"); return false; }
		if(document.getElementById("tipoDoc").value == '1'){
			if(empty("cf2")){ mensaje("Debe ingresar el Ruc, se trata de una Factura."); return false; }
			if(cf2.length != 11 && cf2.length != 0){ mensaje("Ruc debe tener 11 caracteres"); return false; }
		}
		grabar_cliente();
		document.getElementById("cerrar_modal2").click()
	}

	function grabar_cliente(){
		$.ajax({
			data: {
				name: document.getElementById("name").value,
				cf1: document.getElementById("cf1").value,
				cf2: document.getElementById("cf2").value,
				phone: document.getElementById("phone").value,
				email: document.getElementById("email").value,
				direccion: document.getElementById("direccion").value
			},
			url: "<?= base_url("clientes/save") ?>",
			type: "get",
			success: function(response){
				console.log(response)
				document.getElementById("btn_buscar").click()
			}
		})
	}
</script>
