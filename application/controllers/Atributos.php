<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos extends CI_Controller
{
    function __construct() {
        parent::__construct();
        session_start();
        if (!isset($_SESSION["user_id"])) {
            redirect(base_url("welcome/index"));
            return;
        }
        $this->load->helper('url');
        $this->load->model('atributos_model');
    }

    function index() {
        $this->data['page_title'] = "Atributos de Producto";
        $this->data['atributos'] = $this->atributos_model->get_all_atributos();
        foreach ($this->data['atributos'] as &$attr) {
            $attr->valores = $this->atributos_model->get_valores($attr->id);
        }
        $this->template->load('production/index', 'products/atributos', $this->data);
    }

    function guardar_atributo() {
        header('Content-Type: application/json');
        $data = array(
            'id'     => intval($this->input->post('id')),
            'nombre' => trim($this->input->post('nombre')),
            'orden'  => intval($this->input->post('orden'))
        );

        if (empty($data['nombre'])) {
            echo json_encode(array('rpta' => 'error', 'msg' => 'El nombre es obligatorio.'));
            return;
        }

        $id = $this->atributos_model->guardar_atributo($data);
        echo json_encode(array('rpta' => 'success', 'id' => $id));
    }

    function toggle_atributo() {
        header('Content-Type: application/json');
        $id = intval($this->input->post('id'));
        $activo = $this->input->post('activo');

        if ($activo == '1') {
            $this->atributos_model->desactivar_atributo($id);
        } else {
            $this->atributos_model->activar_atributo($id);
        }
        echo json_encode(array('rpta' => 'success'));
    }

    function guardar_valor() {
        header('Content-Type: application/json');
        $data = array(
            'id'          => intval($this->input->post('id')),
            'atributo_id' => intval($this->input->post('atributo_id')),
            'valor'       => trim($this->input->post('valor')),
            'orden'       => intval($this->input->post('orden'))
        );

        if (empty($data['valor'])) {
            echo json_encode(array('rpta' => 'error', 'msg' => 'El valor es obligatorio.'));
            return;
        }

        $id = $this->atributos_model->guardar_valor($data);
        echo json_encode(array('rpta' => 'success', 'id' => $id));
    }

    function eliminar_valor() {
        header('Content-Type: application/json');
        $id = intval($this->input->post('id'));
        $ok = $this->atributos_model->eliminar_valor($id);

        if ($ok) {
            echo json_encode(array('rpta' => 'success'));
        } else {
            echo json_encode(array('rpta' => 'error', 'msg' => 'No se puede eliminar: el valor esta en uso en variantes de producto.'));
        }
    }

    // AJAX: retorna atributos activos con sus valores (para formulario de producto)
    function get_atributos_json() {
        header('Content-Type: application/json');
        $atributos = $this->atributos_model->get_atributos_activos();
        $result = array();
        foreach ($atributos as $a) {
            $valores = $this->atributos_model->get_valores($a->id);
            $result[] = array(
                'id'      => intval($a->id),
                'nombre'  => $a->nombre,
                'valores' => array_map(function($v) {
                    return array('id' => intval($v->id), 'valor' => $v->valor);
                }, $valores)
            );
        }
        echo json_encode($result);
    }
}
