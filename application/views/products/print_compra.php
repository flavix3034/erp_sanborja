<?php
header("Access-Control-Allow-Origin: *");
$habilitacion = '';
if(isset($viene_de_guardar)){
    if($viene_de_guardar == '1'){
        $habilitacion = 'disabled';
    }
}
?>
<?= form_open_multipart(base_url("products/exportar_csv"), 'class="validation" id="form_compra2"'); ?>
<div class="row filitas" style="padding-top:15px"> 
    <div class="col-sm-3 col-md-2">
        <input type="text" name="nro_compra" id="nro_compra" placeholder="Nro_compra" value="0" class="form-control">
    </div>
    <div class="col-sm-3 col-md-2">
        <button type="button" onclick="visualizar_impresionx()" class="btn btn-success">Agregar</button>
    </div>
    
    <div class="col-sm-3 col-md-3">
        <button type="button" onclick="confirmacion()" class="btn btn-primary btn-lg">Descargar CSV</button>
    </div> 

    <div class="col-sm-3 col-md-2">
        <button type="button" onclick="resetear_impresionx()" class="btn btn-danger">Reset</button>
    </div>
</div>
</form>

<?= form_open_multipart("products/save_impresionx", 'class="validation" id="form_compra"'); ?>
<div class="row filitas" style="padding-top:15px"> 
    <div id="taxi" class="col-sm-10 col-md-8">
    </div>
</div>

<div class="row filitas" style="padding-top:15px"> 
    <div class="col-sm-3 col-md-2">
        <button type="submit" class="btn btn-success btn-lg" <?= $habilitacion ?>>&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button>
    </div>

    <div class="col-sm-3 col-md-2">
        <!--<button type="button" class="btn btn-danger btn-lg" onclick="sincronizar_impresionx()">Sincronizar</button>-->
        <a href="http://localhost/procesos-surco/traer_info_remota.php" class="btn btn-danger btn-lg" target="_blank">Sincronia</a>
    </div>
    <!--<a href="http://localhost/procesos-surco/traer_info_remota.php" target="_blank" class="btn btn-primary"><span>Sincronizar</span></a>-->
</div>
<?= form_close(); ?>

<script type="text/javascript">
    var base_url = "<?= base_url() ?>";
    function sincronizar_impresionx(){
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function(){
            alert(this.responseText);
        }
        xhttp.open("GET", "http://localhost/procesos-surco/traer_info_remota.php", true);
        xhttp.send();
    }
</script>
<script type="text/javascript" src="<?=base_url("assets/js/print_compra.js") ?>"></script>