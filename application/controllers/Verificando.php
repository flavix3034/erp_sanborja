<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Verificando extends CI_Controller 
{

    public $nIgv = 18;
    function __construct() {
        
        parent::__construct();

    }

    function index(){
        //$this->load->view($this->themes . "default/views/verificando/index");

        $this->data['page_title'] = "Verificando Comprobantes";
        //die("Midterms");
        $this->load->view('verificando/index', $this->data);
    }

    function analizar(){
        $id = $_GET["id"];

        $cSql = "select a.id,a.store_id tda, concat(a.serie,'-',a.correlativo) compro,a.date,a.total,a.product_tax,a.total_tax,a.grand_total, round(a.grand_total * 0.1 / 1.1,4) mitra,
            concat('|',b.product_id) product_id, b.product_name, b.net_unit_price, b.unit_price, b.item_tax, round(b.quantity,0) q,b.subtotal,b.tax
            from tec_sales a
            left join tec_sale_items b on a.id=b.sale_id
            where a.id = $id limit 1";

        $query = $this->db->query($cSql);

        // Parte 1  Cabecera -------------------------
        $result     = $query->result_array();
        $cols       = array("id","tda","compro","date","total","product_tax","total_tax","grand_total");
        $cols_titulos = array("id","tda","compro","date","total","product_tax","total_tax","grand_total");
        $ar_align   = array("1","1","1","1","1","1","1","1");
        $ar_pie     = array("1","1","1","1","1","1","1","1");

        echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie). "<br>";

        // -----DETALLES-----------------------------

        $cSql = "select a.id,a.store_id tda, concat(a.serie,'-',a.correlativo) compro,a.date,a.total,a.product_tax,a.total_tax,a.grand_total, round(a.grand_total * 0.1 / 1.1,4) mitra,
            concat('|',b.product_id) product_id, b.product_name, b.net_unit_price, b.unit_price, b.item_tax, round(b.quantity,0) q,b.subtotal,b.tax
            from tec_sales a
            left join tec_sale_items b on a.id=b.sale_id
            where a.id = $id";

        $query = $this->db->query($cSql);

        // Parte 2  Detalle ------------------------------
        $result     = $query->result_array();
        $cols       = array("product_id","product_name","net_unit_price","unit_price","item_tax","q","subtotal","tax");
        $cols_titulos = array("product_id","product_name","net_unit_price","unit_price","item_tax","q","subtotal","tax");
        $ar_align   = array("1","1","1","1","1","1","1","1");
        $ar_pie     = array("1","1","1","1","1","1","1","1");

        echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);


        //====================================================
        echo "<div class='row'>";
        echo "<div class='col-sm-12'>";
        echo "<h2 style=\"background-color:rgb(200,180,160)\">subtotal (precio con igv * Q): No intervienen en el calculo</h2>";

        $nS = 0;
        foreach($query->result() as $r){
            $nS     += $r->subtotal;
            $nGrand = $r->grand_total;
        }
        if(round($nS,2) == round($nGrand,2)){
            $rpta = "OK";
        }else{
            $rpta = "Está incoherente";
        }

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>P.con Igv</th><th>Q</th><th>Segun Suma de subtotales</th><th>Es Grand_total</th><th>Rpta</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>-</td><td>-</td><th>{$nS}</td><td>{$nGrand}</td><td>{$rpta}</td>";
        echo "</tr>";
        echo "</table>";

        //=================================================

        $rpta = "";
        foreach($query->result() as $r){
            $net_u  = $r->net_unit_price*1;
            $u      = $r->unit_price*1;
            $net_u_c = round($u/(1+($this->nIgv/100)),4);
            if( round($net_u_c == round($net_u,4)) ){
                $rpta = "OK";
            }else{
                $rpta = "Incoherencia";
                break;
            }
            
        }

        echo "<h2>net_unit_price:</h2>";

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>net_unit_price real</th><th>net_unit_price calculado</th><th>Rpta</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>{$net_u}</td><th>{$net_u_c}</td><td>{$rpta}</td>";
        echo "</tr>";
        echo "</table>";

        //==================================================

        $rpta = "";
        $sub = 0;
        foreach($query->result() as $r){
            $sub  = $r->net_unit_price * 1 * $r->q * ($this->nIgv/100);
            //$u     = $r->unit_price*1;
            $nSub = $r->item_tax*1;
            if(round($sub,2) == round($nSub,2)){
                $rpta = "OK";
            }else{
                //$r_2 .= "Calculado:".round($sub,2)."<br>";
                //$r_2 .= "Item_tax:".round($r->item_tax*1,2)."<br>";
                //$r_2 .= "Incoherencia";
                $rpta = "Incoherencia";
                break;
            }
        }

        echo "<h2 style=\"background-color:rgb(200,180,160)\">item_tax: Es el igv (del subtotal sin igv)</h2>";

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>r->net_unit_price * 1 * r->q * (Igv/100)</th><th>r->item_tax</th><th>Rpta</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>{$sub}</td><th>{$nSub}</td><td>{$rpta}</td>";
        echo "</tr>";
        echo "</table>";

        //=================================================== total:


        foreach($query->result() as $r){
            $parte_db           = $r->total;
            $parte_calculada    = $r->grand_total / (1 + ($this->nIgv/100));
        
            if(round($parte_db,2) == round($parte_calculada,2)){
                $rpta = "OK";
            }else{
                $rpta = "<b>Incoherencia </b>"; 
                //$r_2 .= "Calculado:".$parte_calculada."<br>";
                //$r_2 .= "total:".$parte_db."<br>";
            }
        }

        echo "<h2>total: Operaciones Gravadas, grand_total / 1.1</h2>";

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Total (tec_sale_items)</th><th>Grand_total/1.18</th><th>Rpta</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>{$parte_db}</td><th>{$parte_calculada}</td><td>{$rpta}</td>";
        echo "</tr>";
        echo "</table>";

        //===================================================
