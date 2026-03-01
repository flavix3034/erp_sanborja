<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CajaChica_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    function get_periodo_abierto($store_id) {
        $cSql = "SELECT p.*, u.username AS usuario_nombre
                 FROM tec_cajachica_periodos p
                 LEFT JOIN tec_users u ON p.usuario_apertura = u.id
                 WHERE p.store_id = ? AND p.estado = 'ABIERTO'
                 LIMIT 1";
        $query = $this->db->query($cSql, array($store_id));
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    function crear_periodo($data) {
        $this->db->insert("tec_cajachica_periodos", $data);
        return $this->db->insert_id();
    }

    function cerrar_periodo($id, $usuario_id, $observaciones = '') {
        $this->db->query(
            "UPDATE tec_cajachica_periodos SET estado='CERRADO', fecha_cierre=NOW(), usuario_cierre=?, observaciones=? WHERE id=? AND estado='ABIERTO'",
            array($usuario_id, $observaciones, $id)
        );
        return $this->db->affected_rows() > 0;
    }

    function get_periodo_by_id($id) {
        $cSql = "SELECT p.*,
                    u1.username AS usuario_apertura_nombre,
                    u2.username AS usuario_cierre_nombre,
                    s.name AS tienda_nombre
                 FROM tec_cajachica_periodos p
                 LEFT JOIN tec_users u1 ON p.usuario_apertura = u1.id
                 LEFT JOIN tec_users u2 ON p.usuario_cierre = u2.id
                 LEFT JOIN tec_stores s ON p.store_id = s.id
                 WHERE p.id = ?";
        $query = $this->db->query($cSql, array($id));
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    function registrar_gasto($data) {
        $this->db->insert("tec_cajachica_gastos", $data);
        return $this->db->insert_id();
    }

    function eliminar_gasto($id) {
        $gasto = $this->db->query("SELECT * FROM tec_cajachica_gastos WHERE id = ?", array($id))->row();
        if ($gasto) {
            $this->db->where("id", $id)->delete("tec_cajachica_gastos");
        }
        return $gasto;
    }

    function get_gasto_by_id($id) {
        return $this->db->query("SELECT * FROM tec_cajachica_gastos WHERE id = ?", array($id))->row();
    }

    function actualizar_saldo($periodo_id, $nuevo_saldo) {
        $this->db->query("UPDATE tec_cajachica_periodos SET saldo_actual = ? WHERE id = ?", array($nuevo_saldo, $periodo_id));
    }

    function get_gastos_periodo($periodo_id) {
        $cSql = "SELECT g.id, g.fecha_gasto, g.monto, g.descripcion, g.beneficiario,
                    g.comprobante, g.tipo_documento, g.doc_serie, g.doc_numero,
                    g.vale_id, g.numero_vale_egreso,
                    c.nombre AS categoria, c.color AS categoria_color
                 FROM tec_cajachica_gastos g
                 INNER JOIN tec_cajachica_categorias c ON g.categoria_id = c.id
                 WHERE g.periodo_id = ?
                 ORDER BY g.fecha_gasto DESC, g.id DESC";
        return $this->db->query($cSql, array($periodo_id))->result_array();
    }

    function get_resumen_por_categoria($periodo_id) {
        $cSql = "SELECT c.nombre, c.color, COUNT(g.id) AS num_gastos, SUM(g.monto) AS total
                 FROM tec_cajachica_gastos g
                 INNER JOIN tec_cajachica_categorias c ON g.categoria_id = c.id
                 WHERE g.periodo_id = ?
                 GROUP BY c.id, c.nombre, c.color
                 ORDER BY total DESC";
        return $this->db->query($cSql, array($periodo_id))->result_array();
    }

    function get_categorias_activas() {
        return $this->db->query("SELECT * FROM tec_cajachica_categorias WHERE activo = '1' ORDER BY orden, nombre")->result();
    }

    function get_all_categorias() {
        return $this->db->query("SELECT * FROM tec_cajachica_categorias ORDER BY orden, nombre")->result();
    }

    function get_categoria_by_id($id) {
        return $this->db->query("SELECT * FROM tec_cajachica_categorias WHERE id = ?", array($id))->row();
    }

    function guardar_categoria($data) {
        if (isset($data['id']) && $data['id'] > 0) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id)->update('tec_cajachica_categorias', $data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->insert('tec_cajachica_categorias', $data);
            return $this->db->insert_id();
        }
    }

    function desactivar_categoria($id) {
        $this->db->where('id', $id)->update('tec_cajachica_categorias', array('activo' => ''));
    }

    function get_periodos_cerrados($store_id) {
        $cSql = "SELECT p.id, p.fecha_apertura, p.fecha_cierre, p.monto_inicial,
                    p.saldo_actual AS saldo_final,
                    (p.monto_inicial - p.saldo_actual) AS total_gastos,
                    u.username AS usuario
                 FROM tec_cajachica_periodos p
                 LEFT JOIN tec_users u ON p.usuario_apertura = u.id
                 WHERE p.store_id = ? AND p.estado = 'CERRADO'
                 ORDER BY p.fecha_cierre DESC";
        return $this->db->query($cSql, array($store_id))->result_array();
    }

    // ===================== VALE DE EGRESO INTERNO =====================

    function siguiente_numero_vale_egreso($periodo_id) {
        $r = $this->db->query("SELECT COALESCE(MAX(numero_vale_egreso),0)+1 AS siguiente FROM tec_cajachica_gastos WHERE periodo_id = ?", array($periodo_id))->row();
        return $r->siguiente;
    }

    // ===================== VALES PROVISIONALES =====================

    function registrar_vale($data) {
        $this->db->insert("tec_cajachica_vales", $data);
        return $this->db->insert_id();
    }

    function get_vale_by_id($id) {
        $cSql = "SELECT v.*, u.username AS usuario_nombre,
                    u2.username AS usuario_liquidacion_nombre
                 FROM tec_cajachica_vales v
                 LEFT JOIN tec_users u ON v.usuario_id = u.id
                 LEFT JOIN tec_users u2 ON v.usuario_liquidacion = u2.id
                 WHERE v.id = ?";
        return $this->db->query($cSql, array($id))->row();
    }

    function get_vales_pendientes($periodo_id) {
        $cSql = "SELECT v.*, u.username AS usuario_nombre
                 FROM tec_cajachica_vales v
                 LEFT JOIN tec_users u ON v.usuario_id = u.id
                 WHERE v.periodo_id = ? AND v.estado = 'PENDIENTE'
                 ORDER BY v.fecha_entrega DESC";
        return $this->db->query($cSql, array($periodo_id))->result_array();
    }

    function get_total_vales_pendientes($periodo_id) {
        $r = $this->db->query("SELECT COALESCE(SUM(monto),0) AS total, COUNT(*) AS cantidad FROM tec_cajachica_vales WHERE periodo_id = ? AND estado = 'PENDIENTE'", array($periodo_id))->row();
        return $r;
    }

    function get_vales_periodo($periodo_id) {
        $cSql = "SELECT v.*, u.username AS usuario_nombre,
                    u2.username AS usuario_liquidacion_nombre
                 FROM tec_cajachica_vales v
                 LEFT JOIN tec_users u ON v.usuario_id = u.id
                 LEFT JOIN tec_users u2 ON v.usuario_liquidacion = u2.id
                 WHERE v.periodo_id = ?
                 ORDER BY v.fecha_entrega DESC";
        return $this->db->query($cSql, array($periodo_id))->result_array();
    }

    function liquidar_vale($vale_id, $data) {
        $this->db->where('id', $vale_id)->update('tec_cajachica_vales', $data);
        return $this->db->affected_rows() > 0;
    }

    function anular_vale($vale_id, $usuario_id, $observaciones = '') {
        $this->db->query(
            "UPDATE tec_cajachica_vales SET estado='ANULADO', fecha_liquidacion=NOW(), usuario_liquidacion=?, monto_devuelto=monto, observaciones=? WHERE id=? AND estado='PENDIENTE'",
            array($usuario_id, $observaciones, $vale_id)
        );
        return $this->db->affected_rows() > 0;
    }

    function get_gastos_por_vale($vale_id) {
        $cSql = "SELECT g.*, c.nombre AS categoria
                 FROM tec_cajachica_gastos g
                 INNER JOIN tec_cajachica_categorias c ON g.categoria_id = c.id
                 WHERE g.vale_id = ?
                 ORDER BY g.id";
        return $this->db->query($cSql, array($vale_id))->result_array();
    }

    function get_store_data($store_id) {
        return $this->db->query("SELECT nombre_empresa, ruc, address1 FROM tec_stores WHERE id = ?", array($store_id))->row();
    }
}
?>
