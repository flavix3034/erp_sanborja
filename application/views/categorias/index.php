<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <div class="row" style="margin-top:10px">
        <div class="col-sm-12 col-md-6">
            <!--<h2 style="margin-bottom:26px">Categorias</h2>-->
            <table id="example" class="display" style="width:90%; font-size: 12px; margin-bottom: 20px;">
                <thead>
                    <tr>
                    	<th style="max-width: 20px;">id</th>
                    	<th>Nombre</th>
                        <th>Estado</th>
                        <th style="max-width:60px">Acciones</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            "ajax": "<?= base_url("categorias/get_categorias") ?>"
        });
    });

    function editar(id){
        window.location.href = "<?=base_url("categorias/edit") ?>"+ "/" + id
    }

    function eliminar(id){
        window.location.href = "<?=base_url("categorias/anular") ?>"+ "/" + id
    }

</script>