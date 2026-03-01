<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Servicios extends CI_Controller
{
    function __construct() {
        parent::__construct();
        session_start();
        
        if (!isset($_SESSION["user_id"])) {
            die("No tiene sesión disponible. <a href=\"" . base_url("welcome/index") . "\">Login</a>");
        }
        
        $this->load->model('Servicios_model');
        $this->load->helper('url');
        $this->load->helper('form');
    }

    function index($estado="0",$tecnico="0") {
        $this->data['page_title'] = "Servicio Técnico";
        $this->data['estado'] = $estado;
        $this->data['tecnico'] = $tecnico;
        $this->data['tecnicos'] = $this->Servicios_model->listar_tecnicos();
        $this->data['estados'] = $this->Servicios_model->get_estados_dropdown();
        
        // Mostrar mensaje flash si existe
        if(isset($_SESSION['flash_message'])) {
            $this->data['msg'] = $_SESSION['flash_message'];
            $this->data['rpta_msg'] = $_SESSION['flash_type'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        
        $this->template->load('production/index', 'servicios/index', $this->data);
    }

    function add($id = null){
        if(!is_null($id)){
            $this->data["row"] = $this->Servicios_model->get_servicio_by_id($id);
            $this->data["modo"] = 'update';
            $this->data["id"] = $id;
        }else{
            $this->data["modo"] = 'insert'; 
        }

        $this->data['page_title'] = "Servicio Técnico - ".($id ? "Editar" : "Nuevo");
        $this->data['tecnicos'] = $this->Servicios_model->listar_tecnicos();
        $this->data['estados'] = $this->Servicios_model->get_estados_dropdown();
        $this->data['prioridades'] = $this->Servicios_model->get_prioridades_dropdown();
        $this->data['equipos_tipo'] = $this->Servicios_model->get_equipos_tipo_dropdown();
        
        $this->template->load("production/index", 'servicios/add', $this->data);
    }

    function save(){
        $data = $_POST;
        $modo = strtolower($data['modo']);
        
        // Validaciones simples sin form_validation
        if(empty($data['cliente_nombre']) || empty($data['problema_reportado'])) {
            $this->data['msg'] = "El nombre del cliente y el problema reportado son obligatorios";
            $this->data["rpta_msg"] = "danger";
            
            // Recargar formulario con datos
            $this->data["modo"] = $modo;
            $this->data['page_title'] = "Servicio Técnico - " . ($modo == 'update' ? 'Editar' : 'Nuevo');
            $this->data['tecnicos'] = $this->Servicios_model->listar_tecnicos();
            $this->data['estados'] = $this->Servicios_model->get_estados_dropdown();
            $this->data['prioridades'] = $this->Servicios_model->get_prioridades_dropdown();
            $this->data['equipos_tipo'] = $this->Servicios_model->get_equipos_tipo_dropdown();
            $this->data['row'] = (object)$data; // Para mantener los datos
            
            $this->template->load("production/index", 'servicios/add', $this->data);
            return;
        }
        
        // Manejar valores numéricos
        $data['costo_presupuesto'] = floatval($data['costo_presupuesto']);
        $data['costo_final'] = floatval($data['costo_final']);
        
        // Manejar tecnico_asignado - si está vacío, no incluirlo
        if(isset($data['tecnico_asignado']) && trim($data['tecnico_asignado']) === '') {
            unset($data['tecnico_asignado']);
        }
        
        // Convertir a uppercase
        $data['cliente_nombre'] = strtoupper($data['cliente_nombre']);
        $data['equipo_descripcion'] = strtoupper($data['equipo_descripcion']);
        $data['problema_reportado'] = strtoupper($data['problema_reportado']);
        
        if(isset($data['marca'])) {
            $data['marca'] = strtoupper($data['marca']);
        }
        if(isset($data['modelo'])) {
            $data['modelo'] = strtoupper($data['modelo']);
        }
        
        // Agregar usuario que registra
        if(isset($_SESSION["user_id"])) {
            $data['usuario_registra'] = $_SESSION["user_id"];
        }
        
        // Guardar servicio
        if($this->Servicios_model->guardar_servicio($data)){
            if($modo == 'insert'){
                $mensaje = "Servicio registrado correctamente";
            } else {
                $mensaje = "Servicio actualizado correctamente";
            }
            
            // Guardar mensaje en sesión y redirigir
            $_SESSION['flash_message'] = $mensaje;
            $_SESSION['flash_type'] = "success";
            redirect('servicios/index');
        } else {
            $this->data['msg'] = "No se ha podido guardar"; 
            $this->data["rpta_msg"] = "danger";
            
            // Recargar formulario con datos
            $this->data["modo"] = $modo;
            $this->data['page_title'] = "Servicio Técnico - " . ($modo == 'update' ? 'Editar' : 'Nuevo');
            $this->data['tecnicos'] = $this->Servicios_model->listar_tecnicos();
            $this->data['estados'] = $this->Servicios_model->get_estados_dropdown();
            $this->data['prioridades'] = $this->Servicios_model->get_prioridades_dropdown();
            $this->data['equipos_tipo'] = $this->Servicios_model->get_equipos_tipo_dropdown();
            $this->data['row'] = (object)$data;
            
            $this->template->load("production/index", 'servicios/add', $this->data);
        }
    }

    function view($id) {
        $this->data['servicio'] = $this->Servicios_model->get_servicio_by_id($id);
        $this->data['historial'] = $this->Servicios_model->get_historial_estados($id);
        $this->data['notas'] = $this->Servicios_model->get_notas_servicio($id);
        $this->data['page_title'] = "Detalles del Servicio";
        
        $this->template->load("production/index", 'servicios/view', $this->data);
    }

    function getServicios(){
        // Simplificado: solo leer POST sin validaciones complejas
        $estado = $this->input->post('estado') ? $this->input->post('estado') : '0';
        $tecnico = $this->input->post('tecnico') ? $this->input->post('tecnico') : '0';
        
        $this->Servicios_model->getServicios_simple($estado, $tecnico);
    }

    function anular(){
        $id = $_POST["id"];
        
        // Verificar si el servicio existe y está activo
        $servicio = $this->Servicios_model->get_servicio_by_id($id);
        if(!$servicio) {
            $ar["rpta"] = "danger";
            $ar["msg"] = "Servicio no encontrado";
            echo json_encode($ar);
            return;
        }
        
        if($this->Servicios_model->anular_servicio($id)){
            // Registrar cambio de estado a CANCELADO
            $this->Servicios_model->cambiar_estado(
                $id, 
                $servicio->estado, 
                'CANCELADO',
                null,
                'Servicio anulado desde el sistema',
                $_SESSION["user_id"]
            );
            
            $ar["rpta"] = "success";
            $ar["msg"] = "Servicio anulado correctamente";
        } else {
            $ar["rpta"] = "danger";
            $ar["msg"] = "No se pudo anular el servicio";
        }
        echo json_encode($ar);
    }
}