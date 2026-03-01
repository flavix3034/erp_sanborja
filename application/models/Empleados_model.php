<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleados_model extends CI_Model {

    function lista_empleados() {
        return $this->db->query("SELECT * FROM tec_empleados ORDER BY apellidos, nombres")->result_array();
    }

    function lista_empleados_activos() {
        return $this->db->query("SELECT * FROM tec_empleados WHERE activo = '1' ORDER BY apellidos, nombres")->result_array();
    }

    function get_by_id($id) {
        return $this->db->query("SELECT * FROM tec_empleados WHERE id = ?", array($id))->row();
    }

    function insertar($data) {
        $this->db->insert("tec_empleados", $data);
        return $this->db->insert_id();
    }

    function actualizar($id, $data) {
        $this->db->set($data)->where("id", $id)->update("tec_empleados");
    }

    function anular($id) {
        $this->db->set(array("activo" => ''))->where("id", $id)->update("tec_empleados");
    }

    function activar($id) {
        $this->db->set(array("activo" => '1'))->where("id", $id)->update("tec_empleados");
    }
}
