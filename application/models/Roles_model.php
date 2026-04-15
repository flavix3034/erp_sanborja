<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Roles_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_roles() {
        return $this->db
            ->select('g.id, g.grupo, g.activo, COUNT(gm.modulo_id) AS total_modulos')
            ->from('tec_grupo_usuarios g')
            ->join('tec_grupo_modulos gm', 'g.id = gm.grupo_id', 'left')
            ->group_by('g.id')
            ->order_by('g.id')
            ->get()->result_array();
    }

    public function get_rol($grupo_id) {
        return $this->db
            ->where('id', (int) $grupo_id)
            ->get('tec_grupo_usuarios')->row_array();
    }

    /**
     * Devuelve los módulos padre con sus hijos anidados.
     * Solo se pueden asignar permisos a los hijos (parent_id IS NOT NULL).
     */
    public function get_all_modulos() {
        // Padres ordenados por sección y orden
        $padres = $this->db
            ->where('parent_id IS NULL', null, false)
            ->order_by('seccion')
            ->order_by('orden')
            ->get('tec_modulos')->result_array();

        // Hijos de cada padre
        foreach ($padres as &$padre) {
            $padre['hijos'] = $this->db
                ->where('parent_id', $padre['id'])
                ->order_by('orden')
                ->get('tec_modulos')->result_array();
        }

        return $padres;
    }

    /** Devuelve array plano de modulo_id permitidos para el grupo (solo hijos) */
    public function get_permisos($grupo_id) {
        $rows = $this->db
            ->select('gm.modulo_id')
            ->from('tec_grupo_modulos gm')
            ->join('tec_modulos m', 'gm.modulo_id = m.id')
            ->where('gm.grupo_id', (int) $grupo_id)
            ->where('m.parent_id IS NOT NULL', null, false)
            ->get()->result_array();
        return array_column($rows, 'modulo_id');
    }

    public function save_rol($grupo_id, $nombre, $modulos) {
        if ($grupo_id === 0) {
            $this->db->insert('tec_grupo_usuarios', array('grupo' => $nombre, 'activo' => '1'));
            $grupo_id = $this->db->insert_id();
        } else {
            $this->db->where('id', $grupo_id)->update('tec_grupo_usuarios', array('grupo' => $nombre));
        }

        // Reemplazar todos los permisos del rol
        $this->db->where('grupo_id', $grupo_id)->delete('tec_grupo_modulos');
        foreach ($modulos as $modulo_id) {
            $this->db->insert('tec_grupo_modulos', array(
                'grupo_id'  => $grupo_id,
                'modulo_id' => (int) $modulo_id,
            ));
        }

        return $grupo_id;
    }

    public function tiene_usuarios($grupo_id) {
        return $this->db
            ->where('group_id', (int) $grupo_id)
            ->count_all_results('tec_users') > 0;
    }

    public function eliminar_rol($grupo_id) {
        $this->db->where('grupo_id', $grupo_id)->delete('tec_grupo_modulos');
        $this->db->where('id', $grupo_id)->delete('tec_grupo_usuarios');
    }
}
