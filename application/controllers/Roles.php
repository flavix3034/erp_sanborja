<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('roles_model');
    }

    /** Lista todos los roles */
    public function index() {
        $this->data['page_title'] = 'Gestión de Roles';
        $this->data['roles']      = $this->roles_model->get_roles();
        $this->template->load('production/index', 'roles/index', $this->data);
    }

    /** Formulario de creación */
    public function add() {
        $this->data['page_title']        = 'Nuevo Rol';
        $this->data['rol']               = null;
        $this->data['modulos']           = $this->roles_model->get_all_modulos();
        $this->data['permisos_actuales'] = array();
        $this->template->load('production/index', 'roles/form', $this->data);
    }

    /** Formulario de edición */
    public function edit($grupo_id = 0) {
        $grupo_id = (int) $grupo_id;
        $rol = $this->roles_model->get_rol($grupo_id);
        if (!$rol) {
            redirect(base_url('roles/index'));
        }
        $this->data['page_title']        = 'Editar Rol: ' . htmlspecialchars($rol['grupo']);
        $this->data['rol']               = $rol;
        $this->data['modulos']           = $this->roles_model->get_all_modulos();
        $this->data['permisos_actuales'] = $this->roles_model->get_permisos($grupo_id);
        $this->template->load('production/index', 'roles/form', $this->data);
    }

    /** Guardar (crear o actualizar) */
    public function save() {
        $grupo_id = (int) ($_POST['grupo_id'] ?? 0);
        $nombre   = trim($_POST['nombre'] ?? '');
        $modulos  = isset($_POST['modulos']) ? (array) $_POST['modulos'] : array();

        $this->form_validation->set_rules('nombre', 'Nombre del Rol', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->data['msg']      = validation_errors();
            $this->data['rpta_msg'] = 'danger';
            if ($grupo_id === 0) {
                $this->data['rol']               = null;
                $this->data['modulos']           = $this->roles_model->get_all_modulos();
                $this->data['permisos_actuales'] = array();
                $this->data['page_title']        = 'Nuevo Rol';
            } else {
                $this->data['rol']               = $this->roles_model->get_rol($grupo_id);
                $this->data['modulos']           = $this->roles_model->get_all_modulos();
                $this->data['permisos_actuales'] = $modulos;
                $this->data['page_title']        = 'Editar Rol';
            }
            return $this->template->load('production/index', 'roles/form', $this->data);
        }

        $nuevo_id = $this->roles_model->save_rol($grupo_id, $nombre, $modulos);

        // Invalidar cache de permisos en sesión para el rol modificado
        unset($_SESSION['permisos_grupo_' . $nuevo_id]);

        $this->data['msg']      = 'Rol guardado correctamente';
        $this->data['rpta_msg'] = 'success';
        $this->index();
    }

    /** Eliminar rol (solo si no tiene usuarios asignados) */
    public function eliminar() {
        $grupo_id = (int) ($_REQUEST['id'] ?? 0);

        if ($this->roles_model->tiene_usuarios($grupo_id)) {
            echo '0';
            return;
        }

        $this->roles_model->eliminar_rol($grupo_id);
        unset($_SESSION['permisos_grupo_' . $grupo_id]);
        echo '1';
    }
}
