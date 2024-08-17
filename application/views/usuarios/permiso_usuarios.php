<?php
	
	if(isset($query_p1)){
		foreach($query_p1->result() as $r){
		}
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

<?= form_open(base_url("usuarios/save"), 'method="post" name="form1" id="form1"'); ?>

	<div class="row filitas">
	
		<div class="col-sm-5 col-md-4 col-lg-3 ventas">
			<label>User:</label>
			<?= form_input('username', $username, 'class="form-control" id="username" required'); ?>
		</div>

	</div>


	<div class="row filitas">
		<div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 ventas">
			<br>
			<button type="button" class="btn btn-primary" onclick="validar()">Guardar</button>
		</div>
		<div class="col-xs-5 col-sm-7 col-md-8 col-lg-9 ventas">
		</div>
		<div class="col-xs-2 col-sm-1 col-md-1 col-lg-1 ventas">
			<br>
			<button type="submit" name="submit1" id="submit1">.</button>
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

	function validar(){
		//document.form1.submit()
		document.getElementById("submit1").click()
	}

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
	}
</script>