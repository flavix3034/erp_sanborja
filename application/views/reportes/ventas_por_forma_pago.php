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

<style type="text/css">
    div.ColVis {
        float: left;
    }
    .suma_fila{
        text-align: right;
        font-weight: bold;
    }
</style>

<section class="content" style="margin-left: 20px;">

    <!-------------- SECCION DE FILTROS ------------------------>
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

    <!-------- SECCION GRILLAS ------------------------------------->
    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-11" style="overflow: auto;">
            <table id="example" class="display" style="font-size: 12px; width: 100%; margin-bottom: 20px;" data-page-length='12'>
                <thead>
                    <tr>
                        <!-- "tienda","fecha","cash","Transf BCP","yape","plin","izipay","Transf Scotiabank",'Vendemas','Transf Bbva','Transf Interbank',"otros" -->
                        <th>Tienda</th>
                        <th>Fecha</th>
                        <th style="max-width:29px!important;">Contado</th>
                        <th style="max-width:29px!important;">Bcp</th>
                        <th style="max-width:29px!important;">yape</th>
                        
                        <th style="max-width:29px!important;">plin</th>
                        <th style="max-width:29px!important;">izipay</th>
                        <th style="max-width:46px!important;">Scotiabank</th>
                        <th style="max-width:46px!important;">Vendemas</th>
                        <th style="max-width:29px!important;">Bbva</th>

                        <th style="max-width:29px!important;">Interbank</th>
                        <th style="max-width:29px!important;">otros</th>
                        <th style="max-width:39px!important;">Total</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:29px!important;"></th>
                        
                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:46px!important;"></th>
                        <th style="max-width:46px!important;"></th>
                        <th style="max-width:29px!important;"></th>

                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:29px!important;"></th>
                        <th style="max-width:39px!important;"></th>
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
            scrollY:        "450px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            buttons:        [   { extend: 'copyHtml5', footer: true },
                                { extend: 'excelHtml5', footer: true },
                                { extend: 'csvHtml5', footer: true },
                                { extend: 'pdfHtml5', footer: true, orientation: 'landscape', pageSize: 'A4', 'footer': true,
                                    exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6] } 
                                }, 
                                { extend: 'colvis', text: 'Filtro'} 
                            ],
            //fixedColumns:   { left: 2},

            "ajax": "<?= base_url("reportes/get_ventas_por_forma_pago/{$desde}/{$hasta}/{$store_id}") ?>",
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
                total2 = api.column(2).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total3 = api.column(3).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total4 = api.column(4).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total5 = api.column(5).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total6 = api.column(6).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total7 = api.column(7).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total8 = api.column(8).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total9 = api.column(9).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total10 = api.column(10).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total11 = api.column(11).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
                total12 = api.column(12).data().reduce( function (a, b){return intVal(a) + intVal(b);},0);
     
                // Total over this page
                pageTotal = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
     
                // Update footer
                $( api.column( 2 ).footer() ).html('S/'+ total2.toFixed(2));
                $( api.column( 3 ).footer() ).html('S/'+ total3.toFixed(2));
                $( api.column( 4 ).footer() ).html('S/'+ total4.toFixed(2));
                $( api.column( 5 ).footer() ).html('S/'+ total5.toFixed(2));
                $( api.column( 6 ).footer() ).html('S/'+ total6.toFixed(2));
                $( api.column( 7 ).footer() ).html('S/'+ total7.toFixed(2));
                $( api.column( 8 ).footer() ).html('S/'+ total8.toFixed(2));
                $( api.column( 9 ).footer() ).html('S/'+ total9.toFixed(2));
                $( api.column( 10 ).footer() ).html('S/'+ total10.toFixed(2));
                $( api.column( 11 ).footer() ).html('S/'+ total11.toFixed(2));
                $( api.column( 12 ).footer() ).html('S/'+ total12.toFixed(2));
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                for(let n=2; n<=11; n++){
                    
                    if (aData[n] == 0){ $(nRow).find('td:eq('+n+')').css('color','rgb(180,180,180)'); }
                    //$(nRow).find('td:eq('+n+')').css('max-width','10px');
                }

            },

            "columnDefs":[
                { className: "dt-right", "targets": [2,3,4,5,6,7,8,9,10,11]},
                { className:"suma_fila", "aTargets": [12]}
                //{ "bVisible": false, "aTargets": [5] }
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

        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>reportes/ventas_por_forma_pago/' + desde + '/' + hasta + '/' + store_id + '" id="enlace_grilla_compras">Ejecutar</a>'
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
        if(confirm("Confirme que desea eliminar?")){
            $.ajax({
                data    : {id:id},
                url     : '<?= base_url('sales/delete') ?>',
                type    : 'get',
                success : function(response){
                    //alert(response)
                    var obj = JSON.parse(response)
                    if(obj.rpta == '1'){
                        alert(obj.message)
                    }else{
                        alert(obj.message)
                    }
                    location.reload()

                }
            })
        }
    }

</script>
<style type="text/css">
    .table td:nth-child(4) { text-align: right;}
</style>