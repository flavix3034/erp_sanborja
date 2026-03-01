<?php
class Fm{
	function conectar(){
		$usuario 	= "root";
		$clave 		= "jakamoto";
		$dbname 	= "db_catalogo2";
		$conn 		= new PDO("mysql:host=localhost;dbname=$dbname", $usuario);

		/*$usuario 	= "c1980893_base2";
		$clave 		= "82gakoTIpe";
		$dbname 	= "c1980893_base2";
		$conn 		= new PDO("mysql:host=localhost;dbname=$dbname", $usuario, $clave); */

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}

	function validacion($conn, $usuario, $clave, &$tipo_usuario){
	 // ESTO ES LA VALIDACION ORIGINAL ==========================

		if($usuario == "" and $clave==""){
			$tipo_usuario = "ADMINISTRADOR";
			return true;
		}else{
			sleep(2);
			return false;
		}
	}

	function celda_simple($dato = "&nbsp;"){
		return "<td>" . $dato . "</td>";
	}

	function celda($dato="", $centrar=0, $estilo="", $cAtributo=""){
		if($dato=='0'){
			$dato = "<span style=\"color:#cccccc;\">0</span>";
		}

		$cad = "";

		$cEstilo = "";
		if(strlen($estilo)>0)
			$cEstilo = "style=\"$estilo\"";

		if($centrar==1)
			$cad .= "<td align=\"center\" $cEstilo $cAtributo>$dato</td>";
		elseif($centrar==2)
			$cad .= "<td align=\"right\" $cEstilo $cAtributo>$dato</td>";
		else
			$cad .= "<td align=\"left\" $cEstilo $cAtributo>$dato</td>";
		
		return $cad;
	}

	function  fila($cad=""){
		return "<tr>" . $cad . "</tr>";
	}

	function celda_h($dato="",$centrar=0,$estilo=""){
		$cad = "";
		
		$cEstilo = "";
		if(strlen($estilo)>0)
			$cEstilo = "style=\"$estilo\"";

		if($centrar==1){
			$cad .= "<th align=\"center\" $cEstilo>$dato</th>";
		}elseif($centrar==2){
			$cad .= "<th align=\"right\" $cEstilo>$dato</th>"; // style=\"$estilo\"
		}else{
			$cad .= "<th align=\"left\" $cEstilo>$dato</th>";
		}
		
		return $cad;
	}

	function espacio($n){
		$cad = "";
		for($i=0; $i < $n; $i++){
			$cad .= "&nbsp;";
		}
		return $cad;
	}

	function mostrado($msg,$bandera){
		if($bandera){
			echo $msg . "<br>";
		}
	}

	function traer_campo($conn, $table, $campo, $where){
		$cSql = "select $campo from $table where $where";
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
		$result = $pdo->fetchAll();
		foreach($result as $r){
			return $r[$campo];
		}
		return "";
	}

	function traer_campo2($conn, $cSql, $campo){
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
		$result = $pdo->fetchAll();
		foreach($result as $r){
			return $r[$campo];
		}
		return "";
	}

	function result($conn, $cSql, $var1=null){
		$pdo = $conn->prepare($cSql);
		$pdo->bindParam(1,$var1);
		$pdo->execute();
		return $pdo->fetchAll();
	}

    function campo_result($result,$campo){
        foreach($result as $r){
            return $r[$campo];
        }
        return "";
    }

	function alertas($mensaje="",$tipo_alerta="success"){
		$mensaje = "<div class=\"alert alert-$tipo_alerta\">$mensaje</div>";
		return $mensaje;
	}

