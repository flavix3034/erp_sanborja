<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
    .mobex{
        padding: 10px;
    }
    .tit_table{
        /*background-color: rgb(220,220,220);*/
        padding:  10px 10px 6px 10px;
        font-size: 14px;
        /*text-align: center!important;
        border-style: solid!important;
        border-color:red!important;
        border-width:1px;*/
    }
    .un-modal{
        background-color:#fff;
    }
    .un-cuerpo{
        background-color:#cde;
    }
    .un-titulo{
        background-color:#abc;
    }
    .un-pie{
        background-color:#abc;
    }

    .w3-button{-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}   
    .w3-display-topright{position:absolute;right:0;top:0}
    .w3-large{font-size:18px!important; margin: 10px;}
</style>

    <div class="row" style="margin-left:10px; margin-top:10px; border-style:none; border-color:red;">
        <div class="col-sm-12 col-md-10 col-lg-8" style="border-style:none; border-color:blue;">
            <?php 
                $nExiste_datos = 0;
                
                $result         = $this->db->query("select count(*) cantidad from tec_maestro_inv")->result_array();
                $nExiste_datos  = $this->fm->campo_result($result,"cantidad");

                if($nExiste_datos > 0){ 
                    $query = $this->inventarios_model->listar_cabecera_inventarios($_SESSION["store_id"]);
                    //echo "Estoy aqui...";

            ?>
            <table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;">
                <thead>
                    <tr>
                    	<th style="max-width: 20px;" class="tit_table">id</th>
                    	<th class="tit_table">Tienda</th>
                        <th class="tit_table">Inicio</th>
                        <th class="tit_table">Fin</th>
                        <th class="tit_table">Responsable</th>
                        <th class="tit_table">Estado</th>
                        <th class="tit_table">-</th>
                    </tr>
                </thead>
                <?php foreach($query->result() as $r){ ?>
                <tbody>
                    <td class="mobex"><?= $r->id ?></td>
                    <td class="mobex"><?= $r->tienda ?></td>
                    <td class="mobex"><?= $r->fecha_i ?></td>
                    <td class="mobex"><?= $r->fecha_f ?></td>
                    <td class="mobex"><?= $r->responsable ?></td>
                    <td class="mobex"><?= ($r->finaliza=='1' ? 'Cerrado' : '') ?></td>
                    <td class="mobex">
                        <a href="#" onclick="modificar(<?= $r->id ?>)" class="btn btn-primary btn-sm">Modificar</a>
                        <a href="<?= base_url("inventarios/ver_inventario/" . $r->id) ?>" class="btn btn-primary btn-sm">Ver</a>
                    </td>
                </tbody>
                <?php } ?>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <?php
                }else{
                    echo "Todavía no existen datos...";
                }
            ?>
        </div>
    </div>

    <span id="botone1" onclick="document.getElementById('id01').style.display='block'"></span>
    <input type="hidden" name="id" id="id">
    <div class="row" style="margin-left:10px;">
        <div class="col-sm-12" style="border-radius: 20px;">
            <!--<button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-black" id="boto">Open Modal</button>-->
            
            <div id="id01" class="row" style="display:none">
                <div class="col-sm-12 col-lg-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <header class="row un-titulo"> 
                                <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright w3-large">&times;</span>
                                <p style="font-size: 16px; font-weight:bold; margin: 10px; font-style: italic;">Editar Inventario</p>
                            </header>
                            <div class="row un-cuerpo">
                                <div class="col-sm-12">
                                    <div class="row" style="margin:10px">
                                        <div class="col-sm-6 col-sm-4">
                                            <label>Fecha Inicio:</label>
                                            <input type="datetime-local" name="fecha_i" id="fecha_i">
                                        </div>
                                        <div class="col-sm-6 col-sm-4">
                                            <label>Fecha Fin:</label>
                                            <input type="datetime-local" name="fecha_f" id="fecha_f">
                                        </div>
                                    </div>

                                    <div class="row" style="margin:10px">
                                        <div class="col-sm-6 col-md-4">
                                            <label>Responsable:</label>
                                            <input type="text" name="responsable" id="responsable">
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <button type="button" class="btn btn-primary" style="margin-top:20px" onclick="actualizar()">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <footer class="row un-pie">
                                <p></p>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">

    //$("#botone1").click()

    function modificar(id){
        document.getElementById("botone1").click()
        $.ajax({
            data    :{id: id},
            url     :"<?= base_url("inventarios/obtener_inv") ?>",
            type    :"get",
            success :function(res){
                obj = JSON.parse(res)
                console.log(obj.fecha_i)
                console.log(obj.fecha_f)
                var a_ = obj.fecha_i
                var b_ = obj.fecha_f
                document.getElementById("fecha_i").value        = a_.substr(0,10) + "T" + a_.substr(11,8)
                document.getElementById("fecha_f").value        = b_.substr(0,10) + "T" + b_.substr(11,8)
                document.getElementById("responsable").value    = obj.responsable
                document.getElementById("id").value             = id
            } 
        })
    }

    function actualizar(){
        $.ajax({
            data    : {
                id          : $("#id").val(),
                fecha_i     : $("#fecha_i").val(),
                fecha_f     : $("#fecha_f").val(),
                responsable : $("#responsable").val()
            },
            url     : "<?= base_url("inventarios/editar_inv") ?>",
            type    : "get",
            success : function(res){
                if (res == "OK"){
                    alert("Se actualizó correctamente.")
                    location.reload()
                }else{
                    alert("Por algún momento no se pudo grabar.")
                }
            }
        })
    }

</script>