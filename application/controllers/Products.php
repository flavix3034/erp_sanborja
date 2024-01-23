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
        }else{
            $this->data["modo"] = 'insert'; 
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
        $code           = $_POST["code"];
        $name           = $_POST["name"]; 
        $category_id    = $_POST["category_id"];
        $unidad         = $_POST["unidad"]; 
        $alert_cantidad = $_POST["alert_cantidad"]; 
        $price          = $_POST["price"];
        $imagen         = $_POST["imagen"];
        $marca          = $_POST["marca"];
        $modelo         = $_POST["modelo"];
        $color          = $_POST["color"];
        $precio_x_mayor = $_POST["precio_x_mayor"];

        $validacion = true;
        $nu_codigo = 10000001;

        $modo = strtolower($_POST["modo"]);

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
        $ar["modelo"]       = $modelo;
        $ar["color"]        = $color;
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

                $cSql = "select max(id)+1 nuevo from tec_products where id < 99999";
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
        $ar["modelo"]       = $modelo;
        $ar["color"]        = $color;
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
                $cSql = "select max(id)+1 nuevo from tec_products where id < 99999";
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
        $tipo_precio    = $_REQUEST["tipo_precio"];

        $this->db->select("price, precio_x_mayor");
        $this->db->where("id",$dato1);
        
        $result = $this->db->get("tec_products")->result();

        $respuesta = "";
        foreach($result as $r){
            if($tipo_precio == 'por_menor'){
                $respuesta = $r->price;
            }else{
                $respuesta = $r->precio_x_mayor;
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

        $cSql       = "select a.id, a.code, a.name, b.name category_id, a.marca, a.modelo, a.color, a.alert_cantidad, a.price, a.precio_x_mayor,
            concat('<button onclick=editar(', a.id, ')><i class=\'glyphicon glyphicon-edit\'></i></button>',
            '<button onclick=anular(', a.id, ') style=\'color:rgb(255,100,100)\' title=\'Anular\'><i class=\'glyphicon glyphicon-remove\'></i></button>')".
            " as acciones, z.costo_con_igv".
            " from tec_products a".
            " left join (
                select b.product_id, round(max(b.precio_con_igv),2) costo_con_igv from tec_compra_items b
                inner join tec_compras c on b.compra_id=c.id
                group by b.product_id
            ) z on a.id=z.product_id".
            " inner join tec_categories b on a.category_id=b.id".
            $cad_1 . $cad_2 . $cad_3 .
            " where a.activo='1'".
            " order by a.name";

        //echo($cSql);

        //$gn = fopen("samaniego.txt","a+");
        //fputs($gn,$cSql);
        //fclose($gn);
        // '<button onclick=eliminar(', a.id, ') style=\'color:rgb(200,0,0)\' title=\'Eliminar\'><i class=\'glyphicon glyphicon-remove\'></i></button>',
        $result     = $this->db->query($cSql)->result_array();
        $ar_campos  = array('id', 'code', 'name', 'category_id', 'marca', 'modelo', 'color', 'alert_cantidad', 'price', 'precio_x_mayor','costo_con_igv','acciones');
        echo $this->fm->json_datatable($ar_campos, $result);
    }

    function mostrar(){
        $categoria = $_POST["categoria"];
        $cad_1 = $cad_2 = "";
        if($categoria != ''){
            $cad_2 = " and a.category_id = {$categoria}";
        }
        
        $cSql   = "select a.id, a.code, a.name, b.name category_id, a.unidad, a.alert_cantidad, a.price, a.imagen, a.marca, a.modelo, a.color".
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

        // ---- Recibiendo el archivo -----
        $file_tmp   = $_FILES['fichero1']['tmp_name'];
        
        
        if(strlen($file_tmp)>0){
            $file_name  = $_FILES['fichero1']['name'];
            $file_size  = $_FILES['fichero1']['size'];
            $file_type  = $_FILES['fichero1']['type'];
            
            $ar_f = explode('.',$file_name);
            $miE  = end($ar_f);

            $file_ext  = strtolower($miE);

            //$expensions = array("jpeg","jpg","png");
            //if(in_array($file_ext, $expensions) === false){
            //    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            //}
         
            if($file_size > 2097152*2) {
                $errors[]='File size must be exactely 4 MB';
            }
         
            if(empty($errors) == true) {
                $ar["imagen"] = $file_name;
                
                move_uploaded_file($file_tmp, 'assets/uploads/'.$file_name);
                
                // GRABANDO EL NOMBRE DEL ARCHIVO EN LA TABLA
                //$arx = array("archivo1"=>$file_name);
                //$this->db->set($arx)->where("suscrip_id",$suscrip_id)->where("examen_id",$examen_id)->update("suscrip_detalle");

                $data["msg"]            = "Se sube correctamente el archivo";
                $data["rpta_msg"]       = "success";

            }else{
                
                $data["msg"]            = print_r($errors,true);
                $data["rpta_msg"]       = "warning";
            }
        }
        

        //**** LEER UN FICHERO CSV DESDE PHP ****
        $cads = "";
        $file = fopen(base_url('/assets/uploads/'.$file_name), "r");

        if($file != false){
            $data = array();
            $n=0; $nr=0; 
            while (!feof($file) && $n < 10000) {
                $n++;
                $validar_fila = true;
                
                if($opciones_csv == "2"){ // un simple texto delimitado por ;
                    $cLinea = fgets($file);
                    $ar = explode(";",$cLinea);
                }

                if($opciones_csv == "1"){ // delimitado ; e entrcomillado
                    $ar = array();
                    $ar[] = fgetcsv($file,null,';','"');
                    //print_r($ar);
                    //echo "<br><br>";
                }

                if($n>1){
                    
                    // codigo   nombre  marca   modelo  categoria   unidad  precio_x_menor  precio_x_mayor  alerta_cantidad

                    if($opciones_csv == "2"){
                        $code       = $ar[0];
                        $name       = $ar[1];
                        $marca      = $ar[2];
                        $modelo     = $ar[3];
                        $categoria  = $ar[4];
                        $unidad     = $ar[5];
                        $precio_x_menor = $ar[6];
                        $precio_x_mayor = $ar[7];
                        $alerta_cantidad = $ar[8];
                    }

                    if($opciones_csv == "1"){
                        $code       = $ar[0][0];
                        $name       = $ar[0][1];
                        $marca      = $ar[0][2];
                        $modelo     = $ar[0][3];
                        $categoria  = $ar[0][4];
                        $unidad     = $ar[0][5];
                        $precio_x_menor = $ar[0][6];
                        $precio_x_mayor = $ar[0][7];
                        $alerta_cantidad = $ar[0][8];
                    }
                    
                    $fec_act    = date("Y-m-d");
                    
                    // Verificando su Unicidad
                    $cSql = "select id from tec_products where code='".$code."'"; 
                    if($n==2){
                        //$cads .= $cSql . "<br><br>";
                    }
                    $query = $this->db->query($cSql); // ,array($cod_afi, $ccodigo, $anno, $mes)
                    $existe = false;
                    foreach($query->result() as $r){
                        $existe = true;
                    }

                    // Validando la categoria
                    $que = $this->db->where("id",$categoria)->get("tec_categories");
                    $n_que = 0;
                    foreach($que->result() as $r){
                        $n_que++;
                    }
                    if($n_que == 0){ $validar_fila = false; }

                    if(!$existe && $validar_fila==true){
                        $cSql = "insert into tec_products(code, name, marca, modelo, category_id, unidad, price, precio_x_mayor, alert_cantidad) values".
                            "('".$code."','".$name."','".$marca."','".$modelo."','".$categoria."','".$unidad."','".$precio_x_menor."','".$precio_x_mayor."','".$alerta_cantidad."')";
                        $this->db->query($cSql);
                        $nr++;
                    }else{
                        $cads .= $n.") [YA EXISTE O NO ES VALIDO LA CATEGORIA] ". $code . ' ' . $name . ' ' . $marca . ' ' . $modelo . "<br>";
                    }
                }
            }
            fclose($file);
            $cads .= "<p style=\"font-weight:bold;\">Ingresadas:".$nr." de ".($n-1)." filas</p>";
        }

        $data["page_title"] = "Agregar Productos en CSV";
        $data["respuesta"]  = $cads;
            
        $this->template->load('production/index', 'products/importacion', $data);

    }

    public function importacion(){
        //if ($this->fm->verificar_permisos($this->conexion1, $_SESSION["usuario"], 'CONCEPTOS', 'listar')){
        $data["page_title"] = "Agregar Productos en CSV";
            
        $this->template->load('production/index', 'products/importacion', $data);
    }

}
