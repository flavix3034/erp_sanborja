<section style="padding-left:10px; padding-top:8px;">

    <div class="row">
	    <div class="col-6 col-sm-2" style="margin-top:10px; margin-bottom: 10px;">
			<a href="<?= base_url('caja/aperturar_caja'); ?>" class="btn btn-primary">Aperturar</a>
		</div>
	</div>

    <div class="row" id="grilla">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-11" style="border-style:none; border-color:red;">
            <table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;" data-page-length='12'>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Fecha</th>
                        <th>Caja</th>
                        <th>Responsable</th>
                        <th>Inicial</th>
                        
                        <th>Vta Efectivo</th>
                        <th>Compras<br>Gastos</th>
                        <th>Fin Efectivo<br>Calculado</th>
                        <th>Fin Efectivo<br>Real</th>
                        <th>Difer</th>

                        <th>Estado</th>
                        <th style="">.</th>
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
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <button id="btn_cer" type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal"></button>
    <input type="hidden" id="registro_id">
    <input type="hidden" name="vtas_efectivo" id="vtas_efectivo" value="">
    <input type="hidden" name="compras_efectivo" id="compras_efectivo" value="">
    <input type="hidden" name="monto_calculado" id="monto_calculado" value=""> 
     

	<div class="row">
		<div class="col-12 col-sm-6" id="registro1">
		</div>
	</div>

  <div id="modal1" class="w3-modal">
    <div class="w3-modal-content" style="width:400px;">
        <div class="w3-container">
            <span onclick="document.getElementById('modal1').style.display='none'" class="w3-button w3-display-topright">&times;</span>

            <div class="">
                <h4>Cierre de Caja</h4>
            </div>
            <div class="">
                <table>
                    <tr>
                        <td>Monto Final Calculado:</td>
                        <td><input type="text" name="txt_monto_calculado" id="txt_monto_calculado" value="" readonly></td>
                    </tr>
                    <tr>
                        <td>Monto Final Encontrado:</td>
                        <td><input type="text" name="txt_monto_encontrado" id="txt_monto_encontrado" value=""></td>
                    </tr>
                </table>
            </div>
            <div class="">
                <button type="button" class="btn btn-primary" onclick="cerrar_caja()">Guardar</button>
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
                                    exportOptions: { columns: [ 0, 1, 2, 3, 4] } 
                                }, 
                                { extend: 'colvis', text: 'Filtro'} 
                            ],
            fixedColumns:   {
                left: 2
            },

            "ajax": "<?= base_url("caja/get_ver_cajas/1") ?>",
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
                /*
                total = api
                    .column(5)
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
                */
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                //if (aData[5] == '1'){ $('td', nRow).css('background-color', 'gray');}
            },
            "columnDefs":[
                //{ className: "dt-right", "targets": [4,5]},
                //{ "bVisible": false, "aTargets": [5] }
            ]

        });
    })

    function proceso_cerrar(id, vtas_efectivo, compra, monto_calculado){
        //document.getElementById('btn_cer').click()
        document.getElementById('modal1').style.display     ='block';
        document.getElementById('registro_id').value        = id;
        document.getElementById('vtas_efectivo').value      = vtas_efectivo; 
        document.getElementById('compras_efectivo').value   = compra; 
        document.getElementById('monto_calculado').value    = monto_calculado;

        // copiando en modal:
        document.getElementById('txt_monto_calculado').value = monto_calculado;
        //calcular_cierre_caja(id)
    }

    function guardar_apertura(){
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() { 
            if(this.responseText == 'KO'){
                alert("No se pudo grabar.");
            }else{
                document.getElementById("registro1").innerHTML = this.responseText;
                alert("Grabación correcta");
            }
        }
        let cFecha = document.getElementById("fecha").value 
        let cMonto = document.getElementById("monto").value
        xhttp.open("GET", '<?= base_url("caja/save_apertura_caja/") ?>' + cFecha + "/" + parseStr(cMonto), true);
        xhttp.send();
    }

    function cerrar_caja(){
        let id          = document.getElementById('registro_id').value
        let cMontoFin   = document.getElementById('txt_monto_encontrado').value
        let cMontoCalculado = document.getElementById('txt_monto_calculado').value
        let cVenta      = document.getElementById('vtas_efectivo').value
        let cCompra     = document.getElementById('compras_efectivo').value
        if(confirm("Desea cerrar caja?")){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() { 
                if(this.responseText == 'KO'){
                    alert("No se pudo cerrar.");
                }else{
                    document.getElementById("registro1").innerHTML = this.responseText;
                    alert("Se cierra caja");
                }
                window.location.reload();
            }
            xhttp.open("GET", '<?= base_url("caja/cerrar_caja/") ?>' + id + "/" + cMontoFin + "/" + cMontoCalculado + "/" + cVenta + "/" + cCompra, true);
            xhttp.send();
        }
    }

    function calcular_cierre_caja(id){
        
        document.getElementById("registro_id").value = id

        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() { 
            if(this.responseText == 'KO'){
                alert("No se pudo cerrar.");
            }else{
                document.getElementById("txt_monto_calculado").value = this.responseText;
                //window.location.reload();
                //alert(this.responseText);
            }
        }
        xhttp.open("GET", '<?= base_url("caja/calcular_cierre_caja/") ?>' + id, true);
        xhttp.send();
    }
</script>