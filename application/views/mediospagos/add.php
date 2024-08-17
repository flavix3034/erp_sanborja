<?php
	if(isset($query1)){
		foreach($query1->result() as $r){
			$id 		= $r->id;
			$forma_pago = $r->forma_pago;
			$descrip 	= $r->descrip;
		}
		$modo = "U";
	}else{
		$id 		= "";	
		$forma_pago = "";
		$modo 		= "I";
		$descrip 	= "";
	}
?>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(170,170,170);
	}
	.filitas{
		margin-top: 15px;
	}	
</style>

<?= form_open(base_url("mediospagos/save"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Nombre</label>
			<?= form_input('forma_pago', $forma_pago, 'class="form-control tip" id="forma_pago"'); ?>
			<input type="hidden" name="id" id="id" value="<?= $id ?>">
			<input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
		</div>

	</div>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Descripci&oacute;n</label>
			<?= form_input('descrip', $descrip, 'class="form-control tip" id="descrip"'); ?>
		</div>

	</div>

	<div class="row filitas">
		<div class="col-sm-3 col-lg-2 ventas">
			<br>
			<button type="submit" class="form-control btn btn-primary">Guardar</button>
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

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
	}

</script>