	function obtener_ip(){
		if(getenv('HTTP_CLIENT_IP')){
			$ip = getenv('HTTP_CLIENT_IP');
		}elseif(getenv('HTTP_X_FORWARDED_FOR')){
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}elseif(getenv('HTTP_X_FORWARDED')){
			$ip = getenv('HTTP_X_FORWARDED');
		}elseif(getenv('HTTP_FORWARDED_FOR')){
			$ip = getenv('HTTP_FORWARDED_FOR');
		}elseif(getenv('HTTP_FORWARDED')){
			$ip = getenv('HTTP_FORWARDED');
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	function guardar_ip($conn, $mi_ip, $padre, $hijo){
		$mi_fecha = date("Y-m-d H:i:s");
		$cSql = "insert into visitas(padre, hijo, ip, fecha) values('$padre','$hijo','$mi_ip','$mi_fecha')";
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
	}

	function casilla($nombre, $valor_default, $size=10){
		$cad = "<input type='text' id='" . $nombre . "' name='" . $nombre . "' value='" . $valor_default . "' size='" . $size . "'>";
		return $cad;
	}

	function query_a_array($result,$key,$valor){
	    $ar = array();
	    foreach($result as $r){
	        $ar[$r[$key]] = $r[$valor];
	    }
	    return $ar;
	}

	function option($id, $cad="vacio", $valor=""){
		$selected = ""; //$codo = "";
		if(strlen($valor)>0){
			//$codo .= "valor: $valor = id: $id";
			if($valor == $id){
				$selected = " selected";
			}
		}
		return "<option value=\"$id\" " . $selected . ">" . $cad . "</option>";
	}

	function message($cad="", $alerta=0){
		if ($alerta == 0){
			$class = "success";
			$color = "rgb(250,255,230)";
		}elseif($alerta == 1){
			$class = "warning";
			$color = "rgb(255,255,225)";
		}elseif($alerta == 2){
			$class = "danger";
			$color = "rgb(255,160,140)";
		}else{
			$class = "cualquiera";
			$color = "rgb(240,240,255)";
		}
		return "<div class=\"alert-$class\" style=\"height:40px;background-color:$color;padding:9px\"><strong>" . $cad . "</strong></div>";
	}

    function ymd_dmy($cad=""){
        $n = strlen($cad);
        if($n >= 10){
            return substr($cad,8,2) . "-" . substr($cad,5,2) . "-" . substr($cad,0,4) . substr($cad,10);
        }else{
            return "vacio";
        }
    }

    function floor_dec($nU,$precision=0){
	    $cU = $nU . "";
	    $nLim = strlen($cU);
	    for($n=0; $n<$nLim; $n++){
	        if(substr($cU,$n,1)=="."){
	           $nDecimales = $nLim - $n - 1;
	           $nPos = $n;
	           
	           // Extrayendo o mejor dicho truncando.
	           $nQuitar = $nDecimales - $precision;

	           $nU = substr($cU,0,$nLim-$nQuitar)*1;
	           return $nU;
	        }
	    }
	    return $nU;
	}

	function basico($numero) {
		$valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho',
		'nueve','diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciseis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidos', 'veintitres', 'veinticuatro','veinticinco',
		'veintiséis','veintisiete','veintiocho','veintinueve');
		return $valor[$numero - 1];
	}

	function decenas($n) {
		$decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',
		70=>'setenta',80=>'ochenta',90=>'noventa');
		if( $n <= 29) return $this->basico($n);
		$x = $n % 10;
		if ( $x == 0 ) {
		return $decenas[$n];
		} else return $decenas[$n - $x].' y '. $this->basico($x);
	}

	function centenas($n) {
		$cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',
		400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
		700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
		if( $n >= 100) {
		if ( $n % 100 == 0 ) {
		return $cientos[$n];
		} else {
		$u = (int) substr($n,0,1);
		$d = (int) substr($n,1,2);
		return (($u == 1)?'ciento':$cientos[$u*100]).' '.$this->decenas($d);
		}
		} else return $this->decenas($n);
	}

	function miles($n) {
		if($n > 999) {
		if( $n == 1000) {return 'mil';}
		else {
		$l = strlen($n);
		$c = (int)substr($n,0,$l-3);
		$x = (int)substr($n,-3);
		if($c == 1) {$cadena = 'mil '. $this->centenas($x);}
		else if($x != 0) {$cadena = $this->centenas($c).' mil '. $this->centenas($x);}
		else $cadena = $this->centenas($c). ' mil';
		return $cadena;
		}
		} else return $this->centenas($n);
	}

