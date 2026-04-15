<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleados extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Empleados_model');
    }

    function index() {
        $this->data['page_title'] = "Empleados";
        $this->data['empleados'] = $this->Empleados_model->lista_empleados();
        $this->template->load('production/index', 'empleados/index', $this->data);
    }

    function add() {
        $this->data['page_title'] = "Agregar Empleado";
        $this->template->load('production/index', 'empleados/add', $this->data);
    }

    function edit($id) {
        $empleado = $this->Empleados_model->get_by_id($id);
        if (!$empleado) {
            show_404();
            return;
        }
        $this->data['page_title'] = "Editar Empleado";
        $this->data['empleado'] = $empleado;
        $this->template->load('production/index', 'empleados/add', $this->data);
    }

    function save() {
        $modo = $_POST["modo"];
        $data = array(
            "nombres" => strtoupper(trim($_POST["nombres"])),
            "apellidos" => strtoupper(trim($_POST["apellidos"])),
            "dni" => trim($_POST["dni"]),
            "telefono" => trim($_POST["telefono"]),
            "cargo" => strtoupper(trim($_POST["cargo"])),
            "area" => strtoupper(trim($_POST["area"])),
            "especialidad" => strtoupper(trim($_POST["cargo"])) == 'TECNICO' ? strtoupper(trim($_POST["especialidad"])) : null,
            "fecha_ingreso" => !empty($_POST["fecha_ingreso"]) ? $_POST["fecha_ingreso"] : null
        );

        if (empty($data["nombres"]) || empty($data["apellidos"])) {
            $this->data['msg'] = "Los campos Nombres y Apellidos son obligatorios.";
            $this->data['rpta_msg'] = "danger";
            if ($modo == "I") {
                $this->add();
            } else {
                $this->edit($_POST["id"]);
            }
            return;
        }

        if ($modo == "I") {
            $this->Empleados_model->insertar($data);
            $this->data['msg'] = "Empleado registrado correctamente.";
        } else {
            $id = intval($_POST["id"]);
            $this->Empleados_model->actualizar($id, $data);
            $this->data['msg'] = "Empleado actualizado correctamente.";
        }

        $this->data['rpta_msg'] = "success";
        $this->index();
    }

    function anular($id) {
        $this->Empleados_model->anular($id);
        $this->data['msg'] = "Empleado desactivado correctamente.";
        $this->data['rpta_msg'] = "success";
        $this->index();
    }

    function activar($id) {
        $this->Empleados_model->activar($id);
        $this->data['msg'] = "Empleado activado correctamente.";
        $this->data['rpta_msg'] = "success";
        $this->index();
    }

    // AJAX: lista para selects (usado por CajaChica)
    function get_activos_json() {
        $empleados = $this->Empleados_model->lista_empleados_activos();
        header('Content-Type: application/json');
        echo json_encode($empleados);
    }
}
