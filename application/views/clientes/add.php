<?php
	$name = "";
	$cf1 = "";
	$cf2 = "";
	$phone = "";
	$email = "";
	$direccion = "";
	$cerrar   = isset($cerrar) ? $cerrar : "";
?>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(170,170,170);
	}
	.filitas{
		margin-top: 15px;
	}	
</style>

<?= form_open(base_url("clientes/save"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Nombre</label>
			<?= form_input('name', $name, 'class="form-control tip" id="name"'); ?>
		</div>

		<div class="col-sm-4 col-lg-2 ventas">
			<label>Dni:</label>
			<?= form_input('cf1', $cf1, 'class="form-control" id="cf1"'); ?>
		</div>

		<div class="col-sm-4 col-lg-2 ventas">
			<label>Ruc:</label>
			<?= form_input('cf2', $cf2, 'class="form-control" id="cf2"'); ?>
		</div>
		
	</div>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Telf/Celular</label>
			<?= form_input('phone', $phone, 'class="form-control" id="phone"'); ?>
		</div>

		<div class="col-sm-4 col-lg-3 ventas">
			<label>Email:</label>
			<?= form_input('email', $email, 'class="form-control" id="email"'); ?>
		</div>

		<div class="col-sm-4 col-lg-4 ventas">
			<label>Direccion:</label>
			<?= form_input('direccion', $direccion, 'class="form-control" id="direccion"'); ?>
			<input type="hidden" name="cerrar" value="<?= $cerrar ?>">
		</div>
		
	</div>

	<div class="row filitas">
		<div class="col-sm-3 col-lg-2 ventas">
			<br>
			<button type="button" class="form-control btn btn-primary" onclick="validar_cliente()">Guardar</button>
		</div>
	</div>

<?= form_close() ?>

<script type="text/javascript">
	ar_items = new Array()

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

	function validar_cliente(){
	
		if(empty(document.getElementById("name").value)){
			mensaje("Ingrese Nombre")
			return false;
		}

		var cf1 = document.getElementById("cf1").value
		var cf2 = document.getElementById("cf2").value

		if(empty(cf1) && empty(cf2)){
			mensaje("Falta ingresar datos en dni o Ruc")
			return false;
		}

		if(<?php 
			if($cerrar == '1'){
				echo "1";
			}else{
				echo "0";
			} ?>){

			grabar();
		}else{
			document.form1.submit()
		}
	}

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
	}

	function grabar(){
		$.ajax({
			data: {
				name 	: document.getElementById("name").value,
				cf1 	: document.getElementById("cf1").value,
				cf2 	: document.getElementById("cf2").value,
				phone 	: document.getElementById("phone").value,
				email 	: document.getElementById("email").value,
				direccion:document.getElementById("direccion").value
			},
			url : "<?= base_url("clientes/save") ?>",
			type: "get",
			success: function(response){
				alert(response)
				window.close()
			}
		})
	}
</script>