	function millones($n) {
		if($n == 1000000) {return 'un millón';}
		else {
		$l = strlen($n);
		$c = (int)substr($n,0,$l-6);
		$x = (int)substr($n,-6);
		if($c == 1) {
		$cadena = ' millón ';
		} else {
		$cadena = ' millones ';
		}
		return $this->miles($c).$cadena.(($x > 0)? $this->miles($x):'');
		}
	}

	function convertir($n){
		switch (true) {
			case ($n >= 1 && $n <= 29) : return $this->basico($n); break;
			case ($n >= 30 && $n < 100) : return $this->decenas($n); break;
			case ($n >= 100 && $n < 1000) : return $this->centenas($n); break;
			case ($n >= 1000 && $n <= 999999): return $this->miles($n); break;
			case ($n >= 1000000): return $this->millones($n);
		}
	}

	function traza($msg){
	    $nombre_file = "traza.txt";
        $gestor = fopen($nombre_file,"a+");
        $msg .= "\n";
        fputs($gestor,$msg);
        fclose($gestor);
    }

    // DEPRECADO **********
    function menu_principal2_xxxxx($Admin, $store_id, $multi_store){ ?>
        
        <?php if ($Admin){ ?>
	        <script>
	        	function mostrar_ul(cId){
	        		cTipo = document.getElementById(cId).style.display
	        		if(cTipo != "block"){
	        			$("#"+cId).show(300)
	        			document.getElementById(cId).style.display = "block"
	        		}else{
	        			$("#"+cId).hide(300)
	        		}
	        	}
	        </script>
	        <aside><!--  class="main-sidebar" -->
	            <section class="sidebar">
	                <div class="row">
	                	<div class="col-sm-6 text-center">
	                		<img src="<?= base_url("/assets/images/logo.png") ?>" height="75">
	                		<!--<span style="text-align: center; color: white; font-weight:bold; font-size:20px">JFK System</span>-->
	                	</div>
	                </div><br>

			    	<ul style="margin-left:-30px; list-style: none;">
			    		<li>
			    			<a href="<?= base_url('dash/index'); ?>">
			    				<i class="fa fa-dashboard"></i> <span><?= 'Dashboard' ?></span>
			    			</a>
			    		</li>
			    	</ul>
			        
			    	<ul style="margin-left:-30px; list-style: none;">
			    		<li>
					    	<a href="#" id="h3">
					            <span style="font-weight:bold; color:darkred" onclick="mostrar_ul('ul_ventas')">Ventas</span>
					        </a>
					    	<ul id="ul_ventas" style="margin-left:-35px; list-style: none; display:none">
					    		<li>
					    			<a href="<?= base_url('sales/index'); ?>">
					    				<span>Listar</span>
					    			</a>
					    		</li>
					    		<li>
					    			<a href="<?= base_url('sales/add'); ?>">
					    				<span>Agregar</span>
					    			</a>
					    		</li>

					    	</ul>

					    </li>
					</ul>

					<!-- **********  C O M P R A S   **************** -->
					<ul style="margin-left:-30px; list-style: none;">
				    	<li>
				    		<a href="#" id="h3">
				            	<span style="font-weight:bold" onclick="mostrar_ul('ul_compras')">Compras</span>
				        	</a>
				    	</li>
				    	
					    <ul id="ul_compras" style="margin-left:-35px; list-style: none; display:none">
					    	<li>
					    		<a href="<?= base_url('compras/index'); ?>">
					    			<span>Listar</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('compras/add'); ?>">
					    			<span>Agregar</span>
					    		</a>
					    	</li>
					    </ul>
				    </ul>

			    	<ul style="margin-left:-30px; list-style: none;">
			    		<li>
					    	<a href="#" id="h5">
					            <span style="font-weight:bold" onclick="mostrar_ul('ul_clientes')">Clientes</span>
					        </a>
					    </li>
				    	<ul id="ul_clientes" style="margin-left:-35px; list-style: none; display:none">
				    		<li>
				    			<a href="<?= base_url('clientes/index'); ?>">
				    				<span>Listar</span>
				    			</a>
				    		</li>
				    		<li>
				    			<a href="<?= base_url('clientes/add'); ?>">
				    				<span>Agregar</span>
				    			</a>
				    		</li>
				    	</ul>
					</ul>

				    <!-- **********  P R O D U C T O S   **************** -->	
					<ul style="margin-left:-30px; list-style: none;">
						<li>
							<a href="#" id="hprod">
								<span style="font-weight:bold" onclick="mostrar_ul('ul_productos')">Productos</span>
							</a>
						</li>

						<ul id="ul_productos" style="margin-left:-35px; list-style: none; display:none">
							<li>
								<a href="<?= base_url('products/index'); ?>">Listar</a>
							</li>
							<li>
								<a href="<?= base_url('products/add'); ?>">Agregar</a>
							</li>
					    	<li>
					    		<a href="<?= base_url('inventarios/stock_productos'); ?>">
					    			<span>Stock Productos</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('products/print_inicial'); ?>">
					    			<span>Codigo de Barras</span>
					    		</a>
					    	</li>
						</ul>
					</ul>	

				    <ul style="margin-left:-30px; list-style: none;">
				    	<li>
				            <a href="#" id="h2">
				                <span style="font-weight:bold" onclick="mostrar_ul('ul_categorias')">Categorias</span>
				            </a>
				        </li>
			            <ul id="ul_categorias" style="margin-left:-35px; list-style: none; display:none">
			                <li><a href="<?= site_url('categorias/index'); ?>">Listar</a></li>
			                <li><a href="<?= site_url('categorias/add'); ?>">Agregar</a></li>
			            </ul>
			        </ul>

					<!-- **********  P R O V E E D O R E S  **************** -->
					<ul style="margin-left:-30px; list-style: none;">
				    	<li>
				    		<a href="#" id="h4">
				            	<span style="font-weight:bold" onclick="mostrar_ul('ul_proveedores')">Proveedores</span>
				        	</a>
				    	</li>
				    	
					    <ul id="ul_proveedores" style="margin-left:-35px; list-style: none; display:none">
					    	<li>
					    		<a href="<?= base_url('proveedores/index'); ?>">
					    			<span>Listar</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('proveedores/add'); ?>">
					    			<span>Agregar</span>
					    		</a>
					    	</li>
					    </ul>
				    </ul>

					<!-- **********  I N V E N T A R I O S **************** -->
					<ul style="margin-left:-30px; list-style: none;">
				    	
				    	<li>
				    		<a href="#" id="h5">
				            	<span style="font-weight:bold" onclick="mostrar_ul('ul_inventarios')">Inventarios</span>
				        	</a>
				    	</li>
				    	
					    <ul id="ul_inventarios" style="margin-left:-35px; list-style: none; display:none;">
					    	<li>
					    		<a href="<?= base_url('inventarios/index'); ?>">
					    			<span>Ver</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('inventarios/add'); ?>">
					    			<span>Agregar</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('inventarios/registrar_productos'); ?>">
					    			<span>Registrar</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('inventarios/kardex'); ?>">
					    			<span>Kardex</span>
					    		</a>
					    	</li>
					    	<li>
					    		<a href="<?= base_url('inventarios/ver_movimientos'); ?>">
					    			<span>Ver Movimientos</span>
					    		</a>
					    	</li>

					    	<li>
					    		<a href="<?= base_url('inventarios/add_traslados'); ?>">
					    			<span>Movim. Traslado</span>
					    		</a>
					    	</li>

					    	<li>
					    		<a href="<?= base_url('inventarios/add_movimientos'); ?>">
					    			<span>Movim. Otros</span>
					    		</a>
					    	</li>

					    </ul>
					</ul>
					
					<!-- ******** M O V I M I E N T O S ********************-->
					<!--<ul style="margin-left:-30px; list-style: none;">
					    	
				    	<li>
				    		<a href="#" id="h6">
				            	<span style="font-weight:bold" onclick="mostrar_ul('ul_movimientos')">Movimientos</span>
				        	</a>
				    	</li>
					    	
				    	<ul id="ul_movimientos" style="margin-left:-35px; list-style: none; display:none;">

					    </ul>
				    </ul>-->

					<!-- ******** USUARIOS ********************-->
					<?php if($_SESSION["group_id"] == "1"){ ?>
						<ul style="margin-left:-30px; list-style: none;">
						    	
					    	<li>
					    		<a href="#" id="h6">
					            	<span style="font-weight:bold" onclick="mostrar_ul('ul_usuarios')">Usuarios</span>
					        	</a>
					    	</li>
						    	
					    	<ul id="ul_usuarios" style="margin-left:-35px; list-style: none; display:none;">
						    	<li>
						    		<a href="<?= base_url('usuarios/ver_usuarios'); ?>">
						    			<span>Ver Usuarios</span>
						    		</a>
						    	</li>

						    	<li>
						    		<a href="<?= base_url('usuarios/add'); ?>">
						    			<span>Agregar Usuarios</span>
						    		</a>
						    	</li>
						    </ul>
					    </ul>
					<?php } ?>

				    <ul style="margin-left:-20px; margin-top:20px;">
		                <li>
		                	<a href="<?= site_url('welcome/cierra_sesion'); ?>" style="color:red"><i class="fa fa-circle-o"></i> Cerrar</a>
		                </li>
			        </ul>
				</section>
			</aside>
        <?php } 
    }

