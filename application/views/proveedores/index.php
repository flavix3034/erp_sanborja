<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<div class="row" style="margin-top:15px">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
		<?php echo $tabla_proveedores; ?>		
	</div>
</div>
<script type="text/javascript">
    function modificar(id){
        window.location.href = "<?= base_url() ?>proveedores/add?id=" + id
    }

    function eliminar(id){
        if(confirm("Confirme que desea eliminar?")){
            $.ajax({
                data : {id:id},
                type : 'get',
                url  : '<?= base_url('proveedores/eliminar') ?>',
                success:function(res){
                    if(res=='0'){
                        window.location.href = "<?= base_url() ?>proveedores/index";
                    }else{
                        alert("No se puede eliminar")
                    }
                }
            })
            
                    
        }
    }
</script>