?>

<table border="1" width="100%">
    <tr>
        <td width="80%">
            <input type="text" name="consulta1" id="consulta1" value="<?= "update tec_sale_items set net_unit_price = unit_price / (1+({$this->nIgv}/100)),tax={$this->nIgv} where sale_id=$id" ?>" size="150">
        </td>
        <td width="20%">
            <button id="btn_1" onclick="ejecutar(document.getElementById('consulta1').value)">Ejecutar</button>
        </td>
    </tr>

    <tr>
        <td>
            <input type="text" name="consulta2" id="consulta2" value="<?= "update tec_sale_items set subtotal = unit_price * quantity, item_tax = net_unit_price*1*quantity*({$this->nIgv}/100) where sale_id=$id" ?>" size="150">
       </td>
        <td>
            <button id="btn_2" onclick="ejecutar(document.getElementById('consulta2').value)">Ejecutar</button>
        </td>
    </tr>


    <tr>
        <td>
            <input type="text" name="consulta3" id="consulta3" value="<?= "update tec_sales set total = grand_total/(1+({$this->nIgv}/100)), total_tax = (grand_total/(1+({$this->nIgv}/100)))*({$this->nIgv}/100) where id = $id" ?>" size="150">
        </td>
        <td>
            <button id="btn_3" onclick="ejecutar(document.getElementById('consulta3').value)">Ejecutar</button>
        </td>
    </tr>

    <tr>
        <td>
            <button id="btn_4" onclick="envio_sunat()">Enviar a Sunat</button>
        </td>
        <td id="popper">
            
        </td>
    </tr>

</table>

<?php
    }

    function ver_rpta_sunat(){
        $id = $_GET["id"];
        
        $cmd = "cd /home/lacabktv/public_html/POS/log && ls -la *_".$id."*";
        $salida = shell_exec($cmd);
        $name_file = "";
        
        if(strlen($salida)==0){
            $salida = "DATO VACIO";
        }else{
        
            $ultima_fila = trim(substr($salida,-120));

            for($n=strlen($ultima_fila)-1; $n>0; $n--){
                $letra = substr($ultima_fila,$n,1);
                if($letra == " "){
                    break;
                }
            }

            if($n>0){
                $name_file = trim(substr($ultima_fila,$n));
                $cmd = "cat /home/lacabktv/public_html/POS/log/".$name_file;
                //echo $cmd;
                $salida = shell_exec($cmd);    
            }else{
                $salida = "No hay archivo...";
            }

        }
        echo $salida;

    }

    function ejecuta_consulta(){
        $origen = $_GET["dato1"];
        $cSql = $this->descriptar($origen);
        $query = $this->db->query($cSql);
        echo "Se ejecutó consulta : $cSql";
    }

    function descriptar($cSql){
        $cSql = str_replace("wMundano", "select", $cSql);
        $cSql = str_replace("wrisco", "*", $cSql);
        $cSql = str_replace("wdesde", "from", $cSql);
        $cSql = str_replace("_ier__", "inner", $cSql);
        $cSql = str_replace("_lef__", "left", $cSql);
        $cSql = str_replace("_donde__", "where", $cSql);
        $cSql = str_replace("_-__", "-", $cSql);
        $cSql = str_replace("Mundiali", "%", $cSql);
        $cSql = str_replace("megusta_", "like", $cSql);
        $cSql = str_replace("j_eb_epu", "group", $cSql);
        return $cSql;
    }    

    /*
    function otros(){
        $cSql = "select a.id, date(a.date) fecha, a.total, a.product_tax, a.total_tax, a.grand_total, a.serie, a.correlativo, a.envio_electronico envio, a.dir_comprobante".
        " from tec_sales a".
        " where a.dir_comprobante!=''".
        " order by a.id";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
        }
    }
    */

}