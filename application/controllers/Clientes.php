<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends MY_Controller {

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
    	$cf1 	= isset($_REQUEST["cf1"]) ? trim($_REQUEST["cf1"]) : "";
    	$cf2 	= isset($_REQUEST["cf2"]) ? trim($_REQUEST["cf2"]) : "";
    	$phone 	= isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : "";
    	$email 	= isset($_REQUEST["email"]) ? $_REQUEST["email"] : "";
    	$direccion = isset($_REQUEST["direccion"]) ? $_REQUEST["direccion"] : "";

    	// Verificando si ya existe
    	$existe_id = 0;

    	if(strlen($cf1) > 0){
	    	$row = $this->db->select("id")->from("tec_customers")->where("cf1",$cf1)->get()->row();
	    	if($row) $existe_id = $row->id;
	    }

    	if($existe_id == 0 && strlen($cf2) > 0){
	    	$row = $this->db->select("id")->from("tec_customers")->where("cf2",$cf2)->get()->row();
	    	if($row) $existe_id = $row->id;
	    }

    	if($existe_id > 0){
    		// Actualizar cliente existente
    		$this->db->where("id", $existe_id);
    		$this->db->update("tec_customers", array(
    			"name" => $name, "cf1" => $cf1, "cf2" => $cf2,
    			"phone" => $phone, "email" => $email, "direccion" => $direccion
    		));
    		echo json_encode(array("ok" => true, "msg" => "Cliente actualizado", "modo" => "update", "id" => $existe_id));
    	} else {
	    	$cSql = "insert into tec_customers(name, cf1, cf2, phone, email, direccion) values(?,?,?,?,?,?)";
	    	if($this->db->query($cSql, array($name, $cf1, $cf2, $phone, $email, $direccion))){
	    		echo json_encode(array("ok" => true, "msg" => "Cliente guardado", "modo" => "insert", "id" => $this->db->insert_id()));
			} else {
				echo json_encode(array("ok" => false, "msg" => "Error al guardar"));
			}
		}
    }

    function holak($documento){
    	$obj = $this->fm->consulta_dato_api($documento);
    	print_r($obj);
    }

    function verificar_existe(){
        $documento = isset($_REQUEST["documento"]) ? trim($_REQUEST["documento"]) : "";
        $respuesta = array("existe" => false);

        if(strlen($documento) == 8){
            $row = $this->db->select("id, name, cf1, cf2, phone, email, direccion")
                ->from("tec_customers")->where("cf1", $documento)->get()->row();
        } elseif(strlen($documento) == 11){
            $row = $this->db->select("id, name, cf1, cf2, phone, email, direccion")
                ->from("tec_customers")->where("cf2", $documento)->get()->row();
        } else {
            $row = null;
        }

        if($row){
            $respuesta["existe"] = true;
            $respuesta["id"] = $row->id;
            $respuesta["name"] = $row->name;
            $respuesta["cf1"] = $row->cf1;
            $respuesta["cf2"] = $row->cf2;
            $respuesta["phone"] = $row->phone;
            $respuesta["email"] = $row->email;
            $respuesta["direccion"] = $row->direccion;
        }
        echo json_encode($respuesta);
    }

    function consultar_reniec(){
        $documento = isset($_REQUEST["documento"]) ? trim($_REQUEST["documento"]) : "";
        $respuesta = array("ok" => false, "msg" => "Documento no válido");

        if (strlen($documento) == 8 || strlen($documento) == 11) {
            $obj = $this->fm->consulta_dato_api($documento);
            if (isset($obj->error)) {
                $respuesta["msg"] = $obj->error;
            } else {
                $respuesta["ok"] = true;
                if (strlen($documento) < 11) {
                    $respuesta["nombres"] = $obj->datos->nombres;
                    $respuesta["ape_paterno"] = $obj->datos->ape_paterno;
                    $respuesta["ape_materno"] = $obj->datos->ape_materno;
                    $respuesta["direccion"] = isset($obj->datos->domiciliado->direccion) ? $obj->datos->domiciliado->direccion : "";
                } else {
                    $respuesta["nombres"] = $obj->datos->razon_social;
                    $respuesta["ape_paterno"] = "";
                    $respuesta["ape_materno"] = "";
                    $respuesta["direccion"] = isset($obj->datos->domiciliado->direccion) ? $obj->datos->domiciliado->direccion : "";
                }
            }
        }
        echo json_encode($respuesta);
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
	   			$respuesta["name_cliente"]		= $obj->error;
	   		}else{
	   			$id = $this->ingreso_clientes_api($obj, $tipo_datos);

	   			$respuesta["rpta"]				= true;

	   			if(strlen(trim($dato1))<11){
	   			
	   				$respuesta["name_cliente"]		= $obj->datos->nombres . " " . $obj->datos->ape_paterno . " " . $obj->datos->ape_materno;
	   				$respuesta["cf2"] 				= $obj->datos->dni;
	   				$respuesta["id"] 				= $id;
	   				$respuesta["direccion"] 		= $obj->datos->domiciliado->direccion;
	   			}

	   			if(strlen(trim($dato1))==11){
	   			
	   				$respuesta["name_cliente"]		= $obj->datos->razon_social;
	   				$respuesta["cf2"] 				= $obj->datos->ruc;
	   				$respuesta["id"] 				= $id;
	   				$respuesta["direccion"] 		= $obj->datos->domiciliado->direccion;
	   			}
	   		}

		}
    	echo json_encode($respuesta);
    }
/*
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
*/
	function ingreso_clientes_api($obj, $tipo_datos){
		if($tipo_datos == "RUC"){
			//var_dump($objeto);
			$ruc 		= $obj->datos->ruc;
			$nombre 	= $obj->datos->razon_social; //$obj->datos->nombres . " " . $obj->datos->ape_paterno . " " . $obj->datos->ape_materno;
			$direccion 	= $obj->datos->domiciliado->direccion;
			$ar = array("cf2"=>$ruc, "name"=>$nombre, "direccion"=>$direccion);
		}else{
			$ruc 		= $obj->datos->dni;
			$nombre 	= $obj->datos->nombres . " " . $obj->datos->ape_paterno . " " . $obj->datos->ape_materno;
			$direccion 	= $obj->datos->domiciliado->direccion;
			$ar = array("cf1"=>$ruc, "name"=>$nombre, "direccion"=>$direccion);
		}

		$this->db->set($ar)->insert("tec_customers");
		return $this->db->insert_id();
		
		//return 1;
	}    

}
