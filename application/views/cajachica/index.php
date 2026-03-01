<div class="row filitas">
	<div class="col-sm-12">
		<h3><i class="fa fa-money"></i> <?= $page_title ?></h3>
	</div>
</div>

<?php if(isset($msg)): ?>
<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'success' ?>"><?= $msg ?></div>
<?php endif; ?>

<?php
	$tiene_periodo = !empty($periodo);
	$saldo = $tiene_periodo ? floatval($periodo->saldo_actual) : 0;
	$monto_ini = $tiene_periodo ? floatval($periodo->monto_inicial) : 0;
	$gastado = $monto_ini - $saldo;
	$porcentaje_saldo = $monto_ini > 0 ? ($saldo / $monto_ini) * 100 : 0;

	if ($porcentaje_saldo > 50) {
		$color_barra = '#28a745';
		$estado_texto = 'Saludable';
	} elseif ($porcentaje_saldo > 20) {
		$color_barra = '#ffc107';
		$estado_texto = 'Moderado';
	} else {
		$color_barra = '#dc3545';
		$estado_texto = 'Bajo';
	}
?>

<!-- Tarjeta de Saldo -->
<?php if($tiene_periodo): ?>
<div class="row filitas">
	<div class="col-sm-12">
		<div class="card" style="border-left: 4px solid <?= $color_barra ?>;">
			<div class="card-body" style="padding:15px;">
				<div class="row">
					<div class="col text-center">
						<small class="text-muted">Fondo Inicial</small>
						<h4 style="margin:5px 0;font-weight:bold;">S/. <?= number_format($monto_ini, 2) ?></h4>
					</div>
					<div class="col text-center">
						<small class="text-muted">Total Gastado</small>
						<h4 style="margin:5px 0;font-weight:bold;color:#dc3545;">S/. <?= number_format($gastado, 2) ?></h4>
					</div>
					<div class="col text-center">
						<small class="text-muted">Saldo Disponible</small>
						<h4 style="margin:5px 0;font-weight:bold;color:<?= $color_barra ?>;">S/. <?= number_format($saldo, 2) ?></h4>
					</div>
					<?php if($total_vales_pendientes > 0): ?>
					<div class="col text-center">
						<small class="text-muted">Vales Provisionales</small>
						<h4 style="margin:5px 0;font-weight:bold;color:#ff8c00;">S/. <?= number_format($total_vales_pendientes, 2) ?></h4>
						<small class="text-muted">(<?= $cantidad_vales_pendientes ?> pendiente<?= $cantidad_vales_pendientes > 1 ? 's' : '' ?>)</small>
					</div>
					<?php endif; ?>
					<div class="col text-center">
						<small class="text-muted">Estado</small>
						<h4 style="margin:5px 0;">
							<span class="badge" style="background-color:<?= $color_barra ?>;color:#fff;font-size:13px;padding:5px 12px;"><?= $estado_texto ?></span>
						</h4>
					</div>
				</div>
				<div class="progress" style="height:8px;margin-top:10px;margin-bottom:0;">
					<div class="progress-bar" style="width:<?= $porcentaje_saldo ?>%;background-color:<?= $color_barra ?>;"></div>
				</div>
				<small class="text-muted">Aperturado: <?= date('d/m/Y H:i', strtotime($periodo->fecha_apertura)) ?> por <?= $periodo->usuario_nombre ?></small>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="row filitas">
	<div class="col-sm-12">
		<ul class="nav nav-tabs" id="cajachicaTabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-gastos" data-toggle="tab" href="#gastos" role="tab">
					<i class="fa fa-list"></i> Gastos
				</a>
			</li>
			<?php if($tiene_periodo): ?>
			<li class="nav-item">
				<a class="nav-link" id="tab-vales" data-toggle="tab" href="#vales" role="tab">
					<i class="fa fa-ticket"></i> Vales Provisionales
					<?php if($cantidad_vales_pendientes > 0): ?>
					<span class="badge" style="background-color:#ff8c00;color:#fff;margin-left:4px;"><?= $cantidad_vales_pendientes ?></span>
					<?php endif; ?>
				</a>
			</li>
			<?php endif; ?>
			<li class="nav-item">
				<a class="nav-link" id="tab-categorias" data-toggle="tab" href="#categorias" role="tab">
					<i class="fa fa-tags"></i> Categorías
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-cerradas" data-toggle="tab" href="#cerradas" role="tab">
					<i class="fa fa-archive"></i> Cajas Cerradas
				</a>
			</li>
		</ul>

		<div class="tab-content" style="padding-top:15px;">

			<!-- ============ TAB 1: GASTOS ============ -->
			<div class="tab-pane fade show active" id="gastos" role="tabpanel">

				<?php if(!$tiene_periodo): ?>
				<!-- Sin periodo abierto: Formulario de apertura -->
				<div class="panel panel-default">
					<div class="panel-heading"><h4><i class="fa fa-unlock"></i> Aperturar Caja Chica</h4></div>
					<div class="panel-body">
						<p class="text-muted">No hay una caja chica abierta. Ingrese el monto inicial para comenzar.</p>
						<div class="row">
							<div class="col-sm-3">
								<label>Monto Inicial (S/.)</label>
								<input type="number" id="monto_apertura" class="form-control" step="0.01" min="0" value="500.00">
							</div>
							<div class="col-sm-3">
								<label>&nbsp;</label><br>
								<button type="button" class="btn btn-success" onclick="aperturarCaja()">
									<i class="fa fa-check"></i> Aperturar
								</button>
							</div>
						</div>
					</div>
				</div>
				<?php else: ?>
				<!-- Con periodo abierto: Formulario de gasto + historial -->

				<!-- Formulario de Gasto -->
				<div class="panel panel-default">
					<div class="panel-heading"><h4><i class="fa fa-minus-circle"></i> Registrar Gasto</h4></div>
					<div class="panel-body">
						<form id="formGasto" enctype="multipart/form-data">
							<div class="row filitas">
								<div class="col-sm-3">
									<label>Fecha y Hora *</label>
									<input type="datetime-local" name="fecha_gasto" id="fecha_gasto" class="form-control" required>
								</div>
								<div class="col-sm-2">
									<label>Monto (S/.) *</label>
									<input type="number" name="monto" id="gasto_monto" class="form-control" step="0.01" min="0.01" required>
								</div>
								<div class="col-sm-3">
									<label>Categoría *</label>
									<select name="categoria_id" id="gasto_categoria" class="form-control" required>
										<option value="">-- Seleccione --</option>
										<?php foreach($categorias as $cat): ?>
										<option value="<?= $cat->id ?>" data-color="<?= $cat->color ?>"><?= $cat->nombre ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-sm-4">
									<label>Descripción *</label>
									<input type="text" name="descripcion" id="gasto_descripcion" class="form-control" placeholder="Ej: Pilas para el control del aire" required style="text-transform:uppercase;">
								</div>
							</div>
							<div class="row filitas">
								<div class="col-sm-3">
									<label>Beneficiario</label>
									<select name="beneficiario" id="gasto_beneficiario" class="form-control">
										<option value="">-- Seleccione --</option>
										<?php foreach($empleados as $emp): ?>
										<option value="<?= htmlspecialchars($emp['apellidos'] . ' ' . $emp['nombres']) ?>"><?= htmlspecialchars($emp['apellidos'] . ' ' . $emp['nombres']) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<label>Tipo Documento</label>
									<select name="tipo_documento" id="gasto_tipo_documento" class="form-control" onchange="toggleDocFields()">
										<option value="">-- Ninguno --</option>
										<option value="FACTURA">Factura</option>
										<option value="BOLETA">Boleta</option>
										<option value="RECIBO_HONORARIOS">Recibo por Honorarios</option>
										<option value="SIN_COMPROBANTE">Sin Comprobante</option>
									</select>
								</div>
								<div class="col-sm-2 doc-fields" style="display:none;">
									<label>Serie</label>
									<input type="text" name="doc_serie" id="gasto_doc_serie" class="form-control" placeholder="F001" style="text-transform:uppercase;" maxlength="10">
								</div>
								<div class="col-sm-2 doc-fields" style="display:none;">
									<label>Número</label>
									<input type="text" name="doc_numero" id="gasto_doc_numero" class="form-control" placeholder="00001234" maxlength="20">
								</div>
							</div>
							<div class="row filitas">
								<div class="col-sm-4">
									<label>Comprobante (foto/PDF)</label>
									<input type="file" name="comprobante" id="gasto_comprobante" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
								</div>
								<div class="col-sm-2">
									<label>&nbsp;</label><br>
									<button type="button" class="btn btn-primary" onclick="registrarGasto()">
										<i class="fa fa-save"></i> Registrar Gasto
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Historial de Gastos -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 style="display:inline-block;"><i class="fa fa-history"></i> Historial de Gastos</h4>
						<button type="button" class="btn btn-warning btn-sm pull-right" onclick="abrirRendirCuentas()" style="margin-top:-2px;">
							<i class="fa fa-calculator"></i> Rendir Cuentas
						</button>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="tabla_gastos" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha</th>
										<th>Tipo Doc.</th>
										<th>Categoría</th>
										<th>Descripción</th>
										<th>Beneficiario</th>
										<th>Monto</th>
										<th>Comp.</th>
										<th></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<?php endif; ?>

			</div>

			<!-- ============ TAB 2: VALES PROVISIONALES ============ -->
			<?php if($tiene_periodo): ?>
			<div class="tab-pane fade" id="vales" role="tabpanel">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 style="display:inline-block;"><i class="fa fa-ticket"></i> Vales Provisionales</h4>
						<button type="button" class="btn btn-warning btn-sm pull-right" onclick="abrirModalVale()" style="margin-top:-2px;">
							<i class="fa fa-plus"></i> Entrega de Efectivo
						</button>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="tabla_vales" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha</th>
										<th>Beneficiario</th>
										<th>Motivo</th>
										<th>Monto</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<!-- ============ TAB 3: CATEGORIAS ============ -->
			<div class="tab-pane fade" id="categorias" role="tabpanel">
				<div class="panel panel-default">
					<div class="panel-heading"><h4><i class="fa fa-tags"></i> Administrar Categorías de Gasto</h4></div>
					<div class="panel-body">
						<!-- Formulario categoria -->
						<form id="formCategoria">
							<input type="hidden" name="id" id="cat_id" value="0">
							<div class="row filitas">
								<div class="col-sm-4">
									<label>Nombre *</label>
									<input type="text" name="nombre" id="cat_nombre" class="form-control" required style="text-transform:uppercase;">
								</div>
								<div class="col-sm-2">
									<label>Color</label>
									<input type="color" name="color" id="cat_color" class="form-control" value="#6c757d" style="height:38px;padding:3px;">
								</div>
								<div class="col-sm-2">
									<label>Orden</label>
									<input type="number" name="orden" id="cat_orden" class="form-control" value="0" min="0">
								</div>
								<div class="col-sm-4">
									<label>&nbsp;</label><br>
									<button type="button" class="btn btn-success" onclick="guardarCategoria()">
										<i class="fa fa-save"></i> Guardar
									</button>
									<button type="button" class="btn btn-default" onclick="limpiarFormCategoria()">
										<i class="fa fa-eraser"></i> Limpiar
									</button>
								</div>
							</div>
						</form>

						<hr>

						<!-- Tabla categorias -->
						<table class="table table-bordered table-condensed" id="tabla_categorias">
							<thead>
								<tr style="background-color:#f7f7f7;">
									<th style="width:50px;">#</th>
									<th style="width:50px;">Color</th>
									<th>Nombre</th>
									<th style="width:70px;">Orden</th>
									<th style="width:80px;">Estado</th>
									<th style="width:100px;">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php $nc = 0; foreach($todas_categorias as $cat): $nc++; ?>
								<tr id="cat_row_<?= $cat->id ?>">
									<td class="text-center"><?= $nc ?></td>
									<td class="text-center"><div style="width:24px;height:24px;border-radius:4px;background-color:<?= $cat->color ?>;margin:0 auto;"></div></td>
									<td><?= $cat->nombre ?></td>
									<td class="text-center"><?= $cat->orden ?></td>
									<td class="text-center">
										<?php if($cat->activo == '1'): ?>
										<span class="badge" style="background-color:#28a745;color:#fff;">Activo</span>
										<?php else: ?>
										<span class="badge" style="background-color:#999;color:#fff;">Inactivo</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<?php if($cat->activo == '1'): ?>
										<button onclick="editarCategoria(<?= $cat->id ?>, '<?= addslashes($cat->nombre) ?>', '<?= $cat->color ?>', <?= $cat->orden ?>)" class="btn btn-xs btn-info" title="Editar"><i class="fa fa-edit"></i></button>
										<button onclick="eliminarCategoria(<?= $cat->id ?>)" class="btn btn-xs btn-danger" title="Eliminar"><i class="fa fa-trash"></i></button>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- ============ TAB 4: CAJAS CERRADAS ============ -->
			<div class="tab-pane fade" id="cerradas" role="tabpanel">
				<div class="panel panel-default">
					<div class="panel-heading"><h4><i class="fa fa-archive"></i> Historial de Cajas Cerradas</h4></div>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="tabla_cerradas" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha Apertura</th>
										<th>Fecha Cierre</th>
										<th>Monto Inicial</th>
										<th>Total Gastos</th>
										<th>Saldo Final</th>
										<th>Usuario</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div><!-- /tab-content -->
	</div>
