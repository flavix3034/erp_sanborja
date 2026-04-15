<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends MY_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('categorias_model');
    }

    function index(){
        $this->data['page_title'] = "Categorias";
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'categorias/index', $this->data);
    }

    function get_categorias(){
        //$result     = $this->db->select("id, name")->get("tec_categories")->result_array();
        $result     = $this->categorias_model->lista_categorias();
        $ar_campos  = array("id", "name","activo","actions"); 

        echo $this->fm->json_datatable($ar_campos, $result);
    }

    function add(){
        $this->data['page_title'] = "Agregar Categorias";
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'categorias/add', $this->data);
    }

    function save(){
        $modo = $_POST["modo"];
        $name = $_POST["name"];
        //$activo = $_POST["activo"];
        $ar["name"] = $name;

        if($modo == "I"){
            $maximo = $this->db->query("select max(id) maximo from tec_categories where id < 9000")->row()->maximo;
            $ar["id"] = $maximo + 1;
            $this->db->insert("tec_categories",$ar);
            $this->index();
        }else{
            $id = $_POST["id"];
            $this->db->set($ar)->where("id",$id)->update("tec_categories");
            $this->index();
        }
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
        $this->data['page_title'] = "Categorias";
        $this->data['Admin'] = $this->Admin;

        // anulando la categoria
        $ar = array("activo"=>'');
        $this->db->set($ar)->where("id",$id)->update("tec_categories");
        
        $this->data["msg"] = "Se ha anulado la categoria";
        $this->data["rpta_msg"] = "success";
        $this->template->load('production/index', 'categorias/index', $this->data);
    }

}