<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function afiliados($nOrder){
        $cOrder = "";
        if($nOrder == 1){$cOrder = "order by a.cod_afi";}
        elseif($nOrder == 2){$cOrder = "order by b.nombre";}
        
        $cSql = "select a.cod_afi, b.nombre, a.socio, b.dato1, b.tipo1 from afiliados a inner join socios b on a.socio=b.cod_socio where a.activo='1' {$cOrder}";
        $query = $this->db->query($cSql);
        return $query->result(); 
    }

    public function conceptos(){
        //$cOrder = "";
        //if($nOrder == 1){$cOrder = "order by a.cod_afi";}
        //elseif($nOrder == 2){$cOrder = "order by b.nombre";}
        
        $cSql = "select tipo, concepto, moneda, tip_cuo, mov, activo, tipo_cuota from concepto where mov='I' and activo='1'";
        $query = $this->db->query($cSql);
        return $query->result(); 
    }

}