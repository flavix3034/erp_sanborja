<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="row" style="margin-top:15px">
    <div class="col-sm-12 col-md-10">
        <?php
            echo $this->load->inventarios_model->ver_inventario($id_inv);
        ?>
    </div>
    
</div>
<script type="text/javascript">
    function eliminar(id){
        if (confirm("Desea eliminar?")){
            $.ajax({
                data    :{id:id},
                type    :"get",
                url     :"<?= base_url("inventarios/eliminar_registro_inv") ?>",
                success :function(res){
                    var obj = JSON.parse(res)
                    if (obj.rpta == "OK"){
                        alert("Se logra eliminar dicho Registro.")
                        location.reload()
                    }else{
                        alert(res) // "No se puede eliminar..."
                    }
                }
            })
        }
    }
</script>