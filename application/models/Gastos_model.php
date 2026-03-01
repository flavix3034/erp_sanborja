<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Gastos_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    function get_gastos($store_id, $cDesde, $cHasta) {
        $cad_desde = $cad_hasta = $cad_store_id = "";

        if (!is_null($cDesde) && strlen($cDesde) > 0 && $cDesde != 'null') {
            $cad_desde = " AND date(a.fecha) >= '{$cDesde}'";
        }
        if (!is_null($cHasta) && strlen($cHasta) > 0 && $cHasta != 'null') {
            $cad_hasta = " AND date(a.fecha) <= '{$cHasta}'";
        }
        if (!is_null($store_id) && strlen($store_id) > 0 && $store_id != 'null') {
            $cad_store_id = " AND a.store_id = {$store_id}";
        }

        $cSql = "SELECT a.id, date(a.fecha) fecha, c.descrip tipoDoc, a.nroDoc,
                    tp.nombre proveedor,
                    SUBSTR(GROUP_CONCAT(gi.descripcion SEPARATOR ', '), 1, 50) conceptos,
                    a.total, a.estado_pago, a.comprobante_archivo
                 FROM tec_gastos a
                 LEFT JOIN tec_gastos_items gi ON a.id = gi.gasto_id
                 LEFT JOIN tec_tipos_doc c ON a.tipoDoc = c.id
                 LEFT JOIN tec_proveedores tp ON a.proveedor_id = tp.id
                 WHERE 1=1" . $cad_desde . $cad_hasta . $cad_store_id .
                " GROUP BY a.id, date(a.fecha), c.descrip, a.nroDoc, tp.nombre, a.total, a.estado_pago, a.comprobante_archivo
                 ORDER BY a.fecha DESC";

        return $this->db->query($cSql);
    }

    function get_gasto_by_id($id) {
        $cSql = "SELECT a.*, tp.nombre prov_nombre, tp.ruc prov_ruc, tp.direccion prov_direccion,
                    tp.correo prov_correo, tp.phone prov_phone,
                    c.descrip tipo_doc_nombre, u.username usuario_nombre
                 FROM tec_gastos a
                 LEFT JOIN tec_proveedores tp ON a.proveedor_id = tp.id
                 LEFT JOIN tec_tipos_doc c ON a.tipoDoc = c.id
                 LEFT JOIN tec_users u ON a.created_by = u.id
                 WHERE a.id = ?";
        return $this->db->query($cSql, array($id))->row();
    }

    function get_items_gasto($gasto_id) {
        $cSql = "SELECT gi.*, gc.nombre categoria_nombre, gc.color categoria_color
                 FROM tec_gastos_items gi
                 LEFT JOIN tec_gastos_categorias gc ON gi.categoria_id = gc.id
                 WHERE gi.gasto_id = ?
                 ORDER BY gi.id ASC";
        return $this->db->query($cSql, array($gasto_id))->result();
    }

    function insertar_gasto($data) {
        $this->db->insert('tec_gastos', $data);
        return $this->db->insert_id();
    }

    function actualizar_gasto($id, $data) {
        $this->db->where('id', $id)->update('tec_gastos', $data);
    }

    function eliminar_gasto($id) {
        $this->db->where('id', $id)->delete('tec_gastos');
    }

    function insertar_item($data) {
        $this->db->insert('tec_gastos_items', $data);
        return $this->db->insert_id();
    }

    function eliminar_items_gasto($gasto_id) {
        $this->db->where('gasto_id', $gasto_id)->delete('tec_gastos_items');
    }

    function buscar_proveedor($q) {
        $q = $this->db->escape_like_str($q);
        $cSql = "SELECT id, nombre, ruc, direccion, correo, phone
                 FROM tec_proveedores
                 WHERE nombre LIKE '%{$q}%' OR ruc LIKE '%{$q}%'
                 ORDER BY nombre LIMIT 15";
        return $this->db->query($cSql)->result();
    }

    function get_categorias_activas() {
        return $this->db->query("SELECT * FROM tec_gastos_categorias WHERE activo = '1' ORDER BY orden, nombre")->result();
    }

    function get_all_categorias() {
        return $this->db->query("SELECT * FROM tec_gastos_categorias ORDER BY orden, nombre")->result();
    }

    function guardar_categoria($data) {
        if (isset($data['id']) && $data['id'] > 0) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id)->update('tec_gastos_categorias', $data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->insert('tec_gastos_categorias', $data);
            return $this->db->insert_id();
        }
    }

    function desactivar_categoria($id) {
        $this->db->where('id', $id)->update('tec_gastos_categorias', array('activo' => ''));
    }

    function get_resumen_por_categoria($store_id, $desde, $hasta) {
        $cad = "";
        if ($store_id && $store_id != 'null') $cad .= " AND g.store_id = {$store_id}";
        if ($desde && $desde != 'null') $cad .= " AND date(g.fecha) >= '{$desde}'";
        if ($hasta && $hasta != 'null') $cad .= " AND date(g.fecha) <= '{$hasta}'";

        $cSql = "SELECT gc.nombre, gc.color, COUNT(gi.id) num_items, SUM(gi.subtotal) total
                 FROM tec_gastos_items gi
                 INNER JOIN tec_gastos g ON gi.gasto_id = g.id
                 INNER JOIN tec_gastos_categorias gc ON gi.categoria_id = gc.id
                 WHERE 1=1" . $cad .
                " GROUP BY gc.id, gc.nombre, gc.color
                 ORDER BY total DESC";
        return $this->db->query($cSql)->result_array();
    }
}
