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

    function get_detalle_todos_gastos($store_id, $desde, $hasta) {
        $cad_store1 = $cad_store2 = $cad_store3 = "";
        $cad_desde1 = $cad_desde2 = $cad_desde3 = "";
        $cad_hasta1 = $cad_hasta2 = $cad_hasta3 = "";

        if (!is_null($store_id) && strlen($store_id) > 0 && $store_id != 'null') {
            $cad_store1 = " AND g.store_id = {$store_id}";
            $cad_store2 = " AND per.store_id = {$store_id}";
            $cad_store3 = " AND r.store_id = {$store_id}";
        }
        if (!is_null($desde) && strlen($desde) > 0 && $desde != 'null') {
            $cad_desde1 = " AND date(g.fecha) >= '{$desde}'";
            $cad_desde2 = " AND date(g.fecha_gasto) >= '{$desde}'";
            $cad_desde3 = " AND r.fecha >= '{$desde}'";
        }
        if (!is_null($hasta) && strlen($hasta) > 0 && $hasta != 'null') {
            $cad_hasta1 = " AND date(g.fecha) <= '{$hasta}'";
            $cad_hasta2 = " AND date(g.fecha_gasto) <= '{$hasta}'";
            $cad_hasta3 = " AND r.fecha <= '{$hasta}'";
        }

        $cSql = "SELECT 'Gasto' AS origen,
                    date(g.fecha) AS fecha,
                    COALESCE(gc.nombre, '-') AS categoria,
                    COALESCE(tp.nombre, '-') AS beneficiario,
                    COALESCE(td.descrip, '-') AS tipo_doc,
                    COALESCE(g.nroDoc, '') AS nroDoc,
                    gi.descripcion,
                    ROUND(gi.subtotal, 2) AS monto
                 FROM tec_gastos g
                 INNER JOIN tec_gastos_items gi ON g.id = gi.gasto_id
                 LEFT JOIN tec_gastos_categorias gc ON gi.categoria_id = gc.id
                 LEFT JOIN tec_proveedores tp ON g.proveedor_id = tp.id
                 LEFT JOIN tec_tipos_doc td ON g.tipoDoc = td.id
                 WHERE 1=1{$cad_store1}{$cad_desde1}{$cad_hasta1}

                 UNION ALL

                 SELECT 'Caja Chica' AS origen,
                    date(g.fecha_gasto) AS fecha,
                    c.nombre AS categoria,
                    COALESCE(g.beneficiario, '-') AS beneficiario,
                    CASE g.tipo_documento
                        WHEN 'FACTURA' THEN 'Factura'
                        WHEN 'BOLETA' THEN 'Boleta'
                        WHEN 'RECIBO_HONORARIOS' THEN 'Rec. Honorarios'
                        WHEN 'SIN_COMPROBANTE' THEN 'Sin Comprobante'
                        ELSE '-'
                    END AS tipo_doc,
                    CASE WHEN g.doc_serie IS NOT NULL AND g.doc_serie != ''
                         THEN CONCAT(g.doc_serie, '-', g.doc_numero)
                         ELSE '' END AS nroDoc,
                    g.descripcion,
                    ROUND(g.monto, 2) AS monto
                 FROM tec_cajachica_gastos g
                 INNER JOIN tec_cajachica_categorias c ON g.categoria_id = c.id
                 INNER JOIN tec_cajachica_periodos per ON g.periodo_id = per.id
                 WHERE 1=1{$cad_store2}{$cad_desde2}{$cad_hasta2}

                 UNION ALL

                 SELECT 'Egreso Caja' AS origen,
                    r.fecha,
                    'Sin Categoría' AS categoria,
                    COALESCE(m.referencia, '-') AS beneficiario,
                    'Manual' AS tipo_doc,
                    '' AS nroDoc,
                    m.descripcion,
                    ROUND(m.monto, 2) AS monto
                 FROM tec_caja_movimientos m
                 INNER JOIN tec_registro_cajas r ON m.registro_caja_id = r.id
                 WHERE m.tipo = 'EGRESO'{$cad_store3}{$cad_desde3}{$cad_hasta3}

                 ORDER BY fecha DESC, origen ASC";

        return $this->db->query($cSql)->result_array();
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
