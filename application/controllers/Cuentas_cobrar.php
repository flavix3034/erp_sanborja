<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuentas_cobrar extends CI_Controller {

    function __construct() {
        parent::__construct();

        session_start();
        $this->load->model('usuarios_model');
    }

    function get_cobrar($desde, $hasta, $store_id){ // 
        
        $cad_desde = $cad_hasta = "";
        if($desde != 'null'){ $cad_desde = " and convert(a.date,date) >= '$desde'";}
        if($hasta != 'null'){ $cad_hasta = " and convert(a.date,date) <= '$hasta'";}

        $cSql = "select a.id, concat(a.serie,'-',a.correlativo) recibo, convert(a.date,date) fecha, a.store_id, b.state tienda, a.customer_id, tc.name as nombre_cliente, round(sum(if(tay.paid_by='Por cobrar',tay.amount,-tay.amount)),2) monto_cuenta
        from tec_sales a 
        inner join tec_stores b on a.store_id = b.id
        inner join tec_customers tc on a.customer_id  = tc.id
        inner join tec_payments tay on a.id = tay.sale_id 
        where tay.paid_by in ('Por cobrar','Amortizar') $cad_desde $cad_hasta
        group by a.id, concat(a.serie,'-',a.correlativo), fecha, a.store_id, tienda, a.customer_id, nombre_cliente
        having monto_cuenta > 0
        order by a.id desc limit 1500";

        //die($cSql);
        $result = $this->db->query($cSql)->result_array();
        foreach($result as &$r){
            //$r["acciones"] = "<a href='#' onclick='saldar(".$r["id"].")'><span class='glyphicon glyphicon-cog iconos'></span></a>";
            $r["acciones"] = "<a href='#' data-toggle='modal' data-target='#myModal' onclick='saldar(".$r["id"].",$store_id,".$r["customer_id"].")'><span class='glyphicon glyphicon-cog iconos'></span></a>";
            
        }
        $ar_campos = array("id","fecha","store_id","tienda","customer_id","nombre_cliente","recibo","monto_cuenta", "acciones");
        echo $this->fm->json_datatable($ar_campos, $result);
    }

    function saldar(){
        $this->data["page_title"] = "Saldar Cuenta";
        $this->template->load('production/index', 'cuentas_cobrar/saldar', $this->data);
    }

    function monto_saldar(){
        echo "Cachorlo";
    }

    function cargar_vta($cliente){
        $ar         = array();
        $cSql       = "select a.id, concat(date_format(convert(a.date,date),'%d-%m-%Y'), '.....',a.serie,'-',a.correlativo,'...',round(a.grand_total,2)) as documento from tec_sales a where a.customer_id = $cliente order by a.tipoDoc, a.date desc";
        $result     = $this->db->query($cSql)->result_array();
        $ar         = $this->fm->conver_dropdown($result, "id", "documento", array(''=>'Seleccione'));
        echo form_dropdown('sale_id',$ar,'','class="form-control tip" id="sale_id" required="required"');
    }

    function save(){
        $store_id           = $_POST["tienda_id"];
        $date               = $_POST["date"];
        $customer_id        = $_POST["cliente"];
        $sale_id            = (isset($_POST["sale_id"]) ? $_POST["sale_id"] : "" );
        $amount             = $_POST["monto"];
        $paid_by            = "Amortizar";

        $validacion         = true;
        $this->form_validation->set_rules('tienda_id', 'Tienda', 'required');
        $this->form_validation->set_rules('monto', 'Monto', 'required');

        if($sale_id == "" || is_null($sale_id) ){
            $validacion = false;
            $this->data["msg"] = "No ha escogido algun documento o factura"; 
        }

        if ($this->form_validation->run() == true && $validacion == true){

            $ar["store_id"]         = $store_id;
            $ar["date"]             = $date;
            $ar["customer_id"]      = $customer_id;
            $ar["sale_id"]          = $sale_id;
            $ar["amount"]            = $amount;
            $ar["paid_by"]          = $paid_by;
            
            if ($this->db->set($ar)->insert("tec_payments")){
                $descrip = $this->db->query("select concat(id,' ',amount) descrip from tec_payments where id = " . $this->db->insert_id())->row()->descrip;
                $this->data["msg"] = "Se graba correctamente ($descrip)";
                $this->data["rpta_msg"] = "success";
            }else{
                $this->data["msg"] = validation_errors();
                $this->data["rpta_msg"] = "danger";
            }
        }else{
            $this->data["msg"] = (validation_errors() == '' ? $this->data["msg"] : validation_errors());
            $this->data["rpta_msg"] = "danger";
        }
        $this->data["page_title"] = "Saldar Cuenta";
        $this->template->load('production/index', 'cuentas_cobrar/saldar', $this->data);        
    }

    function listar($desde='null', $hasta='null', $store_id='null'){
        $this->data['page_title'] = 'Cuentas por Cobrar:';
        //$this->data["tabla_usuarios"] = $this->usuarios_model->ver_usuarios();
        $this->data["desde"] = $desde;
        $this->data["hasta"] = $hasta;
        $this->data["store_id"] = $store_id;
        $this->template->load('production/index', 'cuentas_cobrar/index', $this->data);        
    }
}