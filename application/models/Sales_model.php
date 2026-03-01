<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_model extends CI_Model
{

    public $token = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VybmFtZSI6ImZsYXZpb21vcmVub3oiLCJjb21wYW55IjoiMjA2MTI0NTI4NTgiLCJpYXQiOjE3MjM0MjM3MzYsImV4cCI6ODAzMDYyMzczNn0.iCitQ_JhAZdb3WaC93GgKeL-B3e3RAaWEg2iagJo4wevVhfzk-h5AfAdSH06DDMs4kiwJkcRNZxIIB8kL6m8t-YUQDYiQtwXIgpgHDg0U2r89QvO7SyMtJIO9Om5HnM5KCdhc_9bs6lBdBgpLS2R56dCJpFauSXbrY4Xz62LDWQJ5GxjJjwLQrexph-6qWz9szkbCCRWtSVdQS59PcXtN52oz47qvIdo6mVsGG9lZ6qSB8PH-R1d7uMrohhpnAGimjTuhekhxEegT7VNpMMRwi0NG9C5WwzfK2y3jODmKm07_KtvYyoflJiALJPsHMJ1_g1-kr4ZV80G2DMk5CK-EaG1UaxOnJeFQ7CddCLT2nt9n7scY8j2R8Y8_tVNV_ZvB-XEWXRZom3oZg4NQcWQXhNfXZuvb6EDeahphp1lrZhPeWxxDUmOku7Vm33CM0agbmNN_V-Q7SfdEpfYdtOva-s8q3Qd-GRAxb2imzm5I2_9OCbAaaZ-tRox6q4NsZ_ILkWvUJTi6zEgfYhLU6q-Q-BumkGCg5NPRtpkzWbSj1dijOGniTG8QvgsqihE9ElxQlWIOSbl7nDlji719Mk-x3rD8eBH-DZOP0ttSsJLj5UfuW9a-enplMNA39jay_wgLJBcb3bPGIPwMMDTurqfZiihxBnM1Jfp3CgKtsmjM2k";

    public function __construct(){
        parent::__construct();
    }

	function customer_id($dni_cliente, $name_cliente){
        $query = $this->db->select("id, name, cf1, cf2")->from("tec_customers")->where("cf1",$dni_cliente)->get();
        foreach($query->result() as $r){
            return $r->id;
        }
        return 0;
	}
	
	function forma_pago($id, $ar_forma, $ar_monto, $store_id){
        
        $nLim = count($ar_forma);
        for($i=0; $i<$nLim; $i++){
            $forma = $ar_forma[$i];
            $monto = $ar_monto[$i];

            //echo $i . ")" . $forma . " " . $monto . "<br>";

            if (strlen($monto."")>0 && ($monto."") != "0"){
                $monto = floatval($monto);
                if( $monto > 0){
                    //var_dump($_SESSION);
                    $ar_i = array("date" => date("Y-m-d H:i:s"),
                        "sale_id" => $id,
                        "paid_by"=> $forma,
                        "amount"=> $monto,
                        "currency"=> "PEN",
                        "note"=> "",
                        "store_id"=> $store_id,
                        "created_by"=> $_SESSION['user_id']);

                    $this->db->insert("tec_payments", $ar_i);
                }
            }
        }
        return true;
	}
	
	function correlativo($serie){
		//$cSql = "select max(correlativo) maximo from tec_sales where serie = '$serie' and anulado!='1'";
		//$query = $this->db->query($cSql);
		
		$this->db->select_max('correlativo');
		$this->db->where('serie',$serie);
		$this->db->where('correlativo is not null');
		//$this->db->where('anulado!=','1');
		
		//$cad_sql = $this->db->get_compiled_select("tec_sales");

		$query = $this->db->get("tec_sales");
		
		foreach($query->result() as $r){
			$el_maximo = $r->correlativo;
		}
		
		if(is_null($el_maximo)){
			return 1;
		}else{
			return $el_maximo + 1;
		}

	}

	function view($idx){
		$this->db->select('a.tipoDoc, e.descrip tipo_documento,concat(a.serie,\'-\',a.correlativo) recibo, a.customer_name razon, concat(d.cf1,\' \',d.cf2) doc_personal, date(a.date) as fecha, a.id, a.total, a.total_discount, a.total_tax, a.grand_total, b.product_id, b.product_name, if(b.discount is null,0,b.discount) discount, c.name, c.marca, c.modelo, c.color, b.quantity, b.unit_price, b.net_unit_price, (b.net_unit_price - if(b.discount is null,0,b.discount))*b.quantity as subtotal, b.comment, b.group_id, b.group_name');
		$this->db->from('tec_sales as a');
		$this->db->join('tec_sale_items as b','a.id=b.sale_id','left');
		$this->db->join('tec_products as c','b.product_id=c.id', 'left');
		$this->db->join('tec_customers as d','a.customer_id=d.id','left');
		$this->db->join('tec_tipos_doc as e','a.tipoDoc = e.id','left');
		$this->db->where('a.id',$idx);
		
        $query = $this->db->get();
		
		return $query;
	}

    function view_interno($idx){
        $this->db->select('a.tipoDoc, e.descrip tipo_documento,concat(a.serie,\'-\',a.correlativo) recibo, a.customer_name razon, concat(d.cf1,\' \',d.cf2) doc_personal, date(a.date) as fecha, a.id, a.total, a.total_discount, a.total_tax, a.grand_total, b.product_id, b.product_name, if(b.discount is null,0,b.discount) discount, c.name, c.marca, c.modelo, c.color, b.quantity, b.unit_price, b.net_unit_price, (b.net_unit_price - if(b.discount is null,0,b.discount))*b.quantity as subtotal, b.series, b.group_id, b.group_name');
        $this->db->from('tec_sales as a');
        $this->db->join('tec_sale_items as b','a.id=b.sale_id','left');
        $this->db->join('tec_products as c','b.product_id=c.id', 'left');
        $this->db->join('tec_customers as d','a.customer_id=d.id','left');
        $this->db->join('tec_tipos_doc as e','a.tipoDoc = e.id','left');
        $this->db->where('a.id',$idx);
        
        $query = $this->db->get();
        
        return $query;
    }

    public function enviar_doc_sunat_individual($sale_id){ // , $data, $items, $tipo_envio
        
        // *** CONSTRUYENDO ARRAY DATA **************************************
        $cSql = "select date(date) fecha, store_id, tipoDoc, customer_id, total, total_tax, grand_total, serie, correlativo, customer_name, product_tax ".
            " from tec_sales where id = ?";
        $query = $this->db->query($cSql,array($sale_id));

        $data = array();
        $data["id"]             = $sale_id;
        $tipoDoc                = "";

        foreach($query->result() as $r){
            $data["date"]           = $r->fecha;
            $data["store_id"]       = $r->store_id;          
            //die("Tienda:".$r->store_id);
            $data["tipoDoc"]        = $r->tipoDoc;
            $data["customer_id"]    = $r->customer_id;
            $data["total"]          = $r->total;
            $data["total_tax"]      = $r->total_tax;
            $data["grand_total"]    = $r->grand_total;
            $data["serie"]          = $r->serie;
            $data["correlativo"]    = $r->correlativo;
            $data["customer_name"]  = trim($r->customer_name);
            $data["store_id"]       = $r->store_id;
            $data["product_tax"]    = $r->product_tax;
            //$data["forma_pago"]     = $forma_pago;
        }

         $items = array();
            
        if($tipoDoc != '5'){
            // ******************************************************************************
            $rpta_sunat = $this->enviar_doc_sunat($sale_id, $data, $items, "ENVIO");
            // ******************************************************************************

            if ($this->sales_model->analizar_rpta_sunat($rpta_sunat)){

                // *****************************************************************************
                $rpta_sunat_xml = $this->enviar_doc_sunat($sale_id, $data, $items, "XML");
                // *****************************************************************************

                $gn = fopen("comprobantes/doc_{$sale_id}_xml.txt","w");
                fputs($gn, $rpta_sunat_xml);
                fclose($gn);
                $gn = null;

                $this->db->set(array('envio_electronico'=>'1'))->where("id",$sale_id)->update("tec_sales");
            }

            echo $rpta_sunat;
        }

    }
    
    public function enviar_doc_sunat($sale_id, $data, $items, $tipo_envio){

        // Token que sale del Loguin de la Empresa.

        $result = $this->db->select("tipoDoc")->where("id",$sale_id)->get("tec_sales")->result();
        foreach($result as $r){
            $tipo_documento = $r->tipoDoc;
        }

        //if ($tipo_documento != "Nota_de_credito"){ // se trata de generar una boleta o factura.
            //$tipo_documento = $data["tipoDoc"];

            //Averiguando los datos de la empresa
            $store_id   = $data["store_id"];
            $result     = $this->db->select("code, city, state, ubigeo, address1, address2, nombre_empresa, ruc")->where("id",$store_id)->get("tec_stores")->result_array();
        //}
        $correlativo = $data["correlativo"];

        foreach($result as $r){
            $this->COMPANY_DIRECCION      = $r["address1"]; // "Las casuarinas 666"
            $this->COMPANY_PROV           = $r["city"];
            $this->COMPANY_DPTO           = "LIMA";
            $this->COMPANY_DISTRITO       = $r["state"];
            $this->COMPANY_UBIGEO         = $r["ubigeo"];
            $this->COMPANY_RAZON_SOCIAL   = $r["nombre_empresa"]; //"DAVID MORENO PLETS"
            $this->COMPANY_RUC            = $r["ruc"]; //"10075047946";
        }

        $porcentajeIgv = $data["product_tax"];

        if($tipo_documento == '2'){ // Boleta
            $tipoDoc_       = "03";
            $tipoDoc_client = "1"; // DNI
        }elseif($tipo_documento == '1'){ // Factura
            $tipoDoc_       = "01";
            $tipoDoc_client = "6"; // RUC
        }

        // Subvariables aun por definir:
        $serie      = $data["serie"];
        $tip_forma  = "Contado";
        $fecha_emi  = date("Y-m-d") . "T" . date("H:i:s");
        $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
        $icbper     = 0;

        // tipoDoc : 01: Factura, 03: BV

        //$correlativo        = $this->correlativo($tipo_documento);

        $cSql = "select a.id, a.date, a.customer_id, a.customer_name, a.total, a.tipoDoc, a.grand_total,
            c.cf1, c.cf2,
            b.id id_items,
            b.product_id,
            b.product_name,
            b.quantity,
            b.net_unit_price,
            b.tax,
            b.real_unit_price,
            b.subtotal,
            b.group_id,
            b.group_name
            from tec_sales a
            inner join tec_sale_items b on a.id = b.sale_id
            inner join tec_customers c on a.customer_id = c.id
            inner join tec_products d on b.product_id = d.id
            where a.id = $sale_id and b.quantity > 0";

        //die($cSql);
        $query = $this->db->query($cSql);

        foreach ($query->result() as $r){
            if($tipoDoc_ == "01"){ // Ruc
                $numDoc         = $r->cf2;
            }elseif($tipoDoc_ == "03"){ // Boleta
                $numDoc         = $r->cf1;
            }
            //$grand_total    = $r["grand_total"];
            $total          = $r->total;
            //$tax            = is_null($r->tax) ? $porcentajeIgv : $r->tax;

            // Provisionalmente se hara esto, ya que muchas veces no le ponen IGV:
            $tax            = $porcentajeIgv;
            
            $Cliente        = $r->customer_name;
            $codProdSunat   = ""; //$r->codProdSunat;
            //$fecha_venc     = $r->fec 
        }

        traza("");
        traza("$serie - $correlativo");
        traza("tax : $tax");
        $nTotal             = $total * (1 + ($tax/100)) * 1;
        $nTotal             = round($nTotal,2);

        // Variables segun la API:
        $Cliente            = $data["customer_name"];
        $direccion_cliente  = "sin direccion"; 

        $mtoOperGravadas    = round($total, 2); //200.2       
        $icbper             = round($icbper * 1, 2); //0.8
        
        $subTotal           = $nTotal; // 237.04
        $redondeo           = 0; // 0.04
        $mtoImpVenta        = $nTotal; // 237
        $mtoOperExoneradas  = 0;

        //tipoOperacion: 0101: venta interna
        
        $campus1 = "{
          \"ublVersion\": \"2.1\",
          \"fecVencimiento\": \"" . $fecha_emi . "-05:00\",
          \"tipoOperacion\": \"0101\", 
          \"tipoDoc\": \"{$tipoDoc_}\",
          \"serie\": \"$serie\",
          \"correlativo\": \"{$correlativo}\",
          \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
          \"formaPago\": {
            \"moneda\": \"PEN\",
            \"tipo\": \"$tip_forma\"
          },
          \"tipoMoneda\": \"PEN\",
          \"client\": {
            \"tipoDoc\": \"{$tipoDoc_client}\",
            \"numDoc\": \"$numDoc\",
            \"rznSocial\": \"{$Cliente}\",
            \"address\": {
              \"direccion\": \"{$direccion_cliente}\",
              \"provincia\": \"LIMA\",
              \"departamento\": \"LIMA\",
              \"distrito\": \"LIMA\",
              \"ubigueo\": \"150101\"
            }
          },";

        $campus2 = "\"company\": {
            \"ruc\": $this->COMPANY_RUC,
            \"razonSocial\": \"$this->COMPANY_RAZON_SOCIAL\",
            \"address\": {
              \"direccion\": \"$this->COMPANY_DIRECCION\",
              \"provincia\": \"$this->COMPANY_PROV\",
              \"departamento\": \"$this->COMPANY_DPTO\",
              \"distrito\": \"$this->COMPANY_DISTRITO\",
              \"ubigueo\": \"$this->COMPANY_UBIGEO\"
            }
          },";

        
        // Construir items de visualizacion (agrupando items con group_id)
        $display_items = array();
        $group_aggregates = array();

        foreach ($query->result() as $r){
            if(!empty($r->group_id)){
                if(!isset($group_aggregates[$r->group_id])){
                    $group_aggregates[$r->group_id] = array(
                        'group_name'     => $r->group_name,
                        'product_id'     => 'GRP' . $r->group_id,
                        'net_value_total'=> 0,
                        'tax'            => $r->tax
                    );
                }
                // Sumar el valor neto de este item al grupo
                $group_aggregates[$r->group_id]['net_value_total'] += round($r->net_unit_price * $r->quantity, 2);
            }else{
                $display_items[] = $r;
            }
        }

        // Convertir grupos agregados en items de visualizacion
        foreach($group_aggregates as $gid => $g){
            $obj = new stdClass();
            $obj->product_id      = $g['product_id'];
            $obj->product_name    = $g['group_name'];
            $obj->quantity        = 1;
            $obj->net_unit_price  = $g['net_value_total'];
            $obj->tax             = $g['tax'];
            $display_items[] = $obj;
        }

        $campus4                = "";
        $acu_mtoBaseIgv = $mtoOperGravadas  = $mtoIGV = $valorVenta = $acu_subTotal = $acu_totalImpuestos = 0;
        foreach ($display_items as $r){

            $codProducto        = "P" . $r->product_id;
            $descripcion        = $r->product_name;
            $cantidad           = round($r->quantity,0);
            $mtoValorUnitario   = round($r->net_unit_price,2)*1;
            $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
            $mtoBaseIgv         = round($r->net_unit_price * $cantidad * 1,2);

            $porcentajeIgv_       = $porcentajeIgv;

            $igv                = round($mtoBaseIgv * ($porcentajeIgv_/100),2);
            traza("igv : $igv");

            $tipAfeIgv          = 10;
            $totalImpuestos     = $igv;

            $igvX               = 1 + ($porcentajeIgv_/100);

            $mtoPrecioUnitario      = round($mtoValorUnitario * $igvX,2);

            $campus4 .= "{
              \"codProducto\": \"$codProducto\",
              \"unidad\": \"NIU\",
              \"descripcion\": \"$descripcion\",
              \"cantidad\": {$cantidad},
              \"mtoValorUnitario\": $mtoValorUnitario,
              \"mtoValorVenta\": {$mtoValorVenta},
              \"mtoBaseIgv\": {$mtoBaseIgv},
              \"porcentajeIgv\": {$porcentajeIgv_},
              \"igv\": {$igv},
              \"tipAfeIgv\": $tipAfeIgv,
              \"totalImpuestos\": {$totalImpuestos},
              \"mtoPrecioUnitario\": {$mtoPrecioUnitario}
            },";

            $mtoOperGravadas    += $mtoValorVenta;
            $mtoIGV             += $igv;
            $valorVenta         += $mtoValorVenta;
            $acu_totalImpuestos += $totalImpuestos;
            $acu_subTotal       += $mtoPrecioUnitario * $cantidad * 1;
        }

        // Se asume que si o si hay items, por tanto se quita la ultima coma:
        $campus4 = substr($campus4,0,strlen($campus4)-1);

        //$mtoImpVenta = round($acu_mtoBaseIgv * (1+($porcentajeIgv/100)),2);
        //$subTotal =  $mtoImpVenta;   

        $campus3 = "\"mtoOperGravadas\": {$mtoOperGravadas},
          \"mtoOperExoneradas\": $mtoOperExoneradas,
          \"mtoIGV\": {$mtoIGV},
          \"icbper\": $icbper,
          \"valorVenta\": {$valorVenta},
          \"totalImpuestos\": {$acu_totalImpuestos},
          \"subTotal\": {$acu_subTotal},
          \"redondeo\": $redondeo,
          \"mtoImpVenta\": {$acu_subTotal},
          \"details\": [";
        traza($campus3);

        $cValor         = $acu_subTotal . "";
        $pos            = strpos($cValor, ".");
        $valor_entero   = substr($cValor,0,$pos);
        $valor_dec      = substr($cValor,$pos+1);

        $valor_dec = substr($valor_dec . "00",0,2);

        $en_letras =  "Son " . $this->fm->convertir($valor_entero) . " y $valor_dec/100 Soles";

        $campus5 = "],
          \"legends\": [
            {
              \"code\": \"1000\",
              \"value\": \"$en_letras\"
            }
          ]
        }";
        
        $campos = $campus1 . $campus2 . $campus3 . $campus4 . $campus5;

        switch ($tipo_envio) {
            case 'ENVIO':
                $url = "https://facturacion.apisperu.com/api/v1/invoice/send";

                $nombre_file    = "comprobantes/doc_{$data['id']}_cadena.txt";
                $gestor         = fopen($nombre_file,"w");
                fputs($gestor, $campos);
                fclose($gestor);
                break;
            
            case 'XML':
                $url = "https://facturacion.apisperu.com/api/v1/invoice/xml";

                $nombre_file    = "comprobantes/doc_{$data['id']}_xml_cadena.txt";
                $gestor         = fopen($nombre_file,"w");
                fputs($gestor, $campos);
                fclose($gestor);
                break;

            default:
                $url = "";
                break;
        }
        $gestor = null;
        

        /*}elseif($tipo_documento == 'Nota_de_credito' || $tipo_documento == 'Nota_de_debito'){

            $campos = $this->Notas_varias($sale_id);

            $url = "https://facturacion.apisperu.com/api/v1/note/send";
        }*/


        $response = "";
        

        //**********************************
        return $this->rulo($url, $campos);
        //**********************************
    }

    function analizar_rpta_sunat($bloque){
        $rpta = strpos($bloque, ", ha sido aceptada");
        $rpta2 = strpos($bloque, ", ha sido aceptado");
        if(($rpta != false && $rpta > 0) || ($rpta2 != false && $rpta2 > 0)){
            return true;
        }else{ return false;}
    }

    function rulo($url, $campos){  // Envia a Sunat y retorna la respuesta
        
        $cToken = $this->token;
        
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

        //$response = curl_exec($curl);
        $response = "Inactiva en modo desarrollo.";    // X X X X X X X X X X X X X X X X X X X X X SE DEBE QUITAR ESTO ***************

        curl_close($curl);

        return $response;
    }

    function enviar_anulacion($id){

        $result = $this->db->select("b.codigo_sunat tipoDoc, a.date, a.serie, a.correlativo, a.store_id")
            ->from("tec_sales a")
            ->join("tec_tipos_doc b","a.tipoDoc=b.id","left")
            ->where("a.id",$id)->get()->result();

        foreach($result as $r){
            $tipo_documento = $r->tipoDoc; // 1 Factura, 2 Boleta
            $fec_gen        = substr($r->date,0,10) . 'T' . substr($r->date,11,8) . '-05:00';
            $serie          = $r->serie;
            $correlativo    = $r->correlativo;
            $store_id       = $r->store_id;
        }

        $maximon        = 1;
        $fec_hoy        = date("Y-m-d") . "T" . "00:00:00-05:00";

 
        //Averiguando los datos de la empresa
        $result     = $this->db->select("code, city, state, ubigeo, address1, address2, nombre_empresa, ruc")
                    ->where("id",$store_id)->get("tec_stores")->result_array();
        
        foreach($result as $r){
            $this->COMPANY_DIRECCION      = $r["address1"]; 
            $this->COMPANY_PROV           = $r["city"];
            $this->COMPANY_DPTO           = "LIMA";
            $this->COMPANY_DISTRITO       = $r["state"];
            $this->COMPANY_UBIGEO         = $r["ubigeo"];
            $this->COMPANY_RAZON_SOCIAL   = $r["nombre_empresa"]; 
            $this->COMPANY_RUC            = $r["ruc"]; 
        }

        $campus2 = 
            "\"company\": {".
            "    \"ruc\":\"" . $this->COMPANY_RUC . '",' .
                "\"razonSocial\":\"" .  $this->COMPANY_RAZON_SOCIAL . '",' .
                "\"address\": {".
                    "\"direccion\": \"" . $this->COMPANY_DIRECCION . '",'.
                    "\"provincia\": \"" . $this->COMPANY_PROV . '",'.
                    "\"departamento\": \"" .$this->COMPANY_DPTO . '",'.
                    "\"distrito\": \"" . $this->COMPANY_DISTRITO . '",'.
                    "\"ubigueo\": \"" . $this->COMPANY_UBIGEO . '"' .
                "}".
            "},";
        
        $cad = '';
        $cad .= '{';
        $cad .= '  "correlativo": "' . $maximon . '",';
        $cad .= '  "fecGeneracion": "' . $fec_gen . '",';
        $cad .= '  "fecComunicacion": "' . $fec_hoy . '",';
        
        $cad .= $campus2;
        
        $cad .= '  "details": [';
        $cad .= '    {';
        $cad .= '      "tipoDoc": "' . $tipo_documento . '",';
        $cad .= '      "serie": "' . $serie . '",';
        $cad .= '      "correlativo": "' . $correlativo . '",';
        $cad .= '      "desMotivoBaja": "ERROR EN CÁLCULOS"';
        $cad .= '    }';
        $cad .= '  ]';
        $cad .= '} '; 

        $datos = $cad;

        //echo $datos . "<br><br>";

        $url = "https://facturacion.apisperu.com/api/v1/voided/send";

        $respuesta = $this->rulo($url, $datos);
        
        $gn             = null;
        $nombre_file    = "comprobantes/doc_{$id}_anulacion.txt";
        $gn             = fopen($nombre_file,"w");
        fputs($gn, $respuesta);
        fclose($gn);

        return $respuesta;
    }

    function analizar_rpta_anulacion($respuesta){
        return true;
    }

    public function anular_localmente($id){
        $this->db->set('anulado','1')->where('id',$id);
        $this->db->update('tec_sales');
    }

    public function restar_stock($id, $store_id){
        $query = $this->db->query("select * from tec_sale_items where sale_id = ?",array($id));
        foreach($query->result() as $r){
            $cantidad       = $r->quantity;
            $product_id     = $r->product_id;
            
            if(!is_null($product_id) && $product_id != 0){
                $cSql = "update tec_prod_store set stock = stock - {$cantidad} where store_id = {$store_id} and product_id = {$product_id}";
                //echo($cSql."<br>");
                $this->db->query($cSql);
            }
        }
    }
}