<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Servicios_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    function listar_servicios($estado="", $tecnico=""){
        $cad_estado = $cad_tecnico = "";
        
        if(strlen($estado)>0 && $estado!='0'){
            $cad_estado = " and a.estado = '".$estado."'";
        }
        if(strlen($tecnico)>0 && $tecnico!='0'){
            $cad_tecnico = " and a.tecnico_asignado = ".$tecnico;
        }
        
        $cSql = "select a.id, a.codigo, a.cliente_nombre, a.cliente_telefono, 
                    a.equipo_descripcion, a.estado, a.prioridad, a.fecha_recepcion,
                    ifnull(CONCAT(b.apellidos,' ',b.nombres),'Sin Asignar') tecnico_nombre,
                    concat('<a href=\"#\" onclick=\"modificar(',a.id,')\"><i class=\"glyphicon glyphicon-edit\"></i></a>&nbsp;&nbsp;
                    <a href=\"#\" onclick=\"eliminar(',a.id,')\"><i class=\"glyphicon glyphicon-remove\"></i></a>') op
                from tec_servicios_tecnicos a
                left join tec_empleados b on a.tecnico_asignado = b.id
                where a.activo='1' ".$cad_estado.$cad_tecnico."
                order by a.id desc";
        
        $result = $this->db->query($cSql)->result_array();
        
        $cols = array("id","codigo","cliente_nombre","cliente_telefono","equipo_descripcion","estado","prioridad","fecha_recepcion","tecnico_nombre","op");
        $cols_titulos = array("id","Código","Cliente","Teléfono","Equipo","Estado","Prioridad","Fecha Recepción","Técnico","Acciones");
        $ar_align = array("0","0","0","0","0","0","0","0","0","0");
        $ar_pie = array("","","","","","","","","","");
        
        return $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
    }

    function generar_codigo_servicio() {
        $cSql = "select max(id)+1 nuevo from tec_servicios_tecnicos where id < 99999";
        $query = $this->db->query($cSql);
        
        if($query->num_rows() > 0) {
            $nuevo_id = $query->row()->nuevo;
        } else {
            $nuevo_id = 1;
        }
        
        return "ST-" . str_pad($nuevo_id, 3, "0", STR_PAD_LEFT);
    }

    function guardar_servicio($data) {
        // Extraer items del POST antes de guardar en tec_servicios_tecnicos
        $items_keys = array('item', 'variant_id', 'descripo', 'quantity', 'cost', 'impuestos', 'obs', 'prod_serv_arr');
        $items_data = array();
        foreach($items_keys as $key) {
            if(isset($data[$key])) {
                $items_data[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        // Manejar tecnico_asignado
        if(isset($data['tecnico_asignado'])) {
            if(trim($data['tecnico_asignado']) === '') {
                $data['tecnico_asignado'] = null;
            } else {
                $data['tecnico_asignado'] = (int)$data['tecnico_asignado'];
            }
        }

        if($data['modo'] == 'insert') {
            $data['codigo'] = $this->generar_codigo_servicio();
            unset($data['modo']);

            $cSql = "select max(id)+1 nuevo from tec_servicios_tecnicos where id < 99999";
            $query = $this->db->query($cSql);

            if($query->num_rows() > 0) {
                $data['id'] = $query->row()->nuevo;
            } else {
                $data['id'] = 1;
            }
            $data['activo'] = '1';

            $result = $this->db->insert("tec_servicios_tecnicos", $data);
            if($result) {
                $this->guardar_items($data['id'], $items_data);
            }
            return $result;
        } else {
            $id = $data['id'];
            unset($data['modo']);
            unset($data['id']);

            $result = $this->db->set($data)->where('id',$id)->update("tec_servicios_tecnicos");
            if($result) {
                $this->guardar_items($id, $items_data);
            }
            return $result;
        }
    }

    function guardar_items($servicio_id, $items_data) {
        // Borrar items existentes
        $this->db->where('servicio_id', $servicio_id)->delete('tec_servicio_items');

        if(empty($items_data) || !isset($items_data['item'])) {
            return true;
        }

        $lim = count($items_data['item']);
        for($i = 0; $i < $lim; $i++) {
            $unit_price = floatval($items_data['cost'][$i]);
            $qty = floatval($items_data['quantity'][$i]);
            $imp = floatval($items_data['impuestos'][$i]);
            $net_price = $unit_price / (1 + ($imp / 100));

            $ar = array(
                'servicio_id'  => $servicio_id,
                'product_id'   => $items_data['item'][$i],
                'variant_id'   => isset($items_data['variant_id'][$i]) ? intval($items_data['variant_id'][$i]) : 0,
                'product_name' => $items_data['descripo'][$i],
                'prod_serv'    => $items_data['prod_serv_arr'][$i],
                'quantity'     => $qty,
                'unit_price'   => $unit_price,
                'impuesto'     => $imp,
                'subtotal'     => round($net_price * $qty, 2),
                'observaciones'=> isset($items_data['obs'][$i]) ? $items_data['obs'][$i] : null
            );
            $this->db->insert('tec_servicio_items', $ar);
        }
        return true;
    }

    function get_items_by_servicio($servicio_id) {
        $store_id = isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 1;
        $cSql = "SELECT a.*, IF(c.stock IS NULL, 0, c.stock) as stock_actual
                 FROM tec_servicio_items a
                 LEFT JOIN tec_prod_store c ON a.product_id = c.product_id AND COALESCE(c.variant_id,0) = COALESCE(a.variant_id,0) AND c.store_id = ?
                 WHERE a.servicio_id = ?
                 ORDER BY a.id";
        return $this->db->query($cSql, array($store_id, $servicio_id))->result();
    }

    function update_sale_id($servicio_id, $sale_id) {
        return $this->db->set('sale_id', $sale_id)
                        ->where('id', $servicio_id)
                        ->update('tec_servicios_tecnicos');
    }

    function get_servicio_by_id($id) {
        $cSql = "select a.*, CONCAT(b.apellidos,' ',b.nombres) tecnico_nombre
                from tec_servicios_tecnicos a
                left join tec_empleados b on a.tecnico_asignado = b.id
                where a.id = ".$id;
        
        return $this->db->query($cSql)->row();
    }

    function listar_tecnicos() {
        $cSql = "SELECT id, CONCAT(apellidos,' ',nombres) AS nombre, especialidad, telefono
                 FROM tec_empleados
                 WHERE activo='1' AND UPPER(cargo) = 'TECNICO'
                 ORDER BY apellidos, nombres";
        return $this->db->query($cSql)->result();
    }

    function get_estados_dropdown() {
        return array(
            "" => "-- Seleccione --",
            "RECIBIDO" => "RECIBIDO",
            "EN DIAGNOSTICO" => "EN DIAGNOSTICO", 
            "EN REPARACION" => "EN REPARACION",
            "ESPERA REPUESTOS" => "ESPERA REPUESTOS",
            "REPARADO" => "REPARADO",
            "ENTREGADO" => "ENTREGADO",
            "CANCELADO" => "CANCELADO"
        );
    }

    function get_prioridades_dropdown() {
        return array(
            "BAJA" => "BAJA",
            "NORMAL" => "NORMAL",
            "ALTA" => "ALTA",
            "URGENTE" => "URGENTE"
        );
    }

    function get_equipos_tipo_dropdown() {
        return array(
            "Computadora" => "Computadora",
            "Laptop" => "Laptop", 
            "Celular" => "Celular",
            "Tablet" => "Tablet",
            "Otro" => "Otro"
        );
    }

    function anular_servicio($id) {
        return $this->db->set(array('activo'=>''))->where(array('id' => $id))->update("tec_servicios_tecnicos");
    }

    function cambiar_estado($servicio_id, $estado_anterior, $estado_nuevo, $tecnico_id = null, $comentarios = null, $usuario_id = null) {
        // Actualizar estado del servicio
        $this->db->set('estado', $estado_nuevo)
                 ->set('updated_at', date('Y-m-d H:i:s'));
        
        if($tecnico_id) {
            $this->db->set('tecnico_asignado', $tecnico_id);
        }
        
        $this->db->where('id', $servicio_id)->update("tec_servicios_tecnicos");
        
        // Registrar en historial de estados
        $data_estado = array(
            'servicio_id' => $servicio_id,
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => $estado_nuevo,
            'tecnico_id' => $tecnico_id,
            'comentarios' => $comentarios,
            'usuario_id' => $usuario_id
        );
        
        return $this->db->insert("tec_servicios_estados", $data_estado);
    }

    function agregar_nota($servicio_id, $nota, $tipo_nota = 'GENERAL', $tecnico_id = null, $usuario_id = null) {
        $data = array(
            'servicio_id' => $servicio_id,
            'tipo_nota' => $tipo_nota,
            'nota' => $nota,
            'tecnico_id' => $tecnico_id,
            'usuario_id' => $usuario_id
        );
        
        return $this->db->insert("tec_servicios_notas", $data);
    }

    function get_historial_estados($servicio_id) {
        $cSql = "select a.*, CONCAT(b.apellidos,' ',b.nombres) tecnico_nombre
                from tec_servicios_estados a
                left join tec_empleados b on a.tecnico_id = b.id
                where a.servicio_id = ".$servicio_id."
                order by a.fecha_registro desc";
        
        return $this->db->query($cSql)->result();
    }

    function get_notas_servicio($servicio_id) {
        $cSql = "select a.*, CONCAT(b.apellidos,' ',b.nombres) tecnico_nombre
                from tec_servicios_notas a
                left join tec_empleados b on a.tecnico_id = b.id
                where a.servicio_id = ".$servicio_id."
                order by a.fecha_registro desc";
        
        return $this->db->query($cSql)->result();
    }

    // Métodos para DataTable
    function getServicios($estado="0",$tecnico="0"){
        try {
            $ar = array();
            $cad_estado = $cad_tecnico = "";
            
            if($estado != '0' && $estado != ''){
                $cad_estado = " and a.estado = '".$estado."'";
            }
            if($tecnico != '0' && $tecnico != ''){
                $ar[] = $tecnico;
                $cad_tecnico = " and a.tecnico_asignado = ?";
            }
            
            $cSql = "select a.id, a.codigo, a.cliente_nombre, a.cliente_telefono, 
                        a.equipo_descripcion, a.estado, a.prioridad, 
                        a.fecha_recepcion, ifnull(CONCAT(b.apellidos,' ',b.nombres),'Sin Asignar') tecnico_nombre,
                        concat('<button onclick=\"ver(', a.id, ')\" title=\"Detalles\" style=\"color:rgb(0,120,200)\"><i class=\"glyphicon glyphicon-eye-open\"></i></button> ',
                               '<button onclick=\"editar(', a.id, ')\" title=\"Editar\"><i class=\"glyphicon glyphicon-edit\"></i></button> ',
                               '<button onclick=\"print_etiqueta(', a.id, ')\" title=\"Imprimir Etiqueta\" style=\"color:rgb(100,100,100)\"><i class=\"glyphicon glyphicon-print\"></i></button> ',
                               '<button onclick=\"anular(', a.id, ')\" style=\"color:rgb(255,100,100)\" title=\"Anular\"><i class=\"glyphicon glyphicon-remove\"></i></button>') as acciones
                    from tec_servicios_tecnicos a
                    left join tec_empleados b on a.tecnico_asignado = b.id
                    where a.activo='1'".$cad_estado.$cad_tecnico."
                    order by a.id desc";

            $result = $this->db->query($cSql, $ar)->result_array();

            // Preparar datos para DataTables JSON
            $data = array();
            foreach($result as $row) {
                // Formatear fecha
                $row['fecha_recepcion'] = date('d/m/Y H:i', strtotime($row['fecha_recepcion']));
                $data[] = $row;
            }
            
            // Response para DataTables
            $response = array(
                "data" => $data
            );
            
            header('Content-Type: application/json');
            echo json_encode($response);
            
        } catch (Exception $e) {
            // Enviar respuesta de error
            header('Content-Type: application/json');
            echo json_encode(array(
                "error" => true,
                "message" => $e->getMessage()
            ));
        }
    }

    // Método simplificado para evitar errores
    function getServicios_simple($estado="0",$tecnico="0"){
        try {
            $ar = array();
            $cad_estado = $cad_tecnico = "";

            if($estado != '0' && $estado != ''){
                $cad_estado = " and a.estado = '".$estado."'";
            }
            if($tecnico != '0' && $tecnico != ''){
                $ar[] = $tecnico;
                $cad_tecnico = " and a.tecnico_asignado = ?";
            }

            $cSql = "select a.id, a.codigo, a.cliente_nombre, a.cliente_telefono,
                        ifnull(a.equipo_tipo,'') equipo_tipo, ifnull(a.marca,'') marca, ifnull(a.modelo,'') modelo,
                        a.estado, a.prioridad,
                        a.fecha_ingreso, a.fecha_estimada_reparacion,
                        ifnull(a.costo_final,0) costo_final,
                        concat('<button onclick=\"ver(', a.id, ')\" title=\"Detalles\" style=\"color:rgb(0,120,200)\"><i class=\"glyphicon glyphicon-eye-open\"></i></button> ',
                               '<button onclick=\"editar(', a.id, ')\" title=\"Editar\"><i class=\"glyphicon glyphicon-edit\"></i></button> ',
                               '<button onclick=\"print_etiqueta(', a.id, ')\" title=\"Imprimir Etiqueta\" style=\"color:rgb(100,100,100)\"><i class=\"glyphicon glyphicon-print\"></i></button> ',
                               '<button onclick=\"anular(', a.id, ')\" style=\"color:rgb(255,100,100)\" title=\"Anular\"><i class=\"glyphicon glyphicon-remove\"></i></button>') as acciones
                    from tec_servicios_tecnicos a
                    left join tec_empleados b on a.tecnico_asignado = b.id
                    where a.activo='1'".$cad_estado.$cad_tecnico."
                    order by a.id desc";

            $result = $this->db->query($cSql, $ar)->result_array();

            // Preparar datos para DataTables JSON
            $data = array();
            foreach($result as $row) {
                $row['fecha_ingreso'] = !empty($row['fecha_ingreso']) ? date('d/m/Y H:i', strtotime($row['fecha_ingreso'])) : '';
                $row['fecha_estimada_reparacion'] = !empty($row['fecha_estimada_reparacion']) ? date('d/m/Y H:i', strtotime($row['fecha_estimada_reparacion'])) : '';
                $row['costo_final'] = number_format($row['costo_final'], 2, '.', '');
                $data[] = $row;
            }

            // Response para DataTables
            $response = array(
                "data" => $data
            );

            header('Content-Type: application/json');
            echo json_encode($response);

        } catch (Exception $e) {
            // Enviar respuesta de error
            header('Content-Type: application/json');
            echo json_encode(array(
                "error" => true,
                "message" => $e->getMessage()
            ));
        }
    }
}
?>