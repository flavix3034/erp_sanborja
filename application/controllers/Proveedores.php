<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

    function __construct(){
        parent::__construct();
        session_start();
    }

    public function index(){
		$this->data['title'] = 'Proveedores:';
		$result 	= $this->db->select("id,nombre,ruc,correo,phone,direccion, 
			concat('<a href=\'#\' onclick=\'modificar(',id,')\'><i class=\'glyphicon glyphicon-edit\'></i></a>&nbsp;&nbsp;
            <a href=\'#\' onclick=\'eliminar(',id,')\'><i class=\'glyphicon glyphicon-remove\'></i></a>') op")->from('tec_proveedores')->get()->result_array();
		//die(str_replace("\n","<br>",var_dump($result,true)));
		$cols 			= array("id","nombre","ruc","correo","phone","direccion","op");
		$cols_titulos 	= array("id","nombre","ruc","correo","phone","direccion","op");
		$ar_align 		= array("1","1","1","1","1","1","1");
		$ar_pie 		= array("","","","","","","");
		$this->data['page_title'] = 'Proveedores:';
		$this->data["tabla_proveedores"] = $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
		$this->template->load('production/index', 'proveedores/index', $this->data);
	}

    function add(){
		if(isset($_REQUEST["id"])){
			$id = $_REQUEST["id"];
			//die($this->db->select("*")->from("tec_proveedores")->where("id",$id)->get_compiled_select());
			$this->data["query_p1"] = $this->db->select("*")->from("tec_proveedores")->where("id",$id)->get(); 
		}
		$this->data['page_title'] = 'Agregar Proveedores:';
		$this->template->load('production/index', 'proveedores/add', $this->data);    	
    }

    function save(){
		$this->form_validation->set_rules('nombre', 'Nombre del Proveedor', 'required');
		$this->form_validation->set_rules('ruc', 'Ruc del Proveedor', 'required');
		$this->form_validation->set_rules('ruc', 'Ruc del Proveedor', 'exact_length[11]');
		if(strlen($_POST["correo"])>0){
			$this->form_validation->set_rules('correo', 'Correo', 'valid_email');
		}

		if ($this->form_validation->run() == true){
		    // id,nombre,ruc,correo,phone,direccion
			$nombre 	= $_POST["nombre"];
			$ruc 		= $_POST["ruc"];
			$correo 	= $_POST["correo"];
			$phone 		= $_POST["phone"];
			$direccion 	= $_POST["direccion"];

			$cSql = "insert into tec_proveedores(nombre, ruc, correo, phone, direccion) values(?,?,?,?,?)";

			if($this->db->query($cSql, array($nombre, $ruc, $correo, $phone, $direccion))){
				$this->data['msg'] 		= "Grabacion correcta";
				$this->data['rpta_msg'] = "success";

				//$this->data['title'] 	= 'Proveedores:';
				//$this->template->load('production/index', 'proveedores/index', $this->data);
				$this->index();
			}else{
				$this->data['msg'] 		= "No se ha podido grabar";
				$this->data['rpta_msg'] = "danger";

				$this->data['title'] 	= 'Proveedores:';
				$this->template->load('production/index', 'proveedores/add', $this->data);				
			}
	    }else{
			$this->data['msg'] 		= validation_errors(); //"No se ha podido grabar";
			$this->data['rpta_msg'] = "danger";

			$this->data['title'] 	= 'Proveedores:';
			$this->template->load('production/index', 'proveedores/add', $this->data);
	    }
		
    }

    function eliminar(){
    	$id = $_REQUEST["id"];
    	$cSql = "select count(1) cant from tec_compras where proveedor_id = ?";
    	$query = $this->db->query($cSql,array($id));
    	$cant = 0;
    	foreach($query->result() as $r){
    		$cant = $r->cant * 1;
    	}
    	if ($cant == 0){
    		$this->db->where("id",$id)->delete("tec_proveedores");
    		echo "0";
    	}else{
    		echo "1";
    	}
    }

}
