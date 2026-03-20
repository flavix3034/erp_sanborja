<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Caja extends CI_Controller {

    function __construct(){
        parent::__construct();
        session_start();
        if (!isset($_SESSION["user_id"])) {
            redirect(base_url("welcome/index"));
            return;
        }
        $this->load->helper('url');
        $this->load->model('caja_model');
    }

    /**
     * Tablero principal de Caja de Ventas
     */
    public function index(){
        $this->data['page_title'] = "Caja de Ventas";
        $this->template->load('production/index', 'caja/dashboard', $this->data);
    }

    /**
     * AJAX: Retorna datos en tiempo real del estado de la caja
     */
    function get_dashboard_data(){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_abierta($store_id);

        if (!$caja) {
            echo json_encode(array("abierta" => false));
            return;
        }

        $ventas   = $this->caja_model->ventas_efectivo_rango($store_id, $caja->fecha, $caja->hora_apertura);
        $mov_ing  = $this->caja_model->total_movimientos($caja->id, 'INGRESO');
        $mov_egr  = $this->caja_model->total_movimientos($caja->id, 'EGRESO');
        $otros    = $this->caja_model->ventas_otros_medios($store_id, $caja->fecha);
        $saldo    = floatval($caja->monto_ini) + $ventas + $mov_ing - $mov_egr;

        echo json_encode(array(
            "abierta"       => true,
            "caja_id"       => intval($caja->id),
            "fecha"         => $caja->fecha,
            "hora_apertura" => $caja->hora_apertura,
            "responsable"   => $caja->responsable,
            "monto_ini"     => floatval($caja->monto_ini),
            "ventas_cash"   => $ventas,
            "mov_ingreso"   => $mov_ing,
            "mov_egreso"    => $mov_egr,
            "saldo_teorico" => round($saldo, 2),
            "otros_yape_plin"     => $otros['yape_plin'],
            "otros_tarjeta"       => $otros['tarjeta'],
            "otros_transferencia" => $otros['transferencia']
        ));
    }

    /**
     * AJAX POST: Apertura de caja
     */
    function aperturar(){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);

        // Verificar que no haya caja abierta
        $existente = $this->caja_model->get_caja_abierta($store_id);
        if ($existente) {
            echo json_encode(array("rpta" => "error", "msg" => "Ya existe una caja abierta."));
            return;
        }

        $monto = floatval($_POST['monto_inicial']);
        if ($monto < 0) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto inicial no puede ser negativo."));
            return;
        }

        $data = array(
            'store_id'      => $store_id,
            'fecha'         => date('Y-m-d'),
            'hora_apertura' => date('H:i:s'),
            'caja_id'       => $store_id,
            'responsable'   => $_SESSION['usuario'],
            'user_id'       => intval($_SESSION['user_id']),
            'monto_ini'     => $monto,
            'estado_cierre' => 0
        );

        $id = $this->caja_model->insertar_caja($data);
        echo json_encode(array("rpta" => "success", "msg" => "Caja aperturada correctamente.", "id" => $id));
    }

    /**
     * AJAX POST: Registrar movimiento manual (INGRESO o EGRESO)
     */
    function registrar_movimiento(){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_abierta($store_id);

        if (!$caja) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay caja abierta."));
            return;
        }

        $tipo        = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $monto       = floatval(isset($_POST['monto']) ? $_POST['monto'] : 0);
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $referencia  = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';

        if (!in_array($tipo, array('INGRESO', 'EGRESO'))) {
            echo json_encode(array("rpta" => "error", "msg" => "Tipo de movimiento inv\u00e1lido."));
            return;
        }
        if ($monto <= 0) {
            echo json_encode(array("rpta" => "error", "msg" => "El monto debe ser mayor a cero."));
            return;
        }
        if (empty($descripcion)) {
            echo json_encode(array("rpta" => "error", "msg" => "La descripci\u00f3n es obligatoria."));
            return;
        }

        $data = array(
            'registro_caja_id' => intval($caja->id),
            'store_id'         => $store_id,
            'tipo'             => $tipo,
            'monto'            => $monto,
            'descripcion'      => $descripcion,
            'referencia'       => $referencia,
            'fecha_hora'       => date('Y-m-d H:i:s'),
            'user_id'          => intval($_SESSION['user_id'])
        );

        $this->caja_model->insertar_movimiento($data);
        echo json_encode(array("rpta" => "success", "msg" => "Movimiento registrado."));
    }

    /**
     * AJAX POST: Eliminar un movimiento manual
     */
    function eliminar_movimiento(){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_abierta($store_id);

        if (!$caja) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay caja abierta."));
            return;
        }

        $id = intval($_POST['id']);
        $this->caja_model->eliminar_movimiento($id, $caja->id);
        echo json_encode(array("rpta" => "success", "msg" => "Movimiento eliminado."));
    }

    /**
     * AJAX: Retorna movimientos de la caja abierta para DataTable
     */
    function get_movimientos(){
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_abierta($store_id);

        if (!$caja) {
            echo '{"data":[]}';
            return;
        }

        $result = $this->caja_model->get_movimientos($caja->id);

        // Formatear para DataTable (usar comillas simples en HTML para no romper json_datatable)
        foreach ($result as &$r) {
            $r['hora'] = substr($r['fecha_hora'], 11, 5);
            $badge_color = ($r['tipo'] == 'INGRESO') ? '#28a745' : '#dc3545';
            $r['tipo_fmt'] = "<span style='background:{$badge_color};color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;'>{$r['tipo']}</span>";
            $r['monto_fmt'] = number_format(floatval($r['monto']), 2);
            $r['accion'] = "<button class='btn btn-xs btn-danger' onclick='eliminarMovimiento({$r['id']})' title='Eliminar'><i class='fas fa-trash'></i></button>";
        }

        $campos = array("id", "hora", "tipo_fmt", "descripcion", "referencia", "monto_fmt", "accion");
        if (empty($result)) {
            echo '{"data":[]}';
        } else {
            echo $this->fm->json_datatable($campos, $result);
        }
    }

    /**
     * AJAX POST: Cerrar caja (arqueo)
     */
    function cerrar(){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_abierta($store_id);

        if (!$caja) {
            echo json_encode(array("rpta" => "error", "msg" => "No hay caja abierta."));
            return;
        }

        $monto_real     = floatval($_POST['monto_real']);
        $arqueo_json    = isset($_POST['arqueo_json']) ? $_POST['arqueo_json'] : '{}';
        $observaciones  = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

        // Calcular saldo teórico
        $ventas   = $this->caja_model->ventas_efectivo_rango($store_id, $caja->fecha, $caja->hora_apertura);
        $mov_ing  = $this->caja_model->total_movimientos($caja->id, 'INGRESO');
        $mov_egr  = $this->caja_model->total_movimientos($caja->id, 'EGRESO');
        $saldo_teorico = floatval($caja->monto_ini) + $ventas + $mov_ing - $mov_egr;
        $diferencia = round($monto_real - $saldo_teorico, 2);

        $update = array(
            'estado_cierre'       => 1,
            'hora_cierre'         => date('H:i:s'),
            'monto_fin'           => $monto_real,
            'monto_calculado'     => round($saldo_teorico, 2),
            'diferencia'          => $diferencia,
            'ventas'              => $ventas,
            'movimientos_ingreso' => $mov_ing,
            'movimientos_egreso'  => $mov_egr,
            'arqueo_json'         => $arqueo_json,
            'observaciones_cierre'=> $observaciones
        );

        $this->caja_model->cerrar_caja($caja->id, $update);

        echo json_encode(array(
            "rpta"           => "success",
            "msg"            => "Caja cerrada correctamente.",
            "saldo_teorico"  => round($saldo_teorico, 2),
            "monto_real"     => $monto_real,
            "diferencia"     => $diferencia
        ));
    }

    /**
     * AJAX: Historial de cajas para DataTable
     */
    function get_historial(){
        $store_id = intval($_SESSION['store_id']);
        $result = $this->caja_model->get_historial($store_id);

        // Usar comillas simples en atributos HTML para no romper json_datatable
        foreach ($result as &$r) {
            $r['monto_ini']  = number_format(floatval($r['monto_ini']), 2);
            $r['ventas']     = number_format(floatval($r['ventas']), 2);
            $r['mov_ing']    = number_format(floatval($r['movimientos_ingreso']), 2);
            $r['mov_egr']    = number_format(floatval($r['movimientos_egreso']), 2);
            $r['calculado']  = number_format(floatval($r['monto_calculado']), 2);
            $r['real']       = number_format(floatval($r['monto_fin']), 2);

            $dif = floatval($r['diferencia']);
            $color = $dif == 0 ? '#6c757d' : ($dif > 0 ? '#28a745' : '#dc3545');
            $r['dif_fmt'] = "<span style='color:{$color};font-weight:600;'>".number_format($dif, 2)."</span>";

            $est_color = $r['estado_texto'] == 'Abierto' ? '#28a745' : '#6c757d';
            $r['estado_fmt'] = "<span style='background:{$est_color};color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;'>{$r['estado_texto']}</span>";

            $r['accion'] = '';
            if ($r['estado_texto'] == 'Cerrado') {
                $r['accion'] = "<button class='btn btn-xs btn-default' onclick='verDetalle({$r['id']})' title='Ver detalle'><i class='fas fa-eye'></i></button>";
            }
        }

        $campos = array("id", "fecha", "responsable", "monto_ini", "ventas", "mov_ing", "mov_egr", "calculado", "real", "dif_fmt", "estado_fmt", "accion");
        if (empty($result)) {
            echo '{"data":[]}';
        } else {
            echo $this->fm->json_datatable($campos, $result);
        }
    }

    /**
     * AJAX: Detalle de una caja cerrada
     */
    function ver_detalle($id){
        header('Content-Type: application/json');
        $store_id = intval($_SESSION['store_id']);
        $caja = $this->caja_model->get_caja_by_id($id, $store_id);
        $movimientos = array();
        if ($caja) {
            $movimientos = $this->caja_model->get_movimientos($id);
        }
        echo json_encode(array("caja" => $caja, "movimientos" => $movimientos));
    }

    // ========================
    // BACKWARD COMPAT REDIRECTS
    // ========================

    function ver_cajas(){
        redirect(base_url('caja/index'));
    }

    function aperturar_caja(){
        redirect(base_url('caja/index'));
    }
}
