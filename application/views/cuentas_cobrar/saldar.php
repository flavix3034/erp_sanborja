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
$date = date("Y-m-d");
$cliente = "";
?>

<script type="text/javascript">
    
</script>

<style type="text/css">
    div.ColVis {
        float: left;
    }
    label{
        font-weight: bold;
    }
</style>

<!-- SECCION DE FILTROS -->
<?= form_open_multipart("cuentas_cobrar/save", 'class="validation" id="form_compra"') ?>
<section class="content" style="margin-left: 20px; margin-top: 15px;">

    <div class="row" style="display:flex;">

        <div class="col-6 col-sm-4 col-md-3" style="border-style:none; border-color:red;">
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

        <div class="col-6 col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fecha:</label>
                <input type="date" name="date" id="date" class="form-control" value="<?= $date ?>">
            </div>    
        </div>
    </div>

    <div class="row" style="display:flex;">
        <div class="col-6 col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Cliente:</label>
                <?php
                    $ar         = array();
                    $result     = $this->db->query("select a.id, upper(a.name) name from tec_customers a order by upper(a.name)")->result_array();
                    $ar         = $this->fm->conver_dropdown($result, "id", "name", array(''=>'Seleccione'));
                    echo form_dropdown('cliente',$ar,$cliente,'class="form-control tip" id="cliente" required="required" onchange="cargar_vta(this)"');
                ?>
            </div>
        </div>
        
        <div class="col-6 col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Documento:</label>
                <span id="spn_sale_id"></span>
            </div>
        </div>
    </div>
    
    <div class="row" style="display:flex;">
        <div class="col-6 col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <label for="">Monto:</label>
            <input type="text" name="monto" id="monto" value="<?= set_value('monto') ?>" class="form-control">
        </div>

        <div id="preparo" class="col-4 col-sm-1" style="border-style:none; border-color:red; padding-top:8px;">
            <br><button type="submit" class="btn btn-primary"><b>Grabar</b></button>
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
    </div>

    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-11">
        </div>
    </div>

</section>
</form>

<script type="text/javascript">
    
    $(document).ready(function() {

    });

    function activo1(){
    }

    function cargar_vta(obj){
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            document.getElementById("spn_sale_id").innerHTML = this.responseText;
        }
        xhttp.open("GET", '<?= base_url("cuentas_cobrar/cargar_vta/") ?>' + obj.value, true);
        xhttp.send();
    }

</script>
<style type="text/css">
    /*.table td:nth-child(4) { text-align: right;}*/
</style>