	function crea_tabla_result($result, $cols, $cols_titulos, $ar_align = array(), $ar_pie = array()){
		
		$cad = "<table class=\"table table-hover\"><tr>";
		
		// titulos ===============
		for($i=0; $i< count($cols); $i++){
			$cad .= "<th id=\"cabeza\" style=\"background-color:rgb(200,200,200);margin:0px;padding:10px\">" . $cols_titulos[$i] . "</th>";
		}

		// Añado operaciones
		//$cad .= "<th style=\"background-color:rgb(200,200,200);margin:0px;padding:10px\">Op.</th>";

		$cad .= "</tr>";

		// body ===============
		$totals = array();
		foreach($result as $r){
			$cad .= "<tr>";
			
			$color = "";

			//if($r["stock"]<3 and !is_null($r["stock"])){
			//	$color = "background-color:rgb(255,100,75)";
			//}

			
			for($i=0; $i < count($cols); $i++){
				$cad .= $this->celda($r[$cols[$i]], $ar_align[$i]);

				if(strtolower($ar_pie[$i]) == "suma"){
					$totals[$i] += $r[$cols[$i]] * 1;
					//echo "Mi suma es :" . $totals[$i] . " _ " . $r[$cols[$i]] . "<br>";
					//print_r($totals);
				}
			}
			
			// Añado operaciones
			/*$cad .= "<td style=\"$color\">";

			if($this->session->userdata["first_name"] == "Administrador"){ 
				$cad .= "<a href=\"" . base_url("insumos/modificar_insumos/") . $r["id"] . "\" alt=\"Editar\"><span class=\"glyphicon glyphicon-edit iconos\"></span></a>\n&nbsp;&nbsp;";
				$cad .= "<a href=\"#\" onclick=\"eliminar_insumo(" . $r["id"] . ")\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>";
			}
			$cad .= "</td>";*/		

			$cad .= "</tr>";
		}

		if (count($totals) > 0){
			for($i=0; $i<count($cols); $i++){
				if($totals[$i] > 0){
					$cad .= $this->celda_h(number_format($totals[$i],2));
				}else{
					$cad .= $this->celda_h($totals[$i]);
				}
			}
		}

		$cad .= "</table>";
		return $cad;
	}

