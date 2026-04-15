<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajustes extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->data['page_title']   = "Ajustes";
        $this->data['nota_pie']     = $this->db->query("select nota_pie from tec_stores where id = 1")->row()->nota_pie;
        
        $this->template->load('production/index', 'ajustes/index', $this->data);
    }

    function save(){
        $ar = array("nota_pie" => $_POST["nota_pie"]);

        $this->db->set($ar)->where("id",1)->update("tec_stores");
        $this->data["msg"] = "Se actualiza correctamente";
        $this->data["rpta_msg"] = "success";

        $this->data['page_title']   = "Ajustes";
        $this->data['nota_pie']     = $this->db->query("select nota_pie from tec_stores where id = 1")->row()->nota_pie;
        
        $this->template->load('production/index', 'ajustes/index', $this->data);        
    }
}