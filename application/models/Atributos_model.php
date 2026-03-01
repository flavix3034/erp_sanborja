<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Atributos_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    // ========================
    // ATRIBUTOS
    // ========================

    function get_atributos_activos() {
        return $this->db->query("SELECT * FROM tec_atributos WHERE activo = '1' ORDER BY orden, nombre")->result();
    }

    function get_all_atributos() {
        return $this->db->query("SELECT * FROM tec_atributos ORDER BY orden, nombre")->result();
    }

    function get_atributo($id) {
        return $this->db->query("SELECT * FROM tec_atributos WHERE id = ?", array($id))->row();
    }

    function guardar_atributo($data) {
        if (isset($data['id']) && $data['id'] > 0) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id)->update('tec_atributos', $data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->insert('tec_atributos', $data);
            return $this->db->insert_id();
        }
    }

    function desactivar_atributo($id) {
        $this->db->where('id', $id)->update('tec_atributos', array('activo' => ''));
    }

    function activar_atributo($id) {
        $this->db->where('id', $id)->update('tec_atributos', array('activo' => '1'));
    }

    // ========================
    // VALORES DE ATRIBUTO
    // ========================

    function get_valores($atributo_id) {
        return $this->db->query("SELECT * FROM tec_atributo_valores WHERE atributo_id = ? ORDER BY orden, valor", array($atributo_id))->result();
    }

    function get_valor($id) {
        return $this->db->query("SELECT * FROM tec_atributo_valores WHERE id = ?", array($id))->row();
    }

    function guardar_valor($data) {
        if (isset($data['id']) && $data['id'] > 0) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id)->update('tec_atributo_valores', $data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->insert('tec_atributo_valores', $data);
            return $this->db->insert_id();
        }
    }

    function eliminar_valor($id) {
        // Verificar que no esté en uso
        $en_uso = $this->db->query("SELECT COUNT(*) AS cnt FROM tec_variante_atributos WHERE valor_id = ?", array($id))->row()->cnt;
        if ($en_uso > 0) return false;
        $this->db->where('id', $id)->delete('tec_atributo_valores');
        return true;
    }

    // ========================
    // VARIANTES DE PRODUCTO
    // ========================

    function get_variantes_producto($product_id) {
        $sql = "SELECT pv.*,
                    GROUP_CONCAT(CONCAT(a.nombre, ': ', av.valor) ORDER BY a.orden SEPARATOR ', ') AS combinacion
                FROM tec_product_variantes pv
                LEFT JOIN tec_variante_atributos va ON pv.id = va.variante_id
                LEFT JOIN tec_atributos a ON va.atributo_id = a.id
                LEFT JOIN tec_atributo_valores av ON va.valor_id = av.id
                WHERE pv.product_id = ?
                GROUP BY pv.id
                ORDER BY pv.id";
        return $this->db->query($sql, array($product_id))->result();
    }

    function get_variante($id) {
        return $this->db->query("SELECT * FROM tec_product_variantes WHERE id = ?", array($id))->row();
    }

    function insertar_variante($data) {
        $this->db->insert('tec_product_variantes', $data);
        return $this->db->insert_id();
    }

    function actualizar_variante($id, $data) {
        $this->db->where('id', $id)->update('tec_product_variantes', $data);
    }

    function eliminar_variante($id) {
        // Verificar que no tenga ventas
        $en_uso = $this->db->query("SELECT COUNT(*) AS cnt FROM tec_sale_items WHERE variant_id = ?", array($id))->row()->cnt;
        if ($en_uso > 0) return false;
        $this->db->where('variante_id', $id)->delete('tec_variante_atributos');
        $this->db->where('id', $id)->delete('tec_product_variantes');
        return true;
    }

    function insertar_variante_atributo($data) {
        $this->db->insert('tec_variante_atributos', $data);
    }

    function eliminar_variante_atributos($variante_id) {
        $this->db->where('variante_id', $variante_id)->delete('tec_variante_atributos');
    }

    function producto_tiene_variantes($product_id) {
        $cnt = $this->db->query("SELECT COUNT(*) AS cnt FROM tec_product_variantes WHERE product_id = ? AND activo = '1'", array($product_id))->row()->cnt;
        return $cnt > 0;
    }

    function get_atributos_de_producto($product_id) {
        $sql = "SELECT DISTINCT a.id, a.nombre
                FROM tec_variante_atributos va
                INNER JOIN tec_product_variantes pv ON va.variante_id = pv.id
                INNER JOIN tec_atributos a ON va.atributo_id = a.id
                WHERE pv.product_id = ?
                ORDER BY a.orden";
        return $this->db->query($sql, array($product_id))->result();
    }
}