</div>

<!-- Modal Rendir Cuentas -->
<?php if($tiene_periodo): ?>
<div class="modal fade" id="modalRendirCuentas" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-calculator"></i> Rendir Cuentas - Cierre de Caja Chica</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id="resumen_loading" class="text-center" style="padding:20px;">
					<i class="fa fa-spinner fa-spin fa-2x"></i> Cargando resumen...
				</div>
				<div id="resumen_content" style="display:none;">
					<h5>Resumen de Gastos por Categoría</h5>
					<table class="table table-bordered" id="tabla_resumen">
						<thead>
							<tr style="background-color:#f7f7f7;">
								<th>Categoría</th>
								<th class="text-center" style="width:100px;">Nro. Gastos</th>
								<th class="text-right" style="width:120px;">Total</th>
							</tr>
						</thead>
						<tbody id="tbody_resumen"></tbody>
						<tfoot>
							<tr style="font-weight:bold;background-color:#f0f0f0;">
								<td>TOTAL GASTADO</td>
								<td></td>
								<td class="text-right" id="td_total_gastado"></td>
							</tr>
							<tr style="font-weight:bold;font-size:15px;">
								<td colspan="2">SALDO TEÓRICO (dinero en el sobre)</td>
								<td class="text-right" id="td_saldo_teorico" style="color:#28a745;"></td>
							</tr>
						</tfoot>
					</table>

					<div class="form-group" style="margin-top:15px;">
						<label>Observaciones (opcional)</label>
						<textarea id="rendir_observaciones" class="form-control" rows="2" placeholder="Comentarios sobre esta rendición..."></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-warning" onclick="confirmarRendicion()" id="btn_confirmar_rendicion">
					<i class="fa fa-check"></i> Confirmar Rendición
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Registrar Vale Provisional -->
<div class="modal fade" id="modalRegistrarVale" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-ticket"></i> Entrega de Efectivo - Vale Provisional</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-info" style="font-size:12px;">
					<i class="fa fa-info-circle"></i> El monto será descontado del saldo de caja. El beneficiario deberá rendir con comprobantes.
				</div>
				<form id="formVale">
					<div class="form-group">
						<label>Monto a entregar (S/.) *</label>
						<input type="number" name="monto" id="vale_monto" class="form-control" step="0.01" min="0.01" required>
						<small class="text-muted">Saldo disponible: S/. <span id="vale_saldo_disp"><?= number_format($saldo, 2) ?></span></small>
					</div>
					<div class="form-group">
						<label>Beneficiario *</label>
						<select name="beneficiario" id="vale_beneficiario" class="form-control" required>
							<option value="">-- Seleccione empleado --</option>
							<?php foreach($empleados as $emp): ?>
							<option value="<?= htmlspecialchars($emp['apellidos'] . ' ' . $emp['nombres']) ?>"><?= htmlspecialchars($emp['apellidos'] . ' ' . $emp['nombres']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Motivo *</label>
						<textarea name="motivo" id="vale_motivo" class="form-control" rows="2" placeholder="Para qué se entrega el efectivo" required style="text-transform:uppercase;"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-warning" onclick="registrarVale()">
					<i class="fa fa-check"></i> Registrar y Imprimir Vale
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Liquidar Vale -->
<div class="modal fade" id="modalLiquidarVale" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-check-circle"></i> Liquidar Vale Provisional</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="liq_vale_id" value="">
				<!-- Info del vale -->
				<div class="row" style="margin-bottom:15px;background:#f7f7f7;padding:10px;border-radius:4px;">
					<div class="col-sm-4">
						<strong>Beneficiario:</strong><br><span id="liq_beneficiario"></span>
					</div>
					<div class="col-sm-4">
						<strong>Monto Entregado:</strong><br><span id="liq_monto_entregado" style="font-size:16px;font-weight:bold;color:#dc3545;"></span>
					</div>
					<div class="col-sm-4">
						<strong>Fecha Entrega:</strong><br><span id="liq_fecha"></span>
					</div>
				</div>

				<h5>Gastos a registrar:</h5>
				<div id="liq_gastos_container">
					<!-- Filas de gastos dinámicas -->
				</div>
				<button type="button" class="btn btn-sm btn-info" onclick="agregarFilaLiquidacion()" style="margin-top:5px;">
					<i class="fa fa-plus"></i> Agregar otro gasto
				</button>

				<hr>
				<div class="row">
					<div class="col-sm-4">
						<strong>Total Gastado:</strong>
						<span id="liq_total_gastado" style="font-size:15px;font-weight:bold;"> S/. 0.00</span>
					</div>
					<div class="col-sm-4">
						<strong>Monto Devuelto:</strong>
						<span id="liq_monto_devuelto" style="font-size:15px;font-weight:bold;color:#28a745;"> S/. 0.00</span>
					</div>
					<div class="col-sm-4">
						<strong>Diferencia:</strong>
						<span id="liq_diferencia" style="font-size:15px;font-weight:bold;"> S/. 0.00</span>
					</div>
				</div>
				<div class="form-group" style="margin-top:10px;">
					<label>Observaciones</label>
					<textarea id="liq_observaciones" class="form-control" rows="2" placeholder="Observaciones de la liquidación..."></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success" onclick="confirmarLiquidacion()">
					<i class="fa fa-check"></i> Confirmar Liquidación
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Ver Vale -->
<div class="modal fade" id="modalVerVale" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-eye"></i> Detalle del Vale</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" id="ver_vale_body">
				<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<script>
<?php if($tiene_periodo): ?>
// ======= DATATABLES =======
var tablaGastos, tablaVales;
var liqFilaCount = 0;
var liqMontoEntregado = 0;

$(document).ready(function() {
	// Prefill fecha con ahora
	var ahora = new Date();
	var y = ahora.getFullYear();
	var m = String(ahora.getMonth()+1).padStart(2,'0');
	var d = String(ahora.getDate()).padStart(2,'0');
	var h = String(ahora.getHours()).padStart(2,'0');
	var mi = String(ahora.getMinutes()).padStart(2,'0');
	$('#fecha_gasto').val(y+'-'+m+'-'+d+'T'+h+':'+mi);

	tablaGastos = $('#tabla_gastos').DataTable({
		"ajax": {
			"url": "<?= base_url('cajachica/getGastos') ?>",
			"type": "post",
			"dataSrc": "data"
		},
		"columns": [
			{ "data": "num", "className": "text-center", "width": "40px" },
			{ "data": "fecha_fmt", "width": "120px" },
			{ "data": "tipo_doc_badge", "className": "text-center" },
			{ "data": "categoria_badge" },
			{ "data": "descripcion" },
			{ "data": "beneficiario" },
			{ "data": "monto_fmt", "className": "text-right" },
			{ "data": "comprobante_link", "className": "text-center", "width": "50px", "orderable": false },
			{ "data": "acciones", "className": "text-center", "width": "80px", "orderable": false, "searchable": false }
		],
		"order": [[ 0, "asc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
		},
		"pageLength": 25,
		"dom": 'frtip'
	});

	// DataTable de Vales
	tablaVales = $('#tabla_vales').DataTable({
		"ajax": {
			"url": "<?= base_url('cajachica/getValesPendientes') ?>",
			"type": "post",
			"dataSrc": "data"
		},
		"columns": [
			{ "data": "num", "className": "text-center", "width": "40px" },
			{ "data": "fecha_fmt", "width": "130px" },
			{ "data": "beneficiario" },
			{ "data": "motivo" },
			{ "data": "monto_fmt", "className": "text-right" },
			{ "data": "estado_badge", "className": "text-center" },
			{ "data": "acciones", "className": "text-center", "width": "150px", "orderable": false, "searchable": false }
		],
		"order": [[ 0, "desc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
		},
		"pageLength": 25,
		"dom": 'frtip'
	});
});

// ======= TOGGLE TIPO DOCUMENTO FIELDS =======
function toggleDocFields() {
	var tipo = $('#gasto_tipo_documento').val();
	if (tipo == 'FACTURA' || tipo == 'BOLETA') {
		$('.doc-fields').show();
	} else {
		$('.doc-fields').hide();
		$('#gasto_doc_serie').val('');
		$('#gasto_doc_numero').val('');
	}
}

// ======= REGISTRAR GASTO =======
function registrarGasto() {
	var monto = parseFloat($('#gasto_monto').val());
	var saldo = <?= $saldo ?>;
	var tipoDoc = $('#gasto_tipo_documento').val();

	if (!$('#gasto_categoria').val()) {
		Swal.fire('Error', 'Seleccione una categoría.', 'error');
		return;
	}
	if (isNaN(monto) || monto <= 0) {
		Swal.fire('Error', 'Ingrese un monto válido mayor a cero.', 'error');
		return;
	}
	if (!$('#gasto_descripcion').val().trim()) {
		Swal.fire('Error', 'La descripción es obligatoria.', 'error');
		return;
	}
	if (!$('#gasto_beneficiario').val()) {
		Swal.fire('Error', 'Seleccione un beneficiario.', 'error');
		return;
	}
	if (!tipoDoc) {
		Swal.fire('Error', 'Seleccione un tipo de documento.', 'error');
		return;
	}
	if ((tipoDoc == 'FACTURA' || tipoDoc == 'BOLETA') && !$('#gasto_doc_serie').val().trim()) {
		Swal.fire('Error', 'La serie es obligatoria para ' + tipoDoc + '.', 'error');
		return;
	}
	if ((tipoDoc == 'FACTURA' || tipoDoc == 'BOLETA') && !$('#gasto_doc_numero').val().trim()) {
		Swal.fire('Error', 'El número es obligatorio para ' + tipoDoc + '.', 'error');
		return;
	}
	if ((tipoDoc == 'FACTURA' || tipoDoc == 'BOLETA' || tipoDoc == 'RECIBO_HONORARIOS') && !$('#gasto_comprobante').val()) {
		Swal.fire('Error', 'Debe adjuntar el comprobante (foto/PDF) para ' + tipoDoc + '.', 'error');
		return;
	}
	if (monto > saldo) {
		Swal.fire('Sin Saldo', 'El monto (S/. ' + monto.toFixed(2) + ') excede el saldo disponible (S/. ' + saldo.toFixed(2) + ').', 'error');
		return;
	}

	var formData = new FormData($('#formGasto')[0]);

	Swal.fire({
		title: 'Registrando gasto...',
		allowOutsideClick: false,
		didOpen: function() { Swal.showLoading(); }
	});

	$.ajax({
		url: '<?= base_url("cajachica/registrar_gasto") ?>',
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function(r) {
			if (r.rpta == 'success') {
				// Si es SIN_COMPROBANTE, abrir impresion de Vale de Egreso
				if (r.tipo_documento == 'SIN_COMPROBANTE' && r.gasto_id) {
					window.open('<?= base_url("cajachica/imprimir_vale_egreso") ?>/' + r.gasto_id, 'vale_egreso_' + r.gasto_id, 'width=400,height=600,scrollbars=yes');
				}
				Swal.fire('Registrado', r.msg, 'success').then(function() {
					location.reload();
				});
			} else {
				Swal.fire('Error', r.msg, 'error');
			}
		},
		error: function() {
			Swal.fire('Error', 'Error de conexión al servidor.', 'error');
		}
	});
}

// ======= ELIMINAR GASTO =======
function eliminarGasto(id) {
	Swal.fire({
		title: '¿Eliminar este gasto?',
		text: 'El saldo será restaurado.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Sí, eliminar',
		cancelButtonText: 'Cancelar'
	}).then(function(result) {
		if (result.isConfirmed) {
			$.post('<?= base_url("cajachica/eliminar_gasto") ?>', { id: id }, function(r) {
				if (r.rpta == 'success') {
					Swal.fire('Eliminado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

// ======= IMPRIMIR VALE DE EGRESO =======
function imprimirValeEgreso(gastoId) {
	window.open('<?= base_url("cajachica/imprimir_vale_egreso") ?>/' + gastoId, 'vale_egreso_' + gastoId, 'width=400,height=600,scrollbars=yes');
}

// ======= RENDIR CUENTAS =======
function abrirRendirCuentas() {
	$('#resumen_loading').show();
	$('#resumen_content').hide();
	$('#modalRendirCuentas').modal('show');

	$.getJSON('<?= base_url("cajachica/resumen_periodo/" . $periodo->id) ?>', function(data) {
		var html = '';
		for (var i = 0; i < data.resumen.length; i++) {
			var r = data.resumen[i];
			html += '<tr>';
			html += '<td><span class="badge" style="background-color:' + r.color + ';color:#fff;padding:4px 8px;">' + r.nombre + '</span></td>';
			html += '<td class="text-center">' + r.num_gastos + '</td>';
			html += '<td class="text-right">S/. ' + parseFloat(r.total).toFixed(2) + '</td>';
			html += '</tr>';
		}

		if (data.resumen.length == 0) {
			html = '<tr><td colspan="3" class="text-center text-muted">No hay gastos registrados</td></tr>';
		}

		$('#tbody_resumen').html(html);
		$('#td_total_gastado').text('S/. ' + parseFloat(data.total_gastado).toFixed(2));
		$('#td_saldo_teorico').text('S/. ' + parseFloat(data.saldo_teorico).toFixed(2));

		$('#resumen_loading').hide();
		$('#resumen_content').show();
	});
}

function confirmarRendicion() {
	Swal.fire({
		title: '¿Confirmar Rendición?',
		text: 'Se cerrará el periodo actual de Caja Chica. Los gastos pasarán al historial de Cajas Cerradas.',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Sí, cerrar periodo',
		cancelButtonText: 'Cancelar'
	}).then(function(result) {
		if (result.isConfirmed) {
			var obs = $('#rendir_observaciones').val();
			$.post('<?= base_url("cajachica/rendir_cuentas") ?>', { observaciones: obs }, function(r) {
				if (r.rpta == 'success') {
					$('#modalRendirCuentas').modal('hide');
					Swal.fire('Cerrado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

// ======= VALES PROVISIONALES =======
function abrirModalVale() {
	$('#formVale')[0].reset();
	$('#modalRegistrarVale').modal('show');
}

function registrarVale() {
	var monto = parseFloat($('#vale_monto').val());
	var saldo = <?= $saldo ?>;

	if (isNaN(monto) || monto <= 0) {
		Swal.fire('Error', 'Ingrese un monto válido mayor a cero.', 'error');
		return;
	}
	if (!$('#vale_beneficiario').val().trim()) {
		Swal.fire('Error', 'El beneficiario es obligatorio.', 'error');
		return;
	}
	if (!$('#vale_motivo').val().trim()) {
		Swal.fire('Error', 'El motivo es obligatorio.', 'error');
		return;
	}
	if (monto > saldo) {
		Swal.fire('Sin Saldo', 'El monto (S/. ' + monto.toFixed(2) + ') excede el saldo disponible (S/. ' + saldo.toFixed(2) + ').', 'error');
		return;
	}

	Swal.fire({
		title: 'Registrando vale...',
		allowOutsideClick: false,
		didOpen: function() { Swal.showLoading(); }
	});

	$.post('<?= base_url("cajachica/registrar_vale") ?>', $('#formVale').serialize(), function(r) {
		if (r.rpta == 'success') {
			$('#modalRegistrarVale').modal('hide');
			// Abrir impresion del vale
			if (r.vale_id) {
				window.open('<?= base_url("cajachica/imprimir_vale") ?>/' + r.vale_id, 'vale_' + r.vale_id, 'width=400,height=600,scrollbars=yes');
			}
			Swal.fire('Registrado', r.msg, 'success').then(function() {
				location.reload();
			});
		} else {
			Swal.fire('Error', r.msg, 'error');
		}
	}, 'json');
}

function imprimirVale(valeId) {
	window.open('<?= base_url("cajachica/imprimir_vale") ?>/' + valeId, 'vale_' + valeId, 'width=400,height=600,scrollbars=yes');
}

// ======= LIQUIDAR VALE =======
function abrirLiquidarVale(valeId) {
	$('#liq_vale_id').val(valeId);
	$('#liq_gastos_container').html('');
	$('#liq_observaciones').val('');
	liqFilaCount = 0;

	// Obtener datos del vale
	$.getJSON('<?= base_url("cajachica/get_vale_data") ?>?id=' + valeId, function(data) {
		if (!data.vale) {
			Swal.fire('Error', 'No se encontró el vale.', 'error');
			return;
		}
		$('#liq_beneficiario').text(data.vale.beneficiario);
		$('#liq_monto_entregado').text('S/. ' + parseFloat(data.vale.monto).toFixed(2));
		$('#liq_fecha').text(data.vale.fecha_entrega_fmt);
		liqMontoEntregado = parseFloat(data.vale.monto);

		// Agregar primera fila
		agregarFilaLiquidacion();

		$('#modalLiquidarVale').modal('show');
	});
}

function agregarFilaLiquidacion() {
	liqFilaCount++;
	var n = liqFilaCount;
	var catOptions = '<option value="">-- Cat --</option>';
	<?php foreach($categorias as $cat): ?>
	catOptions += '<option value="<?= $cat->id ?>"><?= addslashes($cat->nombre) ?></option>';
	<?php endforeach; ?>

	var html = '<div class="liq-fila" id="liq_fila_' + n + '" style="border:1px solid #ddd;padding:10px;margin-bottom:8px;border-radius:4px;background:#fafafa;">';
	html += '<div class="row">';
	html += '<div class="col-sm-2"><label>Monto *</label><input type="number" class="form-control liq-monto" name="liq_monto[]" step="0.01" min="0.01" onchange="calcularTotalesLiq()" onkeyup="calcularTotalesLiq()"></div>';
	html += '<div class="col-sm-2"><label>Tipo Doc.</label><select class="form-control liq-tipo-doc" name="liq_tipo_documento[]" onchange="toggleLiqDocFields(this)"><option value="">Ninguno</option><option value="FACTURA">Factura</option><option value="BOLETA">Boleta</option><option value="RECIBO_HONORARIOS">Rec. Hon.</option><option value="SIN_COMPROBANTE">Sin Comp.</option></select></div>';
	html += '<div class="col-sm-1 liq-doc-extra" style="display:none;"><label>Serie</label><input type="text" class="form-control" name="liq_doc_serie[]" maxlength="10" style="text-transform:uppercase;"></div>';
	html += '<div class="col-sm-1 liq-doc-extra" style="display:none;"><label>Número</label><input type="text" class="form-control" name="liq_doc_numero[]" maxlength="20"></div>';
	html += '<div class="col-sm-2"><label>Categoría *</label><select class="form-control" name="liq_categoria_id[]" required>' + catOptions + '</select></div>';
	html += '<div class="col-sm-3"><label>Descripción *</label><input type="text" class="form-control" name="liq_descripcion[]" style="text-transform:uppercase;"></div>';
	if (n > 1) {
		html += '<div class="col-sm-1" style="padding-top:25px;"><button type="button" class="btn btn-xs btn-danger" onclick="removerFilaLiq(' + n + ')"><i class="fa fa-times"></i></button></div>';
	}
	html += '</div>';
	html += '</div>';

	$('#liq_gastos_container').append(html);
}

function removerFilaLiq(n) {
	$('#liq_fila_' + n).remove();
	calcularTotalesLiq();
}

function toggleLiqDocFields(sel) {
	var fila = $(sel).closest('.liq-fila');
	var tipo = $(sel).val();
	if (tipo == 'FACTURA' || tipo == 'BOLETA') {
		fila.find('.liq-doc-extra').show();
	} else {
		fila.find('.liq-doc-extra').hide().find('input').val('');
	}
}

function calcularTotalesLiq() {
	var total = 0;
	$('.liq-monto').each(function() {
		var v = parseFloat($(this).val());
		if (!isNaN(v)) total += v;
	});
	var devuelto = liqMontoEntregado - total;
	$('#liq_total_gastado').text(' S/. ' + total.toFixed(2));
	$('#liq_monto_devuelto').text(' S/. ' + (devuelto > 0 ? devuelto.toFixed(2) : '0.00'));
	var diff = liqMontoEntregado - total - (devuelto > 0 ? devuelto : 0);
	$('#liq_diferencia').text(' S/. ' + diff.toFixed(2));

	if (total > liqMontoEntregado) {
		$('#liq_total_gastado').css('color', '#dc3545');
	} else {
		$('#liq_total_gastado').css('color', '');
	}
}

function confirmarLiquidacion() {
	var valeId = $('#liq_vale_id').val();
	var totalGastado = 0;
	var valid = true;

	$('.liq-fila').each(function() {
		var m = parseFloat($(this).find('.liq-monto').val());
		var cat = $(this).find('select[name="liq_categoria_id[]"]').val();
		var desc = $(this).find('input[name="liq_descripcion[]"]').val();
		if (isNaN(m) || m <= 0) valid = false;
		if (!cat) valid = false;
		if (!desc || !desc.trim()) valid = false;
		totalGastado += (isNaN(m) ? 0 : m);
	});

	if (!valid) {
		Swal.fire('Error', 'Complete todos los campos obligatorios (monto, categoría, descripción) en cada gasto.', 'error');
		return;
	}

	if (totalGastado > liqMontoEntregado) {
		Swal.fire('Error', 'El total gastado (S/. ' + totalGastado.toFixed(2) + ') no puede exceder el monto entregado (S/. ' + liqMontoEntregado.toFixed(2) + ').', 'error');
		return;
	}

	var devuelto = liqMontoEntregado - totalGastado;

	Swal.fire({
		title: '¿Confirmar Liquidación?',
		html: 'Total gastado: <b>S/. ' + totalGastado.toFixed(2) + '</b><br>Devuelto a caja: <b>S/. ' + devuelto.toFixed(2) + '</b>',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Sí, liquidar',
		cancelButtonText: 'Cancelar'
	}).then(function(result) {
		if (result.isConfirmed) {
			Swal.fire({
				title: 'Liquidando vale...',
				allowOutsideClick: false,
				didOpen: function() { Swal.showLoading(); }
			});

			// Recolectar datos - incluir campos ocultos para filas sin serie/numero
			var formData = {
				vale_id: valeId,
				monto_devuelto: devuelto.toFixed(2),
				observaciones: $('#liq_observaciones').val(),
				'liq_monto[]': [],
				'liq_tipo_documento[]': [],
				'liq_doc_serie[]': [],
				'liq_doc_numero[]': [],
				'liq_categoria_id[]': [],
				'liq_descripcion[]': []
			};

			$('.liq-fila').each(function() {
				formData['liq_monto[]'].push($(this).find('.liq-monto').val());
				formData['liq_tipo_documento[]'].push($(this).find('.liq-tipo-doc').val());
				formData['liq_doc_serie[]'].push($(this).find('input[name="liq_doc_serie[]"]').val() || '');
				formData['liq_doc_numero[]'].push($(this).find('input[name="liq_doc_numero[]"]').val() || '');
				formData['liq_categoria_id[]'].push($(this).find('select[name="liq_categoria_id[]"]').val());
				formData['liq_descripcion[]'].push($(this).find('input[name="liq_descripcion[]"]').val());
			});

			$.post('<?= base_url("cajachica/liquidar_vale") ?>', $.param(formData, true), function(r) {
				if (r.rpta == 'success') {
					$('#modalLiquidarVale').modal('hide');
					Swal.fire('Liquidado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

// ======= ANULAR VALE =======
function anularVale(valeId) {
	Swal.fire({
		title: '¿Anular este vale?',
		html: 'El monto será devuelto al saldo de caja chica.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Sí, anular',
		cancelButtonText: 'Cancelar',
		input: 'text',
		inputLabel: 'Motivo de anulación (opcional)',
		inputPlaceholder: 'Ej: No se realizó la compra'
	}).then(function(result) {
		if (result.isConfirmed) {
			$.post('<?= base_url("cajachica/anular_vale") ?>', {
				vale_id: valeId,
				observaciones: result.value || ''
			}, function(r) {
				if (r.rpta == 'success') {
					Swal.fire('Anulado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

// ======= VER VALE =======
function verVale(valeId) {
	$('#ver_vale_body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
	$('#modalVerVale').modal('show');

	$.getJSON('<?= base_url("cajachica/get_vale_data") ?>?id=' + valeId, function(data) {
		if (!data.vale) {
			$('#ver_vale_body').html('<div class="alert alert-danger">No se encontró el vale.</div>');
			return;
		}
		var v = data.vale;
		var html = '<table class="table table-bordered table-condensed" style="font-size:13px;">';
		html += '<tr><td><strong>Beneficiario</strong></td><td>' + v.beneficiario + '</td></tr>';
		html += '<tr><td><strong>Motivo</strong></td><td>' + v.motivo + '</td></tr>';
		html += '<tr><td><strong>Monto Entregado</strong></td><td><strong>S/. ' + parseFloat(v.monto).toFixed(2) + '</strong></td></tr>';
		html += '<tr><td><strong>Fecha Entrega</strong></td><td>' + v.fecha_entrega_fmt + '</td></tr>';
		html += '<tr><td><strong>Estado</strong></td><td>' + v.estado + '</td></tr>';
		html += '<tr><td><strong>Entregado por</strong></td><td>' + (v.usuario_nombre || '') + '</td></tr>';

		if (v.estado == 'LIQUIDADO') {
			html += '<tr><td><strong>Monto Gastado</strong></td><td>S/. ' + parseFloat(v.monto_gastado).toFixed(2) + '</td></tr>';
			html += '<tr><td><strong>Monto Devuelto</strong></td><td>S/. ' + parseFloat(v.monto_devuelto).toFixed(2) + '</td></tr>';
			html += '<tr><td><strong>Fecha Liquidación</strong></td><td>' + (v.fecha_liquidacion || '') + '</td></tr>';
			html += '<tr><td><strong>Liquidado por</strong></td><td>' + (v.usuario_liquidacion_nombre || '') + '</td></tr>';
		}
		if (v.observaciones) {
			html += '<tr><td><strong>Observaciones</strong></td><td>' + v.observaciones + '</td></tr>';
		}
		html += '</table>';

		// Gastos vinculados
		if (data.gastos && data.gastos.length > 0) {
			html += '<h5 style="margin-top:10px;">Gastos registrados:</h5>';
			html += '<table class="table table-bordered table-condensed" style="font-size:12px;">';
			html += '<thead><tr style="background:#f7f7f7;"><th>Categoría</th><th>Descripción</th><th class="text-right">Monto</th></tr></thead><tbody>';
			for (var i = 0; i < data.gastos.length; i++) {
				var g = data.gastos[i];
				html += '<tr><td>' + g.categoria + '</td><td>' + g.descripcion + '</td><td class="text-right">S/. ' + parseFloat(g.monto).toFixed(2) + '</td></tr>';
			}
			html += '</tbody></table>';
		}

		$('#ver_vale_body').html(html);
	});
}
<?php endif; ?>

// ======= APERTURAR CAJA =======
function aperturarCaja() {
	var monto = parseFloat($('#monto_apertura').val());
	if (isNaN(monto) || monto <= 0) {
		Swal.fire('Error', 'Ingrese un monto válido mayor a cero.', 'error');
		return;
	}

	Swal.fire({
		title: '¿Aperturar Caja Chica?',
		text: 'Se abrirá un nuevo periodo con S/. ' + monto.toFixed(2),
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Sí, aperturar',
		cancelButtonText: 'Cancelar'
	}).then(function(result) {
		if (result.isConfirmed) {
			$.post('<?= base_url("cajachica/aperturar") ?>', { monto_inicial: monto }, function(r) {
				if (r.rpta == 'success') {
					Swal.fire('Aperturado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

// ======= CATEGORIAS =======
function guardarCategoria() {
	var nombre = $('#cat_nombre').val().trim();
	if (!nombre) {
		Swal.fire('Error', 'Ingrese el nombre de la categoría.', 'error');
		return;
	}

	$.post('<?= base_url("cajachica/guardar_categoria") ?>', $('#formCategoria').serialize(), function(r) {
		if (r.rpta == 'success') {
			Swal.fire('Guardado', r.msg, 'success').then(function() {
				location.reload();
			});
		} else {
			Swal.fire('Error', r.msg, 'error');
		}
	}, 'json');
}

function editarCategoria(id, nombre, color, orden) {
	$('#cat_id').val(id);
	$('#cat_nombre').val(nombre);
	$('#cat_color').val(color);
	$('#cat_orden').val(orden);
	// Switch to categorias tab
	$('#tab-categorias').tab('show');
	$('#cat_nombre').focus();
}

function eliminarCategoria(id) {
	Swal.fire({
		title: '¿Eliminar esta categoría?',
		text: 'Se desactivará y no aparecerá en nuevos gastos.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Sí, eliminar',
		cancelButtonText: 'Cancelar'
	}).then(function(result) {
		if (result.isConfirmed) {
			$.post('<?= base_url("cajachica/eliminar_categoria") ?>', { id: id }, function(r) {
				if (r.rpta == 'success') {
					Swal.fire('Eliminado', r.msg, 'success').then(function() {
						location.reload();
					});
				} else {
					Swal.fire('Error', r.msg, 'error');
				}
			}, 'json');
		}
	});
}

function limpiarFormCategoria() {
	$('#cat_id').val(0);
	$('#cat_nombre').val('');
	$('#cat_color').val('#6c757d');
	$('#cat_orden').val(0);
}

// ======= CAJAS CERRADAS DATATABLE =======
$(document).ready(function() {
	$('#tabla_cerradas').DataTable({
		"ajax": {
			"url": "<?= base_url('cajachica/getCajasCerradas') ?>",
			"type": "post",
			"dataSrc": "data"
		},
		"columns": [
			{ "data": "num", "className": "text-center", "width": "40px" },
			{ "data": "fecha_apertura_fmt" },
			{ "data": "fecha_cierre_fmt" },
			{ "data": "monto_inicial_fmt", "className": "text-right" },
			{ "data": "total_gastos_fmt", "className": "text-right" },
			{ "data": "saldo_final_fmt", "className": "text-right" },
			{ "data": "usuario" },
			{ "data": "acciones", "className": "text-center", "orderable": false, "searchable": false }
		],
		"order": [[ 0, "asc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
		},
		"dom": 'Bfrtip',
		"buttons": [
			'copy', 'csv', 'excel',
			{ extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4' },
			'print'
		]
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

.nav-tabs .nav-link { color: #555; font-weight: 500; }
.nav-tabs .nav-link.active { color: #333; font-weight: bold; border-bottom: 2px solid #337ab7; }

.panel-heading h4 { margin: 0; font-size: 15px; font-weight: bold; }

#tabla_gastos th, #tabla_gastos td,
#tabla_cerradas th, #tabla_cerradas td,
#tabla_vales th, #tabla_vales td {
	font-size: 12px;
	vertical-align: middle;
	white-space: nowrap;
}

#tabla_gastos thead th, #tabla_cerradas thead th, #tabla_vales thead th {
	background-color: #f7f7f7;
	font-weight: bold;
}

#tabla_categorias { font-size: 13px; }
#tabla_categorias td { vertical-align: middle; }

#tabla_resumen td, #tabla_resumen th { font-size: 13px; }

.progress { background-color: #e9ecef; border-radius: 4px; }

.liq-fila label { font-size: 11px; margin-bottom: 2px; }
.liq-fila .form-control { font-size: 12px; }
</style>
