<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Controller {

    function __construct() {
        parent::__construct();

        /*if (!$this->loggedIn) {
            redirect('login');
        }*/ 
        
        session_start();

        if (!isset($_SESSION["store_id"])) {
            die("No tiene sesión disponible. <a href=\"" . base_url("welcome/index") . "\">Login</a>");
        }
        
        $this->load->model('sales_model');
		$this->Igv = 18;  // ojo sistema erp
        $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';
        $this->load->model("compras_model");

    }

    function index($cDesde='null', $cHasta='null', $cStore_id='null') {
        $this->data['page_title'] = "Ventas";
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['store_id'] = $cStore_id;
        $this->data['Admin'] = $this->Admin;
        
        $this->template->load('production/index', 'sales/index', $this->data);
    }

    function add(){
        $query = $this->db->select("estado_cierre")->where("fecha",date("Y-m-d"))->get("tec_registro_cajas");
        $existe_apertura = false;
        foreach($query->result() as $r){
            $existe_apertura = true;
        }

        $this->data['page_title']   = "Agregar Ventas";
        $this->data['productos']    = $this->db->query("select name,marca,code,modelo,id from tec_products order by name,marca");
        $this->data["existe_apertura"] = $existe_apertura;
        $this->template->load('production/index', 'sales/add', $this->data);
    }

	function save(){
        $this->data["error"] = false;
        $fecha 			= substr($_POST["fecha"],0,10) . " " . date("H:i:s");
		$tipoDoc 		= $_POST["tipoDoc"];
		//$customer_id 	= $_POST["customer_id"];
		
		$dni_cliente 	= $_POST["dni_cliente"];
		$name_cliente 	= $_POST["name_cliente"];
		$customer_id 	= $_POST["txt_customer_id"]; //$this->sales_model->customer_id($dni_cliente, $name_cliente);
		
		$forma_pago 	= $_POST["forma_pago"];
        $forma_pago_monto = $_POST["forma_pago_monto"];
		$subtotal 		= $_POST["subtotal"];
		$igv 			= $_POST["igv"];
		$grand_total	= $_POST["total"];
        
        //die("Total de totales:".$grand_total);

        $correlativo    = $_POST["txt_recibo"];
        $created_by     = $_SESSION["user_id"];
		
        $this->db->reset_query();
        //$this->db->trans_begin();

	    $ar = array();
        $ar["store_id"]         = $_SESSION["store_id"];
        $ar["date"]             = $fecha;
        $ar["tipoDoc"]          = $tipoDoc;
        $ar["customer_id"]      = $customer_id;
        $ar["total"]            = $subtotal;
        $ar["total_tax"]        = $igv;
        $ar["grand_total"]      = $grand_total;
        $serie                  = $this->serie($tipoDoc);
        $ar["serie"]            = $serie;
        $ar["product_tax"]      = $this->Igv;
        $ar["correlativo"]      = $correlativo;
        $ar["created_by"]       = $created_by;
        $ar["customer_name"]    = $name_cliente;


        // ****** VERIFICANDO QUE CADA PRODUCTO TENGA AL MENOS UNA COMPRA *****
        
        $Lim = count($_REQUEST['item']);
        $items = array();
        for ($i = 0; $i < $Lim; $i++){
            $product_idx = $_REQUEST['item'][$i];
            if(!$this->isServicio($product_idx)){
                if(!$this->existe_compra($product_idx)){
                    $name_product = $this->db->select("name")->where("id",$product_idx)->get("tec_products")->row()->name;
                    $this->data["msg"] = "El producto con Id $product_idx ($name_product) no tiene compras, ingrese primero la compra.";
                    $this->data["error"] = true;
                }
            }
        }
        
        if($this->data["error"] == false){
        
            if ($this->db->insert("tec_sales", $ar)){
    			$id = $this->db->insert_id();
    			
                $ar_monto[0] = $forma_pago_monto;
                $ar_forma[0] = $forma_pago;
                
                if(isset($_POST["forma_pago2"])){
                    
                    $ar_monto[1] = $_POST["forma_pago_monto2"];
                    $ar_forma[1] = $_POST["forma_pago2"];
                }

                if($this->sales_model->forma_pago($id, $ar_forma, $ar_monto, $_SESSION["store_id"])){
    				
                    $this->data["msg"] = "grabacion Correcta de ".$this->serie($tipoDoc). "-" .$correlativo." ".
                        '<button type="button" onclick="ver_documento('.$id.')" class="btn btn-info">Imprimir</button>';
    				
                    $this->data["error"] = false;
    			
                    $Lim = count($_REQUEST['item']);

                    $items = array();
                    for ($i = 0; $i < $Lim; $i++){
                        $item_id = $_REQUEST['item'][$i];

                        $ard = array();
                        
                        $ard["sale_id"]     = $id;
                        $ard["product_id"]  = $item_id;
                        $ard["quantity"]    = $_REQUEST['quantity'][$i];
                        $ard["tax"]         = $_REQUEST['impuestos'][$i];
                        
                        $precio_unitario = $_REQUEST['cost'][$i];
                        
                        // en caso sea Ticket no se quita el IGV
                        if($tipoDoc == '5'){ // ticket
                            $costo_unitario = $precio_unitario;
                        }else{ 
                            $costo_unitario =  $precio_unitario / (1+($_REQUEST['impuestos'][$i]/100));
                        }
                        
                        $subtotal_      = $costo_unitario * ($_REQUEST['quantity'][$i] * 1);

                        $ard["unit_price"]      = $precio_unitario;
                        $ard["net_unit_price"]  = $costo_unitario;
                        $ard["subtotal"]        = $subtotal_;
                        $ard["real_unit_price"] = $precio_unitario;
                        
                        // En el caso de que sea un servicio se coloca la observacion a su costado
                        if ($this->isServicio($item_id)){
                            $observaciones = " " . $_REQUEST["obs"][$i];
                        }else{ 
                            $observaciones = "";
                            $ard["compra_id"]       = $this->enlazar_compra($_SESSION["store_id"], $item_id, $_REQUEST['quantity'][$i]);
                        }

                        $ard["product_name"]    = trim($_REQUEST['descripo'][$i]);
                        $ard["comment"]         = trim($observaciones);

                        $this->db->insert("tec_sale_items", $ard);

                        $itm["sale_id"]     = $id;
                        $itm["product_id"]  = $item_id;
                        $itm["quantity"]    = $_REQUEST['quantity'][$i];
                        $itm["unit_price"]  = $precio_unitario;
                        $itm["net_unit_price"] = $_REQUEST['cost'][$i];
                        $itm["subtotal"]    = $subtotal_;
                        $itm["real_unit_price"] = $precio_unitario;
                        $itm["product_name"] = $_REQUEST['descripo'][$i];
                        
                        $items[] = $itm;

                        $this->compras_model->disminuir_al_stock($item_id, $_SESSION["store_id"], $_REQUEST['quantity'][$i]);
                    }

                    $data = array();
                    $data["id"]             = $id;
                    $data["date"]           = $fecha;          
                    $data["tipoDoc"]        = $tipoDoc;
                    $data["customer_id"]    = $customer_id;
                    $data["total"]          = $subtotal;
                    $data["total_tax"]      = $igv;
                    $data["grand_total"]    = $grand_total;
                    $data["serie"]          = $serie;
                    $data["correlativo"]    = $correlativo;
                    $data["customer_id"]    = $customer_id;
                    $data["customer_name"]  = $name_cliente;
                    $data["store_id"]       = $_SESSION["store_id"];
                    $data["product_tax"]    = $this->Igv;
                    $data["forma_pago"]     = $forma_pago;

                    
                    if($tipoDoc != '5'){

                        // ******************************************************************************
                        $rpta_sunat = $this->sales_model->enviar_doc_sunat($id, $data, $items, "ENVIO");
                        // ******************************************************************************

                        //$gn = fopen("rpta_{$id}.txt","a+");
                        $gn = fopen("comprobantes/doc_{$id}_rpta.txt","w");
                        fputs($gn, $rpta_sunat);
                        fclose($gn);
                        $gn = null;

                        if ($this->sales_model->analizar_rpta_sunat($rpta_sunat)){

                            // *****************************************************************************
                            $rpta_sunat_xml = $this->sales_model->enviar_doc_sunat($id, $data, $items, "XML");
                            // *****************************************************************************

                            $gn = fopen("comprobantes/doc_{$id}_xml.txt","w");
                            fputs($gn, $rpta_sunat_xml);
                            fclose($gn);
                            $gn = null;

                            $this->db->set(array('envio_electronico'=>'1'))->where("id",$id)->update("tec_sales");
                        }

                        $this->data['rpta_sunat'] = $rpta_sunat;
                    
                    }
                    $this->data["page_title"] = "Agregar Ventas";

                    //$this->template->load('production/index', 'sales/add', $this->data); //$this->load->view("sales/view", $this->data);
                    //$this->db->trans_commit();

                    $this->view($id); //
                    //$this->data['page_title'] = "Agregar Ventas";
                    //$this->template->load('production/index', 'sales/add', $this->data);

                }else{
    				$this->data["msg"] = "No se pudo grabar forma de pago";
    				$this->data["error"] = true;
                    //die("Vacan X");
                    //$this->db->trans_rollback();

                    $this->data["page_title"] = "Agregar Ventas";
                    $this->template->load('production/index', 'sales/add', $this->data);
    			}

    		}else{
                $this->data["msg"] = "No se pudo grabar la Venta";
                $this->data["error"] = true;

                //$this->db->trans_rollback();

                $this->data["page_title"] = "Agregar Ventas";
                $this->template->load('production/index', 'sales/add', $this->data);
            }
        }else{
            $this->data["page_title"] = "Agregar Ventas";
            $this->template->load('production/index', 'sales/add', $this->data);            
        }

	}

    public function isServicio($item_id){
        $query = $this->db->select("prod_serv")->where("id",$item_id)->get("tec_products");
        foreach($query->result() as $r){
            if($r->prod_serv == 'S'){ return true; }else{ return false; }
        }
        return false;
    }

    function enviar_anulacion($id){
        //$this->sales_model->enviar_anulacion($id);
        $kola = "nada";
    }

	function customer_id($a,$b){
		return 1;
	}

    function get_sales($cDesde,$cHasta,$cStore){

        //$this->load->library('datatables');
        //$cDesde = $_REQUEST('desde');
        //$cHasta = $_REQUEST('hasta');
            
        $opcion = 0;
        $cad_desde = $cad_hasta = $cad_store_id = "";
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde !='null'){
                //$this->db->where('tec_sales.date>=', $cDesde);
                $cad_desde = " and date(tec_sales.date)>='{$cDesde}'";
                $opcion += 1;
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 && $cHasta !='null'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_hasta = " and date(tec_sales.date)<='{$cHasta}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }

        if(!is_null($cStore)){
            if(strlen($cStore)>0 && $cStore !='null' && $cStore != '0'){
                //$this->db->where("tec_sales.date<=date_add('$cHasta',interval 1 day)");
                $cad_store_id = " and tec_sales.store_id='{$cStore}'"; // date_add(?,interval 1 day)
                $opcion += 4;
            }
        }


        $cSql = "select tec_sales.id, tec_stores.name tienda, tec_sales.date, customer_name, round(total,2) total, round(grand_total,2) grand_total,  tec_users.username created_by, tec_sales.anulado,
            concat(tec_sales.serie,'-',tec_sales.correlativo) recibo, group_concat(substr(lcase(tec_products.name),1,12)) productos,
            if(tec_sales.envio_electronico = 1, 'Enviado', 'No Enviado') as dir_comprobante,
            concat('<button onclick=\'ver_documento(', tec_sales.id, ')\'><i style = \'color:blue\' class=\'glyphicon glyphicon-eye-open\'></i></button>','&nbsp;<button onclick=\'del_documento(',tec_sales.id,')\'><i style = \'color:red\' class=\'glyphicon glyphicon-remove\'></i></button>') as actions
            from tec_sales
            inner join tec_sale_items on tec_sales.id = tec_sale_items.sale_id
            inner join tec_stores on tec_sales.store_id = tec_stores.id
            left join tec_users on tec_sales.created_by = tec_users.id
            left join tec_products on tec_sale_items.product_id = tec_products.id
            where 1=1 " . $cad_desde . $cad_hasta . $cad_store_id.
            " group by tec_sales.id, tec_stores.name, tec_sales.date, customer_name, round(total,2), round(grand_total,2),  tec_users.username, tec_sales.anulado, concat(tec_sales.serie,'-',tec_sales.correlativo), 
            if(tec_sales.envio_electronico = 1, 'Enviado', 'No Enviado'),
            concat('<button onclick=\'ver_documento(', tec_sales.id, ')\'><i style = \'color:blue\' class=\'glyphicon glyphicon-eye-open\'></i></button>','&nbsp;<button onclick=\'del_documento(',tec_sales.id,')\'><i style = \'color:red\' class=\'glyphicon glyphicon-remove\'></i></button>')";
        
        if($opcion == 1){
            //die(":1");
            $result = $this->db->query($cSql,array($cDesde))->result_array();
            
        }elseif($opcion == 4){
            //die(":2");
            $result = $this->db->query($cSql,array($cHasta))->result_array();
            
        }elseif($opcion == 5){
            //die(":3");
            $result = $this->db->query($cSql,array($cDesde,$cHasta))->result_array();
            
        }else{
            //die(":4");
            $result = $this->db->query($cSql)->result_array();
            
        }
        //die("opcion:".$opcion);

        $ar_campos = array("id","tienda","date","customer_name","recibo","anulado","total","grand_total","productos","actions");  // 
        // ,"dir_comprobante"

        //if($this->Admin){
        //   $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('pos/view/$1/1') . "' title='".lang("view_invoice")."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-list'></i></a> <a href='".       site_url('sales/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a> <a href='".        site_url('sales/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a> <a href='" .   site_url('pos/?edit=$1') . "' title='".lang("edit_invoice")."' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" .                               site_url('sales/delete/$1') . "' onClick=\"return confirm('". lang('alert_x_sale') ."')\" title='".lang("delete_sale")."' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");

        //$this->datatables->add_column("Actions", "hebra"); // <button onclick=\"ver_documento(1)\">Ver</button>

        echo $this->fm->json_datatable($ar_campos,$result);
        
        //}

    }

	function view($id){
		if(!is_null($id)){
			
            $ar["query"] = $this->sales_model->view($id);
            //print_r($ar);
            //die("llego a casax");
            $this->load->view('sales/view',$ar);
		}
	}

    function view_popup($id){
        if(!is_null($id)){
            
            $ar["query"] = $this->sales_model->view($id);
            //print_r($ar);
            //die("llego a casax");
            $this->load->view('sales/view_pop',$ar);
        }
    }

    function correlativo(){
        $tipo = $_GET["tipo"];
        $serie = $this->serie($tipo);
        $numero = $this->sales_model->correlativo($serie);
        echo $numero;
    }

    function serie($tipo){
        if($tipo == "1"){ // factura
            $serie = "F001";
        }elseif($tipo == "2"){ // Boleta
            $serie = "B001";
        }elseif($tipo == "5"){
            $serie = "TK";
        }else{
            $serie = "";
        }
        return $serie;
    }

    function delete(){
        
        if(isset($_GET["id"])){
           $id = $_GET["id"];
           // Tec_sales
           $this->db->set(array("anulado"=>"1","grand_total"=>"0"));
           $this->db->where("id",$id);
           $this->db->update("tec_sales");

           // tec_payments:
           //$this->db->where("sale_id",$id);
           //$this->db->delete("tec_payments");

           //Se agrega al stock cada item
            $query = $this->db->select("product_id, quantity")->where("sale_id",$id)->get("tec_sale_items");
            foreach($query->result() as $r){
                
                $product_id = $r->product_id;
                $cantidad   = $r->quantity;
                //echo "Pasada:" . $product_id . " " . $cantidad . "<br>";
                $this->compras_model->agregar_al_stock($product_id, $_SESSION["store_id"], $cantidad);
            }
            //die("Fin");

            $objDoc = $this->db->select("*")->where("id",$id)->get("tec_sales")->row();

            $cad_a = "";
            if($objDoc->tipoDoc == "1" || $objDoc->tipoDoc == "2"){  // Factura, boleta
                $this->sales_model->enviar_anulacion($id);
                $cad_a = " con envio a Sunat.";
            }

            $ar["rpta"] = "1";
            $ar["message"] = "Se anula el Documento {$id} {$cad_a}";
        }else{
            $ar["rpta"] = "0";
            $ar["message"] = "No se pudo anular";
        }

        echo json_encode($ar);
    }

    public function mostrar_antes_de_enviar(){
        $nombre_file    = "ultimo.txt";
        $gn             = fopen($nombre_file,"r");
        
        echo "<div style=\"font-family:courier; font-size:14px;\">";
        while(!feof($gn)){
            $line = fgets($gn);
            $cLinea = htmlspecialchars($line); //htmlspecialchars($line); //htmlentities($line);
            $cLinea = str_replace(" ","&nbsp;",$cLinea);

            echo $cLinea . "<br>";
        }    

        fclose($gn);
        echo "</div>";

        //*****************************************

        $gn = fopen("rpta.txt","r");
        echo "<div style=\"border-style:solid; border-color:black;font-family:courier; font-size:14px;\">";
        if($gn != false){
            while(!feof($gn)){
                $line = fgets($gn);
                $cLinea = htmlspecialchars($line); //htmlspecialchars($line); //htmlentities($line);
                $cLinea = str_replace(" ","&nbsp;",$cLinea);

                echo $cLinea . "<br>";        
            }
            fclose($gn);
        }
        echo "</div>";
    }

    /*
    public function anular(){
        
            if(!isset($_POST["id_venta"])){
            
                $this->data['page_title']   = "Anular Ventas";
            
                $this->template->load('production/index', 'sales/anulacion', $this->data);
            
            }else{
                $id     = $_POST["id_venta"];
                $rpta   = $this->sales_model->enviar_anulacion($id);
                if ($this->sales_model->analizar_rpta_anulacion($rpta)){

                    $this->sales_model->anular_localmente($id);
                    $this->data["msg"]  = "Se anula correctamente el Documento.";
                    
                }else{
                    $this->data["msg"]  = "No se pudo anular";
                }
                $this->data['page_title'] = "Ventas";
                $this->data['desde'] = null;
                $this->data['hasta'] = null;
                $this->data['Admin'] = $this->Admin;
                $this->template->load('production/index', 'sales/index', $this->data);
            }
        
    }*/
    
    function obtener_tipo_precios(){
        
        $product_id  = $_REQUEST["product_id"];
        $opcion      = $_REQUEST["opcion"];

        if(strlen($product_id)>0){
            $query = $this->db->select("price, precio_x_mayor")->from("tec_products")->where("id", $product_id)->get();
            if($opcion == '1'){
                echo $query->row(0)->precio_x_mayor;
            }else{
                echo $query->row(0)->price;
            }
        }else{ echo 0; }
        
    }

    function buscar(){ // LO USA LA BUSQUEDA INCREMENTAL
        $code = $_REQUEST["b"];
        $store_id = $_SESSION['store_id'];
        $cSql = "select a.*, if(b.stock is null,0,b.stock) stock, c.name categoria, a.impuesto, a.prod_serv from tec_products a 
            left join tec_prod_store b on a.id=b.product_id and b.store_id = {$store_id}
            left join tec_categories c on a.category_id=c.id 
            where a.activo='1' and (a.name like '%{$code}%' or a.marca like '%{$code}%' or a.modelo like '%{$code}%' or a.code like '%{$code}%')
            order by a.name, a.marca, a.modelo";
        
        $ar = array();
        if(strlen($code)>1){
            $query = $this->db->query($cSql);
            $cad = "[";
            $n = 0;
            foreach($query->result() as $r){
                $n++;
                $cad .= "{";
                $cad .= '"id":"' . $r->id . '",'; 
                $cad .= '"name":"' . str_replace('"','',$r->name . ' ' . $r->marca . ' ' . $r->modelo) . '",';
                $cad .= '"stock":"' . $r->stock . '",';
                $cad .= '"categoria":"' . $r->categoria . '",'; 
                $cad .= '"impuesto":"' . $r->impuesto . '",';
                $cad .= '"prod_serv":"' . $r->prod_serv . '"';
                $cad .= "},";
            }
            if($n>0){
                $cad = substr($cad,0,strlen($cad)-1);
            }
            $cad .= "]";
            echo $cad;
        }else{
            echo "";
        }
    }

    function buscar2(){ // Para Compras
        $code = $_POST["campo"];
        $store_id = $_SESSION['store_id'];
        
        // Parentesis :
        $code = str_replace(" ","%",$code);

        $cSql = "select a.*, if(b.stock is null,0,b.stock) stock, c.name categoria, a.impuesto from tec_products a 
            left join tec_prod_store b on a.id=b.product_id and b.store_id = {$store_id}
            left join tec_categories c on a.category_id=c.id 
            where a.activo='1' and a.prod_serv='P' and concat(a.name,' ',a.marca,' ',a.modelo) like '%{$code}%'";
        
        //echo $cSql;
        
        $ar = array();
        $cad = "";
        if(strlen($code)>1){
            $query = $this->db->query($cSql);
            
            $n = 0;
            foreach($query->result() as $r){
                $n++;
                /*
                $cad .= "{";
                $cad .= '"id":"' . $r->id . '",'; 
                $cad .= '"name":"' . str_replace('"','',$r->name . ' ' . $r->marca . ' ' . $r->modelo) . '",';
                $cad .= '"stock":"' . $r->stock . '",';
                $cad .= '"categoria":"' . $r->categoria . '",'; 
                $cad .= '"impuesto":"' . $r->impuesto . '"';
                $cad .= "},";
                */

                $completo = $r->name . " " . $r->marca . " " . $r->modelo;
                $cad .= "<li onclick=\"mostrar(" . $r->id . ",'" . $completo . "')\">" . $completo . "</li>";
            }
            if($n>0){
                //$cad = substr($cad,0,strlen($cad)-1);
            }
            //echo $cad;
        }else{
            echo "";
        }

        //echo json_encode($cad, JSON_UNESCAPED_UNICODE);
        //echo json_encode($cad);
        echo $cad;
    }

    function buscar_codigo(){ // LO USA LA BUSQUEDA POR CODIGO DE BARRA
        $code = $_POST["code"];
        $store_id = $_SESSION['store_id'];
        $cSql = "select a.*, b.stock, c.name categoria, a.impuesto from tec_products a 
            left join tec_prod_store b on a.id=b.product_id and b.store_id = {$store_id}
            left join tec_categories c on a.category_id=c.id
            where a.activo='1' and a.code like '%{$code}%'";
        
        $ar = array();
        if(strlen($code)>1){
            $query = $this->db->query($cSql);
            $cad = "[";
            $n = 0;
            foreach($query->result() as $r){
                $n++;
                $cad .= "{";
                $cad .= '"id":"' . $r->id . '",'; 
                $cad .= '"name":"' . str_replace('"','',$r->name . ' ' . $r->marca . ' ' . $r->modelo) . '",';
                $cad .= '"stock":"' . $r->stock . '",';
                $cad .= '"categoria":"' . $r->categoria . '",'; 
                $cad .= '"impuesto":"' . $r->impuesto . '"';
                $cad .= "},";
            }
            if($n>0){
                $cad = substr($cad,0,strlen($cad)-1);
            }
            $cad .= "]";
            echo $cad;
        }else{
            echo "";
        }
    }

    function enlazar_compra($store_id, $product_id, $q){
        // (1) escogemos la compra mas antigua disponible
        $cSql = "SELECT a.compra_id, a.cantidad, a.van
            FROM `tec_compra_items` as `a` 
            LEFT JOIN `tec_compras` as `b` ON `a`.`compra_id` =`b`.`id` 
            WHERE `b`.`store_id` = ? AND `a`.`product_id` = ? and a.precio_sin_igv > 0 and a.cantidad - a.van > 0 order by a.compra_id limit 1";
        //  
        
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
        }else{

            // MEDIDA DE EMERGENCIA
            $cSql = "SELECT a.compra_id, a.cantidad, a.van
            FROM `tec_compra_items` as `a` 
            LEFT JOIN `tec_compras` as `b` ON `a`.`compra_id` =`b`.`id` 
            WHERE `b`.`store_id` = ? AND `a`.`product_id` = ? order by a.compra_id DESC limit 1"; // and a.precio_sin_igv > 0

            $query = $this->db->query($cSql,array($store_id, $product_id));

            $compra_id = 0;
            foreach($query->result() as $r){
                $compra_id = $r->compra_id;
            }
        }

        // (3) retornamos el id de la compra
        return $compra_id;
    }

    function enviar_individual($sale_id){
        echo $this->sales_model->enviar_doc_sunat_individual($sale_id);
        echo "Finish...";
    }

    function existe_compra($product_id){
        $cSql = "select a.* from tec_products a
            inner join (
              select k.product_id
              from tec_compras z
              inner join tec_compra_items k on z.id = k.compra_id
              group by k.product_id
            ) b on a.id = b.product_id and a.id = $product_id";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            return true;
        }
        return false;
    }

}
