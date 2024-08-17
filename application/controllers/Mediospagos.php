<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mediospagos extends CI_controller
{

    function __construct() {
        parent::__construct();

        session_start();
        //$this->load->model('categorias_model');
    }

    function index(){
        $this->data['page_title'] = "Medios de Pagos";
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'mediospagos/index', $this->data);
    }

    function get_medios(){
        //$result     = $this->db->select("id, name")->get("tec_categories")->result_array();
        $cSql       = "select id, forma_pago, descrip, case when activo='1' then 'Activo' else 'Inactivo' end activo" 
                      .", concat('<a href=\'#\' title=\'Anular\' onclick=\'eliminar(',id,')\'><i class=\'glyphicon glyphicon-remove\' style=\'font-size:16px\'></i></a>') actions"
                      ." from tec_forma_pagos order by forma_pago";  // id, forma_pago, descrip, activo
        $result     = $this->db->query($cSql)->result_array();
        $ar_campos  = array("id", "forma_pago","descrip","activo","actions"); 

        echo $this->fm->json_datatable($ar_campos, $result);
    }

    function add(){
        $this->data['page_title'] = "Agregar Medios de Pagos";
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'mediospagos/add', $this->data);
    }

    function save(){
        $modo       = $_POST["modo"];
        $forma_pago = $_POST["forma_pago"];
        $descrip    = $_POST["descrip"];

        // VALIDACION DE DATOS -----------------
        $this->form_validation->set_rules('forma_pago', 'Forma de Pago', 'trim|required'); // |min_length[5]|max_length[12]
        $this->form_validation->set_rules('descrip', 'Descripcion', 'trim|required');

        if ($this->form_validation->run() == true){

            //$ar["name"] = $name;

            if($modo == "I"){
                
                $ar["forma_pago"]   = $forma_pago;
                $ar["descrip"]      = $descrip;
                $this->db->insert("tec_forma_pagos",$ar);
                $this->index();
            }else{
                $id = $_POST["id"];
                $this->db->set($ar)->where("id",$id)->update("tec_forma_pagos");
                $this->index();
            }

        }else{
          $data["msg"] = validation_errors();
          $data["rpta_msg"] = "danger";
        }

        //$activo = $_POST["activo"];
    }

    function edit($id){
        $this->data['page_title'] = "Editar Categoria";
        $this->data['Admin'] = $this->Admin;
        $cSql = "select * from tec_categories where id = $id";
        //echo $cSql;
        $this->data["query1"] = $this->db->query($cSql,array($id));
        
        $this->template->load('production/index', 'categorias/add', $this->data);
    }

    function anular($id){
        $this->data['page_title'] = "Medios de Pagos";
        $this->data['Admin'] = $this->Admin;

        // anulando la categoria
        $ar = array("activo"=>'');
        $this->db->set($ar)->where("id",$id)->update("tec_forma_pagos");
        
        $this->data["msg"] = "Se ha anulado el Medio de Pago";
        $this->data["rpta_msg"] = "success";
        $this->template->load('production/index', 'mediospagos/index', $this->data);
    }

}