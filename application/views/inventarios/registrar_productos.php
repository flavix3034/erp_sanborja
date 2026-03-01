<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    $product_id = $maestro_id = $unidad = "";
    $cantidad = 0;
?>
<style type="text/css">
    .zonas{
        border-style: solid; 
        border-color: gray; 
        border-width: 1px; 
        margin: 15px 0px;
        padding:  10px 0px;
    }
</style>

<?= form_open_multipart("inventarios/registrar_productos", 'class="validation" id="form1"') ?>

<div class="row zonas">
    <div class="col-xs-8 col-sm-6 col-md-4">
        <label>Seleccione el inventario a desarrollar:</label>
        <?php
            $cSql = "select a.id, concat(substr(a.fecha_i,1,10),'-',b.state) frase, b.state tienda 
                from tec_maestro_inv a
                inner join tec_stores b on a.store_id = b.id
                where a.finaliza!='1'";
            $result = $this->db->query($cSql)->result_array();
            $indice = "id";
            $descrip = "frase";
            $ar     = $this->fm->conver_dropdown($result, $indice, $descrip);
            echo form_dropdown('maestro_id', $ar, $maestro_id, 'class="form-control tip" id="maestro_id" required="required"');
        ?>
        <input type="hidden" name="modo" value="1">
    </div>
    <div class="col-xs-4 col-sm-3 col-md-2">
        <br>
        <button type="button" onclick="seleccionar()" class="btn btn-primary">Seleccionar</button>
    </div>
</div>

<div class="row zonas">
    <div class="col-xs-6 col-sm-4 col-md-3">
        <label>Producto:</label>
        <?php
            // fecha product_id cantidad unidad store_id maestro_id
            $cSql = "select a.id, a.name from tec_products a order by a.name";
            $result = $this->db->query($cSql)->result_array();
            $indice = "id";
            $descrip = "name";
            $ar     = $this->fm->conver_dropdown($result, $indice, $descrip);
            echo form_dropdown('product_id', $ar, $product_id, 'class="form-control tip" id="product_id" required="required"');

        ?>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-3">
        <label>Unidades:</label>
        <?php
            $cSql = "select a.id, a.descrip from tec_unidades a";
            $result = $this->db->query($cSql)->result_array();
            $indice = "id";
            $descrip = "descrip";
            $ar     = $this->fm->conver_dropdown($result, $indice, $descrip);
            echo form_dropdown('unidad', $ar, $unidad, 'class="form-control tip" id="unidad" required="required"');
        ?>
    </div>

    <div class="col-xs-5 col-sm-3 col-md-2">
        <label>Cantidad:</label>
        <?php
            $ar = array(
               "name"  =>"cantidad",
               "id"    =>"cantidad",
               "type"  =>"text",
               "value" => $cantidad,
               "class" =>"form-control tip"
            );
            echo form_input($ar);
        ?>
    </div>

    <div class="col-xs-5 col-sm-3 col-md-2">
        <br><button type="button" class="btn btn-danger" onclick="registrar_productos()">Registrar</button>
    </div>

</div>

<div class="row zonas">
    <div class="col-sm-12 col-md-10 col-lg-8" id="pizarra1">
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12">
        Nota.- Solo muestra los ultimos 30 registros...
    </div>
</div>

<?= form_close(); ?>

<?= form_open_multipart(base_url("inventarios/finalizar_inventario"), 'class="validation" id="form2"') ?>
<div class="row zonas">
    <div class="col-sm-2">
        <button type="button" class="btn btn-danger" onclick="$('#maestro_id2').val( $('#maestro_id').val() );document.getElementById('form2').submit();">Finalizar</button>
        <input type="hidden" name="maestro_id2" id="maestro_id2">
    </div>
    <div class="col-sm-10">
        Nota.- Al finalizar se pueden realizar movimientos virtuales para nivelar el inventario calculado (kardex) con el inventario fisico.
    </div>
</div>
<?= form_close(); ?>

<script type="text/javascript">
    function seleccionar(){
        $.ajax({
            data    :{
                maestro_id  : $("#maestro_id").val(),
                limit       : 30
            },
            type    : 'get',
            url     : '<?= base_url("inventarios/get_inventario") ?>',
            success : function(res){
                document.getElementById("pizarra1").innerHTML = res 
            }
        })
    }

    function registrar_productos(){
        $.ajax({
            data    :{
                product_id  : $("#product_id").val(),
                unidad      : $("#unidad").val(),
                cantidad    : $("#cantidad").val(),
                maestro_id  : $("#maestro_id").val()
            },
            type    : 'get',
            url     : '<?= base_url("inventarios/registrar_productos_ajax") ?>',
            success : function(res){
                var obj = JSON.parse(res)
                seleccionar()
                alert(obj.msg)
            }
        })
    }

</script>