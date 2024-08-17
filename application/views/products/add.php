<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
    if($modo == 'insert'){
        $id         = ""; 
        $code       = "";
        $name       = "";
        $category_id = "";
        $unidad     = "";
        $alert_cantidad = "";
        $price      = "";
        $imagen     = "";
        $marca      = "";
        $modelo     = "";
        $color      = "";
        $precio_x_mayor = "";
        $prod_serv  = "";
    }else{
        $code       = $row->code;
        $name       = $row->name;
        $category_id = $row->category_id;
        $unidad     = $row->unidad;
        $alert_cantidad = $row->alert_cantidad;
        $price      = $row->price;
        $imagen     = $row->imagen;
        $marca      = $row->marca;
        $modelo     = $row->modelo;
        $color      = $row->color;
        $precio_x_mayor = $row->precio_x_mayor;
        $prod_serv  = $row->prod_serv;
    }
?>
<style type="text/css">
    .ventas{
        border-style:none; border-width: 1px; border-color:rgb(170,170,170);
    }
    .filitas{
        margin-top: 15px;
    }   
</style>

    <div class="row filitas">
        <div class="col-sm-12 ventas">
            <a href="<?= base_url() ?>products/importacion"><span style="font-weight:bold;color:blue">Importar Productos....</span></a><hr>
        </div>
    </div>


<?= form_open_multipart(base_url("products/save"), 'method="post" name="form1" id="form1"'); ?>

    <div class="row filitas"> 
    
        <div class="col-sm-4 col-lg-1 ventas">
            <label>Id:</label>
            <?= form_input('id', $id, 'class="form-control tip" id="code" readonly'); ?>
        </div>
        
        <div class="col-sm-4 col-lg-2 ventas">
            <label>Code:</label>
            <?= form_input('code', $code, 'class="form-control tip" id="code"'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas" style="margin-left:10px;">
            <label>Nombre:</label>
            <?= form_input('name', $name, 'class="form-control tip" id="name" required"'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Marca:</label>
            <?= form_input('marca', $marca, 'class="form-control tip" id="marca"'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Modelo:</label>
            <?= form_input('modelo', $modelo, 'class="form-control tip" id="modelo"'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Color:</label>
            <?= form_input('color', $color, 'class="form-control tip" id="color"'); ?>
        </div>

    </div>
    <div class="row filitas"> 

        <div class="col-sm-4 col-lg-3 ventas">
            <label>Categoria:</label>
            <?php 
                $result = $this->db->query("select id,name from tec_categories order by name")->result_array();
                $ar     = $this->fm->conver_dropdown($result,"id","name");
                echo form_dropdown('category_id',$ar,'','class="form-control tip" id="category_id" required="required"');
            ?>

        </div>

        <!-- unidad, alert_cantidad, price, imagen -->
        <div class="col-sm-4 col-lg-2 ventas">
            <label>Unidad:</label>
            <?php 
                $result = $this->db->query("select id, descrip from tec_unidades order by id")->result_array();
                $ar     = $this->fm->conver_dropdown($result,"id","descrip");
                echo form_dropdown('unidad',$ar,'','class="form-control tip" id="unidad" required="required"');
            ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Alerta cantidad:</label>
            <?= form_input('alert_cantidad', $alert_cantidad, 'class="form-control tip" id="alert_cantidad" required'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Precio:</label>
            <?= form_input('price', $price, 'class="form-control tip" id="price" required'); ?>
        </div>

        <div class="col-sm-4 col-lg-2 ventas">
            <label>Precio x Mayor:</label>
            <?= form_input('precio_x_mayor', $precio_x_mayor, 'class="form-control tip" id="precio_x_mayor" required'); ?>
        </div>

    </div>
    
    <div class="row filitas">
        <!--<div class="col-sm-4 col-lg-2 ventas">
            <label>Producto:</label>
            <?php 
                $ar     = array(""=>"--ELIGIR--","P"=>"PRODUCTO","S"=>"SERVICIO");
                echo form_dropdown('prod_serv', $ar, $prod_serv, 'class="form-control tip" id="prod_serv" required="required"');
            ?>
        </div>-->

        <div class="col-sm-5 col-lg-4 ventas">
            <label>imagen:</label>
            <input type="text" name='imagen' value="<?= $imagen ?>" class="form-control tip" id="imagen" readonly>
        </div>
        <div class="col-sm-7 col-lg-5 ventas">
            <label>Subir</label>
            <input type="file" name='archivo' class="form-control tip" id="archivo">
            <input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
        </div>

    </div>

    <div class="row filitas">
        <div class="col-sm-4 col-lg-2 ventas">
            <br>
            <button type="button" class="btn btn-primary btn-lg" onclick="validar()">Guardar</button>
            <!--<button type="submit" class="form-control btn btn-primary">Guardar</button>-->
        </div>
    </div>

<?= form_close() ?>

<script type="text/javascript">
    ar_items = new Array()

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

    function validar(){
        var name        = document.getElementById("name").value
        var category_id = document.getElementById("category_id").value
        var unidad      = document.getElementById("unidad").value
        var alert_cantidad = document.getElementById("alert_cantidad").value
        if (name.length == 0){
            alert("Debe ingresar correctamente el nombre")
            return false
        }
        if(category_id.length == 0){
            alert("Debe ingresar correctamente la Categoria")
            return false
        }
        if(unidad.length == 0){
            alert("Debe ingresar correctamente la Unidad")
            return false
        }
        if(alert_cantidad.length == 0){
            alert("Debe ingresar correctamente alerta de cantidad")
            return false
        }

        /*var archivo = document.getElementById("archivo").value
        if(archivo.length > 0){
            var nPos = archivo.indexOf("\\",4)
            document.getElementById("imagen").value = archivo.substr(nPos+1,40)
        }*/

        document.form1.submit()
    }

    function mensaje(cad){
        alert(cad)
    }
    
    function llenar(){
    }
</script>