<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row" style="margin-top:15px">
    <div class="col-sx-12 col-sm-12 col-md-12 col-lg-12" style="overflow: scroll;">
        <?php

            /*$cora = $this->db->select("a.id, a.persona, concat(c.name,' ',c.marca,' ',c.modelo) producto, a.cantidad, d.name store_id, a.fechah, a.inv_id,
                a.tipo_mov, a.obs, e.name store_id_destino, a.confirmado, b.metodo, concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><i class=\"glyphicon glyphicon-remove\"></i></a>') opciones")
                ->from('tec_movim a')
                ->join('tec_metodos_inv b', 'a.metodo = b.id','inner')
                ->join('tec_products c','a.product_id = c.id')
                ->join('tec_stores d','a.store_id = d.id')
                ->join('tec_stores e','a.store_id_destino = e.id','left')
                ->order_by('a.id','desc')->get_compiled_select();
            die($cora);*/

            $result = $this->db->select("a.id, a.persona, concat(c.name,' ',c.marca,' ',c.modelo) producto, a.cantidad, d.name store_id, a.fechah, a.inv_id,
                a.tipo_mov, a.obs, e.name store_id_destino, a.confirmado, b.metodo, concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><i class=\"glyphicon glyphicon-remove\"></i></a>') opciones")
                ->from('tec_movim a')
                ->join('tec_metodos_inv b', 'a.metodo = b.id','inner')
                ->join('tec_products c','a.product_id = c.id')
                ->join('tec_stores d','a.store_id = d.id')
                ->join('tec_stores e','a.store_id_destino = e.id','left')
                ->order_by('a.id','desc')->get()->result_array();
            
            $cols = array("id","persona","producto","cantidad","store_id","tipo_mov","fechah","metodo","confirmado","store_id_destino","obs","opciones");
            $cols_titulos = array("id","persona","producto","cantidad",
                "<span style=\"color:FireBrick\">Tienda<br>Origen</span>",
                "<span style=\"color:FireBrick\">Tipo</span>",
                "fecha-Hora","Motivo",
                "Confirma<br>Traslado",
                "<span style=\"color:green\">Tienda<br>Destino</span>",
                "Observaciones","op");

            $ar_align   = array("1","1","0","1","0","1","0","1","0","1","0","1");
            $ar_pie     = array("","","","","","","","","","","","");
            
            echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
        ?>
    </div>
    
</div>
<script type="text/javascript">

    function eliminar(id){
        Swal.fire({
            title: 'Estás a punto de eliminar un movimiento. Ten en cuenta que esta acción puede tener consecuencias importantes en tu inventario y registros de ventas', showDenyButton: true, showCancelButton: false, 
            confirmButtonText: 'Aceptar', 
            denyButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '¿Desea eliminar la compra?', showDenyButton: true, showCancelButton: false, 
                    confirmButtonText: 'Sí', 
                    denyButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            data    :{id:id},
                            type    :"get",
                            url     :"<?= base_url("inventarios/eliminar_movimiento") ?>",
                            success :function(res){
                                var obj = JSON.parse(res)
                                if (obj.rpta == "OK"){
                                    Swal.fire("Se logra eliminar dicho Movimiento.", "", "success");
                                    location.reload()
                                }
                            }
                        })
                    }
                });
                
            }
        });

    }

    
    
</script>