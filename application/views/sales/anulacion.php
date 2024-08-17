<?php
	$id_venta = isset($id_venta) ? $id_venta : "";
?>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(170,170,170);
	}
	.filitas{
		margin-top: 15px;
	}	
</style>

<?= form_open(base_url("sales/anular"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Digite el Id de la Venta:</label>
			<?= form_input('id_venta', $id_venta, 'class="form-control tip" id="id_venta"'); ?>
		</div>

		<div class="col-sm-3 col-lg-2 ventas" style="margin-top:25px;">
			<button type="button" class="btn btn-danger" onclick="validar_id_venta()">Enviar</button>
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

	function validar_id_venta(){
	
		if(empty(document.getElementById("id_venta").value)){
			mensaje("Ingrese id Venta")
			return false;
		}

		mensaje("Se llega hasta aquin")
		document.getElementById("form1").submit()
	}

	function mensaje(cad){
		alert(cad)
	}
	
</script>