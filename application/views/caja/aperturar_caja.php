
<section style="padding-left:10px; padding-top:8px;">

	<div class="row">

		<div class="col-4 col-sm-4 col-md-3 col-lg-2" style="padding-left:10px; padding-top:8px;">

			<label>Fecha</label><br>
			<input type="date" name="fecha" id="fecha" class="form-control">

		</div>

		<div class="col-4 col-sm-4 col-md-3 col-lg-2" style="padding-left:10px; padding-top:8px;">

			<label>Monto Inicial</label><br>
			<input type="number" name="monto" id="monto" class="form-control">

		</div>

		<div class="col-4 col-sm-4 col-md-3 col-lg-2" style="padding-left:10px; padding-top:8px;">

			<label>Responsable</label><br>
			<input type="text" name="responsable" id="responsable" class="form-control">

		</div>

		<div class="col-4 col-sm-4 col-md-3 col-lg-2" style="margin-bottom:0px;margin-top: 8px;">

			<label>&nbsp;</label><br>
			<button type="button" class="btn btn-primary" onclick="guardar_apertura()">Aceptar</button>

		</div>

	</div>

	<div class="row">
		<div class="col-12 col-sm-6" id="registro1">
			
		</div>
	</div>

</section>

<script type="text/javascript">
	function guardar_apertura(){
		const xhttp = new XMLHttpRequest();
  		xhttp.onload = function() {	
  			if(this.responseText == 'KO'){
  				alert("No se pudo grabar / ya ha sido aperturado.");
  			}else{
  				document.getElementById("registro1").innerHTML = this.responseText;
  				alert("Grabación correcta");
  			}
  		}
		let cFecha = document.getElementById("fecha").value 
    	let cMonto = document.getElementById("monto").value
    	let cResponsable = document.getElementById("responsable").value

    	if (cFecha.length == 0){
    		alert("Ingrese fecha")
    		return false
    	}

    	if (cMonto.length == 0){
    		alert("Ingrese monto")
    		return false
    	}

    	if (cResponsable.length == 0){
    		alert("Ingrese responsable")
    		return false
    	}

  		xhttp.open("GET", '<?= base_url("caja/save_apertura_caja/") ?>' + cFecha + "/" + cMonto + "/" + cResponsable, true);
  		xhttp.send();
	}
</script>