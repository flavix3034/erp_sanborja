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
if(!isset($tipo)){ $tipo = "null"; }
//die("Tipo:".$tipo);
$cad_x = $meses_en_curso;
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<style type="text/css">
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>

    <!-------------- SECCION DE FILTROS ------------------------>
    <div class="row" style="display:flex;margin-top: 15px;">
        <div class="col-sm-3 col-md-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>" class="form-control" value="<?= $desde ?>">
            </div>    
        </div>

        <div class="col-sm-3 col-md-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>" class="form-control" value="<?= $hasta ?>">
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

        <div class="col-sm-3 col-md-2">
            <div class="form-group">
                <label for="">Tipo Reporte:</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="1" <?= $tipo=="1" ? 'selected' : '' ?>>Diario</option>
                    <option value="2" <?= $tipo=="2" ? 'selected' : '' ?>>Mensual</option>
                </select>
            </div>    
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: auto;">
            <br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
    </div>

    <figure class="highcharts-figure">
        <div id="container"></div>
        <p class="highcharts-description">
            Reporte de Ventas <?= $tipo_rep ?>
        </p>
    </figure>


<script type="text/javascript">

    var ar_x = [<?= $cad_x ?>]
    var ar_y = [<?= $cad_y ?>]
    console.log(ar_x)

    // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
    Highcharts.chart('container', {
        chart: { type: 'spline' },
        title: { text: 'Reporte de Ventas <?= $tipo_rep ?>' },
        subtitle: { text: ' ' + '' },
        xAxis: {
            categories: ar_x,
            accessibility: { description: 'Meses' }
        },
        yAxis: {
            title: { text: 'Soles' },
            labels: { formatter: function () { return 'S/ '+this.value; } }
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: { marker: { radius: 4, lineColor: '#666666', lineWidth: 1 } }
        },
        series: [{
            name: 'Ventas',
            marker: { symbol: 'square'},
            data: [<?= $cad_y ?>]
        }]
    });

    function activo1(){
        let desde = document.getElementById("desde").value
        let hasta = document.getElementById("hasta").value
        let store_id = document.getElementById("store_id").value
        let tipo = document.getElementById("tipo").value
        
        if(desde.length == 0){           desde = 'null'       }
        if(hasta.length == 0){           hasta = 'null'       }
        if(store_id.length == 0){        store_id = 'null'    }

        window.location.href = '<?= base_url() ?>reportes/grafico_mensual_ventas/' + desde + '/' + hasta + '/' + store_id + '/' + tipo
    }

</script>