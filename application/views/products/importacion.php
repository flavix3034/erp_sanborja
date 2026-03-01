<?= form_open_multipart(base_url("products/leer_csv"), 'class="validation" id="form1"'); ?>

<div class="row" style="margin:10px;">
    <div class="col-xs-12 col-sm-10 text-left">
        <?= isset($respuesta) ? $respuesta : 'Sin respuesta...' ?>
    </div>
</div>

<div class="row" style="margin:10px;">
    <div class="col-xs-6 col-sm-4 text-left">
        <input type="file" name="fichero1" class="form-control" required>
    </div>
</div>
<div class="row" style="margin:10px;">
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-8">
                <select name="opciones_csv" class="form-control">
                    <option value="">--Elija--</option>
                    <option value="1">CSV (separador puntoycoma y datos entrecomillados)</option>
                    <option value="2">CSV (solo con separador puntoycoma)</option>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top: 15px;">
            <div class="col-sm-4 text-left">
                <button type="submit" class="btn btn-primary">Subir</button>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg-3" style="border-style:dotted;">
        Click para descargar plantilla.<br>
        <b><a href="<?= base_url("downloads/formato_importacion_productos.csv") ?>">Descargar</a></b><br><br>

        <span style="font-weight:bold;margin-top:15px;">Codigo de Categorias:</span>
        <?php
            $result         = $this->db->select('*')->get("tec_categories")->result_array();
            $cols           = array("id","name");
            $cols_titulos   = array("Cod","Categoria");
            $ar_align       = array("2","0");
            $ar_pie         = array("1","1");
            echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
        ?>
    </div>
</div>


<div class="row" style="margin:10px;">    
</div>


<?= form_close(); ?>