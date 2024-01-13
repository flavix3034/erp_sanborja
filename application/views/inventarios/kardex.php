<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
    tbody{
        font-family: Impact,Arial;
    }
</style>

<div class="row" style="margin-top:10px;">
    <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3">
		<label>Productos</label><br>
		<select class="form-control" name="product_id" id="product_id">
		<?php 
		    $cSql = "select a.id, a.code, concat(a.name,' ',a.marca,' ',a.modelo) name, a.price, a.unidad, a.marca, a.modelo, b.stock from tec_products a".
		   		" left join tec_prod_store b on a.id = b.product_id and b.store_id = ?".
		   		" where a.activo='1' order by a.name, a.marca, a.modelo";
		    $result = $this->db->query($cSql, array($_SESSION["store_id"]))->result_array();
		    
			$nx=0;
			foreach($result as $r){
				$nx++;
				if($nx==1){ echo "<option value=\"\" data-subtext=\"\">Seleccione</option>"; }
				echo "<option value=\"" . $r["id"] . "\" data-subtext=\"" . $r["stock"] . "\">" . $r["name"] . "</option>";
			}
		?>
		</select>
    </div>
    <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3">
    	<label>&nbsp;</label><br>
    	<button type="button" onclick="generar_kardex()" class="btn btn-primary">Generar</button>
    </div>
</div>

<script type="text/javascript">
	function generar_kardex(){
		$.ajax({
			data 	: {producto: $('#product_id').val()},
			url 	: "<?= base_url("inventarios/kardex") ?>",
			type 	: "get",
			success : function(res){
				$("#pizarra1").html(res)
			}
		})
	}
</script>

<!----- KARDEX DE UN PRODUCTO --------------------->
<div class="row" style="margin-top:10px;">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8" id="pizarra1">

    </div>

</div>
	</div>
</div>