<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    function listar($categoria=""){
        
        $cad_cat = "";

        if(strlen($categoria)>0 && $categoria!='0'){
            //echo "categoria:".$categoria."X<br>";
            $cad_cat = " and a.category_id = ?";
        }

        $cSql = "select a.id, a.code, a.name, a.category_id, a.unidad, a.alert_cantidad, a.price from tec_products a
            where 1=1" . $cad_cat . 
            " order by a.id desc";

        //die($cSql);
        return $this->db->query($cSql, array($categoria));
    }
}
