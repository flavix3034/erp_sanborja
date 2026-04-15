<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gastos extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('gastos_model');
		$this->Igv = 18;
	}

	function index($store_id='', $cDesde='null', $cHasta='null') {
		if ($store_id == '') { $store_id = $_SESSION["store_id"]; }
		$this->data['page_title'] = "Gastos";
		$this->data['desde'] = $cDesde;
		$this->data['hasta'] = $cHasta;
		$this->data['store_id'] = $store_id;
		$this->template->load('production/index', 'gastos/index', $this->data);
	}

	function add($id = null) {
		$this->data['categorias'] = $this->gastos_model->get_categorias_activas();
		if (!is_null($id)) {
			$this->data["id"] = $id;
			$this->data['page_title'] = "Editar Gasto";
			$this->data['modo'] = "U";
		} else {
			$this->data['page_title'] = "Nuevo Gasto";
		}
		$this->template->load('production/index', 'gastos/add', $this->data);
	}

	function get_gastos($store_id='', $desde='null', $hasta='null') {
		$query = $this->gastos_model->get_gastos($store_id, $desde, $hasta);
		$result = $query->result_array();

		$resultado = array();
		foreach ($result as &$r) {
			// Badge estado de pago
			if ($r["estado_pago"] == 'PENDIENTE') {
				$r["estado_pago_fmt"] = '<span class="badge" style="background-color:#dc3545;color:#fff;padding:4px 8px;font-size:11px;">PENDIENTE</span>';
			} else {
				$r["estado_pago_fmt"] = '<span class="badge" style="background-color:#28a745;color:#fff;padding:4px 8px;font-size:11px;">PAGADO</span>';
			}

			// Icono comprobante
			$r["comprobante_link"] = '';
			if (!empty($r["comprobante_archivo"])) {
				$r["comprobante_link"] = '<a href="' . base_url("gastos/ver_comprobante/" . $r["id"]) . '" target="_blank" title="Ver comprobante"><i class="fa fa-file-image-o" style="font-size:16px;color:#337ab7"></i></a>';
			}

			// Acciones
			$r["actions"] = '<a href="#" title="Ver" onclick="ver(' . $r["id"] . ')"><i class="fa fa-eye" style="font-size:15px;color:#337ab7"></i></a>&nbsp;&nbsp;'
				. '<a href="' . base_url("gastos/add/" . $r["id"]) . '" title="Editar"><i class="fa fa-edit" style="font-size:15px;color:#f0ad4e"></i></a>&nbsp;&nbsp;'
				. '<a href="#" title="Eliminar" onclick="eliminar(' . $r["id"] . ')"><i class="fa fa-trash" style="font-size:15px;color:#d9534f"></i></a>';

			$resultado[] = $r;
		}

		$ar_campos = array("id", "fecha", "tipoDoc", "nroDoc", "proveedor", "conceptos", "total", "estado_pago_fmt", "comprobante_link", "actions");
		$data = array();
		foreach ($resultado as $r) {
			$row = array();
			foreach ($ar_campos as $campo) {
				$row[] = isset($r[$campo]) ? $r[$campo] : '';
			}
			$data[] = $row;
		}
		header('Content-Type: application/json');
		echo json_encode(array('data' => $data));
	}

	function save() {
		$store_id       = $_SESSION["store_id"];
		$user_id        = $_SESSION["user_id"];
		$fecha          = $_POST["date"];
		$tipoDoc        = $_POST["tipoDoc"];
		$nroDoc         = $_POST["nroDoc"];
		$redondeo       = isset($_POST["redondeo"]) ? floatval($_POST["redondeo"]) : 0;
		$proveedor_id   = !empty($_POST["proveedor_id"]) ? intval($_POST["proveedor_id"]) : null;
		$por_igv        = isset($_POST["por_igv"]) ? floatval($_POST["por_igv"]) : 18;
		$estado_pago    = isset($_POST["estado_pago"]) ? $_POST["estado_pago"] : 'PAGADO';
		$fecha_vencimiento = ($estado_pago == 'PENDIENTE' && !empty($_POST["fecha_vencimiento"])) ? $_POST["fecha_vencimiento"] : null;
		$observaciones  = isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : null;
		$modo_edicion   = $_POST["modo_edicion"];

		// Validar que haya al menos un item
		if (!isset($_POST['descripcion']) || !is_array($_POST['descripcion']) || count($_POST['descripcion']) == 0) {
			$this->data["msg"] = "Debe ingresar al menos un item";
			$this->data["rpta_msg"] = "danger";
			$this->data['categorias'] = $this->gastos_model->get_categorias_activas();
			$this->data["page_title"] = "Nuevo Gasto";
			$this->template->load('production/index', 'gastos/add', $this->data);
			return;
		}

		// Calcular totales server-side
		$monto_base = 0;
		$descripciones = $_POST['descripcion'];
		$cantidades    = $_POST['cantidad'];
		$precios       = $_POST['precio'];
		$categorias    = $_POST['categoria_id'];

		for ($i = 0; $i < count($descripciones); $i++) {
			$cant   = floatval($cantidades[$i]);
			$precio = floatval($precios[$i]);
			$monto_base += $cant * $precio;
		}

		if ($tipoDoc == 'G') {
			$igv_calc  = 0;
			$total_calc = round($monto_base + $redondeo, 2);
		} else {
			$igv_calc   = round($monto_base * ($por_igv / 100), 2);
			$total_calc = round($monto_base + $igv_calc + $redondeo, 2);
		}

		// Upload comprobante
		$comprobante_archivo = null;
		if (isset($_FILES['comprobante_file']) && strlen($_FILES['comprobante_file']['tmp_name']) > 0) {
			$cf_name = $_FILES['comprobante_file']['name'];
			$cf_size = $_FILES['comprobante_file']['size'];
			$cf_tmp  = $_FILES['comprobante_file']['tmp_name'];
			$cf_ext  = strtolower(pathinfo($cf_name, PATHINFO_EXTENSION));
			$allowed = array("jpg", "jpeg", "png", "pdf");
			if (in_array($cf_ext, $allowed) && $cf_size <= 5242880) {
				$upload_dir = "uploads/gastos/";
				if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
				$comprobante_archivo = "G_" . date("Ymd_His") . "_" . $store_id . "." . $cf_ext;
				move_uploaded_file($cf_tmp, $upload_dir . $comprobante_archivo);
			}
		}

		$this->db->trans_begin();

		$data_gasto = array(
			"store_id"          => $store_id,
			"fecha"             => $fecha,
			"tipoDoc"           => $tipoDoc,
			"nroDoc"            => $nroDoc,
			"proveedor_id"      => $proveedor_id,
			"monto_base"        => round($monto_base, 2),
			"igv"               => $igv_calc,
			"por_igv"           => $por_igv,
			"total"             => $total_calc,
			"redondeo"          => $redondeo,
			"estado_pago"       => $estado_pago,
			"fecha_vencimiento" => $fecha_vencimiento,
			"observaciones"     => $observaciones,
			"created_by"        => $user_id
		);
		if ($comprobante_archivo) {
			$data_gasto["comprobante_archivo"] = $comprobante_archivo;
		}

		if ($modo_edicion == "1") {
			$id = intval($_POST["id_gasto"]);
			$this->gastos_model->actualizar_gasto($id, $data_gasto);
			$this->gastos_model->eliminar_items_gasto($id);
		} else {
			$id = $this->gastos_model->insertar_gasto($data_gasto);
		}

		// Insertar items
		for ($i = 0; $i < count($descripciones); $i++) {
			$cant   = floatval($cantidades[$i]);
			$precio = floatval($precios[$i]);
			$item = array(
				"gasto_id"        => $id,
				"categoria_id"    => intval($categorias[$i]),
				"descripcion"     => trim($descripciones[$i]),
				"cantidad"        => $cant,
				"precio_unitario" => $precio,
				"subtotal"        => round($cant * $precio, 2)
			);
			$this->gastos_model->insertar_item($item);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			if ($comprobante_archivo && file_exists("uploads/gastos/" . $comprobante_archivo)) {
				@unlink("uploads/gastos/" . $comprobante_archivo);
			}
			$this->data["msg"] = "Error al guardar el gasto. Revise los datos.";
			$this->data["rpta_msg"] = "danger";
		} else {
			$this->db->trans_commit();
			$this->data["msg"] = "Gasto guardado correctamente.";
			$this->data["rpta_msg"] = "success";
		}

		$this->data['categorias'] = $this->gastos_model->get_categorias_activas();
		$this->data["page_title"] = "Nuevo Gasto";
		$this->template->load('production/index', 'gastos/add', $this->data);
	}

	function ver() {
		$id = $_REQUEST["id"];
		$gasto = $this->gastos_model->get_gasto_by_id($id);
		if (!$gasto) {
			echo "<p>Gasto no encontrado</p>";
			return;
		}
		$items = $this->gastos_model->get_items_gasto($id);
		?>
		<style type="text/css">
			.lbl_a{ font-weight:bold; }
			.fila_a{ margin:10px; }
			.celda_a{ padding:8px!important; }
			.celda_a_footer{ padding:4px!important; background-color: rgb(180,200,220);}
		</style>
		<div class="row fila_a">
			<div class="col-sm-3">
				<label class="lbl_a">Fecha</label><br>
				<?= date('d/m/Y', strtotime($gasto->fecha)) ?>
			</div>
			<div class="col-sm-3">
				<label class="lbl_a">Tipo Doc</label><br>
				<?= $gasto->tipo_doc_nombre ?>
			</div>
			<div class="col-sm-3">
				<label class="lbl_a">Nro. Doc</label><br>
				<?= $gasto->nroDoc ?>
			</div>
			<div class="col-sm-3">
				<label class="lbl_a">Estado de Pago</label><br>
				<?php if($gasto->estado_pago == 'PENDIENTE'): ?>
					<span class="badge" style="background-color:#dc3545;color:#fff;padding:4px 8px;">PENDIENTE</span>
					<?php if(!empty($gasto->fecha_vencimiento)): ?>
						<br><small>Vence: <?= date('d/m/Y', strtotime($gasto->fecha_vencimiento)) ?></small>
					<?php endif; ?>
				<?php else: ?>
					<span class="badge" style="background-color:#28a745;color:#fff;padding:4px 8px;">PAGADO</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="row fila_a" style="margin-top:10px;">
			<div class="col-sm-6">
				<label class="lbl_a">Proveedor</label><br>
				<?= $gasto->prov_nombre ?> <?= !empty($gasto->prov_ruc) ? '- RUC: ' . $gasto->prov_ruc : '' ?>
			</div>
			<div class="col-sm-3">
				<?php if(!empty($gasto->comprobante_archivo)): ?>
				<label class="lbl_a">Comprobante</label><br>
				<a href="<?= base_url('gastos/ver_comprobante/' . $id) ?>" target="_blank" class="btn btn-xs btn-info">
					<i class="fa fa-file-image-o"></i> Ver Comprobante
				</a>
				<?php endif; ?>
			</div>
			<div class="col-sm-3">
				<label class="lbl_a">Registrado por</label><br>
				<?= $gasto->usuario_nombre ?>
			</div>
		</div>

		<?php if(!empty($gasto->observaciones)): ?>
		<div class="row fila_a" style="margin-top:10px;">
			<div class="col-sm-12">
				<label class="lbl_a">Observaciones</label><br>
				<?= nl2br(htmlspecialchars($gasto->observaciones)) ?>
			</div>
		</div>
		<?php endif; ?>

		<table class="table table-hover" style="margin-top:20px;">
			<tr>
				<th class="celda_a_footer">CATEGORIA</th>
				<th class="celda_a_footer">DESCRIPCION</th>
				<th class="celda_a_footer text-center">CANTIDAD</th>
				<th class="celda_a_footer text-right">P. UNIT.</th>
				<th class="celda_a_footer text-right">SUBTOTAL</th>
			</tr>
			<?php foreach($items as $item): ?>
			<tr>
				<td class="celda_a"><span class="badge" style="background-color:<?= $item->categoria_color ?>;color:#fff;padding:3px 8px;"><?= $item->categoria_nombre ?></span></td>
				<td class="celda_a"><?= htmlspecialchars($item->descripcion) ?></td>
				<td class="celda_a text-center"><?= number_format($item->cantidad, 2) ?></td>
				<td class="celda_a text-right"><?= number_format($item->precio_unitario, 2) ?></td>
				<td class="celda_a text-right"><?= number_format($item->subtotal, 2) ?></td>
			</tr>
			<?php endforeach; ?>
			<tr><th class="celda_a_footer" colspan="4">Monto Base</th><th class="celda_a_footer text-right"><?= number_format($gasto->monto_base, 2) ?></th></tr>
			<tr><th class="celda_a_footer" colspan="4">IGV (<?= $gasto->por_igv ?>%)</th><th class="celda_a_footer text-right"><?= number_format($gasto->igv, 2) ?></th></tr>
			<?php if($gasto->redondeo != 0): ?>
			<tr><th class="celda_a_footer" colspan="4">Redondeo</th><th class="celda_a_footer text-right"><?= number_format($gasto->redondeo, 2) ?></th></tr>
			<?php endif; ?>
			<tr><th class="celda_a_footer" colspan="4">TOTAL</th><th class="celda_a_footer text-right"><?= number_format($gasto->total, 2) ?></th></tr>
		</table>
		<?php
	}

	function eliminar() {
		if (isset($_GET["id"])) {
			$id = intval($_GET["id"]);

			// Eliminar archivo comprobante si existe
			$gasto = $this->gastos_model->get_gasto_by_id($id);
			if ($gasto && !empty($gasto->comprobante_archivo)) {
				$file_path = "uploads/gastos/" . $gasto->comprobante_archivo;
				if (file_exists($file_path)) {
					@unlink($file_path);
				}
			}

			// CASCADE borra tec_gastos_items automaticamente
			$this->gastos_model->eliminar_gasto($id);

			$ar["rpta_msg"] = "success";
			$ar["message"] = "Se elimino correctamente el Gasto #{$id}";
		} else {
			$ar["rpta_msg"] = "danger";
			$ar["message"] = "No se pudo eliminar";
		}
		echo json_encode($ar);
	}

	function buscar_proveedor() {
		$q = isset($_GET["q"]) ? trim($_GET["q"]) : '';
		if (strlen($q) < 1) {
			header('Content-Type: application/json');
			echo json_encode(array());
			return;
		}
		$resultado = $this->gastos_model->buscar_proveedor($q);
		header('Content-Type: application/json');
		echo json_encode($resultado);
	}

	function ver_comprobante($id) {
		$gasto = $this->gastos_model->get_gasto_by_id($id);
		if (!$gasto || empty($gasto->comprobante_archivo)) {
			show_404();
			return;
		}
		$file_path = "uploads/gastos/" . $gasto->comprobante_archivo;
		if (!file_exists($file_path)) {
			show_404();
			return;
		}
		$ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
		$mime_types = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "pdf" => "application/pdf");
		$mime = isset($mime_types[$ext]) ? $mime_types[$ext] : "application/octet-stream";
		header("Content-Type: " . $mime);
		header("Content-Length: " . filesize($file_path));
		readfile($file_path);
	}

	// ---- CATEGORIAS DE GASTO ----

	function categorias() {
		$this->data['page_title'] = "Categorias de Gasto";
		$this->data['categorias'] = $this->gastos_model->get_all_categorias();
		$this->template->load('production/index', 'gastos/categorias', $this->data);
	}

	function guardar_categoria() {
		$data = array(
			'id'     => isset($_POST['id']) ? intval($_POST['id']) : 0,
			'nombre' => trim($_POST['nombre']),
			'color'  => $_POST['color'],
			'orden'  => intval($_POST['orden']),
			'activo' => '1'
		);
		$id = $this->gastos_model->guardar_categoria($data);
		header('Content-Type: application/json');
		echo json_encode(array('rpta_msg' => 'success', 'id' => $id));
	}

	function eliminar_categoria() {
		$id = intval($_POST['id']);
		$this->gastos_model->desactivar_categoria($id);
		header('Content-Type: application/json');
		echo json_encode(array('rpta_msg' => 'success'));
	}

	function getCategorias() {
		$categorias = $this->gastos_model->get_categorias_activas();
		header('Content-Type: application/json');
		echo json_encode($categorias);
	}

	// AJAX para cargar datos de un gasto al editar
	function get_gasto_data() {
		$id = intval($_GET["id"]);
		$gasto = $this->gastos_model->get_gasto_by_id($id);
		$items = $this->gastos_model->get_items_gasto($id);
		header('Content-Type: application/json');
		echo json_encode(array('gasto' => $gasto, 'items' => $items));
	}
}
