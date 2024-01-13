<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Caja_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    function existe_cajas_abiertas(){
        $cSql = "select * from tec_registro_cajas where estado_cierre = '0'";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            return true;
        }
        return false;
    }
}