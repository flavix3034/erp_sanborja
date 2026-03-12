<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventarios extends CI_Controller {

    function __construct() {
        parent::__construct();

        session_start();
        if(!isset($_SESSION["user_id"])){ 
            die("No tiene sesión disponible. <a href=\"" . base_url("welcome/index") . "\">Login</a>"); 
        }
		$this->Igv = 18;
        $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';
        $this->load->model('inventarios_model');
        $this->load->model("compras_model");
    }

    function index($cDesde='null', $cHasta='null') {

        $this->data['page_title'] = "Inventarios";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'inventarios/index', $this->data);
    }

    function add() {
        

        if(isset($_POST["tienda"])){

            $this->data['Admin']    = ($_SESSION["group_id"] == '1' ? true : false);

            $ar = array();
            $ar["store_id"]     = $_POST["tienda"];
            $ar["fecha_i"]      = $_POST["fecha_i"];
            $ar["fecha_f"]      = $_POST["fecha_f"];
            $ar["responsable"]  = $_POST["responsable"];

            if($this->db->insert("tec_maestro_inv", $ar)){
                $this->data["msg"] = "Se crea correctamente un Inventario con fecha de hoy";
                $this->data["rpta_msg"] = "success";       
            }else{
                $this->data["msg"] = "No se puedo grabar.";
                $this->data["rpta_msg"] = "danger";       
            }     

            $this->data['page_title'] = "Ver Inventarios";
            $this->template->load('production/index', 'inventarios/index', $this->data);

        }else{

            $this->data['page_title'] = "Crear un Inventario";
            $this->template->load('production/index', 'inventarios/add', $this->data);

        } // FIN DE MODO EDICION
    }

    function obtener_inv(){
        $id = $_GET["id"];

        $query = $this->db->select("fecha_i, fecha_f, responsable")
            ->from("tec_maestro_inv")
            ->where("id", $id)->get();

        $ar = array();
        foreach($query->result() as $r){
            $ar["fecha_i"] = $r->fecha_i;
            $ar["fecha_f"] = $r->fecha_f;
            $ar["responsable"] = $r->responsable;
        }
        echo json_encode($ar);
    }

    function editar_inv(){
        $id             = $_GET["id"];
        $fecha_i        = $_GET["fecha_i"];
        $fecha_f        = $_GET["fecha_f"];
        $responsable    = $_GET["responsable"];
        $ar = array();
        $ar["fecha_i"]      = $fecha_i;
        $ar["fecha_f"]      = $fecha_f;
        $ar["responsable"]  = $responsable;
        $this->db->where("id", $id);
        $this->db->update("tec_maestro_inv", $ar);
        echo "OK";
    }

    function add_movimientos(){
        if(isset($_POST["modo"])){
            $ar = array();
            $ar["fechah"]       = $_POST["fechah"];
            $ar["store_id"]     = $_POST["store_id"];
            $ar["tipo_mov"]     = $_POST["tipo_mov"];
            $ar["metodo"]       = $_POST["metodo"];
            $ar["product_id"]   = $_POST["product_id"];
            $ar["variant_id"]   = isset($_POST["variant_id"]) ? $_POST["variant_id"] : 0;
            //$ar["unidad"]     = $_POST["unidad"];
            $ar["cantidad"]     = $_POST["cantidad"];
            $ar["obs"]          = $_POST["obs"];


            // Nota.- En caso de ser SALIDA : costo mas antiguo, de lo contrario costo mas moderno:
            $ar["compra_id"]    = $this->enlazar_compra($ar["store_id"], $ar["product_id"], $ar["cantidad"], $ar["tipo_mov"]);
            
            if ($this->db->set($ar)->insert("tec_movim")){
                $this->data["rpta_msg"] = "success";
                $this->data["msg"]      = "Se agrega correctamente.";

                $v_id = isset($ar["variant_id"]) ? $ar["variant_id"] : 0;
                if($ar["tipo_mov"]=='I'){
                    $this->compras_model->agregar_al_stock($ar["product_id"], $ar["store_id"], $ar["cantidad"], $v_id);
                }else{
                    $this->compras_model->disminuir_al_stock($ar["product_id"], $ar["store_id"], $ar["cantidad"], $v_id);
                }
            }else{
                $this->data["rpta_msg"] = "warning";
                $this->data["msg"]      = "No se pudo grabar, verifique su informacion";
            }
        }
        
        $this->data['page_title'] = "Otros Movimientos";
        $this->data['tipos_mov'] = $this->db->select('*')->get("tec_tipos_mov");
        $this->template->load('production/index', 'inventarios/add_movimientos', $this->data);
    }

    function add_traslados(){
        if(isset($_POST["modo"])){
            
            $ar = array();
            $ar["fechah"]       = $_POST["fechah"];
            $ar["store_id"]     = $_POST["store_id"];
            $ar["store_id_destino"] = $_POST["store_id_destino"];
            $ar["tipo_mov"]     = 'S';
            $ar["metodo"]       = 1;
            $ar["product_id"]   = $_POST["product_id"];
            $ar["variant_id"]   = isset($_POST["variant_id"]) ? $_POST["variant_id"] : 0;
            $ar["cantidad"]     = $_POST["cantidad"];
            $ar["obs"]          = $_POST["obs"];
            $ar["user_id"]      = $_POST["user_id"];
            
            // Nota.- En caso de ser SALIDA : costo mas antiguo, de lo contrario costo mas moderno:
            $ar["compra_id"]    = $this->enlazar_compra($ar["store_id"], $ar["product_id"], $ar["cantidad"], $ar["tipo_mov"]);

            // Primer Movimiento:

            if ($this->db->set($ar)->insert("tec_movim")){
                $this->data["rpta_msg"] = "success";
                $this->data["msg"]      = "Se agrega correctamente.";

                // Se disminuye el stock
                $v_id = isset($ar["variant_id"]) ? $ar["variant_id"] : 0;
                $this->compras_model->disminuir_al_stock($ar["product_id"], $ar["store_id"], $ar["cantidad"], $v_id);

            }else{
                $this->data["rpta_msg"] = "warning";
                $this->data["msg"]      = "No se pudo grabar, verifique su informacion";
            }

            // Segundo Movimiento:

            $ar["store_id"]             = $_POST["store_id_destino"];
            $_POST["store_id_destino"]  = $_POST["store_id"];
            $ar["tipo_mov"]             = 'I';
            
            if ($this->db->set($ar)->insert("tec_movim")){
                $this->data["rpta_msg"] = "success";
                $this->data["msg"]      = "Se agrega correctamente.";

                // Se incrementa el stock
                $v_id = isset($ar["variant_id"]) ? $ar["variant_id"] : 0;
                $this->compras_model->agregar_al_stock($ar["product_id"], $ar["store_id"], $ar["cantidad"], $v_id);
            }else{
                $this->data["rpta_msg"] = "warning";
                $this->data["msg"]      = "No se pudo grabar, verifique su informacion";
            }
        
        }
        
        $this->data['page_title'] = "Traslados";
        $this->data['tipos_mov'] = $this->db->select('*')->get("tec_tipos_mov");
        $this->template->load('production/index', 'inventarios/add_traslados', $this->data);
    }

    function ver_movimientos($cDesde='null', $cHasta='null') {

        $this->data['page_title'] = "Inventarios";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'inventarios/ver_movimientos', $this->data);
    }

    function stock_productos(){
        $id_inv = $_SESSION["inventario_vigente"];
        
        //if(strlen($id_inv."")>0){
            $query = $this->inventarios_model->stock($id_inv, $_SESSION["store_id"]);
        //}else{
        //    $query = false;
        //}
        
        $this->data["query_stock"]  = $query;
        // Variantes agrupadas por product_id
        $qv = $this->inventarios_model->stock_variantes($_SESSION["store_id"]);
        $variantes_map = array();
        foreach($qv->result() as $v){
            $variantes_map[$v->product_id][] = $v;
        }
        $this->data["variantes_map"] = $variantes_map;
        $this->data['page_title']   = "Stock Avanzado";
        $this->template->load('production/index', 'inventarios/stock_productos', $this->data);
    }

    function actualizar_stock_table($store_id){ // ACTUALIZA EL STOCK BASADO EN UN IVENTARIO FISICO
        $id_inv = $_SESSION["inventario_vigente"];
        $query = $this->inventarios_model->stock($id_inv,$_SESSION["store_id"]);
        $n = 0;
        $result = $query->result();
        foreach($result as $r){
            $stock_actual   = $r->stock;
            $product_id     = $r->id;
            
            $cSql = "select id from tec_prod_store where product_id = ? and store_id = ?";
            $query = $this->db->query($cSql,array($product_id, $store_id));
            $existe = false;
            foreach($query->result() as $r){
                $existe = true;
            }
            if (!$existe){
                $ar["product_id"]   = $product_id;
                $ar["store_id"]     = $store_id;
                $this->db->set($ar)->insert('tec_prod_store');
            }
            $cSql = "update tec_prod_store set stock = ? where product_id = ? and store_id = ?";
            $this->db->query($cSql,array($stock_actual, $product_id, $store_id));
            $n++;
        }
        return $n;
    }

    function actualizar_stock_products($store_id, $product_id, $stock, $variant_id = 0){
        $variant_id = $variant_id * 1;
        $cSql = "select id from tec_prod_store where product_id = ? and store_id = ? and COALESCE(variant_id,0) = ?";
        $query = $this->db->query($cSql,array($product_id, $store_id, $variant_id));
        $existe = false;
        foreach($query->result() as $r){
            $existe = true;
        }
        if (!$existe){
            $ar["product_id"]   = $product_id;
            $ar["store_id"]     = $store_id;
            $ar["variant_id"]   = $variant_id;
            $this->db->set($ar)->insert('tec_prod_store');
        }
        $cSql = "update tec_prod_store set stock = ? where product_id = ? and store_id = ? and COALESCE(variant_id,0) = ?";
        $this->db->query($cSql,array($stock, $product_id, $store_id, $variant_id));
    }

    function eliminar_movimiento(){
        $id = $_GET["id"];
        
        // debo averiguar product_id, store_id, cantidad
        $query = $this->db->select("product_id, variant_id, store_id, cantidad, tipo_mov")->where("id",$id)->get("tec_movim");
        foreach($query->result() as $r){
            $product_id = $r->product_id;
            $variant_id = isset($r->variant_id) ? $r->variant_id : 0;
            $store_id   = $r->store_id;
            $cantidad   = $r->cantidad;
            $tipo_mov   = $r->tipo_mov;
        }

        if($this->db->where("id",$id)->delete("tec_movim")){
            if($tipo_mov == 'I'){
                $this->compras_model->disminuir_al_stock($product_id, $store_id, $cantidad, $variant_id);
            }else{
                $this->compras_model->agregar_al_stock($product_id, $store_id, $cantidad, $variant_id);
            }
            $rpta = "OK";
        }else{
            $rpta = "KO";
        }
        $ar = array("rpta"=>$rpta);
        $json_ar = json_encode($ar);
        echo $json_ar;
    }

    function registrar_productos(){  // de inventario
        
        if(isset($_POST["modo"])){
            //select id, fecha, product_id, cantidad, unidad, store_id, maestro_id from tec_inventarios;
            $fecha          = date("Y-m-d");
            $product_id     = $_POST["product_id"];
            $cantidad       = $_POST["cantidad"];
            $unidad         = $_POST["unidad"];
            $store_id       = $_SESSION["store_id"];
            $maestro_id     = $_POST["maestro_id"];

            $ar = array(
                "fecha"         =>$fecha,
                "product_id"    =>$product_id,
                "cantidad"      =>$cantidad,
                "unidad"        =>$unidad,
                "store_id"      =>$store_id,
                "maestro_id"    =>$maestro_id
            );
            if ($this->db->insert("tec_inventarios",$ar)){
                $this->data["msg"]  = "Se grabar correctamente";
                $this->data["rpta_msg"] = "success";
            }else{
                $this->data["msg"] = "No se puedo grabar.";
                $this->data["rpta_msg"] = "danger";
            }
        }
        $this->data['page_title']   = "Registro de Inventario F&iacute;sico";
        $this->template->load('production/index', 'inventarios/registrar_productos', $this->data);
    }

    function registrar_productos_ajax(){
        
        //select id, fecha, product_id, cantidad, unidad, store_id, maestro_id from tec_inventarios;
        $fecha          = date("Y-m-d");
        $product_id     = isset($_GET["product_id"]) ? intval($_GET["product_id"]) : 0;
        $variant_id     = isset($_GET["variant_id"]) ? intval($_GET["variant_id"]) : 0;
        $cantidad       = isset($_GET["cantidad"]) ? floatval($_GET["cantidad"]) : 0;
        $unidad         = isset($_GET["unidad"]) ? $_GET["unidad"] : "";
        //$store_id       = $_SESSION["store_id"];
        $maestro_id     = isset($_GET["maestro_id"]) ? intval($_GET["maestro_id"]) : 0;

        if ($product_id == 0 || $cantidad == 0 || $maestro_id == 0) {
            echo '{"msg":"Debe completar todos los campos.","rpta_msg":"danger"}';
            return;
        }

        $ar = array(
            "fecha"         =>$fecha,
            "product_id"    =>$product_id,
            "variant_id"    =>$variant_id,
            "cantidad"      =>$cantidad,
            "unidad"        =>$unidad,
            //"store_id"      =>$store_id,
            "maestro_id"    =>$maestro_id
        );
        if ($this->db->insert("tec_inventarios",$ar)){
            echo '{"msg":"Se graba correctamente.","rpta_msg":"success"}';
        }else{
            echo '{"msg":"No se pudo grabar.","rpta_msg":"danger"}';
        }
    }

    function get_inventario(){
        $maestro_id = $_GET["maestro_id"];
        $limit      = isset($_GET["limit"]) ? intval($_GET["limit"]) : 50000;
        // fecha product_id cantidad unidad store_id maestro_id
        $cSql = "select a.id, a.fecha, a.product_id,
            IF(a.variant_id > 0, CONVERT(fn_product_display_name(a.product_id, a.variant_id) USING latin1), b.name) productos,
            a.cantidad, a.unidad, c.descrip des_unidad
            from tec_inventarios a
            inner join tec_products b on a.product_id = b.id
            left join tec_unidades c on a.unidad = c.id
            where a.maestro_id = ? order by a.id desc limit ?";

        $result     = $this->db->query($cSql, array($maestro_id,$limit))->result_array();
        $cols       = array("id", "fecha", "productos", "cantidad", "des_unidad");
        $cols_titulos = array("id", "fecha", "productos", "cantidad", "des_unidad");
        $ar_align   = array("1","1","0","1","1","1");
        $ar_pie     = array("","","","","","");
        echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
    }

    function ver_inventario($id){
        $cSql = "select concat(b.state,'_',substr(a.fecha_i,1,10)) inventario from tec_maestro_inv a 
            inner join tec_stores b on a.store_id = b.id 
            where a.id = ?";

        $query = $this->db->query($cSql, array($id));
        $titulo = "";
        foreach($query->result() as $r){
            $titulo = $r->inventario;
        }
        $this->data['id_inv']       = $id;
        $this->data['page_title']   = $titulo;
        $this->template->load('production/index', 'inventarios/ver_inventario', $this->data);
    }

    function eliminar_registro_inv(){
        $id = $_GET["id"];
        if($this->db->where("id",$id)->delete("tec_inventarios")){
            $rpta = "OK";
        }else{
            $rpta = "KO";
        }
        $ar = array("rpta"=>$rpta);
        $json_ar = json_encode($ar);
        echo $json_ar;
    }

    function kardex(){
        $id_inv = $_SESSION["inventario_vigente"];
        $product_id = isset($_GET["producto"]) ? $_GET["producto"] : "";
        $variant_id = isset($_GET["variant_id"]) ? intval($_GET["variant_id"]) : 0;

        if(strlen($product_id)>0){
            echo $this->inventarios_model->kardex($product_id, $id_inv, $_SESSION["store_id"], $variant_id);
        }else{
            $this->data['page_title']   = "Kardex";
            $this->template->load('production/index', 'inventarios/kardex', $this->data);    
        }
    }

    function ingreso_inicial(){ // SE TRATA DEL INGRESO DEL STOCK INICIAL, QUE ENTRARA COMO UNA COMPRA INICIAL.

        $store_id           = $_SESSION["store_id"];
        $fecha              = $_POST["date"];
        $fecha_ingreso      = $_POST["date"];
        $tipoDoc            = 5; // ticket
        $nroDoc             = '1';
        $redondeo           = 0;
        
        $proveedor_id       = 1;
        $subtotal           = 1;  
        $igv                = 0.18;
        $total              = 1.18;
        $por_igv            = 0.18;

        /*$dni_cliente  = $_POST["dni_cliente"];
        $name_cliente   = $_POST["name_cliente"];
        $customer_id    = $this->sales_model->customer_id($dni_cliente, $name_cliente);*/

        $this->db->trans_begin();

        $this->db->set("store_id",$store_id);
        $this->db->set("fecha",$fecha);
        $this->db->set("fecha_ingreso",$fecha_ingreso);
        $this->db->set("tipoDoc",$tipoDoc);
        $this->db->set("nroDoc",$nroDoc);
        
        $this->db->set("redondeo",$redondeo);
        $this->db->set("proveedor_id", $proveedor_id);
        $this->db->set("monto_base", $subtotal);
        $this->db->set("igv", $igv);
        $this->db->set("por_igv", $por_igv*100);

        $this->db->set("total", $total);

        if ($this->db->insert("tec_compras")){
            $id = $this->db->insert_id();
            
            $cSql = "select * from carga_stock_inicial";
            $query = $this->db->query($cSql);
            foreach($query->result() as $r){

                $product_id     = $r->product_id;
                $cantidad       = $r->cantidad;

                $this->db->set("compra_id",$id);
                $this->db->set("product_id",$product_id);
                $this->db->set("cantidad",$cantidad);
                
                $precio_unitario = $r->precio * 1; // El precio se trata del valor unitario con Igv, tal cual se pone
                $subtotal       = $precio_unitario * ($_REQUEST['quantity'][$i] * 1);
                
                $this->db->set("precio_con_igv",    $precio_unitario);          // con igv
                //$precio_sin_igv = $precio_unitario / (1+$por_igv);
                $precio_sin_igv = $r->costo * 1;
                $this->db->set("precio_sin_igv",    $precio_sin_igv);   // sin igv
                $this->db->set("subtotal",          $subtotal);
                $this->db->set("igv",               $precio_sin_igv * $por_igv);
                $this->db->set("product_name",      $r->descrip);
                
                $this->db->insert("tec_compra_items");

                $this->compras_model->agregar_al_stock($product_id, $store_id, $cantidad);
            }
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->data["msg"] = "No se pudo guardar la compra ...revise";
                $this->data["rpta_msg"] = "danger";
            }else{
                $this->db->trans_commit();
                $this->data["msg"] = "Se guarda la compra correctamente";
                $this->data["rpta_msg"] = "success";
            }

            $this->data["page_title"] = "Agregar Compras";
            $this->template->load('production/index', 'compras/add', $this->data);
        }else{
            $this->db->trans_rollback();
        }

    }

    function finalizar_inventario(){

        $inv_id = $_REQUEST["maestro_id2"];

        // Averiguando la fecha exacta del inventario
        $fecha_f        = ""; 
        $query = $this->db->query("select * from tec_maestro_inv where id = $inv_id");
        foreach($query->result() as $r){
            $fecha_f = $r->fecha_f;
        }

        $cad_fecha_f    = "";
        $cad_fecha_f2   = "";
        $cad_fecha_f3   = "";
        if(strlen($fecha_f)>=0){
            $cad_fecha_f = " and com.fecha_ingreso < '$fecha_f'"; // tec_compra
            $cad_fecha_f2 = " and sx.date < '$fecha_f'"; // tec_sales
            $cad_fecha_f3 = " and fechah < '$fecha_f'"; // tec_movim
        }

        // Averiguando la tienda actual
        $row = $this->db->query("select store_id from tec_maestro_inv where id = $inv_id")->row();
        if(is_null($row)){
            return false;
        }
        $store_id =  $row->store_id;

        // Query de stock calculado por product_id + variant_id
        $cSql = "SELECT ps.product_id, COALESCE(ps.variant_id,0) AS variant_id,
                IF(COALESCE(ps.variant_id,0) > 0, CONVERT(fn_product_display_name(ps.product_id, ps.variant_id) USING latin1), a.name) AS name,
                if(isnull(compras.cantidad_comprada),0,compras.cantidad_comprada)
                - if(isnull(ventas.cantidad_vendida),0,ventas.cantidad_vendida)
                + if(isnull(movim.ingreso),0,movim.ingreso)
                - if(isnull(movim.salida),0,movim.salida) as stock
                FROM tec_prod_store ps
                INNER JOIN tec_products a ON ps.product_id = a.id
                left join (
                    select com_i.product_id, COALESCE(com_i.variant_id,0) variant_id, sum(com_i.cantidad) cantidad_comprada from tec_compras com
                    inner join tec_compra_items com_i on com.id = com_i.compra_id
                    where com.store_id='{$store_id}' $cad_fecha_f
                    group by com_i.product_id, COALESCE(com_i.variant_id,0)
                ) compras on ps.product_id = compras.product_id AND COALESCE(ps.variant_id,0) = compras.variant_id
                left join (
                    select sxi.product_id, COALESCE(sxi.variant_id,0) variant_id, sum(sxi.quantity) cantidad_vendida
                    from tec_sales sx
                    inner join tec_sale_items sxi on sx.id = sxi.sale_id
                    where sx.store_id='{$store_id}' $cad_fecha_f2 and sx.anulado != '1'
                    group by sxi.product_id, COALESCE(sxi.variant_id,0)
                ) ventas on ps.product_id = ventas.product_id AND COALESCE(ps.variant_id,0) = ventas.variant_id
                left join (
                    select mo.product_id, COALESCE(mo.variant_id,0) variant_id, sum(if(mo.tipo_mov='I', mo.cantidad, 0)) Ingreso, sum(if(mo.tipo_mov='S', mo.cantidad, 0)) Salida
                    from tec_movim mo
                    where mo.store_id='{$store_id}' $cad_fecha_f3
                    group by mo.product_id, COALESCE(mo.variant_id,0)
                ) movim on ps.product_id = movim.product_id AND COALESCE(ps.variant_id,0) = movim.variant_id
                where a.activo='1' AND ps.store_id = '{$store_id}'
                order by a.name, ps.variant_id";

        $result = $this->db->query($cSql)->result_array();

        // Indexar resultado por clave compuesta product_id_variant_id
        $stock_map = array();
        foreach($result as $row){
            $key = $row['product_id'] . '_' . $row['variant_id'];
            $stock_map[$key] = $row;
        }

        // Ahora recien recorriendo los productos del inventario --------------------------
        $cSql = "select * from tec_inventarios where maestro_id = $inv_id";
        $query = $this->db->query($cSql);

        foreach($query->result() as $r){
            $product_id = $r->product_id;
            $variant_id = isset($r->variant_id) ? intval($r->variant_id) : 0;

            // Buscar stock calculado por clave compuesta
            $key = $product_id . '_' . $variant_id;
            $stock = isset($stock_map[$key]) ? $stock_map[$key]['stock'] : 0;

            $stock_de_inv = $r->cantidad;
            $ar = array();

            // datos en común
            $ar["store_id"]     = $store_id;
            $ar["product_id"]   = $product_id;
            $ar["variant_id"]   = $variant_id;
            $ar["fechah"]       = date("Y-m-d H:i");
            $ar["user_id"]      = $_SESSION["usuario"];

            if($stock != $stock_de_inv){

                if($stock > $stock_de_inv){
                    // Crear Movimiento de salida (por perdida)
                    $cantidad = $stock - $stock_de_inv;
                    $ar["cantidad"]     = $cantidad;
                    $ar["tipo_mov"]     = 'S';
                    $ar["obs"]          = 'POR PERDIDA DE PRODUCTOS';
                }else{
                    $cantidad = $stock_de_inv - $stock;
                    // Crear Movimiento de Ingreso (por haber mas)
                    $ar["cantidad"]     = $cantidad;
                    $ar["tipo_mov"]     = 'I';
                    $ar["obs"]          = "PARA SINCERAR INVENTARIO ($stock_de_inv - $stock)";
                }
                $ar["inv_id"]   = $inv_id;
                $ar["metodo"]   = 4; // otros

                if ($this->db->set($ar)->insert("tec_movim")){
                    $this->data["rpta_msg"] = "success";
                    $this->data["msg"]      = "Se agrega correctamente.";
                }else{
                    $this->data["rpta_msg"] = "warning";
                    $this->data["msg"]      = "No se pudo grabar en tec_movim";
                }
            }

            // Finalizando el inventario
            $cSql = "update tec_maestro_inv set finaliza='1' where id = $inv_id";
            $this->db->query($cSql);

            // Actualizando en stock contador
            $this->actualizar_stock_products($store_id, $product_id, $stock_de_inv, $variant_id);

        }
        //die("Fin");
        $this->data["rpta_msg"]     = "success";
        $this->data["msg"]          = "Se procesa y finaliza el Inventario";
        $this->data['page_title']   = "Registro de Inventario F&iacute;sico";
        $this->template->load('production/index', 'inventarios/registrar_productos', $this->data);
    }

    function buscar_raw($result, $campo, $valor, $campo_res){
        $nLim = count($result);
        for($i=0; $i<$nLim; $i++){
            if($result[$i][$campo] == $valor){
                return $result[$i][$campo_res];
            }
        }
        return "";
    }

    function enlazar_compra($store_id, $product_id, $q, $tipo_mov){
        // Nota.- En caso de ser SALIDA : costo mas antiguo, de lo contrario costo mas moderno.
        
        $cOrden = ($tipo_mov == 'S' ? 'asc' : 'desc');
        $cSql = "SELECT a.compra_id, a.cantidad, a.van
            FROM `tec_compra_items` as `a` 
            LEFT JOIN `tec_compras` as `b` ON `a`.`compra_id` =`b`.`id` 
            WHERE `b`.`store_id` = ? AND `a`.`product_id` = ? and a.cantidad - a.van > 0 order by a.compra_id $cOrden limit 1";
        
        $row = $this->db->query($cSql,array($store_id, $product_id))->row();

        $compra_id = 0;
        if(!is_null($row)){
            
            $compra_id = $row->compra_id;
            
            $dif = ($row->cantidad*1) - $row->van;
            if($dif >= $q*1){
                $van = ($row->van * 1) + $q;
            }else{
                $van = $row->cantidad;
            }

            // (2) actualizamos campo van en tec_compra_items
            $ar = array("van"=>$van);
            $this->db->set($ar)->where("compra_id",$compra_id)->where("product_id",$product_id)->update("tec_compra_items");
            
        }

        // (3) retornamos el id de la compra
        return $compra_id;
    }

    function actualizar_stock(){
        // Sabemos que el stock tambien se lleva en una tabla aparte llamada (tec_prod_store) por tanto se registrará
        $store_id = $_SESSION["store_id"];
        if(isset($_REQUEST["modo"])){

            // Obtener todos los registros de prod_store para esta tienda (productos con y sin variantes)
            $cSql = "SELECT ps.product_id, COALESCE(ps.variant_id,0) AS variant_id, ps.stock,
                CONVERT(fn_product_display_name(ps.product_id, ps.variant_id) USING latin1) AS nombre
                FROM tec_prod_store ps
                INNER JOIN tec_products p ON ps.product_id = p.id
                WHERE ps.store_id = ? AND p.activo = '1'
                ORDER BY p.name, ps.variant_id";
            $query_ps = $this->db->query($cSql, array($store_id));

            $datis = "<table class='table table-bordered'><tr>
                <th>Producto</th>
                <th>Ahora</th>
                <th>Nuevo</th>
            </tr>";

            foreach($query_ps->result() as $r){

                $product_id     = $r->product_id;
                $variant_id     = $r->variant_id * 1;
                $stock_actual   = $r->stock * 1;

                $nuevo_stock    = 1*$this->inventarios_model->kardex_guardar($product_id, $store_id, $variant_id);

                if($nuevo_stock != $stock_actual){
                    $datis .= "<tr><td>" . $product_id . ") " . $r->nombre . "</td><td>" . $stock_actual . "</td><td>" . $nuevo_stock . "</td></tr>";
                }

            }
            $datis .= "</table>";
            $this->data['datis']        = $datis;
        }
        $this->data['page_title']   = "Registro de Inventario F&iacute;sico";

        $this->template->load('production/index', 'inventarios/actualiza_stock', $this->data);

    }

    function listar_stock(){ // LISTA STOCK DE LA TABLA tec_prod_store
        $this->data['q_lista_stock']    = $this->inventarios_model->listar_stock(); // Lista todos los Stocks
        $this->data['store_id']         = $_SESSION["store_id"];
        $this->data['page_title']   = "Stock de Productos";
        $this->template->load('production/index', 'inventarios/listar_stock', $this->data);    
    }

    function get_listar_stock(){
        $query = $this->inventarios_model->listar_stock(); // Lista todos los Stocks
        $result = $query->result_array();
        $ar_campos = array("product_id", "name", "marca", "stock");
        echo $this->fm->json_datatable($ar_campos, $result);
    }

}
