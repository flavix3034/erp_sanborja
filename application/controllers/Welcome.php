<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct(){
        parent::__construct();

        session_start();

        $this->load->helper('url');

        $this->load->model('welcome_model');

        $this->load->model('inventarios_model');
    }

	public function index()
	{
		$this->load->view('login');
	}

	public function inicia_sesion(){
		$empresa_id	= '1';
		$usuario 	= $_POST["usuario"];
		$pass 		= $_POST["pass"];

		$this->db->select("a.id, a.username, a.store_id, a.group_id, b.name nombre_tienda");
		$this->db->from("tec_users a");
		$this->db->join("tec_stores b","a.store_id = b.id");
		$this->db->where("a.username",$usuario);
		$this->db->where("a.password",$pass);
		$query = $this->db->get();

		$bandera = false;
		foreach($query->result() as $r){
			$bandera = true;
			sleep(0.5);
			$_SESSION["empresa_id"] = $empresa_id;
			$_SESSION["usuario"] 	= $usuario;
			$_SESSION["store_id"] 	= $r->store_id;
			$_SESSION["nombre_tienda"] 	= $r->nombre_tienda;
			$_SESSION["group_id"] 	= $r->group_id;
			$_SESSION["user_id"] 	= $r->id;
			$_SESSION["inventario_vigente"] = $this->inventarios_model->inventario_vigente($_SESSION["store_id"]);
			$data['title'] 			= 'Ingresos a Caja:';
			$data['mensaje'] 		= $data['alerta'] = "";

			$this->template->load('production/index', 'inicial', $data);
		}

		if(!$bandera){
			sleep(2);
			$this->load->view('login');
		}
	}

	public function cierra_sesion(){
		session_destroy();
		$this->load->view('login');
	}

	function home(){
		if(!isset($_SESSION['store_id'])){
			$this->index();
			return;
		}
		$this->data['page_title'] = 'Dashboard';
		$this->template->load('production/index', 'welcome/dashboard', $this->data);
	}

	function get_dashboard_data(){
		if(!isset($_SESSION['store_id'])){
			header('Content-Type: application/json');
			echo json_encode(array("rpta" => "error"));
			return;
		}

		$store_id = intval($_SESSION['store_id']);
		$hoy = date('Y-m-d');

		// ===== 1. ESTADO GENERAL =====

		// Ventas del dia
		$r = $this->db->query("SELECT COALESCE(SUM(grand_total),0) AS total
			FROM tec_sales WHERE date(date) = CURDATE() AND anulado != '1' AND store_id = ?", array($store_id))->row();
		$ventas_dia = floatval($r->total);

		// Ganancias del dia (ventas sin IGV - costos)
		$r = $this->db->query("SELECT
			COALESCE(SUM(b.net_unit_price * b.quantity), 0) AS ventas,
			COALESCE(SUM(COALESCE(c.precio_sin_igv,
				IF(si.es_tercerizado = 1,
					IF(si.tipo_doc_proveedor = 5, si.costo_proveedor, si.costo_proveedor / (1 + 18/100)),
					NULL
				), 0) * b.quantity), 0) AS costos
			FROM tec_sales a
			INNER JOIN tec_sale_items b ON a.id = b.sale_id
			LEFT JOIN tec_compra_items c ON b.compra_id = c.compra_id
				AND b.product_id = c.product_id
				AND COALESCE(b.variant_id, 0) = COALESCE(c.variant_id, 0)
			LEFT JOIN tec_servicios_tecnicos st ON st.sale_id = a.id
			LEFT JOIN tec_servicio_items si ON si.servicio_id = st.id
				AND si.product_id = b.product_id
				AND COALESCE(si.variant_id, 0) = COALESCE(b.variant_id, 0)
			WHERE date(a.date) = CURDATE() AND a.anulado != '1' AND a.store_id = ?", array($store_id))->row();
		$ganancias_dia = round(floatval($r->ventas) - floatval($r->costos), 2);

		// Caja Chica
		$r_caja = $this->db->query("SELECT saldo_actual, estado FROM tec_cajachica_periodos
			WHERE store_id = ? AND estado = 'ABIERTO' LIMIT 1", array($store_id))->row();
		$caja_saldo = $r_caja ? floatval($r_caja->saldo_actual) : null;
		$caja_estado = $r_caja ? 'ABIERTA' : 'CERRADA';

		// Servicios activos
		$r = $this->db->query("SELECT COUNT(*) AS total FROM tec_servicios_tecnicos
			WHERE activo = '1' AND estado NOT IN ('ENTREGADO','CANCELADO')")->row();
		$servicios_activos = intval($r->total);

		// ===== 2. SERVICIO TECNICO POR ESTADO =====
		$estados_result = $this->db->query("SELECT estado, COUNT(*) AS cantidad
			FROM tec_servicios_tecnicos
			WHERE activo = '1' AND estado NOT IN ('ENTREGADO','CANCELADO')
			GROUP BY estado")->result_array();

		$servicios = array(
			'RECIBIDO' => 0,
			'EN DIAGNOSTICO' => 0,
			'EN REPARACION' => 0,
			'ESPERA REPUESTOS' => 0,
			'REPARADO' => 0
		);
		foreach($estados_result as $e){
			if(isset($servicios[$e['estado']])){
				$servicios[$e['estado']] = intval($e['cantidad']);
			}
		}

		// ===== 3. RENDIMIENTO DEL MES =====
		$r = $this->db->query("SELECT COALESCE(SUM(grand_total),0) AS ingresos, COUNT(*) AS num_ventas
			FROM tec_sales
			WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())
			AND anulado != '1' AND store_id = ?", array($store_id))->row();
		$ingresos_mes = floatval($r->ingresos);
		$num_ventas_mes = intval($r->num_ventas);
		$ticket_promedio = $num_ventas_mes > 0 ? round($ingresos_mes / $num_ventas_mes, 2) : 0;

		// Margen promedio del mes
		$r_margen = $this->db->query("SELECT
			COALESCE(SUM(b.net_unit_price * b.quantity), 0) AS ventas_netas,
			COALESCE(SUM(COALESCE(c.precio_sin_igv,
				IF(si.es_tercerizado = 1,
					IF(si.tipo_doc_proveedor = 5, si.costo_proveedor, si.costo_proveedor / (1 + 18/100)),
					NULL
				), 0) * b.quantity), 0) AS costos
			FROM tec_sales a
			INNER JOIN tec_sale_items b ON a.id = b.sale_id
			LEFT JOIN tec_compra_items c ON b.compra_id = c.compra_id
				AND b.product_id = c.product_id
				AND COALESCE(b.variant_id, 0) = COALESCE(c.variant_id, 0)
			LEFT JOIN tec_servicios_tecnicos st ON st.sale_id = a.id
			LEFT JOIN tec_servicio_items si ON si.servicio_id = st.id
				AND si.product_id = b.product_id
				AND COALESCE(si.variant_id, 0) = COALESCE(b.variant_id, 0)
			WHERE MONTH(a.date) = MONTH(CURDATE()) AND YEAR(a.date) = YEAR(CURDATE())
			AND a.anulado != '1' AND a.store_id = ?", array($store_id))->row();
		$ventas_netas = floatval($r_margen->ventas_netas);
		$costos_mes = floatval($r_margen->costos);
		$margen_promedio = $ventas_netas > 0 ? round(($ventas_netas - $costos_mes) / $ventas_netas * 100, 1) : 0;

		// ===== 4. CONTROL DEL SISTEMA =====

		// Ordenes incompletas
		$r = $this->db->query("SELECT COUNT(*) AS n FROM tec_servicios_tecnicos
			WHERE activo = '1' AND estado NOT IN ('ENTREGADO','CANCELADO')")->row();
		$ctrl_incompletas = intval($r->n);

		// Sin tecnico
		$r = $this->db->query("SELECT COUNT(*) AS n FROM tec_servicios_tecnicos
			WHERE activo = '1' AND tecnico_asignado IS NULL
			AND estado NOT IN ('ENTREGADO','CANCELADO')")->row();
		$ctrl_sin_tecnico = intval($r->n);

		// Sin diagnostico (excluye RECIBIDO que es normal no tener diagnostico aun)
		$r = $this->db->query("SELECT COUNT(*) AS n FROM tec_servicios_tecnicos
			WHERE activo = '1' AND (diagnostico IS NULL OR diagnostico = '')
			AND estado NOT IN ('ENTREGADO','CANCELADO','RECIBIDO')")->row();
		$ctrl_sin_diagnostico = intval($r->n);

		// Sin cierre financiero (reparado/entregado sin costo_final)
		$r = $this->db->query("SELECT COUNT(*) AS n FROM tec_servicios_tecnicos
			WHERE activo = '1' AND estado IN ('REPARADO','ENTREGADO')
			AND (costo_final IS NULL OR costo_final = 0)")->row();
		$ctrl_sin_cierre = intval($r->n);

		// Ordenes estancadas (>7 dias sin cambiar de estado)
		$r = $this->db->query("SELECT COUNT(*) AS n FROM tec_servicios_tecnicos
			WHERE activo = '1' AND estado NOT IN ('ENTREGADO','CANCELADO')
			AND DATEDIFF(NOW(), COALESCE(updated_at, created_at)) > 7")->row();
		$ctrl_estancadas = intval($r->n);

		// ===== 5. GRAFICO VENTAS ULTIMOS 15 DIAS =====
		$chart_ventas = $this->db->query("SELECT date(date) AS dia, ROUND(SUM(grand_total),2) AS total
			FROM tec_sales
			WHERE date(date) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
			AND anulado != '1' AND store_id = ?
			GROUP BY date(date) ORDER BY dia ASC", array($store_id))->result_array();

		// Respuesta JSON
		header('Content-Type: application/json');
		echo json_encode(array(
			"general" => array(
				"ventas_dia" => $ventas_dia,
				"ganancias_dia" => $ganancias_dia,
				"caja_saldo" => $caja_saldo,
				"caja_estado" => $caja_estado,
				"servicios_activos" => $servicios_activos
			),
			"servicios" => $servicios,
			"rendimiento" => array(
				"ingresos_mes" => $ingresos_mes,
				"num_ventas_mes" => $num_ventas_mes,
				"ticket_promedio" => $ticket_promedio,
				"margen_promedio" => $margen_promedio
			),
			"control" => array(
				"incompletas" => $ctrl_incompletas,
				"sin_tecnico" => $ctrl_sin_tecnico,
				"sin_diagnostico" => $ctrl_sin_diagnostico,
				"sin_cierre" => $ctrl_sin_cierre,
				"estancadas" => $ctrl_estancadas
			),
			"chart_ventas_15d" => $chart_ventas
		));
	}

}
