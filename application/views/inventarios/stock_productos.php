<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    if($query_stock == false){
        echo("No existe el inventario x default, cambie el Inventario vigente en Configuracion.<br>");
        die("<a href=\"" . base_url("welcome/cierra_sesion") . "\">Regresar</a>");
    }
?>
<style type="text/css">
    tbody{
        font-family: Arial, courier, verdana;
    }
</style>
<div class="row" style="margin-top:10px;">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
        <?php
            $estilo             = "padding:5px 4px;";
            $estilo_tit         = "padding:10px 4px;font-style:normal;";
            $estilo_tit_minimo  = "padding:10px 4px;font-style:normal;color:rgb(120,120,120);";
            $estilo_alarma      = "padding:5px 4px;color:red;";
            $estilo_minimo      = "padding:5px 4px; color:rgb(120,120,120);";
            
            echo "<table border='1'>";
            echo "<tr>";
            echo $this->fm->celda("Producto",0,$estilo_tit."min-width:200px;");
            //echo $this->fm->celda("Inventario<br>Inicial",0,$estilo_tit);
            //echo $this->fm->celda("Comprado",0,$estilo_tit);
            //echo $this->fm->celda("Vendido",0,$estilo_tit);
            //echo $this->fm->celda("Ingresos",0,$estilo_tit);
            //echo $this->fm->celda("Salidas",0,$estilo_tit);
            echo $this->fm->celda("Stock",0,$estilo_tit);
            echo $this->fm->celda("Cantidad<br>Mínima",0,$estilo_tit_minimo);
            echo "</tr>";    
            
            foreach($query_stock->result() as $r){
                //die("Luz...");
                echo "<tr>";
                echo $this->fm->celda($r->name,0,$estilo);
                //echo $this->fm->celda(number_format($r->cantidad_inicial,0),0,$estilo);
                //echo $this->fm->celda(number_format($r->cantidad_comprada,0),0,$estilo);
                //echo $this->fm->celda(number_format($r->cantidad_vendida,0),0,$estilo);
                //echo $this->fm->celda(number_format($r->ingreso,0),0,$estilo);
                //echo $this->fm->celda(number_format($r->salida,0),0,$estilo);
                $estilo_per = ($r->stock < $r->alert_cantidad ? $estilo_alarma : $estilo);
                echo $this->fm->celda($r->stock*1,2,$estilo_per);
                echo $this->fm->celda($r->alert_cantidad,2,$estilo_minimo);
                echo "</tr>";
            }
            echo "</table>";
        ?>
    </div>

</div>