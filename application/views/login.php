<?php
	//$usuario = "postgres";
	//$pass = $_REQUEST["pass"];

	$this->db->select("*");
	//$this->db->where("usuario",$usuario);
	$query = $this->db->get('tec_users');

	foreach($query->result() as $r){
		//echo $r->usuario . $r->nombre;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- <script type="text/javascript" src="assets/js/jquery.min.js"></script> -->
   
    <script src="<?= base_url("assets/plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
    <script src="<?= base_url("assets/bootstrap/js/bootstrap.js") ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url("assets/bootstrap/css/bootstrap.css") ?>" media="all"></link>
    <style type="text/css">
    	body{
    		background-color: white;
    	}
    </style>
</head>
<body>
<script type="text/javascript">
</script>
<div class="container">

	<div class="row" style="display:flex; padding-top:20px;">
		<div class="col-xs-12 col-sm-8 col-lg-6" style="border-style: solid; border-color:rgb(200,200,200); margin:auto; border-radius: 15px">

			<form id="form_login" name="form_login" method="post" action="<?= base_url("welcome/inicia_sesion") ?>">
				<div class="row" style="display:flex;">

					<div id="login1" class="col-xs-12 col-sm-12" style="border-style:none; border-color:rgb(50,130,190); margin-right: 0px;">

						<div class="row" style="background-color:rgb(50,130,190); border-radius: 13px 13px 0px 0px;">
							<div class="col-sm-12" style="text-align:center;">
								<h1 style="color:rgb(230,230,230)">
									<!--<img src="<?= base_url('/assets/images/logo.png') ?>" style="height: 100px">-->
									JFK SYSTEM
								</h1>	
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12" style="text-align:center">
								<h3 style="color:rgb(50,130,190)">M&oacutedulo de Ingreso</h3>	
							</div>
						</div>

						<div class="row" style="border-style:none; border-color:red; text-align:center; margin:10px;">

							<div class="col-sm-4">
							</div>

							<div class="col-sm-4">
								<div class="form-group">
								    <label for="pass">Usuario:</label>
									<input type="text" name="usuario" id="usuario" class="form-control" value="admin"
										onblur="">
								</div>
							</div>

							<div class="col-sm-4">
							</div>

						</div>

						<div class="row" style="text-align:center; margin:10px;">

							<div class="col-sm-4">
							</div>

							<div class="col-sm-4">
								<div class="form-group">
								    <label for="pass">Password:</label>
								    <div>
								      <input type="password" name="pass" class="form-control" id="pass" placeholder="Contraseña" value="1357barco">
								    </div>
								</div>
							</div>

							<div class="col-sm-4">
							</div>
						</div>							


						<div class="row" style="text-align:center; margin:10px;">

							<div class="col-sm-3">
							</div>

							<div class="col-sm-6">
								<!--Almacen:<br>
								<input type="hidden" name="desc_almacen" id="desc_almacen">-->

								<button type="submit" class="btn btn-default btn-lg active">Aceptar</button>
							</div>

							<div class="col-sm-3">
							</div>

						</div>

					</div>
					
				</div>

			</form>

			<div class="row" style="display:flex;">

				<div class="col-12 col-sm-6" style="margin:auto;">

					<div id="login1" style="text-align:center; margin:10px;">

						<?php 
						if(isset($message)){
							if(strlen($message)>0){
								echo $message;
							}
						}
						?>

					</div>

				</div>

			</div>

		</div> <!-- columna La Inicial -->
	</div><!-- Fin de fila La Inicial -->

	<div id="div_footer" style="display:flex; padding-top:150px;">
		
	</div>
</div>

<script>
	function tamVentana() {
	  var tam = [0, 0];
	  if (typeof window.innerWidth != 'undefined')
	  {
	    tam = [window.innerWidth,window.innerHeight];
	  }
	  else if (typeof document.documentElement != 'undefined'
	      && typeof document.documentElement.clientWidth !=
	      'undefined' && document.documentElement.clientWidth != 0)
	  {
	    tam = [
	        document.documentElement.clientWidth,
	        document.documentElement.clientHeight
	    ];
	  }
	  else   {
	    tam = [
	        document.getElementsByTagName('body')[0].clientWidth,
	        document.getElementsByTagName('body')[0].clientHeight
	    ];
	  }
	  return tam;
	}

	function ir(){

	}
</script>
</body>
</html>