<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes extends CI_Controller {

    public $Admin = false;
    public $Igv = 18;

    function __construct() {
        parent::__construct();

        session_start();

        if (!isset($_SESSION["store_id"])) {
            die("No tiene sesión disponible. <a href=\"" . base_url("welcome/index") . "\">Login</a>");
        }
        
        //$this->load->helper('url');
        
        if(isset($_SESSION["group_id"])){
            $Admin = ( $_SESSION["group_id"] == 1 ? true : false );
        }  

    }

    function ventas_detalles_prod($cDesde='null', $cHasta='null', $cStore_id='null') {
        $this->data['page_title'] = "Ventas Diarias x Producto";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $cStore_id;
        //$this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'reportes/ventas_detalles_prod', $this->data);
    }

    function get_ventas_detalles_prod($cDesde,$cHasta,$cStore) {

        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                //$this->db->where('tec_sales.date>=', $cDesde);
                $cad_desde = " and date(a.`date`)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_hasta = " and date(a.`date`)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($cStore)){
            if(strlen($cStore)>0 && $cStore !='null' && $cStore != '0'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_store_id = " and a.store_id='{$cStore}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }


        $cSql = "select c.name tienda, date(a.`date`) fecha, p.code,
          if(b.product_id=99999, b.product_name, concat(p.name,' ',p.marca,' ',p.modelo)) producto,  
          round(avg(b.real_unit_price),2) precio, round(sum(b.quantity),2) cant,
          round(avg(b.real_unit_price) * sum(b.quantity),2) subtotal
          from tec_sales a
          inner join tec_sale_items b on a.id=b.sale_id
          left join tec_products p on b.product_id=p.id
          inner join tec_stores c on a.store_id = c.id
          left join tec_users d on a.created_by = d.id
          where a.anulado!='1' $cad_desde $cad_hasta $cad_store_id
          group by c.name, date(`date`), p.code, if(b.product_id=99999, b.product_name, concat(p.name,' ',p.marca,' ',p.modelo))";
        
        //echo($cSql);
        //die();

        $result = $this->db->query($cSql)->result_array();
            
        $ar_campos = array("tienda","fecha","code","producto","precio","cant","subtotal");  // 

        //$this->datatables->add_column("Actions", "hebra"); // <button onclick=\"ver_documento(1)\">Ver</button>

        echo $this->json_datatable($ar_campos,$result);
        

    }

    function json_datatable($ar_campos,$result){ // Devuelve un json preparado para el datatable, el result debe ser result_array
        $nCols = count($ar_campos);

            $cad = "";
            $limite = count($ar_campos);

            foreach($result as $r){
                $cad .= "[";
                for($i=0; $i<$limite; $i++){
                    $cad .=  '"' . $this->quita_char_especiales($r[$ar_campos[$i]]) . '",';
                }
                $cad = substr($cad,0,strlen($cad)-1); // quito la ultima coma
                $cad .= "],";
            }

        $cad = substr($cad,0,strlen($cad)-1);
        $cad = '{"data":[' . $cad . ']}';
        return $cad;

    }

    function quita_char_especiales($cad=""){
        //$cad = str_replace("[","",$cad);
        //$cad = str_replace("]","",$cad);
        $cad = str_replace("|","",$cad);
        $cad = str_replace("Ñ","N",$cad);
        $cad = str_replace("ñ","n",$cad);
        $cad = str_replace('"',"",$cad);
        return $cad;
    }

    function ventas_por_forma_pago($cDesde='null', $cHasta='null', $cStore_id='null') {
        $this->data['page_title'] = "Ventas x Forma de Pago";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $cStore_id;
        //$this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'reportes/ventas_por_forma_pago', $this->data);
    }

    function get_ventas_por_forma_pago($cDesde,$cHasta,$cStore) {

        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                //$this->db->where('tec_sales.date>=', $cDesde);
                $cad_desde = " and date(a.`date`)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_hasta = " and date(a.`date`)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($cStore)){
            if(strlen($cStore)>0 && $cStore !='null' && $cStore != '0'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_store_id = " and a.store_id='{$cStore}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }


        $cSql = "select c.name tienda, date(a.`date`) fecha, 
        round(sum(if(b.paid_by = 'cash',b.amount,0)),2) cash,
        round(sum(if(b.paid_by = 'Transf BCP',b.amount,0)),2) transf_bcp,
        round(sum(if(b.paid_by = 'Yape',b.amount,0)),2) yape,
        round(sum(if(b.paid_by = 'Plin',b.amount,0)),2) plin,
        round(sum(if(b.paid_by = 'IZIPAY',b.amount,0)),2) izipay,
        round(sum(if(b.paid_by = 'Transf Scotiabank',b.amount,0)),2) transf_scotia,
        round(sum(if(b.paid_by = 'Vendemas',b.amount,0)),2) vendemas,
        round(sum(if(b.paid_by = 'Transf Bbva',b.amount,0)),2) transf_bbva,
        round(sum(if(b.paid_by = 'Transf Interbank',b.amount,0)),2) transf_interbank,
        round(sum(if(b.paid_by not in ('cash','Transf BCP','Yape','Plin','IZIPAY','Transf Scotiabank','Vendemas','Transf Bbva','Transf Interbank'),b.amount,0)),2) otros,
          round(sum(if(b.paid_by = 'cash',b.amount,0)) + sum(if(b.paid_by = 'Transf BCP',b.amount,0)) + sum(if(b.paid_by = 'Yape',b.amount,0)) +
          sum(if(b.paid_by = 'Plin',b.amount,0)) + sum(if(b.paid_by = 'IZIPAY',b.amount,0)) + sum(if(b.paid_by = 'Transf Scotiabank',b.amount,0)) +
          sum(if(b.paid_by = 'Vendemas',b.amount,0)) + sum(if(b.paid_by = 'Transf Bbva',b.amount,0)) + sum(if(b.paid_by = 'Transf Interbank',b.amount,0)) +
          sum(if(b.paid_by not in ('cash','Transf BCP','Yape','Plin','IZIPAY','Transf Scotiabank','Vendemas','Transf Bbva','Transf Interbank'),b.amount,0)),2) total
        from tec_sales a
        inner join tec_payments b on a.id=b.sale_id
        inner join tec_stores c on a.store_id = c.id
        where a.anulado!='1' $cad_desde $cad_hasta $cad_store_id
        group by c.name, date(a.`date`)";
        
        $result = $this->db->query($cSql)->result_array();
            
        $ar_campos = array("tienda","fecha","cash","transf_bcp","yape","plin","izipay","transf_scotia",'vendemas','transf_bbva','transf_interbank',"otros","total");  // 

        //$this->datatables->add_column("Actions", "hebra"); // <button onclick=\"ver_documento(1)\">Ver</button>

        echo $this->json_datatable($ar_campos,$result);
        

    }

    function grafico_mensual_ventas($desde="", $hasta="", $store_id="", $tipo=""){
        // 1ra Parte: Se quita el null
        $desde = $desde == 'null' ? '' : $desde;
        $hasta = $hasta == 'null' ? '' : $hasta;
        $store_id = $store_id == 'null' ? '' : $_SESSION["store_id"];
        $tipo = $tipo == 'null' ? '' : $tipo;


        // 2da Parte: se declara las variables
            // Para el where
        $fdesde = strlen($desde."") > 0 ? $desde : date("Y-m-d");
        $fhasta = strlen($hasta."") > 0 ? $hasta : date("Y-m-d");

            // Para el group by
        $desde = strlen($desde."") > 0 ? substr($desde,5,2) : date("m");
        $hasta = strlen($hasta."") > 0 ? substr($hasta,5,2) : date("m");

        $store_id = strlen($store_id) > 0 ? $store_id : $_SESSION["store_id"];
        $tipo = strlen($tipo) > 0 ? $tipo : '1';
        //die("tipo:".$tipo);
        $this->data['page_title'] = "Gr&aacute;fico de Ventas";
        
        $this->data["desde"]            = $fdesde;
        $this->data["hasta"]            = $fhasta;
        $this->data["tipo"]             = $tipo;

        // Nota.- si tipo = 1:diario, 2:mensual, 3:trimestral
        if($tipo == '1'){ // Dias
            $query1 = $this->por_dias($fdesde, $fhasta, $store_id);
            
            $cadM = $cadY = "";
            $n=0;
            foreach($query1->result() as $r){
                $n++;
                if($n==1){
                    $cadM .= "'" . $r->dia . "'";
                    $cadY .= $r->total;
                }else{
                    $cadM .= ",'" . $r->dia . "'";
                    $cadY .= ",".$r->total;
                }
            }
            $this->data["meses_en_curso"]   = $cadM;
            $this->data["cad_y"]            = $cadY;
            $this->data["tipo_rep"]         = "Diario";
        }

        if($tipo == '2'){ // Meses
            $query1 = $this->meses_en_curso($fdesde, $fhasta);
            
            $cadM = $cadY = "";
            $n=0;
            foreach($query1->result() as $r){
                $n++;
                $c_mes = substr($this->fm->aMes($r->mes),0,6);
                if($n==1){
                    $cadM .= "'".$r->anno . "-" . $c_mes ."'";
                    $cadY .= $r->total;
                }else{
                    $cadM .= ",'".$r->anno . "-" . $c_mes ."'";
                    $cadY .= ",".$r->total;
                }
            }
            $this->data["meses_en_curso"]   = $cadM;
            $this->data["cad_y"]            = $cadY;
            $this->data["tipo_rep"]         = "Mensual";
        }

        $this->template->load('production/index', 'reportes/grafico_mensual_ventas', $this->data);
    }

    function meses_en_curso($desde, $hasta){
        $cad_desde = " and a.`date`>= '$desde'";
        $cad_hasta = " and a.`date`<= '$hasta'";
        $cSql = "select year(`date`) anno,month(`date`) mes, sum(grand_total) total 
        from tec_sales a
        where a.anulado!='1' {$cad_desde} {$cad_hasta}
        group by year(`date`),month(`date`)";

        //die($cSql);
        return $this->db->query($cSql);
    }

    function por_dias($fdesde, $fhasta, $store_id){
        $cad_desde = " and a.`date`>= '$fdesde'";
        $cad_hasta = " and a.`date`<= '$fhasta'";
        $cad_tienda = " and a.store_id = $store_id";
        $cSql = "select date(a.`date`) dia, sum(grand_total) total 
        from tec_sales a
        where a.anulado!='1' {$cad_desde} {$cad_hasta} {$cad_tienda}
        group by date(a.`date`)";

        //die($cSql);
        return $this->db->query($cSql);
    }

    function ganancias($cDesde='null', $cHasta='null', $cStore_id='null') {
        $this->data['page_title'] = "Ganancias";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $cStore_id;
        $this->data['Admin'] = $this->Admin;
        $this->template->load('production/index', 'reportes/ganancias', $this->data);
    }

    function get_ganancias($cDesde,$cHasta,$cStore){
        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                //$this->db->where('tec_sales.date>=', $cDesde);
                $cad_desde = " and date(a.`date`)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_hasta = " and date(a.`date`)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($cStore)){
            if(strlen($cStore)>0 && $cStore !='null' && $cStore != '0'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_store_id = " and a.store_id='{$cStore}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        /*
        $cSql_sub = "select a.store_id, date(a.`date`) dia, sum(if( a.tipoDoc!=5, b.unit_price/(1+".$this->Igv."/100) , b.unit_price)) ventas, sum(if(c.precio_con_igv is null, 0, c.precio_con_igv)) costos
            , sum(b.unit_price) - sum(if(c.precio_con_igv is null, 0, c.precio_con_igv)) dif   
            from tec_sales a
            inner join tec_sale_items b on a.id=b.sale_id
            left join tec_compra_items c on b.compra_id=c.compra_id and b.product_id=c.product_id
            where a.anulado!='1' $cad_desde $cad_hasta $cad_store_id
            group by a.store_id, date(a.`date`)";*/

        $cSql_sub = "select a.store_id, date(a.`date`) dia, sum(b.net_unit_price*b.quantity) ventas, sum(if(c.precio_sin_igv is null, 0, c.precio_sin_igv*b.quantity)) costos, 
            sum(b.net_unit_price*b.quantity) - sum(if(c.precio_sin_igv is null, 0, c.precio_sin_igv*b.quantity)) dif   
            from tec_sales a
            inner join tec_sale_items b on a.id=b.sale_id
            left join tec_compra_items c on b.compra_id=c.compra_id and b.product_id=c.product_id
            where a.anulado!='1' $cad_desde $cad_hasta $cad_store_id
            group by a.store_id, date(a.`date`)";

        $cSql = "select x.store_id, y.name tienda, x.dia ".
            " ,CONCAT(ELT(WEEKDAY(x.dia) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', '<b>Sabado</b>', '<b>Domingo</b>')) as dia_semana".
            " ,round(x.ventas,2) ventas, round(x.costos,2) costos, round(x.ventas-x.costos,2) ganancia from (" . $cSql_sub . ") x".
            " left join tec_stores y on x.store_id=y.id";

        $result = $this->db->query($cSql)->result_array();
            
        $ar_campos = array("tienda","dia","dia_semana","ventas","costos","ganancia");  // 

        //$this->datatables->add_column("Actions", "hebra"); // <button onclick=\"ver_documento(1)\">Ver</button>

        echo $this->json_datatable($ar_campos,$result);

    }

    function ganancias_detallado($cDesde='null', $cHasta='null', $cStore_id='null') {
        $this->data['page_title'] = "Ganancias";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $cStore_id;
        $this->data['Admin'] = $this->Admin;
        $this->template->load('production/index', 'reportes/ganancias_detallado', $this->data);
    }

    function get_ganancias_detallado($cDesde,$cHasta,$cStore){
        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                //$this->db->where('tec_sales.date>=', $cDesde);
                $cad_desde = " and date(a.`date`)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_hasta = " and date(a.`date`)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($cStore)){
            if(strlen($cStore)>0 && $cStore !='null' && $cStore != '0'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_store_id = " and a.store_id='{$cStore}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        $cSql = "select a.store_id tienda, date(a.`date`) dia, b.product_id, concat(d.name,' ',d.marca,' ',d.modelo) name,
            round(b.net_unit_price,2) net_unit_price, round(b.quantity,0) quantity,
            round(b.net_unit_price*b.quantity,2) ventas, 
            round(if(c.precio_sin_igv is null, 0, c.precio_sin_igv*b.quantity),2) costos, 
            round((b.net_unit_price*b.quantity) - if(c.precio_sin_igv is null, 0, c.precio_sin_igv*b.quantity),2) ganancia, tc.tipoDoc,
            CONCAT(ELT(WEEKDAY(date(a.`date`)) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', '<b>Sabado</b>', '<b>Domingo</b>')) as dia_semana
            from tec_sales a
            inner join tec_sale_items b on a.id=b.sale_id
            left join tec_compras tc on b.compra_id = tc.id
            left join tec_compra_items c on b.compra_id=c.compra_id and b.product_id=c.product_id
            left join tec_products d on b.product_id = d.id
            where a.anulado!='1' $cad_desde $cad_hasta $cad_store_id
            order by a.store_id, date(a.`date`)";

        /*$cSql = "select x.store_id, y.name tienda, x.dia ".
            " ,CONCAT(ELT(WEEKDAY(x.dia) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', '<b>Sabado</b>', '<b>Domingo</b>')) as dia_semana".
            " ,round(x.ventas,2) ventas, round(x.costos,2) costos, round(x.ventas-x.costos,2) ganancia from (" . $cSql_sub . ") x".
            " left join tec_stores y on x.store_id=y.id";*/

        $result = $this->db->query($cSql)->result_array();
            
        $ar_campos = array("tienda", "dia", "dia_semana", "name", "net_unit_price", "quantity", "ventas", "costos", "ganancia");  // 

        //$this->datatables->add_column("Actions", "hebra"); // <button onclick=\"ver_documento(1)\">Ver</button>

        echo $this->json_datatable($ar_campos,$result);

    }

    function productos_sin_compra(){
        $this->data["page_title"] = "Productos sin ninguna Compra";
        $this->template->load('production/index', 'reportes/productos_sin_compra', $this->data);        
    }

    function get_productos_sin_compra(){
        $cSql = "select a.id, a.code, concat(a.name,' ',a.marca,' ',a.modelo) name from tec_products a
        left join (
          select product_id from tec_compra_items
          group by product_id
        ) b on a.id = b.product_id
        where b.product_id is null
        order by a.name, a.marca, a.modelo";

        $result = $this->db->query($cSql)->result_array();
            
        $ar_campos = array("id","code","name");  // 

        echo $this->json_datatable($ar_campos,$result);
    }

    function analisis(){
        
        if(isset($_POST["desde"])){

            $this->form_validation->set_rules('desde', 'fecha Desde', 'required');
            //$this->form_validation->set_rules('', $this->lang->line("name"), 'required');
            //$this->form_validation->set_rules('', $this->lang->line("email_address"), 'valid_email');
            
            
            if ($this->form_validation->run() == true){
                $desde = $_POST["desde"];
                $hasta = $_POST["hasta"];
                $store_id = $_POST["store_id"];

                $cad_desde = $cad_hasta = "";
                if(!empty($desde)){
                    $cad_desde = "and a.fecha >= '$desde'";
                }
                if(!empty($hasta)){
                    $cad_hasta = "and a.fecha <= '$hasta'";
                }

                /*
                $cSql = "select a.id, a.fecha, a.fecha_ingreso, a.monto_base, a.igv, a.total, a.proveedor_id, a.store_id, a.tipoDoc, b.descrip, c.product_id, c.product_name, c.cantidad, c.precio_sin_igv, c.precio_con_igv,  
                    c.igv, c.subtotal
                    from tec_compras a
                    left join tec_tipos_doc b on a.tipoDoc=b.id
                    left join tec_compra_items c on a.id=c.compra_id
                    where 1=1 $cad_desde $cad_hasta and a.store_id = $store_id limit 2";

                $result = $this->db->query($cSql)->result_array();

                $cols       = array('id','fecha','fecha_ingreso','monto_base','igv','total','proveedor_id','store_id','tipoDoc','descrip','product_id','product_name','cantidad','precio_sin_igv','precio_con_igv','igv','subtotal');
                $cols_tit   = array('id','fecha','fecha_ingreso','monto_base','igv','total','proveedor_id','store_id','tipoDoc','descrip','product_id','product_name','cantidad','precio_sin_igv','precio_con_igv','igv','subtotal');
                $ar_align   = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
                $ar_pie     = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
                */

                $cSql = "select a.id, a.date, a.customer_id, a.customer_name, a.total, a.product_tax, a.total_tax, a.grand_total, c.descrip documento, a.serie, a.correlativo nro, a.fecha_registro, a.anulado, b.product_id, concat(d.name,' ',d.marca,' ',d.modelo) nombre_producto, round(b.quantity,0) quantity, b.net_unit_price, b.unit_price
                    from tec_sales a
                    left join tec_sale_items b on a.id=b.sale_id
                    left join tec_tipos_doc c on a.tipodoc=c.id
                    left join tec_products d on b.product_id=d.id
                    where a.fecha_registro >= ? and a.fecha_registro <= ? and a.anulado != '1'";

                $result = $this->db->query($cSql, array($desde, $hasta))->result_array();

                $cols       = array('id','date','customer_id','customer_name','total','product_tax','total_tax','grand_total','documento','serie','nro','fecha_registro','anulado','product_id','nombre_producto','quantity','net_unit_price','unit_price');
                $cols_tit   = array('id','fecha','cliente_id','cliente','total','product_igv','total_igv','total_total','documento','serie','nro','fecha_registro','anulado','product_id','nombre_producto','cantidad','precio_sin_igv','precio_con_igv');
                $ar_align   = array('1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1');
                $ar_pie     = array('1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1');

                setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
                $file_name = 'compras';
                header('Content-Type: application/vnd.ms-excel; charset=iso-8859-1'); // iso-8859-1  en_US.UTF-8
                header('Content-Disposition: attachment;filename="' . $file_name . '.xls"');
                header('Cache-Control: max-age=0');
                header("Pragma: no-cache");
                header("Expires: 0");

                echo $this->fm->crea_tabla_result($result, $cols, $cols_tit, $ar_align, $ar_pie);

            }else{
              $data["msg"] = validation_errors();
              $data["rpta_msg"] = "danger";
            }

        }else{
            $this->template->load('production/index', 'reportes/analisis', $this->data);
        }

    }

}
