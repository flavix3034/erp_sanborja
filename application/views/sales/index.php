<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($desde)){ $desde = "null"; }
if(!isset($hasta)){ $hasta = "null"; }
if(!isset($store_id)){ 
    $store_id = $_SESSION["store_id"]; 
}else{
    if($store_id == 'null'){
        $store_id = $_SESSION["store_id"];    
    }
}
?>

<script type="text/javascript">
    
    function reenvio_sunat(sale_id1){
        $.ajax({
            data : {sale_id : sale_id1},
            url  : '<?= base_url("sales/envio_individual") ?>',
            type : 'get',
            success: function(response){
                if(response == "OK"){
                    alert("Se envía satisfactoriamente a Sunat.")
                }else{
                    alert("Hubo problemas en el envío.")
                }
            },
            beforeSend: function(){
                console.log("Por favor espere....")
            }
        })
    }

</script>

<style type="text/css">
    div.ColVis {
        float: left;
    }
</style>

<!-- SECCION DE FILTROS -->
<section class="content" style="margin-left: 20px; margin-top:10px;">
    <div class="row" style="display:flex;">
        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" class="form-control" value="<?= $desde ?>">
            </div>    
        </div>

        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?= $hasta ?>">
            </div>
        </div>
        
        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $_SESSION["group_id"];
                    $q = $this->db->get('tec_stores');

                    $ar = array();
                    if ($group_id == '1'){
                        $ar[] = "Todas";
                        foreach($q->result() as $r){
                            $ar[$r->id] = $r->name;
                        }
                    }else{
                        //echo "Pachoni:{$store_id}";
                        foreach($q->result() as $r){
                            if($r->id == $_SESSION["store_id"]){
                                $ar[$r->id] = $r->name;
                            }
                        }
                    }
                    echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required="required"');
                ?>
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: auto;">
            <br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
    </div>

    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-11">
            <table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;" data-page-length='12'>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Tienda</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>recibo</th>
                        <th>Anulado</th>
                        <th>subtotal</th>
                        <th>Total</th>
                        <th>Productos</th>
                        <!--<th>Sunat</th>-->
    					<th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <!--<th></th>-->
    					<th>.</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div>
        <!-- Trigger the modal with a button -->
        <span id="myBtn"></span>

        <!-- Modal -->
        <div class="modal fade" id="pizarra" role="dialog">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <!--<h4 class="modal-title">Modal Header</h4>-->
                </div>
                <div class="modal-body">
                  <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('#example').DataTable({
            dom:            "Bfrtip",
            order           : [[0,'desc']],
            scrollY:        "300px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            buttons:        [   { extend: 'copyHtml5', footer: true },
                                { extend: 'excelHtml5', footer: true },
                                { extend: 'csvHtml5', footer: true },
                                { extend: 'pdfHtml5', footer: true, orientation: 'landscape', pageSize: 'A4', 'footer': true,
                                    exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 7, 8, 9] } 
                                }, 
                                { extend: 'colvis', text: 'Filtro'} 
                            ],
            fixedColumns:   {
                left: 2
            },

            "ajax": "<?= base_url("sales/get_sales/{$desde}/{$hasta}/{$store_id}") ?>",
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
     
                // Total over all pages
                total = api
                    .column(7)
                    .data()
                    .reduce( function (a, b){
                        return intVal(a) + intVal(b);
                    },0);
     
                // Total over this page
                pageTotal = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Update footer
                $( api.column( 7 ).footer() ).html('S/'+ total.toFixed(2));
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if (aData[5] == '1'){ $('td', nRow).css('background-color', 'gray');}
            },
            "columnDefs":[
                { className: "dt-right", "targets": [4,5]},
                { "bVisible": false, "aTargets": [5] }
            ]

        });

        $("#myBtn").click(function(){
            $("#pizarra").modal();
        });

    });

    function activo1(){
        let desde = document.getElementById("desde").value
        let hasta = document.getElementById("hasta").value
        let store_id = document.getElementById("store_id").value
        
        if(desde.length == 0){           desde = 'null'       }
        if(hasta.length == 0){           hasta = 'null'       }
        if(store_id.length == 0){        store_id = 'null'    }

        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>sales/index/' + desde + '/' + hasta + '/' + store_id + '" id="enlace_grilla_compras">Ejecutar</a>'
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla_compras').click()
    }

    function ver_documento(id){
        $.ajax({
            url     : '<?= base_url('sales/view_popup/') ?>' + id,
            type    :'get',
            success : function(response){
                $(".modal-body").html(response)
                document.getElementById("myBtn").click()
            }
        })
    }

    function del_documento(id){
        Swal.fire({
            title: "Desea eliminar la Venta?",
            showDenyButton: true, showCancelButton: false, confirmButtonText: "Si", denyButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    data    : {id:id},
                    url     : '<?= base_url('sales/delete') ?>',
                    type    : 'get',
                    success : function(response){
                        var obj = JSON.parse(response)
                        if(obj.rpta == '1'){
                            Swal.fire("Anulación correcta", "", "success");
                        }else{
                            alert(obj.message)
                        }
                        location.reload()
                    }
                })
            }
        });

    }

</script>
<style type="text/css">
    .table td:nth-child(4) { text-align: right;}
</style>