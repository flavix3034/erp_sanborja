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
    .margen-corto{
        padding: 5px 3px !important;    
    }
    .margen-corto2{
        padding: 10px 3px !important;
        font-size: 9px;
    }
    @media print {
      body * {
        visibility: hidden; /* Oculta todo */
      }
      #pizarra, #pizarra * {
        visibility: visible; /* Muestra solo el modal */
      }
      #pizarra {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
        #caja-fecha{
            min-width: 150px;
            color:red!important;
        }
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
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            hola cariño <?php echo $this->config->item('ENVIOSUNAT'); ?>
            <table id="example" class="display" style="width:100%; font-size: 12px; margin:0px!important;">
                <thead>
                    <tr>
                        <th style="max-width:45px;color:red;text-align:center;">id</th>
                        <th style="max-width:55px;color:red;text-align:center;">Tienda</th>
                        <th style="min-width:100px;color:red;">Fecha</th>
                        <th style="min-width:170px;color:red;">Cliente</th>
                        <th style="min-width:55px;color:red;">recibo</th>

                        <th style="max-width:35px;color:red;text-align:center;">Nulo</th>
                        <th style="min-width:35px;color:red;text-align:center;">subtotal</th>
                        <th style="min-width:60px;color:red;">Total</th>
                        <th style="min-width:130px;color:red;">Productos</th>
                        <th style="min-width:10px;color:red;">Sunat</th>
    					
                        <th style="min-width:120px;color:red;">Actions</th>
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
                        <th></th>

                        <th></th>
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
                  <button type="button" class="btn btn-default" data-dismiss="modal" onclick="window.print();">Imprimir</button>
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
            /*pageLength      : 13,*/
            dom:            "Bfrtip",
            order           : [[0,'desc']],
            scrollY:        "355px",
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
                { className: "dt-right", "targets": [4,5]}
                ,{ 
                    render:function(data, type, row){
                        let valore = row[9]
                        return "<a href='https://cubifact.com/erp-surco/comprobantes/doc_" + row[0] + "_rpta.txt'>" + (valore.trim().length > 0 ? row[9] : ".") + "</a>"
                    },
                    "targets":[9]
                },{
                    render:function(data, type, row){
                        
                        let tamanio = row[9]
                        let valore = ""
                        if (tamanio.length > 0){
                            valore = valore + "<a href=\"#\" onclick=\"ver_documento('" + row[0] + "')\"><i class='glyphicon glyphicon-eye-open' style='font-size:16px;color:lightgreen'></i></a>"
                        }else{
                            valore = valore + "&nbsp;<a href=\"#\" onclick=\"ver_documento_interno('" + row[0] + "')\"><i class='glyphicon glyphicon-eye-open' style='font-size:16px;'></i></a>"
                        }
                        valore = valore + "&nbsp;<a href=\"#\" onclick=\"del_documento('" + row[0] + "')\"><i class='glyphicon glyphicon-remove' style='font-size:16px;color:red'></i></a>"
                        return valore
                    },
                    "targets":[10]
                }

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

    function ver_documento_interno(id){
        $.ajax({
            url     : '<?= base_url('sales/view_popup_interno/') ?>' + id,
            type    :'get',
            success : function(response){
                $(".modal-body").html(response)
                document.getElementById("myBtn").click()
            }
        })
    }

    function del_documento(id){ // Funcion inteligente (si es Factura le da de baja, si es Nota solo lo anula)
        Swal.fire({
            title: "Desea dar de Baja la Venta?",
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
                            Swal.fire("Se dio de Baja correctamente", "", "success");
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
    #example.dataTable tbody td {
        padding: 2px 8px; /* reduce aún más el alto */
    }
</style>