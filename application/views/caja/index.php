
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable( {
            "ajax": "<?= base_url("caja/get_ingresos") ?>"
        } );
    } );
</script>

<div class="row">
    <div style="padding:20px; border-style: solid; border-color:red">
        <h2 style="margin-bottom:26px">Ingresos de Caja</h2>
        <table id="example" class="display" style="font-size: 11px; margin-bottom: 20px; padding-right: 10px;">
            <thead>
                <tr>
                    <th class="col-sm-1">fecha</th>
                    <th class="col-sm-1">cod_afi</th>
                    <th class="col-sm-1">socio</th>
                    <th class="col-sm-3">nombre</th>
                    <th class="col-sm-3">concepto</th>
                    <th class="col-sm-1">Monto</th>
                    <th class="col-sm-2">Obs</th>
                </tr>
            </thead>
            <!--<tfoot>
                <tr>
                    <th>fecha</th>
                    <th>cod_afi</th>
                    <th>socio</th>
                    <th>nombre</th>
                    <th>concepto</th>
                    <th>Monto</th>
                    <th>Obs</th>
                </tr>
            </tfoot>-->
        </table>


    </div>
</div>

<script type="text/javascript">
    function envio_ajax(){
        $.ajax({
            data: {var1 : "hola"},
            type: "get",
            url : "<?= base_url('caja/get_ingresos') ?>",
            success: function (res){
                alert(res)
            }
        })
    }
</script>