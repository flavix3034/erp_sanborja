<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
    tbody{
        font-family: Arial,verdana,courier;
    }
</style>

<div class="row" style="margin-top:10px;">
    <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3">
		<label>Productos</label><br>
		<select class="form-control" name="product_id" id="product_id">
		<?php 
		    $store_id = $_SESSION["store_id"];
		    $cSql = "SELECT a.id AS product_id, 0 AS variant_id, a.name, IF(b.stock IS NULL,0,b.stock) AS stock FROM tec_products a".
		   		" LEFT JOIN tec_prod_store b ON a.id = b.product_id AND b.store_id = ? AND (b.variant_id IS NULL OR b.variant_id = 0)".
		   		" WHERE a.activo='1' AND a.id NOT IN (SELECT product_id FROM tec_product_variantes WHERE activo='1')".
		   		" UNION ALL".
		   		" SELECT pv.product_id, pv.id AS variant_id, CONVERT(fn_product_display_name(pv.product_id, pv.id) USING latin1) AS name, IF(ps.stock IS NULL,0,ps.stock) AS stock".
		   		" FROM tec_product_variantes pv".
		   		" INNER JOIN tec_products a ON pv.product_id = a.id".
		   		" LEFT JOIN tec_prod_store ps ON pv.product_id = ps.product_id AND ps.variant_id = pv.id AND ps.store_id = ?".
		   		" WHERE a.activo='1' AND pv.activo='1'".
		   		" ORDER BY name";
		    $result = $this->db->query($cSql, array($store_id, $store_id))->result_array();

			$nx=0;
			foreach($result as $r){
				$nx++;
				if($nx==1){ echo "<option value=\"\" data-subtext=\"\">Seleccione</option>"; }
				echo "<option value=\"" . $r["product_id"] . "\" data-variant=\"" . $r["variant_id"] . "\" data-subtext=\"" . $r["stock"] . "\">" . $r["name"] . "</option>";
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
		var sel = $('#product_id').find(':selected');
		$.ajax({
			data 	: {producto: $('#product_id').val(), variant_id: sel.data('variant') || 0},
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