<?php
	if(isset($query1)){
		foreach($query1->result() as $r){
			$id = $r->id;
			$name = $r->name;
		}
		$modo = "U";
	}else{
		$id = "";	
		$name = "";
		$modo = "I";
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

<?= form_open(base_url("categorias/save"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
			<label>Nombre</label>
			<?= form_input('name', $name, 'class="form-control tip" id="name"'); ?>
			<input type="hidden" name="id" id="id" value="<?= $id ?>">
			<input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
		</div>

	</div>

	<div class="row filitas">
		<div class="col-sm-3 col-lg-2 ventas">
			<br>
			<button type="button" class="form-control btn btn-primary" onclick="validar_categoria()">Guardar</button>
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

	function validar_categoria(){
	
		if(empty(document.getElementById("name").value)){
			mensaje("Ingrese Nombre")
			return false;
		}

		document.getElementById("form1").submit()
	}

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
	}

</script>