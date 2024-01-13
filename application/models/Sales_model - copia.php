<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

	function customer_id($dni_cliente, $name_cliente){
		/*
        $cSql = "insert into tec_customers(name, cf1, cf2) values (?,?,?)";
		$cf1 = $cf2 = "";
		if(strlen($dni_cliente)==8){
			$cf1 = $dni_cliente;
		}elseif($dni_cliente==11){
			$cf2 = $dni_cliente;
		}

		$this->db->set("name", $name_cliente);
		$this->db->set("cf1", $cf1);
		$this->db->set("cf2", $cf2);
		$this->db->insert("tec_customers");
		return $this->db->insert_id();
        */
        $query = $this->db->select("id, name, cf1, cf2")->from("tec_customers")->where("cf1",$dni_cliente)->get();
        foreach($query->result() as $r){
            return $r->id;
        }
        return 0;
	}
	
	function forma_pago($id, $forma, $monto=0, $store_id){
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

                //$blue = $this->db->get_compiled_insert("tec_payments");                    
                //echo($blue);
                if(!$this->db->insert("tec_payments", $ar_i)){
                    //die($this->db->error());
                    return false;
                }else{
                    return true;
                }
            }
        }
        return false;
	}
	
	function correlativo($serie){
		//$cSql = "select max(correlativo) maximo from tec_sales where serie = '$serie' and anulado!='1'";
		//$query = $this->db->query($cSql);
		
		$this->db->select_max('correlativo');
		$this->db->where('serie',$serie);
		$this->db->where('correlativo is not null');
		$this->db->where('anulado!=','1');
		
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
		$this->db->select('a.tipoDoc, e.descrip tipo_documento,concat(a.serie,\'-\',a.correlativo) recibo, a.customer_name razon, concat(d.cf1,\' \',d.cf2) doc_personal, date(a.date) as fecha, a.id, a.total, a.total_discount, a.total_tax, a.grand_total, b.product_id, if(b.discount is null,0,b.discount) discount, c.name, b.quantity, b.unit_price, b.net_unit_price, (b.net_unit_price - if(b.discount is null,0,b.discount))*b.quantity as subtotal');
		$this->db->from('tec_sales as a');
		$this->db->join('tec_sale_items as b','a.id=b.sale_id','left');
		$this->db->join('tec_products as c','b.product_id=c.id', 'left');
		$this->db->join('tec_customers as d','a.customer_id=d.id','left');
		$this->db->join('tec_tipos_doc as e','a.tipoDoc = e.id','left');
		$this->db->where('a.id',$idx);
		
        /*$gn = fopen("el_query.txt","w");
        if($gn){
            fputs($gn, $this->db->get_compiled_select());
            fclose($gn);
            die("Norkas...");
        }else{
            die("Familias...");
        }*/

        $query = $this->db->get();
		
		return $query;
	}

    public function enviar_doc_sunat($sale_id, $data, $items){

        // Token que sale del Loguin de la Empresa.

        $cToken = "Bearer ";

        $result = $this->db->select("dato")->where("name","TOKEN")->get("tec_variables")->result();
        foreach($result as $r){
            $cToken .= $r->dato;
        }

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

            // Subvariables aun por definir:
            $serie      = $data["serie"];
            $tip_forma  = "Contado";  // $data["forma_pago"];
            $fecha_emi  = date("Y-m-d") . "T" . date("H:i:s");
            $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
            $icbper     = 0;
            
            $query = $this->db->query("select a.id, a.date, a.customer_id, a.customer_name, a.total, a.status, a.tipoDoc, a.grand_total,
                c.cf1, c.cf2, 
                b.id id_items, 
                b.product_id, 
                b.product_name,
                b.quantity,
                b.net_unit_price,
                b.tax,
                b.real_unit_price,
                b.subtotal
                from tec_sales a
                inner join tec_sale_items b on a.id = b.sale_id
                inner join tec_customers c on a.customer_id = c.id
                inner join tec_products d on b.product_id = d.id
                where a.id = $sale_id");
            
            foreach ($query->result() as $r){
                $numDoc         = $r->cf1;
                $grand_total    = $r->grand_total * 1;
                $total          = $r->total * 1;
                $tax            = $r->tax;
                $Cliente        = $r->customer_name;
                $codProdSunat   = "";
            }

            $nTotal             = $total;

            // Variables segun la API:
            $Cliente            = $data["customer_name"];
            $direccion_cliente  = "sin direccion"; 

            $mtoOperGravadas    = round($total, 2); //200.2       
            $mtoIGV             = round($total * $porcentajeIgv / 100,2);
            $icbper             = round($icbper * 1, 2); //0.8
            $valorVenta         = round($total, 2); //200.2
            $totalImpuestos     = $mtoIGV; // 36.84
            
            $redondeo           = 0; // 0.04

            //tipoOperacion: 0101: venta interna

            $campus1 = "{
              \"ublVersion\": \"2.1\",
              \"tipoOperacion\": \"0101\", 
              \"tipoDoc\": \"03\",
              \"serie\": \"$serie\",
              \"correlativo\": \"$correlativo\",
              \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
              \"formaPago\": {
                \"moneda\": \"PEN\",
                \"tipo\": \"$tip_forma\"
              },
              \"tipoMoneda\": \"PEN\",
              \"client\": {
                \"tipoDoc\": \"1\",
                \"numDoc\": \"$numDoc\",
                \"rznSocial\": \"$Cliente\",
                \"address\": {
                  \"direccion\": \"$direccion_cliente\",
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

            //   
            
            $acu_mtoBaseIgv = 0;
            $campus4 = "";
            foreach ($query->result() as $r){
                
                $codProducto        = "P" . $r->product_id; //$r->codProdSunat;
                //$unidad             = $items[0];
                $descripcion        = $r->product_name;
                $cantidad           = round($r->quantity,0);
                $mtoValorUnitario   = round($r->net_unit_price,2)*1;
                $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
                $mtoBaseIgv         = round($cantidad * $mtoValorUnitario,2);
                $porcentajeIgv      = is_null($r->tax) ? $porcentajeIgv * 1: $r->tax * 1;
                $igv                = round($mtoBaseIgv * ($porcentajeIgv/100),2); // round($r->subtotal - round($r->net_unit_price,2),2);
                $tipAfeIgv          = 10;
                $totalImpuestos_    = round($r->net_unit_price * ($porcentajeIgv/100) * ($r->quantity * 1),2);
                //echo $r->net_unit_price . "<br>";
                //echo $porcentajeIgv . "<br>";
                //echo $r->quantity . "<br>";
                
                
                $igvX               = 1 + ($porcentajeIgv/100);
                $mtoPrecioUnitario  = $this->fm->floor_dec($r->net_unit_price * $igvX, 2);           

                $acu_mtoBaseIgv += $mtoBaseIgv;   

                $campus4 .= "{
                  \"codProducto\": \"$codProducto\",
                  \"unidad\": \"NIU\",
                  \"descripcion\": \"$descripcion\",
                  \"cantidad\": $cantidad,
                  \"mtoValorUnitario\": $mtoValorUnitario,
                  \"mtoValorVenta\": $mtoValorVenta,
                  \"mtoBaseIgv\": $mtoBaseIgv,
                  \"porcentajeIgv\": $porcentajeIgv,
                  \"igv\": $igv,
                  \"tipAfeIgv\": $tipAfeIgv,
                  \"totalImpuestos\": {$totalImpuestos_},
                  \"mtoPrecioUnitario\": $mtoPrecioUnitario
                },";
            }
            // Se asume que si o si hay items, por tanto se quita la ultima coma:
            $campus4 = substr($campus4,0,strlen($campus4)-1);

            $mtoImpVenta = round($acu_mtoBaseIgv * (1+($porcentajeIgv/100)),2);
            $subTotal =  $mtoImpVenta;   

            $campus3 = "\"mtoOperGravadas\": $mtoOperGravadas,
              \"mtoIGV\": $mtoIGV,
              \"icbper\": $icbper,
              \"valorVenta\": $valorVenta,
              \"totalImpuestos\": $totalImpuestos,
              \"subTotal\": $subTotal,
              \"redondeo\": $redondeo,
              \"mtoImpVenta\": $mtoImpVenta,
              \"details\": [";

            
            $cValor         = $mtoImpVenta . "";
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

            //die($campos);
            $url = "https://facturacion.apisperu.com/api/v1/invoice/send";

        }elseif($tipo_documento == '1'){ //Factura

            // Subvariables aun por definir:
            $serie      = $data["serie"];
            $tip_forma  = "Contado";
            $fecha_emi  = date("Y-m-d") . "T" . date("H:i:s");
            $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
            $icbper     = 0;

            // tipoDoc : 01: Factura, 03: BV

            //$correlativo        = $this->correlativo($tipo_documento);

            $query = $this->db->query("select a.id, a.date, a.customer_id, a.customer_name, a.total, a.status, a.tipoDoc, a.grand_total,
                c.cf1, c.cf2, 
                b.id id_items, 
                b.product_id, 
                b.product_name,
                b.quantity,
                b.net_unit_price,
                b.tax,
                b.real_unit_price,
                b.subtotal
                from tec_sales a
                inner join tec_sale_items b on a.id = b.sale_id
                inner join tec_customers c on a.customer_id = c.id
                inner join tec_products d on b.product_id = d.id
                where a.id = $sale_id");

            
            foreach ($query->result() as $r){
                $numDoc         = $r->cf2;
                //$grand_total    = $r["grand_total"];
                $total          = $r->total;
                $tax            = is_null($r->tax) ? $porcentajeIgv : $r->tax;
                $Cliente        = $r->customer_name;
                $codProdSunat   = ""; //$r->codProdSunat;
                //$fecha_venc     = $r->fec 
            }

            $nTotal             = $total * (1 + ($tax/100)) * 1;
            $nTotal             = round($nTotal,2);

            // Variables segun la API:
            $Cliente            = $data["customer_name"];
            $direccion_cliente  = "sin direccion"; 

            $mtoOperGravadas    = round($total, 2); //200.2       
            //$mtoIGV             = round($total*$product_tax/100, 2); //36.04
            //$mtoIGV             = round($total * $porcentajeIgv / 100,2);
            $icbper             = round($icbper * 1, 2); //0.8
            //$valorVenta         = round($total, 2); //200.2
            //$totalImpuestos     = $mtoIGV; // 36.84
            
            $subTotal           = $nTotal; // 237.04
            $redondeo           = 0; // 0.04
            $mtoImpVenta        = $nTotal; // 237
            $mtoOperExoneradas  = 0;

            //tipoOperacion: 0101: venta interna
            $campus1 = "{
              \"ublVersion\": \"2.1\",
              \"fecVencimiento\": \"" . $fecha_emi . "-05:00\",
              \"tipoOperacion\": \"0101\", 
              \"tipoDoc\": \"01\",
              \"serie\": \"$serie\",
              \"correlativo\": \"$correlativo\",
              \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
              \"formaPago\": {
                \"moneda\": \"PEN\",
                \"tipo\": \"$tip_forma\"
              },
              \"tipoMoneda\": \"PEN\",
              \"client\": {
                \"tipoDoc\": \"6\",
                \"numDoc\": $numDoc,
                \"rznSocial\": \"$Cliente\",
                \"address\": {
                  \"direccion\": \"$direccion_cliente\",
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

            
            $campus4                = "";
            $acu_mtoBaseIgv = $mtoOperGravadas  = $mtoIGV = $valorVenta = $acu_subTotal = $acu_totalImpuestos = 0;
            foreach ($query->result() as $r){
                
                $codProducto        = "P" . $r->product_id; //$r->codProdSunat;
                $descripcion        = $r->product_name;
                $cantidad           = round($r->quantity,0);
                $mtoValorUnitario   = round($r->net_unit_price,2)*1;
                $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
                $mtoBaseIgv         = round($cantidad * $mtoValorUnitario,2);
                $porcentajeIgv_     = $r->tax*1 > 0 ? $r->tax*1 : $porcentajeIgv;
                $igv                = round($mtoBaseIgv * ($porcentajeIgv_/100),2); // round($r->subtotal - round($r->net_unit_price,2),2);
                
                $tipAfeIgv          = 10;
                $totalImpuestos     = $igv;
                
                $igvX               = 1 + ($porcentajeIgv_/100);
                $mtoPrecioUnitario  = $this->fm->floor_dec($r->net_unit_price * $igvX, 2);           

                $acu_mtoBaseIgv += $mtoBaseIgv; 

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
                $acu_subTotal       += $mtoPrecioUnitario * $cantidad;
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
            

            $cValor         = $acu_subTotal . "";
            $pos            = strpos($cValor, ".");
            $valor_entero   = substr($cValor,0,$pos);
            $valor_dec      = substr($cValor,$pos+1);

            $valor_dec = substr($valor_dec . "00",0,2);

            //die("valor entero:".$valor_entero.",valor_decimal:".$valor_dec);
            $en_letras =  "Son " . $this->fm->convertir($valor_entero) . " y $valor_dec/100 Soles";
            //$en_letras = "Son Ciento noventa y nueve solesssss";

            $campus5 = "],
              \"legends\": [
                {
                  \"code\": \"1000\",
                  \"value\": \"$en_letras\"
                }
              ]
            }";
            
            $campos = $campus1 . $campus2 . $campus3 . $campus4 . $campus5;

            $url = "https://facturacion.apisperu.com/api/v1/invoice/send";

        }elseif($tipo_documento == 'Nota_de_credito' || $tipo_documento == 'Nota_de_debito'){

            $campos = $this->Notas_varias($sale_id);

            $url = "https://facturacion.apisperu.com/api/v1/note/send";
        }

        //echo($campos);
        //die();

        $nombre_file    = "ultimo.txt";
        $gestor         = fopen($nombre_file,"w");
        fputs($gestor, $campos);
        fclose($gestor);
        $response = "";
        
            $cToken = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDQ2ODAwMzQsImV4cCI6NDc5ODI4MDAzNCwidXNlcm5hbWUiOiJmbGF2aW9tb3Jlbm96IiwiY29tcGFueSI6IjIwNjA1NDk1MDYxIn0.Z-zOuSgvNOEI-UfxDmRj-JliXCS1Pe0_A0MuzMCkpO-K5FDq7bWqfMwMGMt6Byq3uBN_zfeBjtKbmvlmGQnTw-UCrJwQG3s4MItVo_WdFZ2-Nh0EJPUFGaJMnGGJfV2sNBmVcMAyhO401X-THbUaLhpvpKu3NwIfAXRm12UC1zjhpiWzTHs6J8UC7wOGYvYQ4-4fGO_HV2hcg4f5CsdcmS0Kd_Y46EZ-pSs78gvAxobqYjN1n3IGCq7gCMSxwvVZ2x0bfQY4zbtNNG2EgLaCcOhkMJiq4CW6hbHR_rNTICtxdH_3NX-A78GfqCrNy222YCU7riCx8_ahJ4Z8rQEVALLTHhmZdNShfEOSc7CLEyQgvQOmTL0bSdSc7SyVbPm3cEkI72L9oHItCOJShrWXQemEQZ5JsDO4Fx3LnN35z1IUPasKsjuid2YqgX5nWa3mxWo4IIST9LOe1Yv-vPBEM_0fNa3rDFP8Yb8GgnecaJHXee_z4OVk6NlpMsVq3DWpOwPUH5LiR5ulwWGIWv0Y45O8Gn8KuKzTziofA0vuPYr0_VFP7m356tJE8QoZ8G6GpwsR_mGwsQzh3C9RLaWZsjY5pZl39zJsrjBeeQ6guPvAmAqZEUhbnjo43aB6psEBbjN8SPnHIioxveRctWsn7HBVtgxR0wKi6VZQZl1ID2k";

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

            //die($response);

            curl_close($curl);
        
        return $response;
    }

}
