<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Guard de sesión
        if (!isset($_SESSION['user_id'])) {
            redirect(base_url('welcome/index'));
            exit;
        }

        // 2. Guard de permisos
        $controller = strtolower($this->router->fetch_class());
        $method     = strtolower($this->router->fetch_method());
        $group_id   = (int) $_SESSION['group_id'];

        if (!$this->_check_access($group_id, $controller, $method)) {
            $this->output->set_status_header(403);
            $this->load->view('errors/403');
            exit;
        }
    }

    /**
     * Verifica si el grupo puede acceder al controlador/método actual.
     *
     * Lógica:
     *  1. Si la ruta exacta (controller/method) existe en tec_modulos como hijo,
     *     se exige que esté en los permisos del grupo.
     *  2. Si la ruta no es un submódulo conocido (ej: método AJAX interno),
     *     basta con que el grupo tenga CUALQUIER submódulo del mismo controlador.
     */
    protected function _check_access($group_id, $controller, $method) {
        $permitidos   = $this->_get_permitted_routes($group_id);   // ['sales/index','sales/add',...]
        $known_routes = $this->_get_known_routes();                // todas las rutas hijas en tec_modulos

        $full_route = $controller . '/' . $method;

        if (in_array($full_route, $known_routes)) {
            // Ruta conocida (está en el menú): requiere permiso exacto
            return in_array($full_route, $permitidos);
        }

        // Ruta interna/AJAX: basta con tener algún permiso del mismo controlador
        foreach ($permitidos as $ruta) {
            if (strtolower(explode('/', $ruta)[0]) === $controller) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rutas hijas (submódulos) permitidas para el grupo.
     * Caché en $_SESSION['permisos_grupo_X'].
     */
    protected function _get_permitted_routes($group_id) {
        $cache_key = 'permisos_grupo_' . $group_id;

        if (!isset($_SESSION[$cache_key])) {
            $rows = $this->db
                ->select('TRIM(LOWER(b.modulo)) AS modulo')
                ->from('tec_grupo_modulos a')
                ->join('tec_modulos b', 'a.modulo_id = b.id')
                ->where('a.grupo_id', $group_id)
                ->where('b.parent_id IS NOT NULL', null, false)
                ->get()->result_array();

            $_SESSION[$cache_key] = array_column($rows, 'modulo');
        }

        return $_SESSION[$cache_key];
    }

    /**
     * Todas las rutas hijas conocidas en tec_modulos.
     * Caché en $_SESSION['known_routes'].
     */
    protected function _get_known_routes() {
        if (!isset($_SESSION['known_routes'])) {
            $rows = $this->db
                ->select('TRIM(LOWER(modulo)) AS modulo')
                ->where('parent_id IS NOT NULL', null, false)
                ->get('tec_modulos')->result_array();

            $_SESSION['known_routes'] = array_column($rows, 'modulo');
        }

        return $_SESSION['known_routes'];
    }
}
