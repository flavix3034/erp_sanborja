<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Caja_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Retorna la caja abierta para un store, o null si no hay
     */
    function get_caja_abierta($store_id){
        $q = $this->db->where('store_id', $store_id)
                      ->where('estado_cierre', 0)
                      ->get('tec_registro_cajas');
        return $q->num_rows() > 0 ? $q->row() : null;
    }

    /**
     * Backward compat - verifica si hay cajas abiertas para el store actual
     */
    function existe_cajas_abiertas(){
        return $this->get_caja_abierta($_SESSION['store_id']) !== null;
    }

    /**
     * Inserta un nuevo registro de caja
     */
    function insertar_caja($data){
        $this->db->insert('tec_registro_cajas', $data);
        return $this->db->insert_id();
    }

    /**
     * Actualiza (cierra) una caja
     */
    function cerrar_caja($id, $data){
        $this->db->where('id', $id)->update('tec_registro_cajas', $data);
    }

    /**
     * Obtiene una caja por ID y store_id
     */
    function get_caja_by_id($id, $store_id){
        return $this->db->where('id', $id)
                        ->where('store_id', $store_id)
                        ->get('tec_registro_cajas')->row_array();
    }

    /**
     * Suma ventas en efectivo por rango horario de una caja específica
     */
    function ventas_efectivo_rango($store_id, $fecha, $hora_inicio, $hora_fin = null){
        $datetime_inicio = $fecha . ' ' . $hora_inicio;
        $datetime_fin = $hora_fin ? $fecha . ' ' . $hora_fin : date('Y-m-d H:i:s');

        $sql = "SELECT COALESCE(SUM(c.amount), 0) AS total
                FROM tec_sales a
                INNER JOIN tec_payments c ON a.id = c.sale_id
                WHERE c.paid_by = 'cash'
                  AND a.anulado != '1'
                  AND a.`date` >= ?
                  AND a.`date` <= ?
                  AND a.store_id = ?";
        $r = $this->db->query($sql, array($datetime_inicio, $datetime_fin, $store_id))->row();
        return floatval($r->total);
    }

    /**
     * Suma ventas en efectivo (paid_by='cash') para una fecha y store
     * Solo ventas no anuladas
     */
    function ventas_efectivo($store_id, $fecha){
        $sql = "SELECT COALESCE(SUM(c.amount), 0) AS total
                FROM tec_sales a
                INNER JOIN tec_payments c ON a.id = c.sale_id
                WHERE c.paid_by = 'cash'
                  AND a.anulado != '1'
                  AND DATE(a.`date`) = ?
                  AND a.store_id = ?";
        $r = $this->db->query($sql, array($fecha, $store_id))->row();
        return floatval($r->total);
    }

    /**
     * Resumen de ventas por medio de pago (no-efectivo) para una fecha y store
     * Agrupa: Yape+Plin, Tarjeta Credito+Debito, Transferencias
     */
    function ventas_otros_medios($store_id, $fecha){
        $sql = "SELECT c.paid_by, COALESCE(SUM(c.amount), 0) AS total
                FROM tec_sales a
                INNER JOIN tec_payments c ON a.id = c.sale_id
                WHERE c.paid_by != 'cash'
                  AND a.anulado != '1'
                  AND DATE(a.`date`) = ?
                  AND a.store_id = ?
                GROUP BY c.paid_by";
        $rows = $this->db->query($sql, array($fecha, $store_id))->result();

        $resumen = array('yape_plin' => 0, 'tarjeta' => 0, 'transferencia' => 0);
        foreach ($rows as $r) {
            $medio = $r->paid_by;
            if ($medio == 'Yape' || $medio == 'Plin') {
                $resumen['yape_plin'] += floatval($r->total);
            } elseif (strpos($medio, 'Tarjeta') !== false) {
                $resumen['tarjeta'] += floatval($r->total);
            } else {
                $resumen['transferencia'] += floatval($r->total);
            }
        }
        return $resumen;
    }

    // ========================
    // MOVIMIENTOS MANUALES
    // ========================

    /**
     * Inserta un movimiento manual (INGRESO o EGRESO)
     */
    function insertar_movimiento($data){
        $this->db->insert('tec_caja_movimientos', $data);
        return $this->db->insert_id();
    }

    /**
     * Obtiene todos los movimientos de una caja como array
     */
    function get_movimientos($registro_caja_id){
        return $this->db->where('registro_caja_id', $registro_caja_id)
                        ->order_by('fecha_hora', 'ASC')
                        ->get('tec_caja_movimientos')->result_array();
    }

    /**
     * Suma total de movimientos por tipo (INGRESO o EGRESO) para una caja
     */
    function total_movimientos($registro_caja_id, $tipo){
        $sql = "SELECT COALESCE(SUM(monto), 0) AS total
                FROM tec_caja_movimientos
                WHERE registro_caja_id = ? AND tipo = ?";
        $r = $this->db->query($sql, array($registro_caja_id, $tipo))->row();
        return floatval($r->total);
    }

    /**
     * Elimina un movimiento verificando que pertenece a la caja indicada
     */
    function eliminar_movimiento($id, $registro_caja_id){
        $this->db->where('id', $id)
                 ->where('registro_caja_id', $registro_caja_id)
                 ->delete('tec_caja_movimientos');
    }

    // ========================
    // HISTORIAL
    // ========================

    /**
     * Obtiene las últimas N cajas del store
     */
    function get_historial($store_id, $limit = 50){
        $sql = "SELECT *, IF(estado_cierre = 1, 'Cerrado', 'Abierto') AS estado_texto
                FROM tec_registro_cajas
                WHERE store_id = ?
                ORDER BY id DESC LIMIT ?";
        return $this->db->query($sql, array($store_id, $limit))->result_array();
    }
}
