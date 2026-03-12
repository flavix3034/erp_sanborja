<?php
  $metodo       = "";
  $unidad       = "";
  $store_id     = "";
  $fechah       = date("Y-m-d") . "T" . date("H:i:s");
  $tipo_mov     = "";
  $cantidad     = "";
  $obs          = "";
?>
<style type="text/css">
    .mostrada{
        border-style: none;
        border-width: 1px;
        border-color: gray;
        margin-top: 10px;
    }
</style>
<section class="content">

    <?php if(isset($mensaje)){ ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="alert alert-<?= $rpta ?>"><?= $mensaje ?></div>
        </div>
    </div>
    <?php } ?>

    <?= form_open_multipart("inventarios/add_movimientos", 'class="validation" id="form1"'); ?>
    <input type="hidden" name="modo" id="modo" value="insert">
    <div class="row">

        <div class="col-xs-6 col-sm-4 col-lg-2 mostrada">
            <label>Fecha:</label>
            <?php
                $ar = array(
                    "name"  =>"fechah",
                    "id"    =>"fechah",
                    "type"  =>"datetime-local",
                    "value" => $fechah,
                    "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>

        <div class="col-xs-6 col-sm-3 col-lg-2 mostrada">
            <div class="form-group">
                <label for="">Tienda Origen:</label>
                <?php
                    $group_id = $_SESSION["group_id"];
                    $q = $this->db->get('tec_stores');

                    $ar = array();
                    if ($group_id == '1'){
                        $ar[""] = "";
                        foreach($q->result() as $r){
                            $ar[$r->id] = $r->state;
                        }
                    }else{
                        foreach($q->result() as $r){
                            if($r->id == $_SESSION["store_id"]){
                                $ar[$r->id] = $r->state;
                            }
                            
                        }
                    }
                    echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required');
                ?>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-xs-6 col-sm-2 mostrada">
            <label>Tipo Mov:</label>
            <?php
                $ar = array();
                foreach($tipos_mov->result() as $r){
                    $ar[$r->tipo_mov] = $r->descrip;
                }
                echo form_dropdown('tipo_mov', $ar, $tipo_mov, 'class="form-control tip" id="tipo_mov" required="required"');
            ?>
        </div>

        <div class="col-xs-6 col-sm-2 mostrada">
            <label>Motivo:</label>
            <?php
                $ar = array();
                $tipo_metodos = $this->db->select('id, metodo')->get("tec_metodos_inv");
                foreach($tipo_metodos->result() as $r){
                    $ar[""] = "";
                    if($r->metodo != 'TRASLADO'){
                        $ar[$r->id] = $r->metodo;
                    }
                }
                echo form_dropdown('metodo', $ar, $metodo, 'class="form-control tip" id="metodo" required="required"');
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-4 mostrada">
            <label>Productos</label><br>
            <select class="form-control" name="product_id" id="product_id">
            <?php 
                $store_id = $_SESSION["store_id"];
                $cSql = "SELECT a.id AS product_id, 0 AS variant_id, a.name FROM tec_products a".
                    " WHERE a.activo='1' AND a.id NOT IN (SELECT product_id FROM tec_product_variantes WHERE activo='1')".
                    " UNION ALL".
                    " SELECT pv.product_id, pv.id AS variant_id, CONVERT(fn_product_display_name(pv.product_id, pv.id) USING latin1) AS name".
                    " FROM tec_product_variantes pv".
                    " INNER JOIN tec_products a ON pv.product_id = a.id".
                    " WHERE a.activo='1' AND pv.activo='1'".
                    " ORDER BY name";
                $result = $this->db->query($cSql)->result_array();

                $nx=0;
                foreach($result as $r){
                    $nx++;
                    if($nx==1){ echo "<option value=\"\">Seleccione</option>"; }
                    echo "<option value=\"" . $r["product_id"] . "\" data-variant=\"" . $r["variant_id"] . "\">" . $r["name"] . "</option>";
                }
            ?>
            </select>
        </div>
        <div class="col-xs-4 col-sm-3 mostrada">
            <label>Unidad:</label>
            <?php
                //$ar = $this->inventarios_model->unidades();
                $result = $this->db->select('id, descrip')->get('tec_unidades')->result_array();
                $ar     = $this->fm->conver_dropdown($result, 'id', 'descrip');
                echo form_dropdown('unidad', $ar, $unidad, 'class="form-control tip" id="unidad" required="required"');
            ?>
        </div>
        <div class="col-xs-6 col-sm-2 mostrada">
            <label>Cantidad:</label>
            <?php
                $ar = array(
                    "name"  =>"cantidad",
                    "id"    =>"cantidad",
                    "type"  =>"text",
                    "value" => $cantidad,
                    "class" =>"form-control tip",
                    "required" => "required"
                );
                echo form_input($ar);
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-8 mostrada">
            <label>Observaciones:</label>
            <?php
                $ar = array(
                    "name"  =>"obs",
                    "id"    =>"obs",
                    "type"  =>"text",
                    "value" => $obs,
                    "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-4 mostrada">
            <button type="submit" class="btn btn-success">Grabar</button>
        </div>
    </div>

    <input type="hidden" name="variant_id" id="variant_id" value="0">
    <?= form_close(); ?>

</section>

<script type="text/javascript">
    $('#product_id').on('change', function(){
        var sel = $(this).find(':selected');
        $('#variant_id').val(sel.data('variant') || 0);
    });
    function validar(){
        //document.getElementById("form_compra").submit()
    }
</script>