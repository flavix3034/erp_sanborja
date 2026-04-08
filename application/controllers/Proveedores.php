<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

    function __construct(){
        parent::__construct();
        session_start();
    }

    public function index(){
		$this->data['page_title'] = 'Proveedores';
		$this->template->load('production/index', 'proveedores/index', $this->data);
	}

    public function ver($id){
        $r = $this->db->where('id', intval($id))->get('tec_proveedores')->row();
        if (!$r) { echo '<p class="text-danger">Proveedor no encontrado.</p>'; return; }
        $fila = function($label, $valor) {
            if (empty($valor)) return '';
            return '<tr><td style="font-weight:600;color:#555;width:38%;padding:6px 8px;">' . $label . '</td>'
                 . '<td style="padding:6px 8px;">' . htmlspecialchars($valor) . '</td></tr>';
        };
        echo '<table class="table table-condensed" style="margin:0; font-size:13px;">';
        echo $fila('Razón Social',      $r->nombre);
        echo $fila('RUC',               $r->ruc);
        echo $fila('Correo',            $r->correo);
        echo $fila('Teléfono',          $r->phone);
        echo $fila('Tel. Adicional',    isset($r->phone2)   ? $r->phone2   : '');
        echo $fila('Contacto directo',  isset($r->contacto) ? $r->contacto : '');
        echo $fila('Dirección',         $r->direccion);
        if (!empty($r->notas)) {
            echo '<tr><td style="font-weight:600;color:#555;padding:6px 8px;vertical-align:top;">Notas / Especialidad</td>'
               . '<td style="padding:6px 8px;white-space:pre-wrap;">' . htmlspecialchars($r->notas) . '</td></tr>';
        }
        echo '</table>';
    }

    public function getProveedores(){
        header('Content-Type: application/json');
        $result = $this->db->query("SELECT
            id, nombre,
            COALESCE(ruc,'') ruc,
            COALESCE(correo,'') correo,
            COALESCE(phone,'') phone,
            COALESCE(phone2,'') phone2,
            COALESCE(contacto,'') contacto,
            COALESCE(direccion,'') direccion,
            COALESCE(notas,'') notas
            FROM tec_proveedores ORDER BY nombre ASC")->result_array();

        $data = array();
        foreach ($result as $r) {
            $acciones = '<a href="#" onclick="verDetalle(' . $r['id'] . ',' . htmlspecialchars(json_encode($r['nombre'])) . ')" title="Ver detalle"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;'
                      . '<a href="#" onclick="modificar(' . $r['id'] . ')" title="Editar"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;'
                      . '<a href="#" onclick="eliminar(' . $r['id'] . ')" title="Eliminar"><i class="glyphicon glyphicon-remove"></i></a>';
            $data[] = array(
                $r['id'],
                $r['nombre'],
                $r['ruc'],
                $r['correo'],
                $r['phone'],
                $r['phone2'],
                $r['contacto'],
                $r['direccion'],
                $r['notas'],
                $acciones
            );
        }
        echo json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE);
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
		if(strlen($_POST["ruc"]) > 0){
			$this->form_validation->set_rules('ruc', 'Ruc del Proveedor', 'exact_length[11]');
		}
		if(strlen($_POST["correo"])>0){
			$this->form_validation->set_rules('correo', 'Correo', 'valid_email');
		}

		if ($this->form_validation->run() == true){
		    // id,nombre,ruc,correo,phone,direccion
			$id        = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$nombre    = $_POST["nombre"];
			$ruc       = $_POST["ruc"];
			$correo    = $_POST["correo"];
			$phone     = $_POST["phone"];
			$phone2    = isset($_POST["phone2"]) ? $_POST["phone2"] : '';
			$contacto  = isset($_POST["contacto"]) ? $_POST["contacto"] : '';
			$direccion = $_POST["direccion"];
			$notas     = isset($_POST["notas"]) ? $_POST["notas"] : '';

			$data_prov = array(
				'nombre'    => $nombre,
				'ruc'       => $ruc,
				'correo'    => $correo,
				'phone'     => $phone,
				'phone2'    => $phone2,
				'contacto'  => $contacto,
				'direccion' => $direccion,
				'notas'     => $notas
			);

			if ($id > 0) {
				$ok = $this->db->where('id', $id)->update('tec_proveedores', $data_prov);
			} else {
				$ok = $this->db->insert('tec_proveedores', $data_prov);
			}

			if($ok){
				$this->data['msg'] 		= "Grabacion correcta";
				$this->data['rpta_msg'] = "success";
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
