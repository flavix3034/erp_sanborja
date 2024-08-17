<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<div class="row" style="margin-top:15px">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-11">
		<?php echo $tabla_usuarios; ?>		
	</div>
</div>
<script type="text/javascript">
    function modificar(id){
        window.location.href = "<?= base_url() ?>usuarios/add?id=" + id
    }

    function eliminar(id){
        if(confirm("Confirme que desea eliminar?")){
            $.ajax({
                data : {id:id},
                type : 'get',
                url  : '<?= base_url('usuarios/eliminar') ?>',
                success:function(res){
                    if(res=='1'){
                        alert("Se logra eliminar el usuario")
                        window.location.href = "<?= base_url() ?>usuarios/ver_usuarios";
                    }else{
                        alert("No se puede eliminar el usuario, todavia está presente en Compras y/o Ventas.")
                    }
                }
            })
            
                    
        }
    }
</script>