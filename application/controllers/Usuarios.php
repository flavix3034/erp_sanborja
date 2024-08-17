<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    function __construct() {
        parent::__construct();

        session_start();
        $this->load->model('usuarios_model');
    }

    function ver_usuarios(){
        $this->data['page_title'] = 'Usuarios:';
        $this->data["tabla_usuarios"] = $this->usuarios_model->ver_usuarios();
        $this->template->load('production/index', 'usuarios/index', $this->data);
    }

    function add(){
        if(isset($_REQUEST["id"])){ 
            $this->data["id"] = $_REQUEST["id"];
            $this->data["query_p1"] = $this->db->select("*")->from("tec_users")->where("id",$this->data["id"])->get();
        }
        $this->data['page_title'] = 'Agregar Usuarios:';
        $this->template->load('production/index', 'usuarios/add_usuarios', $this->data);
    }

    function save(){
        $username    = $_POST["username"];
        $email       = $_POST["email"];
        $active      = $_POST["active"];
        $first_name  = $_POST["first_name"];
        $last_name   = $_POST["last_name"];
        $group_id    = $_POST["group_id"];
        $store_id    = $_POST["store_id"];
        $password    = $_POST["password"];

        $this->form_validation->set_rules('username', 'Denominacion del Usuario', 'required');
        $this->form_validation->set_rules('first_name', 'Nombre de la Persona', 'required');
        $this->form_validation->set_rules('group_id', 'Nivel de Usuario', 'required');
        $this->form_validation->set_rules('store_id', 'Tienda del Usuario', 'required');
        $this->form_validation->set_rules('password', 'Se requiere Password', 'required');
        
        if(strlen($_POST["email"])>0){
            $this->form_validation->set_rules('email', 'Correo electrónico', 'valid_email');
        }
        
        if ($this->form_validation->run() == true){

            $ar["username"]     = $username;
            $ar["email"]        = $email;
            $ar["active"]       = $active;
            $ar["first_name"]   = $first_name;
            $ar["last_name"]    = $last_name;
            $ar["group_id"]     = $group_id;
            $ar["store_id"]     = $store_id;
            $ar["password"]     = $password;
            
            if(strlen($_POST["id"])==0){ 

                // Verifico su unicidad
                $cSql = "select count(*) cant from tec_users where username = ?";
                $cant = $this->db->query($cSql,array($username))->row(0)->cant * 1;
                if($cant==0){
                    if ($this->db->set($ar)->insert("tec_users")){
                        $this->data["msg"] = "Se graba correctamente el usuario";
                        $this->data["rpta_msg"] = "success";

                        $this->data['page_title'] = 'Usuarios:';
                        $this->data["tabla_usuarios"] = $this->usuarios_model->ver_usuarios();
                        $this->template->load('production/index', 'usuarios/index', $this->data);
                    }else{
                        $this->data["msg"] = "Ocurrio un error al grabar el usuario";
                        $this->data["rpta_msg"] = "danger";

                        $this->data['page_title'] = 'Agregar Usuarios:';
                        $this->template->load('production/index', 'usuarios/add_usuarios', $this->data);
                    }
                }else{
                    $this->data["msg"] = "Ya existe un usuario con esta denominacion";
                    $this->data["rpta_msg"] = "danger";

                    $this->data['page_title'] = 'Agregar Usuarios:';
                    $this->template->load('production/index', 'usuarios/add_usuarios', $this->data);
                }
            }else{  // se trata de update

                $this->db->set($ar)->where("id", $_POST["id"])->update("tec_users");

                $this->data["msg"] = "Se actualiza correctamente el usuario";
                $this->data["rpta_msg"] = "success";

                $this->data['page_title'] = 'Usuarios:';
                $this->data["tabla_usuarios"] = $this->usuarios_model->ver_usuarios();
                $this->template->load('production/index', 'usuarios/index', $this->data);
            }


        }
    }

    function eliminar(){
        $id = $_REQUEST["id"];
        $cSql = "select count(1) cant from tec_compras where created_by = ?";
        $query = $this->db->query($cSql,array($id));
        $cant = $query->row(0)->cant * 1;
        $flag_elimina_compras = false;
        if ($cant == 0){
            $flag_elimina_compras = true;
        }

        $cSql = "select count(1) cant from tec_sales where created_by = ?";
        $query = $this->db->query($cSql,array($id));
        $cant = $query->row(0)->cant * 1;
        $flag_elimina_ventas = false;
        if ($cant == 0){
            $flag_elimina_ventas = true;
        }
        
        if($flag_elimina_compras && $flag_elimina_ventas){
            $this->db->where("id",$id)->delete("tec_users");
            echo "1";
        }else{
            echo "0";
        }

    }

    function permiso_usuarios($id){
        $this->data["id"] = $_REQUEST["id"];
        $this->data["query_p1"] = $this->usuarios_model->permiso_usuarios($this->data["id"]);
            /*$this->db->select("*")
            ->from("tec_usuario_modulos a")
            ->join("tec_modulos b","a.modulo_id=b.id")
            ->where("id",$this->data["id"])->get();*/
        
        $this->data['page_title'] = 'Agregar Usuarios:';
        $this->template->load('production/index', 'usuarios/permiso_usuarios', $this->data);    
    }
}