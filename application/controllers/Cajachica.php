<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cajachica extends MY_Controller
{
    function __construct() {
        parent::__construct();
        $this->load->model('CajaChica_model');
        $this->load->model('Empleados_model');
    }

    function index() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $this->data['page_title'] = "Caja Chica";
        $this->data['periodo'] = $this->CajaChica_model->get_periodo_abierto($store_id);
        $this->data['categorias'] = $this->CajaChica_model->get_categorias_activas();
        $this->data['todas_categorias'] = $this->CajaChica_model->get_all_categorias();
        $this->data['empleados'] = $this->Empleados_model->lista_empleados_activos();

        // Vales provisionales
        if ($this->data['periodo']) {
            $vp = $this->CajaChica_model->get_total_vales_pendientes($this->data['periodo']->id);
            $this->data['total_vales_pendientes'] = $vp->total;
            $this->data['cantidad_vales_pendientes'] = $vp->cantidad;
        } else {
            $this->data['total_vales_pendientes'] = 0;
            $this->data['cantidad_vales_pendientes'] = 0;
        }

        $this->template->load('production/index', 'cajachica/index', $this->data);
    }

    function aperturar() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];

        $existente = $this->CajaChica_model->get_periodo_abierto($store_id);
        if ($existente) {
            echo json_encode(array("rpta" => "error", "msg" => "Ya existe un periodo abierto para esta tienda."));
            return;
        }

        $monto = floatval($_POST["monto_inicial"]);
        if ($monto <= 0) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto inicial debe ser mayor a cero."));
            return;
        }

        $data = array(
            "store_id" => $store_id,
            "monto_inicial" => $monto,
            "saldo_actual" => $monto,
            "fecha_apertura" => date("Y-m-d H:i:s"),
            "usuario_apertura" => $user_id,
            "estado" => "ABIERTO"
        );

        $id = $this->CajaChica_model->crear_periodo($data);
        echo json_encode(array("rpta" => "success", "msg" => "Caja Chica aperturada correctamente.", "id" => $id));
    }

    function registrar_gasto() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay periodo abierto."));
            return;
        }

        $monto = floatval($_POST["monto"]);

        if ($monto <= 0) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto debe ser mayor a cero."));
            return;
        }

        if ($monto > $periodo->saldo_actual) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto (S/. " . number_format($monto, 2) . ") excede el saldo disponible (S/. " . number_format($periodo->saldo_actual, 2) . ")."));
            return;
        }

        if (empty($_POST["descripcion"])) {
            echo json_encode(array("rpta" => "error", "msg" => "La descripción es obligatoria."));
            return;
        }

        if (empty($_POST["categoria_id"])) {
            echo json_encode(array("rpta" => "error", "msg" => "Seleccione una categoría."));
            return;
        }

        $beneficiario = isset($_POST["beneficiario"]) ? trim($_POST["beneficiario"]) : '';
        if (empty($beneficiario)) {
            echo json_encode(array("rpta" => "error", "msg" => "Seleccione un beneficiario."));
            return;
        }

        $tipo_doc_post = isset($_POST["tipo_documento"]) ? trim($_POST["tipo_documento"]) : '';
        if (empty($tipo_doc_post)) {
            echo json_encode(array("rpta" => "error", "msg" => "Seleccione un tipo de documento."));
            return;
        }

        if (in_array($tipo_doc_post, array('FACTURA', 'BOLETA'))) {
            if (empty($_POST["doc_serie"])) {
                echo json_encode(array("rpta" => "error", "msg" => "La serie es obligatoria para " . $tipo_doc_post . "."));
                return;
            }
            if (empty($_POST["doc_numero"])) {
                echo json_encode(array("rpta" => "error", "msg" => "El número es obligatorio para " . $tipo_doc_post . "."));
                return;
            }
        }

        if (in_array($tipo_doc_post, array('FACTURA', 'BOLETA', 'RECIBO_HONORARIOS'))) {
            if (!isset($_FILES['comprobante']) || strlen($_FILES['comprobante']['tmp_name']) == 0) {
                echo json_encode(array("rpta" => "error", "msg" => "Debe adjuntar el comprobante (foto/PDF) para " . $tipo_doc_post . "."));
                return;
            }
        }

        // File upload
        $comprobante = null;
        if (isset($_FILES['comprobante']) && strlen($_FILES['comprobante']['tmp_name']) > 0) {
            $file_name = $_FILES['comprobante']['name'];
            $file_size = $_FILES['comprobante']['size'];
            $file_tmp = $_FILES['comprobante']['tmp_name'];
            $ar_f = explode('.', $file_name);
            $file_ext = strtolower(end($ar_f));

            $allowed = array("jpg", "jpeg", "png", "pdf");
            if (!in_array($file_ext, $allowed)) {
                echo json_encode(array("rpta" => "error", "msg" => "Tipo de archivo no permitido. Solo: jpg, jpeg, png, pdf"));
                return;
            }
            if ($file_size > 5242880) {
                echo json_encode(array("rpta" => "error", "msg" => "El archivo excede el tamaño máximo de 5MB."));
                return;
            }

            $upload_dir = "uploads/cajachica/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $comprobante = date("Ymd_His") . "_" . $user_id . "." . $file_ext;
            if (!move_uploaded_file($file_tmp, $upload_dir . $comprobante)) {
                echo json_encode(array("rpta" => "error", "msg" => "Error al subir el archivo."));
                return;
            }
        }

        $this->db->trans_begin();

        // Tipo de documento
        $tipo_documento = isset($_POST["tipo_documento"]) ? trim($_POST["tipo_documento"]) : null;
        $doc_serie = null;
        $doc_numero = null;
        $numero_vale_egreso = null;

        if (in_array($tipo_documento, array('FACTURA', 'BOLETA'))) {
            $doc_serie = strtoupper(trim(isset($_POST["doc_serie"]) ? $_POST["doc_serie"] : ''));
            $doc_numero = trim(isset($_POST["doc_numero"]) ? $_POST["doc_numero"] : '');
        }

        if ($tipo_documento == 'SIN_COMPROBANTE') {
            $numero_vale_egreso = $this->CajaChica_model->siguiente_numero_vale_egreso($periodo->id);
            $comprobante = 'VE_AUTO'; // Vale de Egreso Interno auto-adjunto
        }

        $data = array(
            "periodo_id" => $periodo->id,
            "categoria_id" => intval($_POST["categoria_id"]),
            "monto" => $monto,
            "descripcion" => strtoupper(trim($_POST["descripcion"])),
            "beneficiario" => strtoupper(trim(isset($_POST["beneficiario"]) ? $_POST["beneficiario"] : '')),
            "comprobante" => $comprobante,
            "tipo_documento" => $tipo_documento,
            "doc_serie" => $doc_serie,
            "doc_numero" => $doc_numero,
            "numero_vale_egreso" => $numero_vale_egreso,
            "fecha_gasto" => $_POST["fecha_gasto"],
            "usuario_id" => $user_id
        );

        // Si viene de liquidacion de vale
        if (isset($_POST["vale_id"]) && intval($_POST["vale_id"]) > 0) {
            $data["vale_id"] = intval($_POST["vale_id"]);
        }

        $gasto_id = $this->CajaChica_model->registrar_gasto($data);
        $nuevo_saldo = $periodo->saldo_actual - $monto;
        $this->CajaChica_model->actualizar_saldo($periodo->id, $nuevo_saldo);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($comprobante && file_exists("uploads/cajachica/" . $comprobante)) {
                unlink("uploads/cajachica/" . $comprobante);
            }
            echo json_encode(array("rpta" => "error", "msg" => "Error al registrar el gasto."));
        } else {
            $this->db->trans_commit();
            echo json_encode(array(
                "rpta" => "success",
                "msg" => "Gasto registrado correctamente.",
                "nuevo_saldo" => $nuevo_saldo,
                "gasto_id" => $gasto_id,
                "tipo_documento" => $tipo_documento,
                "numero_vale_egreso" => $numero_vale_egreso
            ));
        }
    }

    function getGastos() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);

        if (!$periodo) {
            header('Content-Type: application/json');
            echo json_encode(array("data" => array()));
            return;
        }

        $gastos = $this->CajaChica_model->get_gastos_periodo($periodo->id);

        $n = 0;
        foreach ($gastos as &$g) {
            $n++;
            $g["num"] = $n;
            $g["fecha_fmt"] = date('d/m/Y H:i', strtotime($g["fecha_gasto"]));
            $g["categoria_badge"] = '<span class="badge" style="background-color:' . htmlspecialchars($g["categoria_color"]) . ';color:#fff;padding:4px 8px;font-size:11px;">' . htmlspecialchars($g["categoria"]) . '</span>';
            $g["monto_fmt"] = 'S/. ' . number_format($g["monto"], 2);

            $g["comprobante_link"] = '';
            if (!empty($g["comprobante"])) {
                if ($g["comprobante"] == 'VE_AUTO') {
                    $g["comprobante_link"] = '<a href="javascript:void(0)" onclick="imprimirValeEgreso(' . $g["id"] . ')" title="Vale de Egreso Interno"><i class="fa fa-file-text-o" style="font-size:16px;color:#f0ad4e"></i> <small>VE</small></a>';
                } else {
                    $g["comprobante_link"] = '<a href="' . base_url("cajachica/ver_comprobante/" . $g["id"]) . '" target="_blank" title="Ver comprobante"><i class="fa fa-file-image-o" style="font-size:16px;color:#337ab7"></i></a>';
                }
            }

            // Tipo documento badge
            $tipo_doc_labels = array('FACTURA'=>'Factura','BOLETA'=>'Boleta','RECIBO_HONORARIOS'=>'Rec. Hon.','SIN_COMPROBANTE'=>'Sin Comprob.');
            $tipo_doc_colors = array('FACTURA'=>'#337ab7','BOLETA'=>'#5cb85c','RECIBO_HONORARIOS'=>'#f0ad4e','SIN_COMPROBANTE'=>'#d9534f');
            $td = isset($g["tipo_documento"]) ? $g["tipo_documento"] : '';
            $g["tipo_doc_badge"] = '';
            if (!empty($td) && isset($tipo_doc_labels[$td])) {
                $g["tipo_doc_badge"] = '<span class="badge" style="background:'.($tipo_doc_colors[$td]).';color:#fff;padding:3px 6px;font-size:10px;">'.$tipo_doc_labels[$td].'</span>';
                if (!empty($g["doc_serie"]) || !empty($g["doc_numero"])) {
                    $g["tipo_doc_badge"] .= '<br><small>' . htmlspecialchars($g["doc_serie"]) . '-' . htmlspecialchars($g["doc_numero"]) . '</small>';
                }
            }

            // Vale reference
            if (!empty($g["vale_id"])) {
                $g["tipo_doc_badge"] .= ' <span class="badge" style="background:#ff8c00;color:#fff;padding:2px 5px;font-size:9px;">VP-'.$g["vale_id"].'</span>';
            }

            $g["acciones"] = '';
            // Print VE button for SIN_COMPROBANTE
            if ($td == 'SIN_COMPROBANTE' && !empty($g["numero_vale_egreso"])) {
                $g["acciones"] .= '<button onclick="imprimirValeEgreso(' . $g["id"] . ')" title="Imprimir Vale Egreso" style="border:none;background:none;cursor:pointer;"><i class="fa fa-print" style="font-size:14px;color:#f0ad4e"></i></button> ';
            }
            $g["acciones"] .= '<button onclick="eliminarGasto(' . $g["id"] . ')" title="Eliminar" style="border:none;background:none;cursor:pointer;"><i class="fa fa-trash" style="font-size:14px;color:#d9534f"></i></button>';
        }

        header('Content-Type: application/json');
        echo json_encode(array("data" => $gastos));
    }

    function getCajasCerradas() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $periodos = $this->CajaChica_model->get_periodos_cerrados($store_id);

        $n = 0;
        foreach ($periodos as &$p) {
            $n++;
            $p["num"] = $n;
            $p["fecha_apertura_fmt"] = date('d/m/Y H:i', strtotime($p["fecha_apertura"]));
            $p["fecha_cierre_fmt"] = date('d/m/Y H:i', strtotime($p["fecha_cierre"]));
            $p["monto_inicial_fmt"] = 'S/. ' . number_format($p["monto_inicial"], 2);
            $p["total_gastos_fmt"] = 'S/. ' . number_format($p["total_gastos"], 2);
            $p["saldo_final_fmt"] = 'S/. ' . number_format($p["saldo_final"], 2);
            $p["acciones"] = '<a href="' . base_url("cajachica/ver_detalle_periodo/" . $p["id"]) . '" title="Ver Detalle" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver</a> '
                . '<button onclick="abrirReporte(' . $p["id"] . ')" title="Reporte PDF" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o"></i> PDF</button>';
        }

        header('Content-Type: application/json');
        echo json_encode(array("data" => $periodos));
    }

    function ver_detalle_periodo($id) {
        $periodo = $this->CajaChica_model->get_periodo_by_id($id);
        if (!$periodo) {
            show_404();
            return;
        }

        $this->data['page_title'] = "Caja Chica - Detalle Periodo #" . $id;
        $this->data['periodo'] = $periodo;
        $this->data['gastos'] = $this->CajaChica_model->get_gastos_periodo($id);
        $this->data['resumen'] = $this->CajaChica_model->get_resumen_por_categoria($id);
        $this->data['vales'] = $this->CajaChica_model->get_vales_periodo($id);

        $this->template->load('production/index', 'cajachica/detalle_periodo', $this->data);
    }

    function guardar_categoria() {
        $data = array(
            "nombre" => strtoupper(trim($_POST["nombre"])),
            "color" => $_POST["color"],
            "orden" => intval($_POST["orden"]),
            "activo" => "1"
        );

        if (isset($_POST["id"]) && $_POST["id"] > 0) {
            $data["id"] = intval($_POST["id"]);
        }

        $id = $this->CajaChica_model->guardar_categoria($data);
        echo json_encode(array("rpta" => "success", "msg" => "Categoría guardada correctamente.", "id" => $id));
    }

    function eliminar_categoria() {
        $id = intval($_POST["id"]);
        $this->CajaChica_model->desactivar_categoria($id);
        echo json_encode(array("rpta" => "success", "msg" => "Categoría eliminada."));
    }

    function getCategorias() {
        $categorias = $this->CajaChica_model->get_categorias_activas();
        header('Content-Type: application/json');
        echo json_encode($categorias);
    }

    function rendir_cuentas() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];
        $observaciones = isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : '';

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay periodo abierto para cerrar."));
            return;
        }

        // Bloquear cierre si hay vales pendientes
        $vp = $this->CajaChica_model->get_total_vales_pendientes($periodo->id);
        if ($vp->cantidad > 0) {
            echo json_encode(array("rpta" => "error", "msg" => "No se puede cerrar la caja: existen " . $vp->cantidad . " vale(s) provisional(es) pendientes por S/. " . number_format($vp->total, 2) . ". Debe liquidarlos o anularlos primero."));
            return;
        }

        $resultado = $this->CajaChica_model->cerrar_periodo($periodo->id, $user_id, $observaciones);

        if ($resultado) {
            echo json_encode(array("rpta" => "success", "msg" => "Rendición realizada correctamente. La Caja Chica ha sido cerrada."));
        } else {
            echo json_encode(array("rpta" => "error", "msg" => "Error al cerrar el periodo."));
        }
    }

    function resumen_periodo($id) {
        $periodo = $this->CajaChica_model->get_periodo_by_id($id);
        if (!$periodo) {
            header('Content-Type: application/json');
            echo json_encode(array("error" => true, "msg" => "Periodo no encontrado"));
            return;
        }

        $resumen = $this->CajaChica_model->get_resumen_por_categoria($id);

        $total_gastado = 0;
        foreach ($resumen as $r) {
            $total_gastado += floatval($r["total"]);
        }

        header('Content-Type: application/json');
        echo json_encode(array(
            "periodo" => $periodo,
            "resumen" => $resumen,
            "total_gastado" => $total_gastado,
            "saldo_teorico" => floatval($periodo->monto_inicial) - $total_gastado
        ));
    }

    function ver_comprobante($gasto_id) {
        $gasto = $this->CajaChica_model->get_gasto_by_id($gasto_id);
        if (!$gasto || empty($gasto->comprobante)) {
            show_404();
            return;
        }

        $file_path = "uploads/cajachica/" . $gasto->comprobante;
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

    function eliminar_gasto() {
        $gasto_id = intval($_POST["id"]);
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No se puede eliminar: no hay periodo abierto."));
            return;
        }

        $gasto = $this->CajaChica_model->get_gasto_by_id($gasto_id);
        if (!$gasto || $gasto->periodo_id != $periodo->id) {
            echo json_encode(array("rpta" => "error", "msg" => "Gasto no encontrado o no pertenece al periodo actual."));
            return;
        }

        $this->db->trans_begin();

        $this->CajaChica_model->eliminar_gasto($gasto_id);
        $nuevo_saldo = $periodo->saldo_actual + $gasto->monto;
        $this->CajaChica_model->actualizar_saldo($periodo->id, $nuevo_saldo);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array("rpta" => "error", "msg" => "Error al eliminar el gasto."));
        } else {
            $this->db->trans_commit();
            if (!empty($gasto->comprobante)) {
                $file_path = "uploads/cajachica/" . $gasto->comprobante;
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }
            echo json_encode(array("rpta" => "success", "msg" => "Gasto eliminado y saldo restaurado.", "nuevo_saldo" => $nuevo_saldo));
        }
    }

    function reporte_periodo($id) {
        $periodo = $this->CajaChica_model->get_periodo_by_id($id);
        if (!$periodo) {
            show_404();
            return;
        }

        $store = $this->db->query("SELECT name, nombre_empresa, address1, ruc FROM tec_stores WHERE id = ?", array($periodo->store_id))->row();

        $ar = array(
            "periodo" => $periodo,
            "gastos" => $this->CajaChica_model->get_gastos_periodo($id),
            "resumen" => $this->CajaChica_model->get_resumen_por_categoria($id),
            "vales" => $this->CajaChica_model->get_vales_periodo($id),
            "store" => $store
        );

        $this->load->view('cajachica/reporte', $ar);
    }

    // ===================== VALES PROVISIONALES =====================

    function registrar_vale() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay periodo abierto."));
            return;
        }

        $monto = floatval($_POST["monto"]);
        if ($monto <= 0) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto debe ser mayor a cero."));
            return;
        }
        if ($monto > $periodo->saldo_actual) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto (S/. " . number_format($monto, 2) . ") excede el saldo disponible (S/. " . number_format($periodo->saldo_actual, 2) . ")."));
            return;
        }
        if (empty($_POST["beneficiario"])) {
            echo json_encode(array("rpta" => "error", "msg" => "El beneficiario es obligatorio."));
            return;
        }
        if (empty($_POST["motivo"])) {
            echo json_encode(array("rpta" => "error", "msg" => "El motivo es obligatorio."));
            return;
        }

        $this->db->trans_begin();

        $data = array(
            "periodo_id" => $periodo->id,
            "monto" => $monto,
            "beneficiario" => strtoupper(trim($_POST["beneficiario"])),
            "motivo" => strtoupper(trim($_POST["motivo"])),
            "estado" => "PENDIENTE",
            "fecha_entrega" => date("Y-m-d H:i:s"),
            "usuario_id" => $user_id
        );

        $vale_id = $this->CajaChica_model->registrar_vale($data);
        $nuevo_saldo = $periodo->saldo_actual - $monto;
        $this->CajaChica_model->actualizar_saldo($periodo->id, $nuevo_saldo);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array("rpta" => "error", "msg" => "Error al registrar el vale."));
        } else {
            $this->db->trans_commit();
            echo json_encode(array(
                "rpta" => "success",
                "msg" => "Vale Provisional registrado correctamente.",
                "vale_id" => $vale_id,
                "nuevo_saldo" => $nuevo_saldo
            ));
        }
    }

    function getValesPendientes() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);

        if (!$periodo) {
            header('Content-Type: application/json');
            echo json_encode(array("data" => array()));
            return;
        }

        $vales = $this->CajaChica_model->get_vales_periodo($periodo->id);

        $n = 0;
        foreach ($vales as &$v) {
            $n++;
            $v["num"] = $n;
            $v["fecha_fmt"] = date('d/m/Y H:i', strtotime($v["fecha_entrega"]));
            $v["monto_fmt"] = 'S/. ' . number_format($v["monto"], 2);
            $estado_color = array('PENDIENTE'=>'#ff8c00','LIQUIDADO'=>'#5cb85c','ANULADO'=>'#d9534f');
            $v["estado_badge"] = '<span class="badge" style="background:'.($estado_color[$v["estado"]]).';color:#fff;padding:4px 8px;font-size:11px;">'.$v["estado"].'</span>';

            $v["acciones"] = '<button onclick="imprimirVale(' . $v["id"] . ')" title="Imprimir" class="btn btn-xs btn-default"><i class="fa fa-print"></i></button> ';
            $v["acciones"] .= '<button onclick="verVale(' . $v["id"] . ')" title="Ver detalle" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></button> ';
            if ($v["estado"] == 'PENDIENTE') {
                $v["acciones"] .= '<button onclick="abrirLiquidarVale(' . $v["id"] . ')" title="Liquidar" class="btn btn-xs btn-success"><i class="fa fa-check"></i> Liquidar</button> ';
                $v["acciones"] .= '<button onclick="anularVale(' . $v["id"] . ')" title="Anular" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Anular</button>';
            }
        }

        header('Content-Type: application/json');
        echo json_encode(array("data" => $vales));
    }

    function liquidar_vale() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];
        $vale_id = intval($_POST["vale_id"]);

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay periodo abierto."));
            return;
        }

        $vale = $this->CajaChica_model->get_vale_by_id($vale_id);
        if (!$vale || $vale->estado != 'PENDIENTE' || $vale->periodo_id != $periodo->id) {
            echo json_encode(array("rpta" => "error", "msg" => "Vale no encontrado o no esta pendiente."));
            return;
        }

        // Recibir arrays de gastos
        $montos = isset($_POST["liq_monto"]) ? $_POST["liq_monto"] : array();
        $tipos_doc = isset($_POST["liq_tipo_documento"]) ? $_POST["liq_tipo_documento"] : array();
        $series = isset($_POST["liq_doc_serie"]) ? $_POST["liq_doc_serie"] : array();
        $numeros = isset($_POST["liq_doc_numero"]) ? $_POST["liq_doc_numero"] : array();
        $categorias = isset($_POST["liq_categoria_id"]) ? $_POST["liq_categoria_id"] : array();
        $descripciones = isset($_POST["liq_descripcion"]) ? $_POST["liq_descripcion"] : array();
        $monto_devuelto = floatval(isset($_POST["monto_devuelto"]) ? $_POST["monto_devuelto"] : 0);

        if (count($montos) == 0) {
            echo json_encode(array("rpta" => "error", "msg" => "Debe registrar al menos un gasto."));
            return;
        }

        $total_gastos = 0;
        for ($i = 0; $i < count($montos); $i++) {
            $total_gastos += floatval($montos[$i]);
        }

        // Validar que gastos + devolucion = vale.monto
        $diferencia = abs(($total_gastos + $monto_devuelto) - floatval($vale->monto));
        if ($diferencia > 0.02) {
            echo json_encode(array("rpta" => "error", "msg" => "Los montos no cuadran. Gastos (S/. " . number_format($total_gastos, 2) . ") + Devolucion (S/. " . number_format($monto_devuelto, 2) . ") debe ser igual al vale (S/. " . number_format($vale->monto, 2) . ")."));
            return;
        }

        $this->db->trans_begin();

        // Crear cada gasto
        for ($i = 0; $i < count($montos); $i++) {
            $tipo_doc = isset($tipos_doc[$i]) ? $tipos_doc[$i] : null;
            $nve = null;
            if ($tipo_doc == 'SIN_COMPROBANTE') {
                $nve = $this->CajaChica_model->siguiente_numero_vale_egreso($periodo->id);
            }

            $data_gasto = array(
                "periodo_id" => $periodo->id,
                "categoria_id" => intval($categorias[$i]),
                "monto" => floatval($montos[$i]),
                "descripcion" => strtoupper(trim($descripciones[$i])),
                "beneficiario" => $vale->beneficiario,
                "tipo_documento" => $tipo_doc,
                "doc_serie" => in_array($tipo_doc, array('FACTURA','BOLETA')) ? strtoupper(trim(isset($series[$i]) ? $series[$i] : '')) : null,
                "doc_numero" => in_array($tipo_doc, array('FACTURA','BOLETA')) ? trim(isset($numeros[$i]) ? $numeros[$i] : '') : null,
                "vale_id" => $vale_id,
                "numero_vale_egreso" => $nve,
                "fecha_gasto" => date("Y-m-d H:i:s"),
                "usuario_id" => $user_id
            );

            $this->CajaChica_model->registrar_gasto($data_gasto);
        }

        // Actualizar vale
        $this->CajaChica_model->liquidar_vale($vale_id, array(
            "estado" => "LIQUIDADO",
            "fecha_liquidacion" => date("Y-m-d H:i:s"),
            "monto_gastado" => $total_gastos,
            "monto_devuelto" => $monto_devuelto,
            "usuario_liquidacion" => $user_id,
            "observaciones" => isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : ''
        ));

        // Si hay devolucion, sumar al saldo
        if ($monto_devuelto > 0) {
            $nuevo_saldo = $periodo->saldo_actual + $monto_devuelto;
            $this->CajaChica_model->actualizar_saldo($periodo->id, $nuevo_saldo);
        }

        // Los gastos NO descuentan saldo adicional porque ya se descontó al crear el vale

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array("rpta" => "error", "msg" => "Error al liquidar el vale."));
        } else {
            $this->db->trans_commit();
            echo json_encode(array("rpta" => "success", "msg" => "Vale liquidado correctamente."));
        }
    }

    function anular_vale() {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $user_id = $_SESSION["user_id"];
        $vale_id = intval($_POST["vale_id"]);

        $periodo = $this->CajaChica_model->get_periodo_abierto($store_id);
        if (!$periodo) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay periodo abierto."));
            return;
        }

        $vale = $this->CajaChica_model->get_vale_by_id($vale_id);
        if (!$vale || $vale->estado != 'PENDIENTE' || $vale->periodo_id != $periodo->id) {
            echo json_encode(array("rpta" => "error", "msg" => "Vale no encontrado o no esta pendiente."));
            return;
        }

        $this->db->trans_begin();

        $obs = isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : 'Dinero devuelto integramente';
        $this->CajaChica_model->anular_vale($vale_id, $user_id, $obs);

        // Restaurar saldo completo
        $nuevo_saldo = $periodo->saldo_actual + floatval($vale->monto);
        $this->CajaChica_model->actualizar_saldo($periodo->id, $nuevo_saldo);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array("rpta" => "error", "msg" => "Error al anular el vale."));
        } else {
            $this->db->trans_commit();
            echo json_encode(array("rpta" => "success", "msg" => "Vale anulado. Saldo restaurado.", "nuevo_saldo" => $nuevo_saldo));
        }
    }

    function imprimir_vale($id) {
        $vale = $this->CajaChica_model->get_vale_by_id($id);
        if (!$vale) { show_404(); return; }

        $store = $this->CajaChica_model->get_store_data($this->db->query("SELECT store_id FROM tec_cajachica_periodos WHERE id=?", array($vale->periodo_id))->row()->store_id);

        $ar = array("vale" => $vale, "store" => $store);
        $this->load->view('cajachica/print_vale_provisional', $ar);
    }

    function imprimir_vale_egreso($gasto_id) {
        $gasto = $this->CajaChica_model->get_gasto_by_id($gasto_id);
        if (!$gasto || $gasto->tipo_documento != 'SIN_COMPROBANTE') { show_404(); return; }

        $periodo = $this->CajaChica_model->get_periodo_by_id($gasto->periodo_id);
        $categoria = $this->CajaChica_model->get_categoria_by_id($gasto->categoria_id);
        $store = $this->CajaChica_model->get_store_data($periodo->store_id);
        $usuario = $this->db->query("SELECT username FROM tec_users WHERE id=?", array($gasto->usuario_id))->row();

        $ar = array("gasto" => $gasto, "periodo" => $periodo, "categoria" => $categoria, "store" => $store, "usuario" => $usuario);
        $this->load->view('cajachica/print_vale_egreso', $ar);
    }

    function get_vale_data() {
        $vale_id = intval(isset($_GET["id"]) ? $_GET["id"] : (isset($_POST["vale_id"]) ? $_POST["vale_id"] : 0));
        $vale = $this->CajaChica_model->get_vale_by_id($vale_id);
        if (!$vale) {
            header('Content-Type: application/json');
            echo json_encode(array("vale" => null, "gastos" => array()));
            return;
        }
        $vale->fecha_entrega_fmt = date('d/m/Y H:i', strtotime($vale->fecha_entrega));
        $gastos = $this->CajaChica_model->get_gastos_por_vale($vale_id);
        header('Content-Type: application/json');
        echo json_encode(array("vale" => $vale, "gastos" => $gastos));
    }
}
