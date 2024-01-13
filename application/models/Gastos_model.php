<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Gastos_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    function get_gastos($store_id, $cDesde, $cHasta){

        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";

        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                $cad_desde = " and date(a.fecha)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                $cad_hasta = " and date(a.fecha)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($store_id)){
            if(strlen($store_id)>0 && $store_id !='null'){
                $cad_store_id = " and a.store_id = $store_id";
            }
        }

        $cSql = "select a.id, b.name tienda, date(a.fecha) fecha, c.descrip tipoDoc, a.nroDoc, tp.nombre proveedor, substr(group_concat(p.name,','),1,40) username, a.total 
            from tec_compras a
            left join tec_compra_items ci on a.id = ci.compra_id
            left join tec_products p on ci.product_id = p.id
            left join tec_stores b on a.store_id = b.id
            left join tec_tipos_doc c on a.tipoDoc = c.id
            left join tec_proveedores tp on a.proveedor_id = tp.id
            left join tec_users tu on a.created_by = tu.id
            where a.tipogasto='GASTOS'" . $cad_desde . $cad_hasta . $cad_store_id .
            " group by a.id, b.name, date(a.fecha), c.descrip, a.nroDoc, tp.nombre, a.total";

        // <i class=\'glyphicon glyphicon-edit\' style=\'font-size:16px\'></i>

        $query = $this->db->query($cSql);

        return $query;
    }
}