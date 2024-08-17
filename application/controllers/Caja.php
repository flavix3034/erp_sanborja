<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Caja extends CI_Controller {

    function __construct(){
        parent::__construct();
        session_start();
        $this->load->helper('url');
        $this->load->model('welcome_model');
        $this->load->model("caja_model");
    }

	public function index()
	{

	}

	public function ingreso_caja(){
		$data['title'] 		= 'Ingresos a Caja:';
		$data['mensaje'] 	= $data['alerta'] = "";
		$this->template->load('view_layout', 'caja/ingreso_caja', $data);

		if ($store_id == ''){ 
			$store_id = $_SESSION["store_id"]; 
		}
	    $this->data['page_title'] = "Apertura de Caja";
	    $this->template->load('production/index', 'caja/index', $this->data);
	    
	}

	public function enviar_correo(){
		$cabeceras = 'From: flaviojuliom@gmail.com' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();
		mail("flaviomorenoz@gmail.com", "Mi situacion es poco comoda", "La ramificacion del Arbol es muy importante.", $cabeceras);
		echo "Se envía";
	}

	function comillar($cad=""){
		return '"' . $cad . '",';
	}

	function ver_cajas(){
		$this->data['page_title'] = "Cajas";
		$this->template->load('production/index', 'caja/ver_cajas', $this->data);
	}

	function get_ver_cajas($caja_id=1){
		$cSql = "select id, fecha, caja_id, responsable, monto_ini, monto_fin, if(estado_cierre = 1,'Cerrado','Abierto') estado_cierre,".
			" monto_calculado, diferencia, ventas, compras".
			" from tec_registro_cajas".
			" where caja_id = ? order by id desc limit 45";
		$result 	= $this->db->query($cSql, array($caja_id))->result_array();
		foreach($result as &$r){
			if($r["estado_cierre"] == 'Abierto'){

				// Ventas en efectivo
				$r["vtas_efectivo"] = $this->ventas_efectivo($caja_id, $r["fecha"]);

				// Compras en efectivo
				$r["compras_efectivo"] = $this->compras_efectivo($caja_id, $r["fecha"]);

				$r["monto_calculado"] = floatval($r["monto_ini"]) + $r["vtas_efectivo"] - $r["compras_efectivo"];
				//$r["monto_fin"] = $this->calcular_cierre_caja($r["id"],2);

				$r["accion"] = "&nbsp;&nbsp;<a href='#' onclick='proceso_cerrar(" . $r["id"] . "," . $r["vtas_efectivo"] . "," . $r["compras_efectivo"] . "," . $r["monto_calculado"] . ")' title='cerrar'><span class='glyphicon glyphicon-log-out' style='font-size:16px'></span></a>";
				$r["accion"] .= "&nbsp;<span id='casilla_cierre_" . $r["id"] . "'></span>";
			
			}else{
				$r["accion"] 			= '';
				$r["vtas_efectivo"] 	= $r["ventas"];
				$r["compras_efectivo"] 	= $r["compras"];

				//$r["accion"] = "&nbsp;&nbsp;<a href='#' onclick='proceso_cerrar(" . $r["id"] . ")' title='cerrar'><span class='glyphicon glyphicon-edit' style='font-size:16px'></span></a>";
				//"&nbsp;<span id='casilla_cierre_" . $r["id"] . "'></span>";
			}

		}
		$ar_campos 	= array("id", "fecha", "caja_id", "responsable","monto_ini", "vtas_efectivo", "compras_efectivo", "monto_calculado", "monto_fin", "diferencia", "estado_cierre", "accion");
		//$ar_campos 	= array("id", "id", "id", "id","id", "", "compras_efectivo", "monto_fin", "estado_cierre", "accion");
		echo $this->fm->json_datatable($ar_campos, $result);
	}

	private function ventas_efectivo($store_id, $fecha){
		$cSql = "select date(a.`date`) fecha, sum(c.amount) amount 
		from tec_sales a
		inner join tec_payments c on a.id = c.sale_id
		where c.paid_by = 'cash' and a.anulado != '1' and date(a.`date`) = '$fecha'
		group by date(a.date)
		order by date(a.date)";

		$query = $this->db->query($cSql);

		$rpta = 0;
		foreach($query->result() as $r){
			$rpta = $r->amount * 1;
		}
		return $rpta;
	}

	private function compras_efectivo($store_id, $fecha){
		// Compras
		$cSql = "select a.total from tec_compras a where date(a.fecha) = '$fecha' and a.store_id = $store_id and a.tipo_pago = 1";  // 1:caja, 2:banco
		
		//die($cSql);
		$query3 = $this->db->query($cSql); // ,array($fecha, $store_id)

		$result 		= $query3->result_array();
		$nTotal_c 		= 0;
		foreach($result as $r){
			$nTotal_c 	+= floatval($r["total"]); 
		}
		//die($nTotal_c);
		return $nTotal_c;
	}

	function aperturar_caja(){
		$this->data['page_title'] = "Apertura de Caja";
		if (!$this->caja_model->existe_cajas_abiertas()){
			$this->template->load('production/index', 'caja/aperturar_caja', $this->data);
		}else{
			$this->data['page_title'] 	= "Cajas";
			$this->data['msg'] 			= "Existen cajas abiertas, primero de cerrar cajas de dias anteriores";
			$this->data['rpta_msg'] 	= "danger";
			$this->template->load('production/index', 'caja/ver_cajas', $this->data);
		}
	}

	function cerrar_caja($id, $cMontoFin, $cMontoCalculado, $cVenta, $cCompra){ // respuesta en texto
		
		$cSql = "select id, store_id, fecha, caja_id, responsable, monto_ini, monto_fin, if(estado_cierre = 1,'Cerrado','Abierto') estado_cierre".
			" from tec_registro_cajas".
			" where id = ? order by id desc limit 45";
		$result 	= $this->db->query($cSql, array($id))->result_array();
		foreach($result as $r){
			$fecha = $r["fecha"];
			$store_id = $r["store_id"];
			$monto_ini = floatval($r["monto_ini"]);
		}
		
		// Ventas en efectivo
		//$vtas_efectivo = $this->ventas_efectivo($store_id, $fecha);
		$vtas_efectivo = floatval($cVenta);

		// Compras en efectivo
		//$compras_efectivo = $this->compras_efectivo($store_id, $fecha);
		$compras_efectivo = floatval($cCompra);

		$monto_fin = floatval($cMontoFin);
		//$monto_fin = floatval($monto_ini) + $vtas_efectivo - $compras_efectivo;
		//$r["monto_fin"] = $this->calcular_cierre_caja($r["id"],2);

		$total = $monto_fin;
		//$total = $this->calcular_cierre_caja($id,2);

		if($total != 'KO'){

			// Calculando la diferencia
			$diferencia = floatval($cMontoCalculado) - floatval($cMontoFin);

			$cSql = "update tec_registro_cajas set estado_cierre = 1, monto_fin = $total, monto_calculado = $cMontoCalculado, diferencia = $diferencia, ventas=$vtas_efectivo, compras=$compras_efectivo where id = $id";
			//die($cSql);
			$query = $this->db->query($cSql,array($total,$id));
			if($this->db->affected_rows() > 0){
				echo "OK";
			}else{
				echo "KO";
			}

		}else{
			echo "KO";
		}
	}

	function calcular_cierre_caja($id,$tipo_rpta_json = 1){ // Calcula y devuelve el monto de cierre

		$existe = false;
		$store_id = 1;
		$cSql = "select id, fecha, caja_id from tec_registro_cajas where id = ?";
		$query = $this->db->query($cSql,array($id));
		foreach($query->result() as $r){
			$fecha 		= $r->fecha;
			$caja_id 	= $r->caja_id; 
			$existe = true;
		}

		if($existe){
			// Apertura de Caja
			$cSql = "select a.id, a.caja_id, a.fecha, a.monto_ini, a.monto_fin, a.estado_cierre from tec_registro_cajas a
				where a.fecha = ? and a.caja_id = ?";
			$query1 = $this->db->query($cSql,array($fecha, $caja_id));

			// Ventas
			/*$cSql = "select a.id, a.tipoDoc, concat(a.serie,'-',a.correlativo) comprobante, a.grand_total  
				from tec_sales a
				where a.anulado != '1' and date(a.date) =  ? and a.store_id = ?";
			$query2 = $this->db->query($cSql,array($fecha, $store_id));

			// Compras
			$cSql = "select a.total
				from tec_compras a
				where date(a.fecha) =  ? and a.store_id = ?";
			$query3 = $this->db->query($cSql,array($fecha, $store_id));*/
			
			$result 		= $query1->result_array();
			$monto_ini = 0;
			foreach($result as $r){ 
				$monto_ini = floatval($r["monto_ini"]); 
			}

			/*$result 		= $query2->result_array();
			$nTotal 		= 0;
			foreach($result as $r){
				$nTotal += floatval($r["grand_total"]);
			}

			$result 		= $query3->result_array();
			$nTotal_c 		= 0;
			foreach($result as $r){
				$nTotal_c 	+= floatval($r["total"]); 
			}*/

			// Ventas en efectivo
			$vtas_efectivo = $this->ventas_efectivo($store_id, $fecha);

			// Compras en efectivo
			$compras_efectivo = $this->compras_efectivo($store_id, $fecha);

			if ($tipo_rpta_json == 1){
				echo ($monto_ini + $vtas_efectivo - $compras_efectivo);
			}else{
				return ($monto_ini + $vtas_efectivo - $compras_efectivo);
			}
		}else{
			if ($tipo_rpta_json == 1){ echo "KO"; }else{ return "KO"; }
		}
	}

	function save_apertura_caja($cFecha,$cMonto,$cResponsable){
		if ( is_numeric($cMonto) ){
			$ar = array();
			$ar["caja_id"] = 1;
			$ar["fecha"] = $cFecha;
			$ar["monto_ini"] = $cMonto;
			$ar["responsable"] = $cResponsable;
			
			// Verifico que no exista aun
			$query = $this->db->select("*")->where("fecha",$cFecha)->get("tec_registro_cajas");
			$existe = false;
			foreach($query->result() as $r){
				$existe = true;
			}

			if (!$existe){
				if($this->db->set($ar)->insert("tec_registro_cajas")){
					$id = $this->db->insert_id();
					$query = $this->db->select("*")->where("id",$id)->get("tec_registro_cajas");
					foreach($query->result() as $r){
						echo "Fecha : {$r->fecha}, Monto Inicial: {$r->monto_ini}";
					}
				}
			}else{
				echo "KO";
			}
		}else{
			echo "KO";
		}
	}

	function save_cierre_caja($id){
		// Verifico que aun no haya sido cerrado
		$query = $this->db->select("*")->where("id",$id)->get("tec_registro_cajas");
		$existe = false;
		foreach($query->result() as $r){
			//
			if($r->estado_cierre == 1){
				$existe = true;	
			}
		}

		if (!$existe){
			$ar = array("estado_cierre"=>1);
			$this->db->where("id",$id)->update("tec_registro_cajas",$ar);
			echo "OK";
		}else{
			echo "KO";
		}
	}

	function cuadre($caja_id=1, $store_id=1, $fecha=''){
		if ($fecha == ''){
			$fecha_actual = date('Y-m-d');

			// Retrocede un día
			$fecha = date('Y-m-d', strtotime('-1 day', strtotime($fecha_actual)));
		}
			
		$this->data['page_title'] 	= "Cuadre de Caja ($fecha)";

		// Apertura de Caja
		$cSql = "select a.id, a.caja_id, a.fecha, a.monto_ini, a.monto_fin, a.estado_cierre from tec_registro_cajas a
			where a.fecha = ? and a.caja_id = ?";
		$query = $this->db->query($cSql,array($fecha, $caja_id));
		$this->data["query1"] 		= $query;

		// Ventas
		$cSql = "select a.id, a.tipoDoc, concat(a.serie,'-',a.correlativo) comprobante, a.grand_total  
			from tec_sales a
			where a.anulado != '1' and date(a.date) =  ? and a.store_id = ?";
		$query = $this->db->query($cSql,array($fecha, $store_id));
		$this->data["query2"] 		= $query;

		// Compras
		$cSql = "select a.total  
			from tec_compras a
			where date(a.fecha) =  ? and a.store_id = ?";
		$query = $this->db->query($cSql,array($fecha, $store_id));
		$this->data["query3"] 		= $query;

		$this->template->load('production/index', 'caja/cuadre', $this->data);
	}

	function analisis_mensual(){
		if(isset($_POST["anno"])){
			$this->data['page_title'] = "ANALISIS MENSUAL";
			$this->data["anno"] 	= $_POST["anno"];
			$this->data["mes"] 		= $_POST["mes"];
			$this->template->load('production/index', 'caja/rpta_analisis_mensual', $this->data);
		}else{
			$this->data['page_title'] = "ANALISIS MENSUAL";
			$this->template->load('production/index', 'caja/analisis_mensual', $this->data);
		}
	}
}
