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
                $cSql = "select a.id, a.code, concat(a.name,' ',a.marca,' ',a.modelo) name, a.price, a.unidad, a.marca, a.modelo, b.stock from tec_products a".
                    " left join tec_prod_store b on a.id = b.product_id and b.store_id = ?".
                    " order by a.name";
                $result = $this->db->query($cSql, array($_SESSION["store_id"]))->result_array();
                
                $nx=0;
                foreach($result as $r){
                    $nx++;
                    if($nx==1){ echo "<option value=\"\">Seleccione</option>"; }
                    echo "<option value=\"" . $r["id"] . "\">" . $r["name"] . "</option>";
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

    <?= form_close(); ?>

</section>

<script type="text/javascript">
    function validar(){
        //document.getElementById("form_compra").submit()
    }
</script>