	function obtener_nombre_doc($cod=""){
		$cod = substr($cod,0,1);
		if(strlen($cod)>0){
			if($cod == "F"){
				return "Factura";
			}elseif($cod == "B"){
				return "Boleta";
			}elseif($cod == "G"){
				return "Guia";
			}else{
				return "clip";
			}
		}else{
			return "";
		}
	}

	function json_datatable($ar_campos,$result){ // Devuelve un json preparado para el datatable, el result debe ser result_array
		$nCols = count($ar_campos);

			$cad = "";
			$limite = count($ar_campos);

			foreach($result as $r){
				$cad .= "[";
				for($i=0; $i<$limite; $i++){
					$cad .=  '"' . $r[$ar_campos[$i]] . '",';
				}
				$cad = substr($cad,0,strlen($cad)-1); // quito la ultima coma
				$cad .= "],";
			}

		$cad = substr($cad,0,strlen($cad)-1);
		$cad = '{"data":[' . $cad . ']}';
		return $cad;

	}

	function edicion_completa($ar_campos,$tabla){
		$nLim = count($ar_campos);
		$cad = "<table>";
		for($i=0; $i<$nLim; $i++){
			$cad .= "<tr>";
			$cad .= "<td><label>" . $ar_campos[$i] . "</label></td>";
			$cad .= "<td><input type='text' name='{$ar_campos[$i]}' id='{$ar_campos[$i]}'></td>";
			$cad .= "</tr>";
		}
		$cad .= "</table>";
		$cad .= "<button>Guardar</button>";
		echo $cad;
	}

