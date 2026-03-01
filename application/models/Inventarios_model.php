<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventarios_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    function listar_cabecera_inventarios($store_id=""){
        
        if(strlen($store_id)==0){
        	return false;
        }

        $store_id = $this->fm->contra_inyeccion($store_id);

        if($_SESSION["group_id"] == '1'){
            $cad_store = "";
        }else{
            $cad_store = "and m.store_id = {$store_id}";
        }
        $cSql = "select m.id, m.store_id, s.state tienda, m.fecha_i, m.fecha_f, m.responsable, m.finaliza 
        	from tec_maestro_inv m
        	inner join tec_stores s on m.store_id = s.id
        	where 1=1 {$cad_store}"; // 

        //echo $store_id;
        return $this->db->query($cSql);
    }

    function stock($id_inv, $store_id){
        /*
        // Averiguando la fecha final del inventario
        $fecha_f = "";
        if(isset($id_inv) && strlen($id_inv."") > 0){
            $cSql = "select fecha_f from tec_maestro_inv where id = {$id_inv}";
            $query = $this->db->query($cSql);
        
            foreach($query->result() as $r){
                $fecha_f = $r->fecha_f;
            }
        }
        */
    /*
        if(strlen($fecha_f)>0){
            $cSql = "select a.id, concat(a.name,' ',a.marca,' ',a.modelo,' ',a.color) name, a.alert_cantidad, i.cantidad_inicial, compras.cantidad_comprada, ventas.cantidad_vendida, movim.ingreso, movim.salida,
                if(isnull(i.cantidad_inicial),0,i.cantidad_inicial) 
                + if(isnull(compras.cantidad_comprada),0,compras.cantidad_comprada) 
                - if(isnull(ventas.cantidad_vendida),0,ventas.cantidad_vendida) 
                + if(isnull(movim.ingreso),0,movim.ingreso) 
                - if(isnull(movim.salida),0,movim.salida) as stock
                from tec_products a
                left join (
                    select tec_inventarios.product_id, tec_inventarios.cantidad cantidad_inicial
                    from tec_inventarios
                    inner join tec_maestro_inv on tec_inventarios.product_id = tec_maestro_inv.id
                    where tec_maestro_inv.id = {$id_inv}
                ) i on a.id = i.product_id
                left join (
                    select com_i.product_id, sum(com_i.cantidad) cantidad_comprada from tec_compras com
                    inner join tec_compra_items com_i on com.id = com_i.compra_id
                    where com.store_id='{$store_id}' and com.fecha_ingreso > '{$fecha_f}'
                    group by com_i.product_id
                ) compras on a.id = compras.product_id
                left join (
                    select sxi.product_id, sum(sxi.quantity) cantidad_vendida 
                    from tec_sales sx 
                    inner join tec_sale_items sxi on sx.id = sxi.sale_id
                    where sx.store_id='{$store_id}' and sx.date > '{$fecha_f}' and sx.anulado != '1'
                    group by sxi.product_id
                ) ventas on a.id = ventas.product_id
                left join (
                    select mo.product_id, sum(if(mo.tipo_mov='I', mo.cantidad, 0)) Ingreso, sum(if(mo.tipo_mov='S', mo.cantidad, 0)) Salida
                    from tec_movim mo
                    where mo.store_id='{$store_id}' and fechah > '{$fecha_f}'
                    group by mo.product_id 
                ) movim on a.id = movim.product_id 
                where a.activo='1' and a.prod_serv='P' order by a.name";
            return $this->db->query($cSql);
        }else{
    */
            $cSql = "select a.id, a.name, a.alert_cantidad, 0 cantidad_inicial, compras.cantidad_comprada, ventas.cantidad_vendida, movim.ingreso, movim.salida,
                if(isnull(compras.cantidad_comprada),0,compras.cantidad_comprada) 
                - if(isnull(ventas.cantidad_vendida),0,ventas.cantidad_vendida) 
                + if(isnull(movim.ingreso),0,movim.ingreso) 
                - if(isnull(movim.salida),0,movim.salida) as stock
                from tec_products a
                left join (
                    select com_i.product_id, sum(com_i.cantidad) cantidad_comprada from tec_compras com
                    inner join tec_compra_items com_i on com.id = com_i.compra_id
                    where com.store_id='{$store_id}'
                    group by com_i.product_id
                ) compras on a.id = compras.product_id
                left join (
                    select sxi.product_id, sum(sxi.quantity) cantidad_vendida 
                    from tec_sales sx 
                    inner join tec_sale_items sxi on sx.id = sxi.sale_id
                    where sx.store_id='{$store_id}' and sx.anulado != '1'
                    group by sxi.product_id
                ) ventas on a.id = ventas.product_id
                left join (
                    select mo.product_id, sum(if(mo.tipo_mov='I', mo.cantidad, 0)) Ingreso, sum(if(mo.tipo_mov='S', mo.cantidad, 0)) Salida
                    from tec_movim mo
                    where mo.store_id='{$store_id}'
                    group by mo.product_id 
                ) movim on a.id = movim.product_id 
                where a.activo='1' and a.prod_serv='P' order by a.name";
            return $this->db->query($cSql);
        //}
    }

    function kardex($product_id, $id_inv, $store_id){
        // Averiguando la fecha final del inventario
        $fecha = "";
        if(isset($id_inv) && strlen($id_inv."") > 0){
            $cSql = "select fecha_f from tec_maestro_inv where id = {$id_inv}";
            $query = $this->db->query($cSql);
            //$fecha_f = "";
            foreach($query->result() as $r){
                $fecha = $r->fecha_f;
            }
        }

        if(strlen($fecha)>0){
            //$fecha      = "2022-03-01";
            //$product_id = "1";
            //$store_id   = "1";
            //die("IDragon");
            $cSql = "select a.* from 
                (
                select date(i.fecha) fecha, if(isnull(i.cantidad),0,i.cantidad) cantidad, 'INICIAL' tipo 
                    from tec_inventarios i
                    where i.product_id = {$product_id}
                union 
                select date(com.fecha_ingreso) fecha, if(isnull(com_i.cantidad),0,com_i.cantidad) cantidad, 'COMPRA' tipo 
                    from tec_compras com
                    inner join tec_compra_items com_i on com.id = com_i.compra_id
                    where com.store_id='{$store_id}' and com.fecha_ingreso > '{$fecha}' and com_i.product_id = {$product_id}
                union
                select date(sx.`date`) fecha, sxi.quantity cantidad, 'VENTA' tipo
                    from tec_sales sx 
                    inner join tec_sale_items sxi on sx.id = sxi.sale_id
                    where sx.store_id='{$store_id}' and sx.date > '{$fecha}' and sx.anulado != '1' and sxi.product_id = {$product_id}
                union
                select date(mo.fechah) fecha, mo.cantidad, if(mo.tipo_mov='I','INGRESO','SALIDA') tipo
                    from tec_movim mo
                    where mo.store_id='{$store_id}' and fechah > '{$fecha}' and mo.product_id = {$product_id}
                ) a order by a.fecha";            
            $query_kardex = $this->db->query($cSql);
        }else{
            $cSql = "select a.* from 
                (
                select date(i.fecha) fecha, if(isnull(i.cantidad),0,i.cantidad) cantidad, 'INICIAL' tipo 
                    from tec_inventarios i
                    where i.product_id = {$product_id}
                union 
                select date(com.fecha_ingreso) fecha, sum(if(isnull(com_i.cantidad),0,com_i.cantidad)) cantidad, 'COMPRA' tipo 
                    from tec_compras com
                    inner join tec_compra_items com_i on com.id = com_i.compra_id
                    where com.store_id='{$store_id}' and com_i.product_id = {$product_id}
                    group by date(com.fecha_ingreso)
                union
                select date(sx.`date`) fecha, sum(sxi.quantity) cantidad, 'VENTA' tipo
                    from tec_sales sx 
                    inner join tec_sale_items sxi on sx.id = sxi.sale_id
                    where sx.store_id='{$store_id}' and sx.anulado != '1' and sxi.product_id = {$product_id}
                    group by date(sx.`date`)
                union
                select date(mo.fechah) fecha, mo.cantidad, if(mo.tipo_mov='I','INGRESO','SALIDA') tipo
                    from tec_movim mo
                    where mo.store_id='{$store_id}' and mo.product_id = {$product_id}
                ) a order by a.fecha";            
            //die($cSql);
            $query_kardex = $this->db->query($cSql);
        }

        $estilo             = "padding:5px 8px;";
        $estilo_tit         = "padding:10px 4px;font-style:normal;";
        $estilo_tit_minimo  = "padding:10px 4px;font-style:normal;color:rgb(120,120,120);";
        $estilo_alarma      = "padding:5px 4px;color:red;";
        $estilo_minimo      = "padding:5px 4px; color:rgb(120,120,120);";
        
        echo "<h2>" . $this->getNombre_producto($product_id) . "</h2>";

        echo "<table border='1'>";
        echo "<tr>";
        echo $this->fm->celda("Fecha",0,$estilo_tit."min-width:200px;");
        echo $this->fm->celda("Tipo",0,$estilo_tit);
        echo $this->fm->celda("Cantidad",0,$estilo_tit);
        echo "</tr>";    
        
        $cont = 0;
        
        foreach($query_kardex->result() as $r){
            if($r->tipo != 'INICIAL'){
                echo "<tr>";
                echo $this->fm->celda($r->fecha,0,$estilo);
                echo $this->fm->celda($r->tipo,0,$estilo);
                echo $this->fm->celda(number_format($r->cantidad,0),2,$estilo);
                echo "</tr>";
                
                $signo = 1;
                if($r->tipo == "SALIDA" || $r->tipo == "VENTA"){
                    $signo = -1;
                }
                $cont += ($r->cantidad * 1) * ($signo);
            }
        }
        echo "<tr>";
        echo $this->fm->celda("",0,$estilo."background-color:rgb(130,130,130);");
        echo $this->fm->celda("Stock Actual",0,$estilo."background-color:rgb(130,130,130);");
        echo $this->fm->celda($cont,2,$estilo."background-color:rgb(130,130,130);");
        echo "</tr>";
        echo "</table>";
    }

    function kardex_guardar($product_id, $store_id){
        // ES LO MISMO QUE LA FUNCION KARDEX, PERO SE CENTRA EN REGISTRAR Y ENTREGAR EL STOCK DEL PRODUCTO
        
        // VERIFICA SI EXISTE EL PRODUCTO EN TABLA STOCK
        $cSql = "select id from tec_prod_store where product_id = ? and store_id = ?";
        $query = $this->db->query($cSql, array($product_id, $store_id));
        $existe = false;
        foreach($query->result() as $r){
            $existe = true;
        }
        if (!$existe){
            $ar["product_id"]   = $product_id;
            $ar["store_id"]     = $store_id;
            $this->db->set($ar)->insert('tec_prod_store');
        }

        // CALCULANDO EL STOCK
        $cSql = "select a.* from 
            (
            select date(i.fecha) fecha, if(isnull(i.cantidad),0,i.cantidad) cantidad, 'INICIAL' tipo 
                from tec_inventarios i
                where i.product_id = {$product_id}
            union 
            select date(com.fecha_ingreso) fecha, sum(if(isnull(com_i.cantidad),0,com_i.cantidad)) cantidad, 'COMPRA' tipo 
                from tec_compras com
                inner join tec_compra_items com_i on com.id = com_i.compra_id
                where com.store_id='{$store_id}' and com_i.product_id = {$product_id}
                group by date(com.fecha_ingreso)
            union
            select date(sx.`date`) fecha, sum(sxi.quantity) cantidad, 'VENTA' tipo
                from tec_sales sx 
                inner join tec_sale_items sxi on sx.id = sxi.sale_id
                where sx.store_id='{$store_id}' and sx.anulado != '1' and sxi.product_id = {$product_id}
                group by date(sx.`date`)
            union
            select date(mo.fechah) fecha, mo.cantidad, if(mo.tipo_mov='I','INGRESO','SALIDA') tipo
                from tec_movim mo
                where mo.store_id='{$store_id}' and mo.product_id = {$product_id}
            ) a order by a.fecha";            
        //die($cSql);
        $query_kardex = $this->db->query($cSql);

        //echo "<h2>" . $this->getNombre_producto($product_id) . "</h2>";

        $cont = 0;
        foreach($query_kardex->result() as $r){
            $signo = 1;
            if($r->tipo != 'INICIAL'){
                if($r->tipo == "SALIDA" || $r->tipo == "VENTA"){
                    $signo = -1;
                }
                
                $cont += ($r->cantidad * 1) * ($signo);

                //echo $product_id . "|" . $r->fecha . "|" . $r->tipo . "|" . $r->cantidad . "|" . $cont . "<br>";
            }
        }
        //echo "<br>";

        // POR ULTIMO GUARDAMOS ESTE VALOR EN LA TABLA TEC_PROD_STORE
        $ar = array();
        $ar['stock'] = ($cont >= 0 ? $cont : 0);
        
        $this->db->set($ar)->where('product_id',$product_id)->where('store_id',$store_id)->update("tec_prod_store");
        //echo $this->db->set($ar)->where('product_id',$product_id)->where('store_id',$store_id)->get_compiled_update("tec_prod_store") . "<br>"; 
        
        //$ar_rpta = array( $cont, '');
        return $cont;
    }

    function ver_inventario($id_inv){
        $parte1 = "<a href=\"#\" onclick=\"eliminar(";
        $parte2 = ")\">Eliminar</a>";
        $cSql = "select a.id, a.fecha, a.product_id, b.name productos, a.cantidad, a.unidad, c.descrip des_unidad, mi.store_id,
            concat('{$parte1}',a.id,'{$parte2}') op
            from tec_inventarios a
            inner join tec_maestro_inv mi on a.maestro_id = mi.id
            inner join tec_products b on a.product_id = b.id
            left join tec_unidades c on a.unidad = c.id
            where a.maestro_id = ? order by a.id desc limit ?";

        $result     = $this->db->query($cSql, array($id_inv,50000))->result_array();
        $cols       = array("id", "fecha", "product_id", "productos", "cantidad", "des_unidad", "op");
        $cols_titulos = array("id", "fecha", "ID Producto", "productos", "cantidad", "des_unidad", "op");
        $ar_align   = array("0","0","0","0","1","0","0");
        $ar_pie     = array("","","","","","","");
        return $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
    }

    function getNombre_producto($product_id){
        $cSql = "select name from tec_products where id = ?";
        $query = $this->db->query($cSql,array($product_id));
        foreach($query->result() as $r){
            return $r->name;
        }
        return "";
    }

    function inventario_vigente($store_id){
        $query = $this->db->select("id, name, dato")->from("tec_variables")->where("name","INVENTARIO")->where("store_id", $store_id)->get();
        foreach($query->result() as $r){
            $id_inv = $r->dato;
        }

        return "";
    }

    function listar_stock(){
        // LISTA TODOS LOS PRODUCTOS
        $cSql = "select b.name, b.marca, b.modelo, a.* from tec_prod_store a".
            " left join tec_products b on a.product_id=b.id".
            " where a.store_id = ".$_SESSION["store_id"]." and b.activo='1'".
            " order by b.name";
        //echo($cSql);
        return $this->db->query($cSql);
    }
}
