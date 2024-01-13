<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
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
		$empresa_id	= '1'; //$_POST["empresa"];
		$usuario 	= $_POST["usuario"];
		$pass 		= $_POST["pass"];

		$this->db->select("a.id, a.username, a.store_id, a.group_id, b.name nombre_tienda");
		$this->db->from("tec_users a");
		$this->db->join("tec_stores b","a.store_id = b.id");
		$this->db->where("a.username",$usuario);
		$this->db->where("a.password",$pass);
		$query = $this->db->get();

		//$cSql = "select username, store_id, group_id from tec_users where username='$usuario' and password='$pass'";
		//$query = $this->db->query($cSql);

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
			//die("Mi inventario vigente:" . $_SESSION["inventario_vigente"]);
			$data['title'] 			= 'Ingresos a Caja:';
			$data['mensaje'] 		= $data['alerta'] = "";
			
			//$this->template->load('view_layout', 'inicial', $data);
			$this->template->load('production/index', 'inicial', $data);
		}

		if(!$bandera){
			sleep(2);
			$this->load->view('login');
		}
	}

	/*public function inicial(){
		$data['title'] = 'Ingresos a Caja:';
		$data['mensaje'] = $data['alerta'] = "";
		$this->template->load('view_layout', 'inicial', $data);
	}*/

	public function inicial(){
		$data['title'] = 'Sistema Inicial:';
		$data['mensaje'] = $data['alerta'] = "";
		$this->template->load('production/index', 'inicial', $data);
	}

	public function cierra_sesion(){
		session_destroy();
		$this->load->view('login');
	}

	function home(){
		if(isset($_SESSION['store_id'])){

			$store_id = $_SESSION['store_id'];
			$factor = 0.08;
			$cSql = "select z.fecha, z.dia_semana, z.totales, concat('<div',' style=','\'width:',round(z.totales*" . $factor . ",0),'px;','background-color:',if(z.totales>=500,'green','gray'),'\'','>.</div>') barras
			from (
				select date(`date`) fecha, 
				CONCAT(ELT(WEEKDAY(date) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', '<b>Sabado</b>', '<b>Domingo</b>')) as dia_semana,
				round(sum(grand_total),2) totales 
				from tec_sales a where a.store_id = {$store_id}
				group by date(`date`) order by date(`date`) desc limit 28
			) z order by z.fecha desc";
			$query = $this->db->query($cSql);
			$cad = "<table border='1' style=\"margin:15px;border-color:rgb(180,180,180)\">";
			$estilo1 = "padding:8px";
			foreach($query->result() as $r){
				$cad .= "<tr>";
				$cad .= $this->fm->celda($r->fecha,'0',$estilo1);
				$cad .= $this->fm->celda($r->dia_semana,'0',$estilo1);
				$cad .= $this->fm->celda($r->totales,'2',$estilo1);
				$cad .= $this->fm->celda($r->barras,'0',$estilo1);
				$cad .= "</tr>";
			}
			$cad .= "</table>";
			$this->data["mitabla"] = $cad;

			$this->data['title'] = 'Sistema :';
			$this->data['mensaje'] = $this->data['alerta'] = "";
			

			// Creando tabla de categoria por Semana

			$cSql = "select week(a.date) as semana, date_format(convert(a.date,date) , '%d-%m-%Y') fecha, round(sum(grand_total),2) totales 
				from tec_sales a 
				where a.date > date_add(curdate(),interval - 38 day)
				group by semana limit 4";

			$cad = "";

			$query = $this->db->query($cSql);

			$cad = "<table class=\"table\" border='1' style=\"margin:15px;border-color:rgb(180,180,180)\">";
			$cad .= "<tr><th>Semana</th><th>Fecha</th><th>Total</th></tr>";
			$estilo1 = "padding:8px";
			foreach($query->result() as $r){
				$cad .= "<tr>";
				$cad .= $this->fm->celda($r->semana,'1',$estilo1);
				$cad .= $this->fm->celda($r->fecha,'1',$estilo1);
				$cad .= $this->fm->celda(number_format($r->totales,2),'2',$estilo1);
				$cad .= "</tr>";
			}
			$cad .= "</table>";

			$this->data["tabla_semanal"] = $cad;

			// Verificandolo por Categrias ----------------------------------------

				// Hallando el total de la consulta
			$cSql = "select round(sum(b.net_unit_price*b.quantity),2) subtotal
			from tec_sales a
			inner join tec_sale_items b on a.id=b.sale_id
			inner join tec_products c on b.product_id = c.id
			left join tec_categories d on c.category_id = d.id
			where date(a.date) > curdate() - 31";
			$query = $this->db->query($cSql);
			foreach($query->result() as $r){
				$totaye = floatval($r->subtotal);
			}

			$cSql = "select c.category_id, d.name categoria, round(sum(b.net_unit_price*b.quantity),2) subtotal, round(sum(b.net_unit_price*b.quantity)*100/$totaye,2) porcentaje
			from tec_sales a
			inner join tec_sale_items b on a.id=b.sale_id
			inner join tec_products c on b.product_id = c.id
			left join tec_categories d on c.category_id = d.id
			where date(a.date) > curdate() - 31
			group by c.category_id, d.name
			order by round(sum(b.net_unit_price*b.quantity),2) desc";

			$query = $this->db->query($cSql);
			$cad = "<table class=\"table\" border='1' style=\"margin:15px;border-color:rgb(180,180,180)\">";
			$cad .= "<tr><th>Categoria</th><th>Venta Total</th><th>%</th></tr>";
			$estilo1 = "padding:8px";
			foreach($query->result() as $r){
				$cad .= "<tr>";
				$cad .= $this->fm->celda($r->categoria,'1',$estilo1);
				$cad .= $this->fm->celda(number_format($r->subtotal,2),'2',$estilo1);
				$cad .= $this->fm->celda($r->porcentaje,'1',$estilo1);
				$cad .= "</tr>";
			}
			$cad .= "</table>";

			$this->data["categorias"] = $cad;

			$this->template->load('production/index', 'welcome/whome', $this->data);
		}else{
			$this->index();
		}
	}

}
