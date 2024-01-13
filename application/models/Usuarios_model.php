<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    function ver_usuarios(){
        $result     = $this->db->select("tec_users.id, tec_users.username, tec_users.email, if(tec_users.active='1','Activo','Inactivo') active, tec_users.first_name, tec_users.last_name, tec_users.group_id, tec_stores.name store_id, tec_grupo_usuarios.grupo,
            concat('<a href=\'#\' onclick=\'modificar(',tec_users.id,')\'><i class=\'glyphicon glyphicon-edit\'></i></a>&nbsp;&nbsp;
            <a href=\'#\' onclick=\'eliminar(',tec_users.id,')\'><i class=\'glyphicon glyphicon-remove\'></i></a>&nbsp;&nbsp;
            <a href=\'#\' onclick=\'eliminar(',tec_users.id,')\'><i class=\'glyphicon glyphicon-cog\'></i></a>') op")
            ->from('tec_users')
            ->join('tec_grupo_usuarios','tec_users.group_id = tec_grupo_usuarios.id')
            ->join('tec_stores','tec_users.store_id = tec_stores.id')
            ->get()->result_array();
        
        $cols           = array("id","username","email","first_name","last_name","active","grupo","store_id","op");
        $cols_titulos   = array("id","Usuario","Correo","Primer Nombre","Apellidos","Estado","Grupo","Tienda","Acciones");
        $ar_align       = array("0","0","0","0","0","0","0","0","0");
        $ar_pie         = array("","","","","","","","","");

        return $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
    }

    function permiso_usuarios($usuario_id){
        $cSql = "select tm.id, tm.modulo, if(um.id is not null,1,0) califica
            from tec_modulos tm
            left join tec_usuario_modulos um on tm.id = um.modulo_id
            where tm.usuario_id = ?";
        return $this->db->query($cSql,array($usuario_id));
    }
}