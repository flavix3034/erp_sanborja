<?php
$timezone = "America/Lima";
date_default_timezone_set($timezone);

ini_set('display_errors', '1');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

class Queries_por_fuera extends CI_Controller
{

    function __construct() {    
        parent::__construct();
        $this->load->model('sales_model');
    }

    public function reenvios_programados(){
        $cSql = "select id, serie, correlativo from tec_sales where envio_electronico!='1' and serie in ('B001','F001') and date(date) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
        //$cSql = "select id, serie, correlativo from tec_sales where envio_electronico!='1' and serie in ('B001','F001') and date(date) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY) and (serie='B001' and correlativo=1224)";
        $query = $this->db->query($cSql);

        echo("\n\n Iniciando reenvios (" . date("Y-m-d H:i") . ")\n");

        foreach($query->result() as $r){
            $this->reenvio_individual_apisperu($r->id);
            echo(date("Y-m-d") . "  id : " . $r->id . " " . trim($r->serie) . "-" . trim($r->correlativo) . "\n");
        }
        //fclose($gn);
    }

    public function reenvio_individual_apisperu($sale_id){  // FUNCIONA MUY BIEN!

        $query = $this->db->select("envio_electronico")->where("id",$sale_id)->get("tec_sales");

        foreach($query->result() as $r){
            if($r->envio_electronico != '1'){
                
                $this->sales_model->enviar_doc_sunat_individual($sale_id);

            }

        }

    }
}