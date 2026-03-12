<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_controller
{
    public $Igv = 18;
    
    function __construct() {
        parent::__construct();
        session_start();
        $this->load->model('products_model');
    }

    function index($store_id="0",$tipo='P',$categoria="0") {
        
        $this->data['page_title']    = "Lista de Productos";
        $this->data['store_id'] = "";
        $this->data["categoria"] = "";

        if(strlen($categoria)>0){
            $this->data["categoria"] = $categoria;
        }

        if(strlen($store_id)>0){
            $this->data["store_id"] = $store_id;
        }

        if(strlen($tipo)>0){
            $this->data["tipo"] = $tipo;
        }

        $query = $this->products_model->listar($categoria);

        $this->data["query"] = $query;

        $this->template->load('production/index', 'products/index', $this->data);
    }

    function add($id = null){
        if(!is_null($id)){
            $this->data["row"]  = $this->db->select('*')->from('tec_products')->where('id',$id)->get()->row();
            $this->data["modo"] = 'update';
            $this->data["id"]   = $id;
            // Verificar si tiene variantes
            $this->load->model('atributos_model');
            $this->data["tiene_variantes"] = $this->atributos_model->producto_tiene_variantes($id);
        }else{
            $this->data["modo"] = 'insert';
            $this->data["tiene_variantes"] = false;
        }

        $this->data['page_title']    = "Agregar Producto:";
        $this->template->load("production/index", 'products/add', $this->data);
    }

    function add_servicio($id = null){
        if(!is_null($id)){
            $this->data["row"]  = $this->db->select('*')->from('tec_products')->where('id',$id)->get()->row();
            $this->data["modo"] = 'update';
            $this->data["id"]   = $id;
        }else{
            $this->data["modo"] = 'insert'; 
        }

        $this->data['page_title']    = "Agregar Servicio:";
        $this->template->load("production/index", 'products/add_servicio', $this->data);
    }

    function save(){
        /*
            if(isset($_POST["modo_api"])){
                $modo_api   = $_POST["modo_api"];
            }else{
                $modo_api   = '0';
            }
        */
        $code           = $_POST["code"];
        $name           = $_POST["name"]; 
        $category_id    = $_POST["category_id"];
        $unidad         = $_POST["unidad"]; 
        $alert_cantidad = $_POST["alert_cantidad"]; 
        $price          = $_POST["price"];
        $imagen         = $_POST["imagen"];
        $marca          = $_POST["marca"];
        $precio_x_mayor = $_POST["precio_x_mayor"];

        $validacion = true;
        $nu_codigo = 10000001;

        $modo = strtolower($_POST["modo"]);
        $modo_api = "";

        if(strlen($code)==0){
            /*
            $cSql = "select max(code)+1 nu_codigo from tec_products where activo='1' and length(code)=8";
            $query = $this->db->query($cSql);
            
            foreach($query->result() as $r){
                if(!is_null($r->nu_codigo)){
                    $nu_codigo = $r->nu_codigo;
                }
            }
            */

            // VOLVIENDO EL CODE GENERADO ALEATORIO:
            $continua = true; $ix = 0;
            while ($continua) {
                $nu_codigo = ("" . random_int(1,9)) . random_int(0,999999999999);
                $row = $this->db->select("code")->where("code",$nu_codigo)->get("tec_products")->row();
                if($row){
                    $nada = 'nada';
                }else{
                    $continua = false;
                }
            }

            $code = $nu_codigo;

        }else{
            $code = strtoupper($code);
            // Verifico que no exista
            if($modo == 'insert'){
                $cSql = "select * from tec_products where activo='1' and code = ?";
                $query = $this->db->query($cSql,array($code));
                $n=0;
                foreach($query->result() as $r){ $n++;}
                if($n>0){ $validacion = false; }
            }
        }

        $ar["code"]         = strtoupper($code);
        $ar["name"]         = strtoupper($name);
        $ar["category_id"]  = $category_id;
        $ar["unidad"]       = $unidad;
        $ar["alert_cantidad"] = $alert_cantidad;
        $ar["price"]        = $price;
        //$ar["imagen"]       = $imagen;
        $ar["marca"]        = $marca;
        $ar["precio_x_mayor"] = $precio_x_mayor;
        $ar["impuesto"]     = $this->Igv;
        $ar["prod_serv"]    = "P"; // Producto

        /*
            $ _FILES ['file'] ['tmp_name'] - el archivo cargado en el directorio temporal en el servidor web.
            $ _FILES ['file'] ['name'] - el nombre real del archivo cargado.
            $ _FILES ['file'] ['size'] - el tamaño en bytes del archivo cargado.
            $ _FILES ['file'] ['type'] - el tipo MIME del archivo cargado.
            $ _FILES ['file'] ['error'] - el código de error asociado con la carga de este archivo.
        */

        $file_tmp   = $_FILES['archivo']['tmp_name'];
        
        if(strlen($file_tmp)>0){
            $file_name  = $_FILES['archivo']['name'];

            $file_size  = $_FILES['archivo']['size'];
            $file_type  = $_FILES['archivo']['type'];
            /*$file_ext   = "png"; strtolower(end(explode('.',$file_name)));*/
            
            $ar_f = explode('.',$file_name);
            $miE  = end($ar_f);

            $file_ext  = strtolower($miE);

            $expensions = array("jpeg","jpg","png");
         
            if(in_array($file_ext, $expensions) === false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
         
            if($file_size > 2097152) {
                $errors[]='File size must be excately 2 MB';
            }
         
            if(empty($errors) == true) {
                $ar["imagen"] = $file_name;
                move_uploaded_file($file_tmp, "imagenes/".$file_name);
            }else{
                print_r($errors);
            }
        }

        if($validacion == true){

            if($modo == 'insert'){

                $cSql = "select COALESCE(max(id),0)+1 nuevo from tec_products where id < 99999";
                $ar["id"] = $this->db->query($cSql)->row()->nuevo;

                if($this->db->insert("tec_products", $ar)){
                    $this->data['msg']      = "Se guarda correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }

            }else{

                $id = $_POST['id'];
                // update
                if($this->db->set($ar)->where('id',$id)->update("tec_products")){
                    $this->data['msg']      = "Se actualiza correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }

            }

            // === GUARDAR VARIANTES ===
            if (isset($_POST['tiene_variantes']) && $_POST['tiene_variantes'] == '1') {
                $product_id = ($modo == 'insert') ? $ar["id"] : $_POST['id'];
                $this->guardar_variantes_producto($product_id);
            }

        }else{

            $this->data['msg']      = "Hay informacion que debe cambiarse, codigo de barra ya existe";
            $this->data["rpta_msg"] = "danger";

        }

        $this->data["modo"]         = 'insert';
        $this->data['page_title']   = "Agregar Producto:";

        if($modo_api == '1'){
            echo json_encode(array("msg"=>$this->data["msg"]));
        }else{
            $this->template->load("production/index", 'products/add', $this->data);
        }

    }

    /**
     * Guarda las variantes enviadas desde el formulario de producto
     */
    private function generar_barcode_unico() {
        do {
            $barcode = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $row = $this->db->select("barcode")->where("barcode", $barcode)->get("tec_product_variantes")->row();
        } while ($row);
        return $barcode;
    }

    private function generar_sku_unico() {
        do {
            $sku = ("" . random_int(1,9)) . random_int(0,999999999999);
            $row = $this->db->select("sku")->where("sku", $sku)->get("tec_product_variantes")->row();
        } while ($row);
        return $sku;
    }

    private function generar_code_producto_unico() {
        do {
            $code = 'P' . str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            $row = $this->db->select("id")->where("code", $code)->get("tec_products")->row();
        } while ($row);
        return $code;
    }

    private function guardar_variantes_producto($product_id) {
        $this->load->model('atributos_model');

        $var_skus    = isset($_POST['var_sku']) ? $_POST['var_sku'] : array();
        $var_barcodes = isset($_POST['var_barcode']) ? $_POST['var_barcode'] : array();
        $var_prices  = isset($_POST['var_price']) ? $_POST['var_price'] : array();
        $var_pmayors = isset($_POST['var_pmayor']) ? $_POST['var_pmayor'] : array();
        $var_activos = isset($_POST['var_activo']) ? $_POST['var_activo'] : array();
        $var_attrs   = isset($_POST['var_attrs']) ? $_POST['var_attrs'] : array();
        $var_ids     = isset($_POST['var_id']) ? $_POST['var_id'] : array();

        // Obtener IDs existentes para saber cuales eliminar
        $existentes = $this->atributos_model->get_variantes_producto($product_id);
        $ids_existentes = array();
        foreach ($existentes as $e) { $ids_existentes[] = intval($e->id); }

        $ids_procesados = array();
        $store_id = intval($_SESSION['store_id']);

        for ($i = 0; $i < count($var_skus); $i++) {
            $sku     = trim($var_skus[$i]);
            $barcode = isset($var_barcodes[$i]) ? trim($var_barcodes[$i]) : '';

            // Autogenerar SKU si está vacío
            if (strlen($sku) == 0) {
                do {
                    $sku = ("" . random_int(1,9)) . random_int(0,999999999999);
                    $row = $this->db->select("sku")->where("sku",$sku)->get("tec_product_variantes")->row();
                } while ($row);
            }

            // Autogenerar barcode si está vacío (12 dígitos numéricos únicos)
            if (strlen($barcode) == 0) {
                do {
                    $barcode = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
                    $row = $this->db->select("barcode")->where("barcode",$barcode)->get("tec_product_variantes")->row();
                } while ($row);
            }

            $price   = isset($var_prices[$i]) && strlen(trim($var_prices[$i])) > 0 ? floatval($var_prices[$i]) : null;
            $pmayor  = isset($var_pmayors[$i]) && strlen(trim($var_pmayors[$i])) > 0 ? floatval($var_pmayors[$i]) : null;
            $activo  = in_array(strval($i), $var_activos) ? '1' : '';
            $attrs_json = isset($var_attrs[$i]) ? $var_attrs[$i] : '[]';

            $data_var = array(
                'product_id'     => $product_id,
                'sku'            => $sku,
                'barcode'        => strlen($barcode) > 0 ? $barcode : null,
                'price'          => $price,
                'precio_x_mayor' => $pmayor,
                'activo'         => $activo
            );

            // Si tiene ID existente, actualizar; si no, insertar
            if (isset($var_ids[$i]) && intval($var_ids[$i]) > 0) {
                $var_id = intval($var_ids[$i]);
                $this->atributos_model->actualizar_variante($var_id, $data_var);
                $ids_procesados[] = $var_id;
            } else {
                $var_id = $this->atributos_model->insertar_variante($data_var);
                $ids_procesados[] = $var_id;

                // Crear stock inicial en tec_prod_store
                $this->db->insert('tec_prod_store', array(
                    'product_id' => $product_id,
                    'store_id'   => $store_id,
                    'stock'      => 0,
                    'variant_id' => $var_id
                ));
            }

            // Guardar relacion variante-atributos
            $this->atributos_model->eliminar_variante_atributos($var_id);
            $attrs = json_decode($attrs_json, true);
            if (is_array($attrs)) {
                foreach ($attrs as $a) {
                    $this->atributos_model->insertar_variante_atributo(array(
                        'variante_id' => $var_id,
                        'atributo_id' => intval($a['atributo_id']),
                        'valor_id'    => intval($a['valor_id'])
                    ));
                }
            }
        }

        // Eliminar variantes que ya no estan (fueron removidas del formulario)
        foreach ($ids_existentes as $eid) {
            if (!in_array($eid, $ids_procesados)) {
                $this->atributos_model->eliminar_variante($eid);
                // Eliminar stock de la variante
                $this->db->where('variant_id', $eid)->delete('tec_prod_store');
            }
        }
    }

    /**
     * AJAX: Retorna variantes de un producto para modo edicion
     */
    function get_variantes_producto($product_id) {
        header('Content-Type: application/json');
        $this->load->model('atributos_model');
        $variantes = $this->atributos_model->get_variantes_producto($product_id);

        $result = array();
        foreach ($variantes as $v) {
            // Obtener atributos de cada variante
            $attrs_raw = $this->db->query(
                "SELECT va.atributo_id, va.valor_id FROM tec_variante_atributos va WHERE va.variante_id = ?",
                array($v->id)
            )->result_array();

            $result[] = array(
                'id'             => $v->id,
                'sku'            => $v->sku,
                'barcode'        => $v->barcode,
                'price'          => $v->price,
                'precio_x_mayor' => $v->precio_x_mayor,
                'activo'         => $v->activo,
                'combinacion'    => $v->combinacion,
                'atributos'      => $attrs_raw
            );
        }
        echo json_encode($result);
    }

    function save1(){
        
        if(isset($_POST["modo_api"])){
            $modo_api   = $_POST["modo_api"];
            //die("Macromedia");
        }else{
            $modo_api   = '0';
            //die("Flash");
        }

        $code           = $_POST["code"];
        $name           = $_POST["name"]; 
        $category_id    = $_POST["category_id"];
        $unidad         = $_POST["unidad"]; 
        $alert_cantidad = $_POST["alert_cantidad"]; 
        $price          = $_POST["price"];
        $imagen         = (isset($_POST["imagen"]) ? $_POST["imagen"] : "");
        $marca          = $_POST["marca"];
        $precio_x_mayor = $_POST["precio_x_mayor"];

        $validacion = true;
        $nu_codigo = 10000001;

        if(isset($_POST["modo"])){
            $modo = strtolower($_POST["modo"]);
        }else{
            $modo = "insert";
        }
    
        // Valida que nombre marca y modelo no esten vacios
        if($name!='' && $marca!='' && $modelo!='' && $price!='' && $category_id!='' && $unidad!='' && $alert_cantidad!=''){
            $algo = "";
        }else{
            $validacion = false;
        }

        if(strlen($code)==0){

            // VOLVIENDO EL CODE GENERADO ALEATORIO:
            $continua = true; $ix = 0;
            while ($continua) {
                $nu_codigo = ("" . random_int(1,9)) . random_int(0,999999999999);
                $row = $this->db->select("code")->where("code",$nu_codigo)->get("tec_products")->row();
                if($row){
                    $nada = 'nada';
                }else{
                    $continua = false;
                }
            }

            $code = $nu_codigo;

        }else{
            $code = strtoupper($code);
            // Verifico que no exista
            if($modo == 'insert'){
                $cSql = "select * from tec_products where activo='1' and code = ?";
                $query = $this->db->query($cSql,array($code));
                $n=0;
                foreach($query->result() as $r){ $n++;}
                if($n>0){ $validacion = false; }
            }
        }
    
        $ar["code"]         = strtoupper($code);
        $ar["name"]         = strtoupper($name);
        $ar["category_id"]  = $category_id;
        $ar["unidad"]       = $unidad;
        $ar["alert_cantidad"] = $alert_cantidad;
        $ar["price"]        = $price;
        $ar["marca"]        = $marca;
        $ar["precio_x_mayor"] = $precio_x_mayor;
        $ar["impuesto"]     = $this->Igv;
        $ar["prod_serv"]    = "P"; // Producto
    
        $id_producto = "";
        if($validacion == true){

            if($modo == 'insert'){

                $cSql = "select COALESCE(max(id),0)+1 nuevo from tec_products where id < 99999";
                $ar["id"] = $this->db->query($cSql)->row()->nuevo;
                

                if($this->db->insert("tec_products", $ar)){
                    $id_producto = $this->db->insert_id();
                    $this->data['msg']      = "Se guarda correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }

            }else{

                $id = $_POST['id'];
                // update
                if($this->db->set($ar)->where('id',$id)->update("tec_products")){
                    $this->data['msg']      = "Se actualiza correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }

            }

        }else{

            $this->data['msg']      = "Hay informacion que debe cambiarse, codigo de barra ya existe o faltan datos";
            $this->data["rpta_msg"] = "danger";

        }
        
        $this->data["modo"]         = 'insert'; 
        $this->data['page_title']   = "Agregar Producto:";
        
        $rpta = "OK";
        if($validacion==false){
            $rpta = "KO";
        }

        if($modo_api == '1'){
            echo json_encode(array("rpta"=>$rpta, "msg"=>$this->data["msg"], "id_producto"=>$id_producto));
        }else{
            $this->template->load("production/index", 'products/add', $this->data);
        }
    
    //echo "Ecosistema";
    }

    function save_servicio(){
        $code           = $_POST["code"];
        $name           = $_POST["name"]; 
        $category_id    = $_POST["category_id"];
        $unidad         = "UNIDAD"; 
        $alert_cantidad = "999999"; 
        $price          = $_POST["price"];
        $imagen         = $_POST["imagen"];
        $marca          = "";
        $modelo         = "";
        $color          = "";
        $precio_x_mayor = 0;

        $validacion = true;
        $nu_codigo = 10000001;
        if(strlen($code)==0){
            $cSql = "select max(code)+1 nu_codigo from tec_products where activo='1' and length(code)=8";
            $query = $this->db->query($cSql);
            
            foreach($query->result() as $r){
                if(!is_null($r->nu_codigo)){
                    $nu_codigo = $r->nu_codigo;
                }
            }
            $code = $nu_codigo;
        }else{
            $code = strtoupper($code);
            // Verifico que no exista
            $cSql = "select * from tec_products where activo='1' and code = ?";
            $query = $this->db->query($cSql,array($code));
            $n=0;
            foreach($query->result() as $r){ $n++;}
            if($n>0){ $validacion = false; }
        }

        $ar["code"]         = strtoupper($code);
        $ar["name"]         = strtoupper($name);
        $ar["category_id"]  = $category_id;
        $ar["unidad"]       = $unidad;
        $ar["alert_cantidad"] = $alert_cantidad;
        $ar["price"]        = $price;
        //$ar["imagen"]       = $imagen;
        $ar["marca"]        = $marca;
        $ar["precio_x_mayor"] = $precio_x_mayor;
        $ar["impuesto"]     = $this->Igv;
        $ar["prod_serv"]    = 'S';  // Servicio

        /*
            $ _FILES ['file'] ['tmp_name'] - el archivo cargado en el directorio temporal en el servidor web.
            $ _FILES ['file'] ['name'] - el nombre real del archivo cargado.
            $ _FILES ['file'] ['size'] - el tamaño en bytes del archivo cargado.
            $ _FILES ['file'] ['type'] - el tipo MIME del archivo cargado.
            $ _FILES ['file'] ['error'] - el código de error asociado con la carga de este archivo.
        */

        $modo = $_POST["modo"];

        $file_tmp   = $_FILES['archivo']['tmp_name'];
        
        if(strlen($file_tmp)>0){
            $file_name  = $_FILES['archivo']['name'];

            $file_size  = $_FILES['archivo']['size'];
            $file_type  = $_FILES['archivo']['type'];
            /*$file_ext   = "png"; strtolower(end(explode('.',$file_name)));*/
            
            $ar_f = explode('.',$file_name);
            $miE  = end($ar_f);

            $file_ext  = strtolower($miE);

            $expensions = array("jpeg","jpg","png");
         
            if(in_array($file_ext, $expensions) === false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
         
            if($file_size > 2097152) {
                $errors[]='File size must be excately 2 MB';
            }
         
            if(empty($errors) == true) {
                $ar["imagen"] = $file_name;
                move_uploaded_file($file_tmp, "imagenes/".$file_name);
            }else{
                print_r($errors);
            }
        }

        if($validacion == true){
            if(strtolower($modo) == 'insert'){
                $cSql = "select COALESCE(max(id),0)+1 nuevo from tec_products where id < 99999";
                $ar["id"] = $this->db->query($cSql)->row()->nuevo;

                if($this->db->insert("tec_products", $ar)){
                    $this->data['msg']      = "Se guarda correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }
            }else{
                $id = $_POST['id'];
                // update
                if($this->db->set($ar)->where('id',$id)->update("tec_products")){
                    $this->data['msg']      = "Se actualiza correctamente";
                    $this->data["rpta_msg"] = "success";
                }else{
                    $this->data['msg']      = "No se ha podido guardar"; 
                    $this->data["rpta_msg"] = "danger";
                }
            }
        }else{
            $this->data['msg']      = "Hay informacion que debe cambiarse, codigo de barra ya existe";
            $this->data["rpta_msg"] = "danger";
        }
        
        $this->data["modo"]         = 'insert'; 
        $this->data['page_title']   = "Agregar Producto:";
        $this->template->load("production/index", 'products/add', $this->data);

    }

    function busqueda_precio(){
        $dato1          = $_REQUEST["dato1"];
        $variant_id     = isset($_REQUEST["variant_id"]) ? intval($_REQUEST["variant_id"]) : 0;
        $tipo_precio    = $_REQUEST["tipo_precio"];

        // Si hay variante, buscar precio de la variante primero
        $respuesta = "";
        if ($variant_id > 0) {
            $row = $this->db->select("price, precio_x_mayor")->where("id", $variant_id)->get("tec_product_variantes")->row();
            if ($row) {
                if ($tipo_precio == 'por_menor' && !is_null($row->price) && $row->price > 0) {
                    $respuesta = $row->price;
                } elseif ($tipo_precio != 'por_menor' && !is_null($row->precio_x_mayor) && $row->precio_x_mayor > 0) {
                    $respuesta = $row->precio_x_mayor;
                }
            }
        }

        // Fallback al producto padre si no se encontró precio en variante
        if ($respuesta == "") {
            $row = $this->db->select("price, precio_x_mayor")->where("id", $dato1)->get("tec_products")->row();
            if ($row) {
                $respuesta = ($tipo_precio == 'por_menor') ? $row->price : $row->precio_x_mayor;
            }
        }

        echo $respuesta;
    } 

    function getProducts($store_id,$tipo='P',$categoria=""){
        
        $ar = array();
        $cad_1 = $cad_2 = $cad_3 = "";
        if($store_id != '0'){
            $ar[] = $store_id;
            //$cad_1 = " and a.store_id = {$store_id}";
        }

        if($tipo != '0'){
            if ($tipo == 'P'){ 
                if($categoria != '0' && $categoria != ''){
                    $ar[] = $categoria;
                    $cad_2 = " and a.category_id = {$categoria}";
                }
            }
        }

        $cad_3 = " and a.prod_serv = '$tipo'";        

        $cSql       = "SELECT a.id, a.code, a.name, b.name category_id, a.marca, a.alert_cantidad, a.price, a.precio_x_mayor,
            concat('<button onclick=editar(', a.id, ')><i class=\'glyphicon glyphicon-edit\'></i></button>',
            '<button onclick=anular(', a.id, ') style=\'color:rgb(255,100,100)\' title=\'Anular\'><i class=\'glyphicon glyphicon-remove\'></i></button>')
            as acciones, z.costo_con_igv
            FROM tec_products a
            LEFT JOIN (
                SELECT b.product_id, COALESCE(b.variant_id,0) variant_id, round(max(b.precio_con_igv),2) costo_con_igv FROM tec_compra_items b
                INNER JOIN tec_compras c ON b.compra_id=c.id
                GROUP BY b.product_id, COALESCE(b.variant_id,0)
            ) z ON a.id=z.product_id AND z.variant_id = 0
            INNER JOIN tec_categories b ON a.category_id=b.id
            WHERE a.activo='1' AND a.id NOT IN (SELECT product_id FROM tec_product_variantes WHERE activo='1')
            " . $cad_2 . $cad_3 . "

            UNION ALL

            SELECT a.id, pv.barcode code, CONVERT(fn_product_display_name(pv.product_id, pv.id) USING latin1) name,
            b.name category_id, a.marca, a.alert_cantidad,
            COALESCE(pv.price, a.price) price, COALESCE(pv.precio_x_mayor, a.precio_x_mayor) precio_x_mayor,
            concat('<button onclick=editar(', a.id, ')><i class=\'glyphicon glyphicon-edit\'></i></button>',
            '<button onclick=anular(', a.id, ') style=\'color:rgb(255,100,100)\' title=\'Anular\'><i class=\'glyphicon glyphicon-remove\'></i></button>')
            as acciones, zv.costo_con_igv
            FROM tec_product_variantes pv
            INNER JOIN tec_products a ON pv.product_id = a.id
            INNER JOIN tec_categories b ON a.category_id=b.id
            LEFT JOIN (
                SELECT b.product_id, COALESCE(b.variant_id,0) variant_id, round(max(b.precio_con_igv),2) costo_con_igv FROM tec_compra_items b
                INNER JOIN tec_compras c ON b.compra_id=c.id
                GROUP BY b.product_id, COALESCE(b.variant_id,0)
            ) zv ON pv.product_id=zv.product_id AND pv.id=zv.variant_id
            WHERE a.activo='1' AND pv.activo='1'
            " . $cad_2 . $cad_3 . "

            ORDER BY name";

        //echo($cSql);

        //$gn = fopen("samaniego.txt","a+");
        //fputs($gn,$cSql);
        //fclose($gn);
        // '<button onclick=eliminar(', a.id, ') style=\'color:rgb(200,0,0)\' title=\'Eliminar\'><i class=\'glyphicon glyphicon-remove\'></i></button>',
        $result     = $this->db->query($cSql)->result_array();
        $ar_campos  = array('id', 'code', 'name', 'category_id', 'marca', 'alert_cantidad', 'price', 'precio_x_mayor','costo_con_igv','acciones');
        echo $this->fm->json_datatable($ar_campos, $result);
    }

    function mostrar(){
        $categoria = $_POST["categoria"];
        $cad_1 = $cad_2 = "";
        if($categoria != ''){
            $cad_2 = " and a.category_id = {$categoria}";
        }
        
        $cSql   = "select a.id, a.code, a.name, b.name category_id, a.unidad, a.alert_cantidad, a.price, a.imagen, a.marca".
                " from tec_products a".
                " inner join tec_categories b on a.category_id=b.id".
                $cad_1 . $cad_2 .
                " order by a.name";

        $result = $this->db->query($cSql)->result_array();

        echo json_encode($result);

    }

    function eliminar(){
        $id = $_POST["id"];

        // Verifico si hay movimiento con este producto
        $cSql = "select * from tec_sale_items where product_id = $id";

        $query = $this->db->query($cSql);

        if($query->num_rows()>0){
            $ar["rpta"]     = "warning";
            $ar["msg"]      = "Existe documentos que tienen este producto.";
        }else{
            
            $this->db->delete("tec_products",array('id' => $id));

            $ar["rpta"]     = "success";
            $ar["msg"]      = "Se elimina correctamente";
        }
        echo json_encode($ar);
    }

    function anular(){
        $id = $_POST["id"];

        $this->db->set(array('activo'=>''))->where(array('id' => $id))->update("tec_products");
        $ar["rpta"]     = "success";
        $ar["msg"]      = "Se anula correctamente";
        
        echo json_encode($ar);
    }

    function barcode($product_code = NULL) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        $data['product_details'] = $this->products_model->getProductByCode($product_code);
        $data['img'] = "<img src='" . base_url() . "index.php?products/gen_barcode&code={$product_code}' alt='{$product_code}' />";
        $this->load->view('barcode', $data);

    }

    function product_barcode($product_code = NULL, $bcs = 'code128', $height = 60) {
        
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        
        return $this->tec->barcode($product_code, $bcs, $height);
        
    }

    function gen_barcode($product_code = NULL, $bcs = 'code128', $height = 60, $text = 1) {
        return $this->tec->barcode($product_code, $bcs, $height, $text);
    }

    function print_inicial(){
        $this->data['page_title'] = "Impresion de Codigos de Barra";

        $this->data["query_codigos"] = $this->db->query("select id, code, name descrip from tec_products where category_id != 7 order by name");

        $this->template->load("production/index", 'products/print_inicial', $this->data);
    }


    function print_barcodes() {

        $eleccion   = $_POST["eleccion"];

        //$this->load->helper('pagination');

        if($eleccion == '2'){
            $nro_filas  = $_POST["cantidad"]*1;
            $nro_cols   = $_POST["cantidad_cols"]*1;
            $codigo     = $_POST["codigo"];
            $ancho      = $_POST["ancho"]*1; 
            $alto       = $_POST["alto"]*1;
            $margin_top = $_POST["margin_top"]*1; 

            //$products   = $this->db->query("select * from tec_products where id = ?",array($codigo))->result();
            $products   = $this->db->select('*')->get('tec_products')->result();

            $html       = "<div style=\"height:{$margin_top}px\"></div>";
            $html       .= '<table class="table table-bordered table-centered mb0">
            <tbody>';
            
            foreach ($products as $pr) {

                for($i=0; $i<$nro_filas; $i++){
                    $html .= "<tr>";
                    for($j=0; $j<$nro_cols; $j++){
                        
                        //$rutin = base_url("themes/default/assets/codigo_barras/barcode.php");
                        //die($rutin);
                        $direc = base_url("assets/codigo_barras/barcode.php?text=" . $pr->code . "&size=50&codetype=". $pr->barcode_symbology ."&print=true");

                        $celda = '<td style="width:'.$ancho.'px; height:'.$alto.'px; border-style:solid; border-width:1px; border-color:rgb(220,220,220); text-align:center; font-size:14px;"><strong>' . 
                            substr($pr->name,0,19) . '</strong><br>' . "<img src=" . $direc . "\">" . '</td>'; 
                        // ($pr->code, $pr->barcode_symbology, 30) 
                        
                        $html .= $celda;
                    }
                    $html .= "</tr>";
                }
            }

            $html .= '</tbody>
            </table>';

            //$this->data['html'] = $html;
            //$this->load->view('products/print_barcodes_a', $this->data);

            //$this->data['links'] = $pagination;
            $this->data['html'] = $html;
            $this->data['page_title'] = ""; //lang("print_barcodes");
            $this->load->view('products/print_barcodes', $this->data);

        }elseif($eleccion == '1'){ // Codigo individual
            $nro_filas  = $_POST["cantidad"]*1;
            $nro_cols   = $_POST["cantidad_cols"]*1;
            $codigo     = $_POST["codigo"];
            $ancho      = $_POST["ancho"]*1; 
            $alto       = $_POST["alto"]*1;
            $margin_top = $_POST["margin_top"]*1; 

            //$products   = $this->db->query("select * from tec_products where id = ?",array($codigo))->result();
            $products   = $this->db->select('*')->where('id',$codigo)->get('tec_products')->result();
            

            $html       = "<div style=\"height:{$margin_top}px\"></div>";
            $html       .= '<table class="table table-bordered table-centered mb0">
            <tbody>';
            
            foreach ($products as $pr) {

                for($i=0; $i<$nro_filas; $i++){
                    $html .= "<tr>";
                    for($j=0; $j<$nro_cols; $j++){
                        
                        //$rutin = base_url("themes/default/assets/codigo_barras/barcode.php");
                        //die($rutin);
                        $direc = base_url("assets/codigo_barras/barcode.php?text=" . $pr->code . "&size=50&codetype=". $pr->barcode_symbology ."&print=true");

                        $celda = '<td style="width:'.$ancho.'px; height:'.$alto.'px; border-style:solid; border-width:1px; border-color:rgb(220,220,220); text-align:center; font-size:14px;"><strong>' . 
                            substr($pr->name,0,19) . '</strong><br>' . "<img src=" . $direc . "\">" . '</td>'; 
                        // ($pr->code, $pr->barcode_symbology, 30) 
                        
                        $html .= $celda;
                    }
                    $html .= "</tr>";
                }
            }

            $html .= '</tbody>
            </table>';

            //$this->data['html'] = $html;
            //$this->load->view('products/print_barcodes_a', $this->data);

            //$this->data['links'] = $pagination;
            $this->data['html'] = $html;
            $this->data['page_title'] = ""; //lang("print_barcodes");
            $this->load->view('products/print_barcodes', $this->data);
        }       

    }

    function print_compra($viene_de_guardar=""){
        
        
        $this->data['page_title'] = "Impresion de Codigos de Barra x Compra";

        //$this->data["query_codigos"] = $this->db->query("select id, code, name descrip from tec_products where category_id != 7 order by name");

        if(isset($viene_de_guardar)){
            if($viene_de_guardar=='1'){
                $this->data["viene_de_guardar"] = $viene_de_guardar;
            }
        }
        $this->template->load("production/index", 'products/print_compra', $this->data);
    }
    
    function print_labels() {
        $limit = 10;
        $this->load->helper('pagination');
        $page = $this->input->get('page');
        $total = $this->products_model->products_count();
        $info = ['page' => $page, 'total' => ceil($total/$limit)];
        $pagination = pagination('products/print_labels', $total, $limit, true);
        $products = $this->products_model->fetch_products($limit, (!empty($page) ? (($page-1)*$limit) : 0));
        $html = "";
        foreach ($products as $pr) {
            $html .= '<div class="text-center labels break-after"><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 25) . '<br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($pr->price) . '</span></div>';
        }
        $this->data['links'] = $pagination;
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_labels");
        $this->load->view($this->theme.'products/print_labels', $this->data);
    }
    /*
        function single_barcode($product_id = NULL) {

            $product = $this->site->getProductByID($product_id);

            $html = "";
            $html .= '<table class="table table-bordered table-centered mb0">
            <tbody><tr>';
            if($product->quantity > 0) {
                for ($r = 1; $r <= $product->quantity; $r++) {
                    if ($r != 1) {
                        $rw = (bool)($r & 1);
                        $html .= $rw ? '</tr><tr>' : '';
                    }
                    $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></td>';
                }
            } else {
                for ($r = 1; $r <= 10; $r++) {
                if ($r != 1) {
                    $rw = (bool)($r & 1);
                    $html .= $rw ? '</tr><tr>' : '';
                }
                $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></td>';
            }
            }
            $html .= '</tr></tbody>
            </table>';

            $this->data['html'] = $html;
            $this->data['page_title'] = lang("print_barcodes").' ('.$product->name.')';
            $this->load->view($this->theme . 'products/single_barcode', $this->data);
        }

        function single_label($product_id = NULL, $warehouse_id = NULL) {

            $product = $this->site->getProductByID($product_id);
            $html = "";
            if($product->quantity > 0) {
                for ($r = 1; $r <= $product->quantity; $r++) {
                    $html .= '<div class="text-center labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></div>';
                }
            } else {
                for ($r = 1; $r <= 10; $r++) {
                    $html .= '<div class="text-center labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></div>';
                }
            }
            $this->data['html'] = $html;
            $this->data['page_title'] = lang("print_labels").' ('.$product->name.')';
            $this->load->view($this->theme . 'products/single_label', $this->data);

        }
    */

    function leer_csv(){
        $opciones_csv = $_POST["opciones_csv"];
        $file_tmp = $_FILES['fichero1']['tmp_name'];
        $file_name = $_FILES['fichero1']['name'];
        $cads = "";
        $errors = array();

        if(strlen($file_tmp) == 0){
            $data["page_title"] = "Importación de Productos";
            $data["respuesta"] = "<div class='alert alert-danger'>No se seleccionó ningún archivo.</div>";
            $this->template->load('production/index', 'products/importacion', $data);
            return;
        }

        if($_FILES['fichero1']['size'] > 2097152*2){
            $errors[] = 'El archivo excede el tamaño máximo de 4MB';
        }

        if(!empty($errors)){
            $data["page_title"] = "Importación de Productos";
            $data["respuesta"] = "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
            $this->template->load('production/index', 'products/importacion', $data);
            return;
        }

        move_uploaded_file($file_tmp, 'assets/uploads/' . $file_name);

        // Limpiar BOM y asegurar encoding UTF-8
        $contenido = file_get_contents('assets/uploads/' . $file_name);
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
        if(!mb_check_encoding($contenido, 'UTF-8')){
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }
        file_put_contents('assets/uploads/' . $file_name, $contenido);

        $file = fopen('assets/uploads/' . $file_name, "r");

        if($file == false){
            $data["page_title"] = "Importación de Productos";
            $data["respuesta"] = "<div class='alert alert-danger'>No se pudo abrir el archivo.</div>";
            $this->template->load('production/index', 'products/importacion', $data);
            return;
        }

        // Leer todas las filas
        $filas = array();
        while(!feof($file)){
            if($opciones_csv == "1"){
                $row = fgetcsv($file, null, ';', '"');
            } else {
                $linea = fgets($file);
                if($linea === false) break;
                $row = explode(";", rtrim($linea, "\r\n"));
            }
            if($row !== false && count($row) > 0 && strlen(trim(implode('', $row))) > 0){
                $filas[] = $row;
            }
        }
        fclose($file);

        if(count($filas) < 2){
            $data["page_title"] = "Importación de Productos";
            $data["respuesta"] = "<div class='alert alert-warning'>El archivo no tiene filas de datos.</div>";
            $this->template->load('production/index', 'products/importacion', $data);
            return;
        }

        // Detectar modo: nuevo (col 0 = "tipo") o legacy (col 0 = "codigo")
        $header = $filas[0];
        $col0 = strtolower(trim($header[0]));
        $modo_nuevo = ($col0 == 'tipo');

        if(!$modo_nuevo){
            // === MODO LEGACY (sin variantes) ===
            $this->_leer_csv_legacy($filas);
            return;
        }

        // === MODO NUEVO (con soporte de variantes) ===
        $this->load->model('atributos_model');
        $store_id = intval(isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1);

        // Fase 1: Parsear atributos del header (cols 9+)
        $attr_map = array(); // col_index => atributo_id
        $val_map = array();  // atributo_id => [valor_upper => valor_id]

        for($col = 9; $col < count($header); $col++){
            $attr_nombre = trim($header[$col]);
            if(strlen($attr_nombre) == 0) continue;
            $attr = $this->atributos_model->get_atributo_by_nombre($attr_nombre);
            if(!$attr){
                $data["page_title"] = "Importación de Productos";
                $data["respuesta"] = "<div class='alert alert-danger'>Error en cabecera: El atributo '<b>" . htmlspecialchars($attr_nombre) . "</b>' (columna " . ($col+1) . ") no existe en el sistema.</div>";
                $this->template->load('production/index', 'products/importacion', $data);
                return;
            }
            $attr_map[$col] = $attr->id;
            // Pre-cargar valores de este atributo
            if(!isset($val_map[$attr->id])){
                $val_map[$attr->id] = array();
                $valores = $this->atributos_model->get_valores($attr->id);
                foreach($valores as $v){
                    $val_map[$attr->id][mb_strtoupper($v->valor)] = $v->id;
                }
            }
        }

        // Fase 2: Parsear y agrupar filas
        $entradas = array(); // lista de {tipo, data, variantes[]}
        $nr = 0; $n_errores = 0;
        $current_pv = null;

        for($i = 1; $i < count($filas); $i++){
            $row = $filas[$i];
            $nfila = $i + 1;
            $tipo = strtoupper(trim($row[0]));

            if($tipo == 'P'){
                // Producto simple
                $entradas[] = array(
                    'tipo' => 'P',
                    'fila' => $nfila,
                    'code' => trim(isset($row[1]) ? $row[1] : ''),
                    'name' => trim(isset($row[2]) ? $row[2] : ''),
                    'marca' => trim(isset($row[3]) ? $row[3] : ''),
                    'categoria' => trim(isset($row[4]) ? $row[4] : ''),
                    'unidad' => trim(isset($row[5]) ? $row[5] : ''),
                    'precio_x_menor' => trim(isset($row[6]) ? $row[6] : '0'),
                    'precio_x_mayor' => trim(isset($row[7]) ? $row[7] : '0'),
                    'alerta_cantidad' => trim(isset($row[8]) ? $row[8] : '0'),
                    'variantes' => array()
                );
                $current_pv = null;

            } elseif($tipo == 'PV'){
                // Producto padre con variantes
                $entry = array(
                    'tipo' => 'PV',
                    'fila' => $nfila,
                    'code' => '',
                    'name' => trim(isset($row[2]) ? $row[2] : ''),
                    'marca' => trim(isset($row[3]) ? $row[3] : ''),
                    'categoria' => trim(isset($row[4]) ? $row[4] : ''),
                    'unidad' => trim(isset($row[5]) ? $row[5] : ''),
                    'precio_x_menor' => trim(isset($row[6]) ? $row[6] : '0'),
                    'precio_x_mayor' => trim(isset($row[7]) ? $row[7] : '0'),
                    'alerta_cantidad' => trim(isset($row[8]) ? $row[8] : '0'),
                    'variantes' => array()
                );
                $entradas[] = $entry;
                $current_pv = count($entradas) - 1;

            } elseif($tipo == 'V'){
                if($current_pv === null){
                    $cads .= "Fila {$nfila}: <span style='color:red;'>Variante (V) sin producto padre (PV) previo. Se omite.</span><br>";
                    $n_errores++;
                    continue;
                }
                // Parsear atributos de la variante
                $attrs_var = array();
                foreach($attr_map as $col_idx => $atributo_id){
                    $valor_text = isset($row[$col_idx]) ? trim($row[$col_idx]) : '';
                    if(strlen($valor_text) == 0) continue;
                    $valor_upper = mb_strtoupper($valor_text);
                    if(!isset($val_map[$atributo_id][$valor_upper])){
                        $cads .= "Fila {$nfila}: <span style='color:red;'>Valor '" . htmlspecialchars($valor_text) . "' no existe para el atributo. Se omite variante.</span><br>";
                        $n_errores++;
                        continue 2;
                    }
                    $attrs_var[] = array(
                        'atributo_id' => $atributo_id,
                        'valor_id' => $val_map[$atributo_id][$valor_upper]
                    );
                }
                if(empty($attrs_var)){
                    $cads .= "Fila {$nfila}: <span style='color:red;'>Variante sin ningún atributo definido. Se omite.</span><br>";
                    $n_errores++;
                    continue;
                }

                $precio_var = isset($row[6]) ? trim($row[6]) : '';
                $pmayor_var = isset($row[7]) ? trim($row[7]) : '';

                $entradas[$current_pv]['variantes'][] = array(
                    'fila' => $nfila,
                    'precio' => strlen($precio_var) > 0 ? floatval($precio_var) : null,
                    'precio_x_mayor' => strlen($pmayor_var) > 0 ? floatval($pmayor_var) : null,
                    'attrs' => $attrs_var
                );
            } else {
                $cads .= "Fila {$nfila}: <span style='color:orange;'>Tipo '" . htmlspecialchars($tipo) . "' no reconocido. Se omite.</span><br>";
                $n_errores++;
            }
        }

        // Fase 3 y 4: Validar e insertar
        $this->db->trans_start();

        foreach($entradas as $entry){
            $nfila = $entry['fila'];

            // Validar categoría
            $cat_row = $this->db->where("id", $entry['categoria'])->get("tec_categories")->row();
            if(!$cat_row){
                $cads .= "Fila {$nfila}: <span style='color:red;'>Categoría '" . htmlspecialchars($entry['categoria']) . "' no existe.</span><br>";
                $n_errores++;
                continue;
            }

            if($entry['tipo'] == 'P'){
                // Validar código único
                $existe = $this->db->select("id")->where("code", $entry['code'])->get("tec_products")->row();
                if($existe){
                    $cads .= "Fila {$nfila}: <span style='color:red;'>Código '" . htmlspecialchars($entry['code']) . "' ya existe.</span><br>";
                    $n_errores++;
                    continue;
                }
                // Insertar producto simple
                $this->db->insert('tec_products', array(
                    'code' => $entry['code'],
                    'name' => $entry['name'],
                    'marca' => $entry['marca'],
                    'category_id' => $entry['categoria'],
                    'unidad' => $entry['unidad'],
                    'price' => floatval($entry['precio_x_menor']),
                    'precio_x_mayor' => floatval($entry['precio_x_mayor']),
                    'alert_cantidad' => intval($entry['alerta_cantidad'])
                ));
                $nr++;
                $cads .= "Fila {$nfila}: <span style='color:green;'>Producto '" . htmlspecialchars($entry['name']) . "' importado.</span><br>";

            } elseif($entry['tipo'] == 'PV'){
                if(empty($entry['variantes'])){
                    $cads .= "Fila {$nfila}: <span style='color:orange;'>Producto PV sin variantes (V). Se omite.</span><br>";
                    $n_errores++;
                    continue;
                }
                // Autogenerar código para padre
                $code_padre = $this->generar_code_producto_unico();

                $this->db->insert('tec_products', array(
                    'code' => $code_padre,
                    'name' => $entry['name'],
                    'marca' => $entry['marca'],
                    'category_id' => $entry['categoria'],
                    'unidad' => $entry['unidad'],
                    'price' => floatval($entry['precio_x_menor']),
                    'precio_x_mayor' => floatval($entry['precio_x_mayor']),
                    'alert_cantidad' => intval($entry['alerta_cantidad'])
                ));
                $product_id = $this->db->insert_id();
                $nr++;

                // Insertar variantes
                $nv = 0;
                foreach($entry['variantes'] as $var){
                    $barcode = $this->generar_barcode_unico();
                    $sku = $this->generar_sku_unico();

                    $price_var = $var['precio'] !== null ? $var['precio'] : floatval($entry['precio_x_menor']);
                    $pmayor_var = $var['precio_x_mayor'] !== null ? $var['precio_x_mayor'] : floatval($entry['precio_x_mayor']);

                    $var_id = $this->atributos_model->insertar_variante(array(
                        'product_id' => $product_id,
                        'sku' => $sku,
                        'barcode' => $barcode,
                        'price' => $price_var,
                        'precio_x_mayor' => $pmayor_var,
                        'activo' => '1'
                    ));

                    // Atributos de la variante
                    foreach($var['attrs'] as $a){
                        $this->atributos_model->insertar_variante_atributo(array(
                            'variante_id' => $var_id,
                            'atributo_id' => $a['atributo_id'],
                            'valor_id' => $a['valor_id']
                        ));
                    }

                    // Stock inicial
                    $this->db->insert('tec_prod_store', array(
                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'stock' => 0,
                        'variant_id' => $var_id
                    ));
                    $nv++;
                }
                $cads .= "Fila {$nfila}: <span style='color:green;'>Producto '" . htmlspecialchars($entry['name']) . "' con {$nv} variante(s) importado.</span><br>";
            }
        }

        $this->db->trans_complete();

        $total_filas = count($filas) - 1;
        $cads .= "<br><p style='font-weight:bold;'>Productos importados: {$nr} de {$total_filas} filas procesadas";
        if($n_errores > 0) $cads .= " ({$n_errores} error(es))";
        $cads .= "</p>";

        $data["page_title"] = "Importación de Productos";
        $data["respuesta"] = $cads;
        $this->template->load('production/index', 'products/importacion', $data);
    }

    // Modo legacy: CSV sin columna "tipo" (formato anterior)
    private function _leer_csv_legacy($filas){
        $cads = "";
        $nr = 0;
        for($i = 1; $i < count($filas); $i++){
            $ar = $filas[$i];
            $nfila = $i + 1;
            $code = trim(isset($ar[0]) ? $ar[0] : '');
            $name = trim(isset($ar[1]) ? $ar[1] : '');
            $marca = trim(isset($ar[2]) ? $ar[2] : '');
            $modelo = trim(isset($ar[3]) ? $ar[3] : '');
            $categoria = trim(isset($ar[4]) ? $ar[4] : '');
            $unidad = trim(isset($ar[5]) ? $ar[5] : '');
            $precio_x_menor = trim(isset($ar[6]) ? $ar[6] : '0');
            $precio_x_mayor = trim(isset($ar[7]) ? $ar[7] : '0');
            $alerta_cantidad = trim(isset($ar[8]) ? $ar[8] : '0');

            $existe = $this->db->select("id")->where("code", $code)->get("tec_products")->row();
            $cat_row = $this->db->where("id", $categoria)->get("tec_categories")->row();

            if(!$existe && $cat_row){
                $this->db->insert('tec_products', array(
                    'code' => $code, 'name' => $name, 'marca' => $marca,
                    'category_id' => $categoria, 'unidad' => $unidad,
                    'price' => floatval($precio_x_menor), 'precio_x_mayor' => floatval($precio_x_mayor),
                    'alert_cantidad' => intval($alerta_cantidad)
                ));
                $nr++;
            } else {
                $cads .= $nfila . ") [YA EXISTE O CATEGORÍA INVÁLIDA] " . $code . ' ' . $name . "<br>";
            }
        }
        $cads .= "<p style='font-weight:bold;'>Ingresadas: {$nr} de " . (count($filas)-1) . " filas</p>";

        $data["page_title"] = "Importación de Productos";
        $data["respuesta"] = $cads;
        $this->template->load('production/index', 'products/importacion', $data);
    }

    public function importacion(){
        //if ($this->fm->verificar_permisos($this->conexion1, $_SESSION["usuario"], 'CONCEPTOS', 'listar')){
        $data["page_title"] = "Agregar Productos en CSV";
            
        $this->template->load('production/index', 'products/importacion', $data);
    }

    public function traer_impresionx(){
        $cSql = "select a.id, a.product_id, a.compra_id, a.code, a.nombre_producto, a.cantidad
            from impresionx a";

        $query = $this->db->query($cSql);
        $result = $query->result_array();
        return json_encode($result);
    }

    public function traer_impresionx_api(){
        $cSql = "select a.id, a.product_id, a.compra_id, a.code, a.nombre_producto, a.cantidad
            from impresionx a";

        $query = $this->db->query($cSql);
        $result = $query->result_array();
        echo json_encode($result);
    }
    
    public function incluir_nro_compra(){
        // Vaceando primero impresionx
        //$this->db->query("delete from impresionx");

        // Inserta los productos de una compra a la tabla impresionx luego utiliza traer_impresionx 
        $nro_compra = $this->input->post("nro_compra");

        $cSql = "select b.product_id, a.id compra_id, c.code, c.name producto, round(b.cantidad,0) cantidad
            from tec_compras a
            inner join tec_compra_items b on a.id = b.compra_id
            inner join tec_products c on b.product_id = c.id
            where a.id in (" . $nro_compra . ")";
        
        $query = $this->db->query($cSql);
        
        // llenando tabla impresionx con los items de la compra
        $i=0;
        foreach($query->result() as $r){
            $i++;
            $ari = array(
                "product_id"    =>$r->product_id,
                "cantidad"      =>$r->cantidad,
                "compra_id"     =>$r->compra_id,
                "code"          =>$r->code,
                "nombre_producto" =>$r->producto
            );

            //echo $this->db->set($ari)->get_compiled_insert("impresionx");
            $this->db->set($ari)->insert("impresionx");
        }
        echo $this->traer_impresionx();
        //echo "Se logra incluir los productos, total: {$i}<br>";
        //    <br><button onclick="location.href='http://localhost/procesos-surco/traer_info_remota.php?modo=2';">Continuar</button>
    }
    

    /*
        public function incluir_nro_compra_dt($nro_compra=null){
            // Inserta los productos de una compra a la tabla impresionx
            //$nro_compra = $_REQUEST["nro_compra"];

            $cSql = "select b.product_id, a.id compra_id, c.code, c.name producto, round(b.cantidad,0) cantidad
                from tec_compras a
                inner join tec_compra_items b on a.id = b.compra_id
                inner join tec_products c on b.product_id = c.id
                where a.id in (" . $nro_compra . ")";
            
            $query = $this->db->query($cSql);
            
            // llenando tabla impresionx con los items de la compra
            foreach($query->result() as $r){
                $ari = array(
                    "product_id"    =>$r->product_id,
                    "cantidad"      =>$r->cantidad,
                    "compra_id"     =>$r->compra_id,
                    "code"          =>$r->code,
                    "nombre_producto" =>$r->producto
                );

                //echo $this->db->set($ari)->get_compiled_insert("impresionx");
                $this->db->set($ari)->insert("impresionx");
            }

            $cSql = "select a.id, a.product_id, a.compra_id, a.code, a.nombre_producto, a.cantidad
                from impresionx a";

            $query = $this->db->query($cSql);
            $result = $query->result_array();

            $ar_campos  = array("id", "product_id", "compra_id", "code", "nombre_producto", "cantidad");
            echo $this->fm->json_datatable($ar_campos,$result);
        }
    */

    public function save_impresionx(){
        $ar_producto          = $this->input->post("product_id");
        $ar_cantidad          = $_POST["cantidad"];
        $ar_compra_id       = $this->input->post("compra_id[]");

        $nLim = count($ar_producto);
        for($i=0; $i<$nLim;$i++){

            $ari = array();
            $ari = array(
                "cantidad"      =>$ar_cantidad[$i]
            );
            $this->db->set($ari)->where("product_id",$ar_producto[$i])->where("compra_id",$ar_compra_id[$i])->update("impresionx");
        }
        //$data["viene_de_guardar"] = 1;
        redirect("products/print_compra");
    }

    public function reset_impresionx(){
        $this->db->query("delete from impresionx");
        echo "Se resetea la tabla de impresion";
    }

    public function elimina_item_impresionx(){
        $id = $this->input->post("id");
        $this->db->where("id",$id)->delete("impresionx");
        echo "OK";
    }

    function rulo($url, $campos){  // Envia y retorna la respuesta
        
        $cToken = "nabucodonosor"; //$this->token;
        
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $campos);

        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "content-type: application/json",
                "Authorization: {$cToken}"
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    function get_compra_items($nro_compra){
        $cSql = "select b.product_id, a.id compra_id, c.code, c.name producto, round(b.cantidad,0) cantidad
            from tec_compras a
            inner join tec_compra_items b on a.id = b.compra_id
            inner join tec_products c on b.product_id = c.id
            where a.id in (" . $nro_compra . ")";
        
        $result = $this->db->query($cSql)->result_array();

        echo json_encode($result);
    }
    

    public function exportar_csv(){
        $cSql = "select * from impresionx order by id";
        $result = $this->db->query($cSql)->result_array();
        $this->exportarCSV("Codigos_de_impresion", $result);
        //echo "Se migra a formato CSV en carpeta Descargas";
    }

    function exportarCSV($nombreArchivo, $datos) {
        // Establecer encabezados para indicar que es un archivo CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '.csv"');

        // Abrir el archivo en modo de escritura
        $archivo = fopen('php://output', 'w');

        // Escribir encabezados
        fputcsv($archivo, array_keys($datos[0]));

        // Escribir datos
        foreach ($datos as $fila) {
            fputcsv($archivo, $fila);
        }

        // Cerrar el archivo
        fclose($archivo);
    }
}