	function conver_dropdown($result, $indice, $descrip){
		$ar = array();
		foreach($result as $r){
			$ar[$r[$indice]] = $r[$descrip];
		}
		return $ar;
	}

	function contra_inyeccion($dato=""){
		if(strlen($dato)>0){
			$dato = str_replace(";","",$dato);
		}
		return $dato;
	}

	function aMes($n=0){
		if($n==1){
			return "Enero";
		}elseif($n==2){
			return "Febrero";
		}elseif($n==3){
			return "Marzo";
		}elseif($n==4){
			return "Abril";
		}elseif($n==5){
			return "Mayo";
		}elseif($n==6){
			return "Junio";
		}elseif($n==7){
			return "Julio";
		}elseif($n==8){
			return "Agosto";
		}elseif($n==9){
			return "Setiembre";
		}elseif($n==10){
			return "Octubre";
		}elseif($n==11){
			return "Noviembre";
		}elseif($n==12){
			return "Diciembre";
		}else{
			return false;
		}
	}

	function consulta_dato_api($dato1){
        if(strlen($dato1)<11){
        	return $this->consulta_dni($dato1);
    	}elseif(strlen($dato1)==11){
    		return $this->consulta_ruc($dato1);
    	}else{
    		return "";
    	}
    }

    function consulta_ruc($ruc){
        // Datos
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMTAsImV4cCI6MTc1MTkyNzg4OX0.qLIrY_tWIVGjXng0SOFXQpuRjkXryArOlwh1urAr3Sw';
        //

        // Iniciar llamada a API
        $curl = curl_init();

        // Buscar ruc sunat
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://miapi.cloud/v1/ruc/' . $ruc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Referer: https://miapi.cloud/v1/ruc/',
          'Authorization: Bearer ' . $token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Datos de empresas según padron reducido
        $empresa = json_decode($response);
          
          //var_dump($empresa);
        return $empresa;
    }

    function consulta_dni($dni){
        // Datos
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMTAsImV4cCI6MTc1MTkyNzg4OX0.qLIrY_tWIVGjXng0SOFXQpuRjkXryArOlwh1urAr3Sw';
        //

        // Iniciar llamada a API
        $curl = curl_init();

        // Buscar ruc sunat
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://miapi.cloud/v1/dni/' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Referer: https://miapi.cloud/v1/dni/',
          'Authorization: Bearer ' . $token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Datos de empresas según padron reducido
        $empresa = json_decode($response);
          
          //var_dump($empresa);
        return $empresa;
    }

}
?>