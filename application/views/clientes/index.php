<style type="text/css">
    .titul-table{
        font-weight: bold;
        font-size: 16px;
        padding: 5px 18px !important;
    }
</style>
<?php
    (defined('BASEPATH')) OR exit('No direct script access allowed');
?>
<section class="content">
    <div class="row" style="margin-top:10px">
        <div class="col-sm-12 col-lg-10">
            <table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <!-- ("id","state","date","customer_name","total","total_tax","total_discount","grand_total","paid","status","tipoDoc","recibo","dir_comprobante") -->
                    	<th class="titul-table">id</th>
                    	<th class="titul-table">Nombre Cliente</th>
                    	<th class="titul-table">Dni</th>
                    	<th class="titul-table">Ruc</th>
                    	<th class="titul-table">Movil</th>
                    	<th class="titul-table">Email</th>
                    	<th class="titul-table">Tienda</th>
                    	<th class="titul-table">Direccion</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>id</th>
                        <th>Nombre Cliente</th>
                        <th>Dni</th>
                        <th>Ruc</th>
                        <th>Fono</th>
                        <th>Email</th>
                        <th>Tienda</th>
                        <th>Direccion</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            "ajax": "<?= base_url("clientes/get_clientes") ?>",
            "columnDefs":[
                { visible: false, "targets": [6]}
            ]
        
        });
    });
</script>