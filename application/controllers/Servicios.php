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
            $this->data["items"] = $this->Servicios_model->get_items_by_servicio($id);
        }else{
            $this->data["modo"] = 'insert';
            $this->data["items"] = array();
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
        
        // Formatear fechas de datetime-local a MySQL DATETIME
        if(!empty($data['fecha_ingreso'])) {
            $data['fecha_ingreso'] = date('Y-m-d H:i:s', strtotime($data['fecha_ingreso']));
        }
        if(!empty($data['fecha_estimada_reparacion'])) {
            $data['fecha_estimada_reparacion'] = date('Y-m-d H:i:s', strtotime($data['fecha_estimada_reparacion']));
        }
        if(!empty($data['fecha_entrega'])) {
            $data['fecha_entrega'] = date('Y-m-d H:i:s', strtotime($data['fecha_entrega']));
        } else {
            $data['fecha_entrega'] = null;
        }

        // Validaciones simples sin form_validation
        if(empty($data['cliente_nombre']) || empty($data['problema_reportado']) || empty($data['fecha_ingreso']) || empty($data['fecha_estimada_reparacion'])) {
            $this->data['msg'] = "El nombre del cliente, problema reportado, fecha de ingreso y fecha estimada son obligatorios";
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
        
        // Validar fecha_entrega obligatoria cuando estado es ENTREGADO
        if(isset($data['estado']) && $data['estado'] == 'ENTREGADO' && empty($data['fecha_entrega'])) {
            $this->data['msg'] = "La fecha de entrega es obligatoria cuando el estado es ENTREGADO";
            $this->data["rpta_msg"] = "danger";
            $this->data["modo"] = $modo;
            $this->data['page_title'] = "Servicio Técnico - " . ($modo == 'update' ? 'Editar' : 'Nuevo');
            $this->data['tecnicos'] = $this->Servicios_model->listar_tecnicos();
            $this->data['estados'] = $this->Servicios_model->get_estados_dropdown();
            $this->data['prioridades'] = $this->Servicios_model->get_prioridades_dropdown();
            $this->data['equipos_tipo'] = $this->Servicios_model->get_equipos_tipo_dropdown();
            $this->data['row'] = (object)$data;
            $this->template->load("production/index", 'servicios/add', $this->data);
            return;
        }

        // Auto-calcular costos desde items si hay items
        if(isset($data['item']) && is_array($data['item']) && count($data['item']) > 0) {
            $total_items = 0;
            for($i = 0; $i < count($data['item']); $i++) {
                $total_items += floatval($data['cost'][$i]) * floatval($data['quantity'][$i]);
            }
            $data['costo_presupuesto'] = $total_items;
            $data['costo_final'] = $total_items;
        } else {
            $data['costo_presupuesto'] = floatval($data['costo_presupuesto']);
            $data['costo_final'] = floatval($data['costo_final']);
        }
        
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
        $this->data['items'] = $this->Servicios_model->get_items_by_servicio($id);
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

    function buscar_producto(){
        $term = $this->input->get('term');
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $term_safe = $this->db->escape_like_str($term);

        $cSql = "SELECT a.id, 0 AS variant_id, a.name AS nombres, IF(b.stock IS NULL,0,b.stock) stock,
                    c.name categoria, a.impuesto, a.prod_serv, a.price
                FROM tec_products a
                LEFT JOIN tec_prod_store b ON a.id=b.product_id AND b.store_id={$store_id} AND (b.variant_id IS NULL OR b.variant_id=0)
                LEFT JOIN tec_categories c ON a.category_id=c.id
                WHERE a.activo='1' AND a.name LIKE '%{$term_safe}%'
                AND (a.category_id != 9000 OR a.prod_serv = 'S')
                AND a.id NOT IN (SELECT product_id FROM tec_product_variantes WHERE activo='1')

                UNION ALL

                SELECT pv.product_id AS id, pv.id AS variant_id,
                CONVERT(fn_product_display_name(pv.product_id, pv.id) USING latin1) AS nombres,
                IF(ps.stock IS NULL,0,ps.stock) stock,
                c.name categoria, a.impuesto, a.prod_serv,
                IF(pv.price IS NOT NULL AND pv.price > 0, pv.price, a.price) AS price
                FROM tec_product_variantes pv
                INNER JOIN tec_products a ON pv.product_id = a.id
                LEFT JOIN tec_prod_store ps ON pv.product_id = ps.product_id AND ps.variant_id = pv.id AND ps.store_id={$store_id}
                LEFT JOIN tec_categories c ON a.category_id=c.id
                WHERE a.activo='1' AND pv.activo='1'
                AND (a.category_id != 9000 OR a.prod_serv = 'S')
                AND fn_product_display_name(pv.product_id, pv.id) LIKE '%{$term_safe}%'

                ORDER BY nombres
                LIMIT 20";

        $result = $this->db->query($cSql)->result();
        echo json_encode($result);
    }

    function cambiar_estado_servicio(){
        $servicio_id = $this->input->post('servicio_id');
        $estado_nuevo = $this->input->post('estado_nuevo');

        $servicio = $this->Servicios_model->get_servicio_by_id($servicio_id);
        if(!$servicio) {
            echo json_encode(array('rpta'=>'danger', 'msg'=>'Servicio no encontrado'));
            return;
        }

        // Si es ENTREGADO, validar y guardar fecha_entrega
        if($estado_nuevo == 'ENTREGADO') {
            $fecha_entrega = $this->input->post('fecha_entrega');
            if(empty($fecha_entrega)) {
                echo json_encode(array('rpta'=>'danger', 'msg'=>'Debe ingresar la fecha de entrega'));
                return;
            }
            $fecha_entrega = date('Y-m-d H:i:s', strtotime($fecha_entrega));
            $this->db->set('fecha_entrega', $fecha_entrega)->where('id', $servicio_id)->update('tec_servicios_tecnicos');
        }

        $result = $this->Servicios_model->cambiar_estado(
            $servicio_id,
            $servicio->estado,
            $estado_nuevo,
            null,
            null,
            $_SESSION['user_id']
        );

        if($result) {
            echo json_encode(array('rpta'=>'success', 'msg'=>'Estado cambiado correctamente'));
        } else {
            echo json_encode(array('rpta'=>'danger', 'msg'=>'No se pudo cambiar el estado'));
        }
    }

    function agregar_nota_servicio(){
        $servicio_id = $this->input->post('servicio_id');
        $tipo_nota = $this->input->post('tipo_nota');
        $nota = $this->input->post('nota');

        $result = $this->Servicios_model->agregar_nota(
            $servicio_id,
            $nota,
            $tipo_nota,
            null,
            $_SESSION['user_id']
        );

        if($result) {
            echo json_encode(array('rpta'=>'success', 'msg'=>'Nota agregada correctamente'));
        } else {
            echo json_encode(array('rpta'=>'danger', 'msg'=>'No se pudo agregar la nota'));
        }
    }

    function buscar_proveedor(){
        $q = isset($_GET["q"]) ? trim($_GET["q"]) : '';
        if(strlen($q) < 1) {
            header('Content-Type: application/json');
            echo json_encode(array());
            return;
        }
        $resultado = $this->Servicios_model->buscar_proveedor($q);
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    function print_etiqueta($id) {
        $servicio = $this->Servicios_model->get_servicio_by_id($id);
        if (!$servicio) {
            show_error('Servicio no encontrado', 404);
            return;
        }

        $data['servicio'] = $servicio;
        $this->load->view('servicios/print_etiqueta', $data);
    }

}