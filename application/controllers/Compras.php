<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Compras extends CI_Controller {

    function __construct() {
        parent::__construct();

        session_start();
        $this->load->model('compras_model');
		$this->Igv = 18;
        $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';
        if(!isset($_SESSION["user_id"])){ 
            die("No tiene sesión disponible. <a href=\"" . base_url("welcome/index") . "\">Login</a>"); 
        }
    }

    function index($store_id='', $cDesde='null', $cHasta='null') {
        if ($store_id == ''){ $store_id = $_SESSION["store_id"]; }
        $this->data['page_title'] = "Compras";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $store_id;
        //$this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'compras/index', $this->data);
    }

    function add($id=null){
        if(!is_null($id)){
            $this->data["id"]           = $id;
            $this->data['page_title']   = "Editar Compras";
            $this->data['modo']         = "U";
        }else{
            $this->data['page_title'] = "A&ntilde;adir Compras";
        }
        $this->template->load('production/index', 'compras/add', $this->data);
    }

    function get_compras($store_id='', $desde='null', $hasta='null'){
        //$store_id = $_SESSION["store_id"];
        $query = $this->compras_model->get_compras($store_id, $desde, $hasta);
        $result = $query->result_array();
        $ar_campos = array("id", "tienda", "fecha", "fecha_ingreso", "tipoDoc", "nroDoc", "proveedor", "username", "total", "actions");
        echo $this->fm->json_datatable($ar_campos, $result);
    }

	function save(){

		$store_id           = $_SESSION["store_id"];
        $fecha 			    = $_POST["date"]; // . " " . date("H:i:s");
		$fecha_ingreso      = $_POST["date_ingreso"];
        $tipoDoc 		    = $_POST["tipoDoc"];
		$nroDoc             = $_POST["nroDoc"];
        $redondeo           = isset($_POST["redondeo"]) ? $_POST["redondeo"] : "";
        
        $proveedor_id       = $_POST["proveedor_id"];
        
        $subtotal           = $_POST["txt_gSubtotal"];  
        $igv                = $_POST["txt_gIgv"];
        $total              = $_POST["txt_gTotal"];
        $por_igv            = 0.18;

        $modo_edicion       = $_POST["modo_edicion"];
        $tipo_pago          = $_POST["tipo_pago"];

        /*echo "subtotal:"    . $subtotal .   "<br>";
        echo "igv:"         . $igv .        "<br>";
        echo "total:"       . $total .      "<br>";
        die();*/

        // Validaciones -------------------------------------------
        $validacion = true;
        //$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[12]');

        // -------VERIFICO SI HAY UN ARCHIVO ADJUNTO --------------
        //if($_FILES['fichero1']){
        $file_tmp   = $_FILES['fichero1']['tmp_name'];
            
        if(strlen($file_tmp)>0){
            $file_name  = $_FILES['fichero1']['name'];
            $file_size  = $_FILES['fichero1']['size'];
            $file_type  = $_FILES['fichero1']['type'];
            $ar_f = explode('.',$file_name);
            $miE  = end($ar_f);
            $file_ext  = strtolower($miE);
            $expensions = array("csv");
            if(in_array($file_ext, $expensions) === false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
            if($file_size > 2097152) {
                $errors[]='File size must be excately 2 MB';
            }
            if(empty($errors) == true) {
                $ar["imagen"] = $file_name;
                move_uploaded_file($file_tmp, "uploads/".$file_name);
            }else{
                print_r($errors);
                die();
            }
        
            // Guardando variables para el retorno
            $this->data["date"]         = $_POST["date"]; 
            $this->data["date_ingreso"] = $_POST["date_ingreso"];  
            $this->data["nroDoc"]       = $_POST["nroDoc"];  
            $this->data["tipoDoc"]      = $_POST["tipoDoc"]; 
            $this->data["proveedor_id"] = $_POST["proveedor_id"];
            $this->data["redondeo"]     = $_POST["redondeo"];
            $this->data["modo"]         = $_POST["modo"];

            // --- RECIEN AQUI SE ABRE EL FICHERO PARA GUARDARLO EN UNA TABLA PROVISIONAL ----
            $file = fopen( "uploads/".$file_name, "r");
            $dati = array();
            while (!feof($file)) {
                $dati[] = fgetcsv($file,null,';');
            }
            fclose($file);

            $this->almaceno_en_temporal($_SESSION["usuario"], $dati);

        }else{

            if(isset($_REQUEST['product_id'])){
                $product_id = $_REQUEST['product_id'];
                if (gettype($product_id) == 'string'){
                    $this->data["msg"] = "No ha ingresado productos";
                    $this->data["rpta_msg"] = "danger";
                    $validacion = false; 
                }
            }

            if($validacion){
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
                $this->db->set("tipo_pago", $tipo_pago);

                $bandera_grab = true;

                if($modo_edicion == "1"){
                    $id = $_POST["id_compras"];
                    //$this->db->set("id",$id);
                    //$this->db->replace("tec_compras");    
                    $this->db->where("id",$id)->update("tec_compras");
                }else{
                    $this->db->insert("tec_compras");
                    $id = $this->db->insert_id();
                }

                if($bandera_grab){
        			
        			// Lo primero que debe hacerse es borrar los items anteriores
                    if($modo_edicion == '1'){
                        // Se elimina los item para luego volverlo a ingresar como nuevo
                        $cSql = "select * from tec_compra_items where compra_id = ?";
                        $query = $this->db->query($cSql,$id);
                        foreach($query->result() as $r){
                            $item_id    = $r->id;
                            $cantidad   = $r->cantidad;
                            $product_id = $r->product_id;
                            $this->db->where("id",$item_id)->delete("tec_compra_items");

                            // En el stock simple
                            $this->compras_model->disminuir_al_stock($product_id, $store_id, $cantidad);
                        }
                    }

                    $Lim = count($_REQUEST['product_id']);

        			// Averiguando tipoDoc
                    $tipoDoc = $this->db->select("tipoDoc")->where('id',$id)->get("tec_compras")->row()->tipoDoc;

                    for ($i = 0; $i < $Lim; $i++){

        				$product_id     = $_REQUEST['product_id'][$i];
                        $cantidad       = $_REQUEST['quantity'][$i];

                        $ar["compra_id"]    = $id;
        				$ar["product_id"]   = $product_id;
        				$ar["cantidad"]     = $cantidad;
        				
        				$precio_unitario = $_REQUEST['precio'][$i] * 1; // El precio se trata del valor unitario con Igv, tal cual se pone
        				$subtotal       = $precio_unitario * ($_REQUEST['quantity'][$i] * 1);
        				
        				$ar["precio_con_igv"]   = $precio_unitario;			// con igv

                        if($tipoDoc == '5'){ // ticket
                            $ar["precio_sin_igv"]   = $precio_unitario;
                        }else{
                            $ar["precio_sin_igv"]   = $_REQUEST['cost'][$i] * 1;
                        }
        				
                        $ar["subtotal"]         = $subtotal;
        				$ar["igv"]              = $ar["precio_sin_igv"] * $por_igv;
        				$ar["product_name"]     = $_REQUEST['descripo'][$i];
        				
                        $this->db->set($ar)->insert("tec_compra_items");
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

        		}else{
                    $this->db->trans_rollback();
                }
            } // validacion
        }
        $this->data["page_title"] = "Agregar Compras";
        $this->template->load('production/index', 'compras/add', $this->data);
	}	
	
    function almaceno_en_temporal($usuario, $data){
        // Limpio primero la tabla
        $this->db->where("usuario",$usuario)->delete("tempo_compras");
        $nLim = count($data) - 1;

        //echo "nLim:" . $nLim . "<br>";
        for($i=1; $i<$nLim; $i++){
            //usuario, product_id, code, nombre, precio, cantidad        
            //echo $i . "<br>";
            //echo $data[$i][0] . "<br>"; 
            if(gettype($data[$i][0])=='string' || gettype($data[$i][0])=='integer'){
                //if(!is_null($data[$i][0])){
                $ar["usuario"]  = $usuario;
                $ar["code"]     = $data[$i][0];
                
                //echo $this->db->select("id")->where("code",$data[$i][0])->get_compiled_select("tec_products");
                //die();
                $query = $this->db->select("id")->where("code",$data[$i][0])->get("tec_products");
                $ar["product_id"] = 888888;
                foreach($query->result() as $r){
                    $ar["product_id"] = $r->id;
                }
                $ar["nombre"]   = $data[$i][1];
                $ar["precio"]   = $data[$i][2];
                $ar["cantidad"] = $data[$i][3];
            
                $this->db->set($ar)->insert("tempo_compras");
            }
        }
        $this->data["msg"] = "Se encuentra almacenado los items";
        $this->data["rpta_msg"] = "success";
    }

    function eliminar(){  // Disminuye la cantidad de producto (tabla tec_prod_store) luego elimina la compra.
        
        if(isset($_GET["id"])){
            $id = $_GET["id"];
        
            // Antes de eliminar la compra descuento la tabla stock
            $query_i = $this->db->query("select a.store_id, b.product_id, b.cantidad from tec_compras a inner join tec_compra_items b on a.id=b.compra_id where a.id = {$id}");
            foreach($query_i->result() as $r){
                $this->compras_model->disminuir_al_stock($r->product_id, $r->store_id, $r->cantidad);
            }

           // tec_sale_items:
           $this->db->where("compra_id",$id);
           $this->db->delete("tec_compra_items");

           // tec_payments:
           $this->db->where("sale_id",$id);
           $this->db->delete("tec_payments");

           // Tec_sales
           $this->db->where("id",$id);
           $this->db->delete("tec_compras");

           $ar["rpta_msg"] = "success";
           $ar["message"] = "Se eliminó correctamente el Documento {$id}";
        }else{
            $ar["rpta_msg"] = "danger";
            $ar["message"] = "No se pudo eliminar";
        }
        echo json_encode($ar);
        
    }

    public function envio_individual(){
        $this->load->model('pos_model');
        $sale_id = $_REQUEST["sale_id"];
        
        $this->pos_model->enviar_doc_sunat_nubefact_individual($sale_id);

        $query = $this->db->select("envio_electronico")->where("id",$sale_id)->get("sales");

        foreach($query->result() as $r){
            $rpta = $r->envio_electronico;
        }
        if ($rpta == '1'){
            echo "OK";
        }else{
            echo "No se pudo";
        }
    }

    public function ver(){
        $id = $_REQUEST["id"];
        $cSql = "select a.fecha, fecha_ingreso, a.monto_base, a.igv, a.total, a.tipoDoc, a.nroDoc, a.proveedor_id, c.nombre nombre_proveedor, c.ruc,
            b.product_id, d.code, b.product_name, b.cantidad, b.precio_sin_igv, b.precio_con_igv, b.descuento, b.igv igv2, b.subtotal, e.descrip tdocumento
            from tec_compras a
            left join tec_compra_items b on a.id=b.compra_id
            left join tec_proveedores c on a.proveedor_id=c.id
            left join tec_products d on b.product_id=d.id
            left join tec_tipos_doc e on a.tipoDoc=e.id
            where a.id=?";
        $query = $this->db->query($cSql,array($id));
        $i=0;
        foreach($query->result() as $r){
            $i++;
            if($i==1){
        ?>

        <style type="text/css">
            .lbl_a{ font-weight:bold; }
            .fila_a{ margin:10px; }
            .celda_a{ padding:8px!important; }
            .celda_a_footer{ padding:4px!important; background-color: rgb(180,200,220);}
        </style>
        <div class="row fila_a">
            <div class="col-sm-3">
                <label class="lbl_a">Fecha de pago</label><br>
                <?= $r->fecha ?>
            </div>
            <div class="col-sm-3">
                <label class="lbl_a">fecha de Ingreso Alm</label><br>
                <?= $r->fecha_ingreso ?>
            </div>
        </div>

        <div class="row fila_a">
            <div class="col-sm-2">
                <label class="lbl_a">Tipo Doc</label><br>
                <?= $r->tdocumento ?>
            </div>
            <div class="col-sm-2">
                <label class="lbl_a">Nro. Doc</label><br>
                <?= $r->nroDoc ?>
            </div>
            <div class="col-sm-4">
                <label class="lbl_a">Proveedor</label><br>
                <?= $r->nombre_proveedor . " " . $r->ruc ?>
            </div>

        </div>

        <table class="table table-hover" style="margin-top:28px;">
            <tr>
                <th class="celda_a_footer">CODIGO</th>
                <th class="celda_a_footer">PRODUCTO</th>
                <th class="celda_a_footer">CANTIDAD</th>
                <th class="celda_a_footer">PRECIO</th>
                <th class="celda_a_footer">SUBTOTAL</th>
            </tr>
        <?php
            }
        ?>
            <tr>
                <td class="celda_a"><?= $r->code ?></td>
                <td class="celda_a"><?= $r->product_name ?></td>
                <td class="celda_a"><?= number_format($r->cantidad,0) ?></td>
                <td class="celda_a"><?= number_format($r->precio_con_igv,2) ?></td>
                <td class="celda_a text-right"><?= number_format($r->subtotal,2) ?></td>
            </tr>
        <?php        
        }
        ?>
            <tr><th class="celda_a_footer" colspan="4">Monto Base</th><th class="celda_a_footer text-right"><?= number_format($r->monto_base,2) ?></th></tr>
            <tr><th class="celda_a_footer" colspan="4">Igv</th><th class="celda_a_footer text-right"><?= number_format($r->igv,2) ?></th></tr>
            <tr><th class="celda_a_footer" colspan="4">Total</th><th class="celda_a_footer text-right"><?= number_format($r->total,2) ?></th></tr>
        </table>
        <?php
    }

    function comillar($cad){
        $cad = str_replace('"','',$cad);
        $cad = "\"".$cad."\"";
        return $cad; 
    }

    // Funcion que entrega valores de una tabla
    function get_tempo(){
        $usuario = $_REQUEST["usuario"];
        if(!is_null($usuario)){
            $cSql = "select * from tempo_compras where usuario = ? and product_id!=888888";
            $ar = $this->db->query($cSql,array($usuario))->result_array();
            echo json_encode($ar);
        }
    }
	
}
