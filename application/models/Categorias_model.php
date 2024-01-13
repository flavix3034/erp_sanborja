<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categorias_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function lista_categorias(){
        $cSql = "select a.id, a.name, case when a.activo='1' then 'Activo' else 'Desactivo' end activo, 
        concat('<a href=\'#\' title=\'Editar\' onclick=\'editar(',a.id,')\'><i class=\'glyphicon glyphicon-edit\' style=\'font-size:16px\'></i></a>',
                ' <a href=\'#\' title=\'Eliminar\' onclick=\'eliminar(',a.id,')\'><i class=\'glyphicon glyphicon-remove\' style=\'font-size:16px\'></i></a>') actions
        from tec_categories a order by a.name";
        
        // ' <a href=\'#\' title=\'Ver\' onclick=\'ver(',a.id,')\'><i class=\'glyphicon glyphicon-eye-open\' style=\'font-size:16px\'></i></a>',
        return $this->db->query($cSql)->result_array();
    }

}
