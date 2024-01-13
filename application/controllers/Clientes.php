<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    function __construct(){
        parent::__construct();
        session_start();
        //$this->load->helper('url');
        //$this->load->clientes();
    }

	public function index()
	{
		$this->data['page_title'] = 'Clientes:';
		$this->template->load('production/index', 'clientes/index', $this->data);
	}

    function get_clientes() {
		$campos = "id,name,cf1,cf2,phone,email,store_id,direccion"; // sin espacios en blanco
		$cSql = "select {$campos} from tec_customers";
        $result = $this->db->query($cSql)->result_array();
        
        $ar_campos = explode(",",$campos);
        echo $this->fm->json_datatable($ar_campos,$result);
    }

    function add($cerrar=null){
		$this->data['page_title'] = 'Agregar Clientes:';
		$this->data['cerrar'] = $cerrar;
		$this->template->load('production/index', 'clientes/add', $this->data);    	
    }

    function save(){
    	$name 	= $_REQUEST["name"];
    	$cf1 	= $_REQUEST["cf1"];
    	$cf2 	= $_REQUEST["cf2"];
    	$phone 	= $_REQUEST["phone"];
    	$email 	= $_REQUEST["email"];
    	$direccion = $_REQUEST["direccion"];

    	// Verificando si ya existe
    	$existe_cli = false;

    	if($cf1 != null){
	    	$query = $this->db->select("cf1, name")->from("tec_customers")->where("cf1",$cf1)->get();
	    	foreach($query->result() as $r){
	    		$existe_cli = true;
	    		//die("Kero");
	    	}
	    }

    	if($cf2 != null){
	    	$query = $this->db->select("cf2, name")->from("tec_customers")->where("cf2",$cf2)->get();
	    	foreach($query->result() as $r){
	    		$existe_cli = true;
	    		//die("Mero");
	    	}
	    }

    	if(!$existe_cli){
	    	
	    	$cSql = "insert into tec_customers(name, cf1, cf2, phone, email, direccion) values(?,?,?,?,?,?)";

	    	if($this->db->query($cSql, array($name, $cf1, $cf2, $phone, $email, $direccion))){
	    		$this->data['msg'] 		= "Grabacion correcta";
				$this->data['title'] 	= 'Clientes:';
			
				if(isset($_POST["name"])){
					$this->template->load('production/index', 'clientes/add', $this->data);
				}else{
					echo "Grabacion correcta...Modo Ajax";
				}
			}
		}

    }

    function busqueda_nombre(){
    	$dato1 = $_REQUEST["dato1"];

    	$this->db->select("id, name, cf1, cf2");
    	
    	$bandera = false;
    	if(strlen($dato1)==8){
	    	$this->db->where("cf1",$dato1);    		
	    	$bandera = true;
	    	$tipo_datos = "DNI";
    	}elseif(strlen($dato1)==11){
    	    $this->db->where("cf2",$dato1);
    	    $bandera = true;
    	    $tipo_datos = "RUC";
    	}

	    $result = $this->db->get("tec_customers")->result();

	    $respuesta["name_cliente"] 	= "No existe";
       	$respuesta["rpta"] 			= false;

	    foreach($result as $r){
	        $respuesta["name_cliente"] 	= $r->name;
	        $respuesta["cf2"] 			= $r->cf2; 
	        $respuesta["id"] 			= $r->id;
	        $respuesta["rpta"] 			= true;
	    }

    	if ($bandera && $respuesta["rpta"]==false){
		    
	   		$busqueda_json 			= true;
	   		
	   		//******************************************
	   		$obj = $this->fm->consulta_dato_api($dato1);
	   		//******************************************

	   		if(isset($obj->error)){
	   			$busqueda_json 		= false;
	   		}else{
	   			$id = $this->ingreso_clientes_api($obj, $tipo_datos);

	   			$respuesta["rpta"]				= true;
	   			$respuesta["name_cliente"]		= $obj->nombre;
	   			$respuesta["cf2"] 				= $obj->numeroDocumento;
	   			$respuesta["id"] 				= $id;
	   			$respuesta["direccion"] 		= $obj->direccion;
	   		}

		}
    	echo json_encode($respuesta);
    }

	function ingreso_clientes_api($objeto, $tipo_datos){
		if($tipo_datos == "RUC"){
			//var_dump($objeto);
			$ruc 		= $objeto->numeroDocumento;
			$nombre 	= $objeto->nombre;
			$direccion 	= $objeto->direccion;
			$ar = array("cf2"=>$ruc, "name"=>$nombre, "direccion"=>$direccion);
		}else{
			$ruc 		= $objeto->numeroDocumento;
			$nombre 	= $objeto->nombre;
			$direccion 	= $objeto->direccion;
			$ar = array("cf1"=>$ruc, "name"=>$nombre, "direccion"=>$direccion);
		}

		$this->db->set($ar)->insert("tec_customers");
		return $this->db->insert_id();
	}    
}
