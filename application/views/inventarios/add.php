<?php
	$name = $tienda = "";
	$fecha_i = date("Y-m-d") . "T" . date("H:i:s");
	$fecha_f = date("Y-m-d") . "T" . date("H:i:s");
	$responsable = "";
?>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(170,170,170);
	}
	.filitas{
		margin-top: 15px;
	}	
</style>

<?= form_open(base_url("inventarios/add"), 'method="post" name="form1" id="form1"'); ?>

	<!-- PRIMERA FILA ---------------------------------------------->
	<div class="row filitas" style="margin-left:10px;">
	
		<div class="col-sm-4 col-lg-2 ventas">
			<label>Tienda</label>
	        <?php
	            $group_id = $_SESSION["group_id"];
	            $q = $this->db->get('tec_stores');

	            if ($group_id == '1'){
	                $ar[] = "Todas";
	                foreach($q->result() as $r){
	                    $ar[$r->id] = $r->name;
	                }
	            }else{
	                foreach($q->result() as $r){
	                    if($r->id == $_SESSION["store_id"]){
	                        $ar[$r->id] = $r->name;
	                    }
	                }
	            }
	            echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
	        ?>
		</div>

	</div>

	<!-- SEGUNDA FILA ---------------------------------------------->
	<div class="row filitas" style="margin-left:10px;">
	
		<div class="col-sm-6 col-lg-3 ventas">
			<label>Fecha de Inicio</label>
			<?php  
				$ar = array(
				   "name"  =>"fecha_i",
				   "id"    =>"fecha_i",
				   "type"  =>"datetime-local",
				   "value" => $fecha_i,
				   "class" =>"form-control tip"
				);
				echo form_input($ar);
			?>
		</div>

		<div class="col-sm-6 col-lg-3 ventas">
			<label>Fecha de Fin</label>
			<?php  
				$ar = array(
				   "name"  =>"fecha_f",
				   "id"    =>"fecha_f",
				   "type"  =>"datetime-local",
				   "value" => $fecha_f,
				   "class" =>"form-control tip"
				);
				echo form_input($ar);
			?>
		</div>

	</div>

	<!-- TERCERA FILA ---------------------------------------------->
	<div class="row filitas" style="margin-left:10px;">
	
		<div class="col-sm-6 col-lg-3 ventas">
			<label>Responsable</label>
			<?php  
				$ar = array(
				   "name"  =>"responsable",
				   "id"    =>"responsable",
				   "type"  =>"text",
				   "value" => $responsable,
				   "class" =>"form-control tip"
				);
				echo form_input($ar);
			?>
		</div>

		<div class="col-sm-4 col-lg-2 ventas">
			<button type="submit" class="btn btn-danger" style="margin-top:25px;">Grabar</button>
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