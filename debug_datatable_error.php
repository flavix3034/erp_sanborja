<?php
// Script para debuguear el error AJAX del DataTable
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Diagnóstico del Error AJAX - DataTable</h2>";
    
    // 1. Probar la llamada AJAX exactamente como la haría DataTable
    echo "<div class='test-section'>";
    echo "<h3>1. Simulación de Llamada AJAX</h3>";
    
    $postData = array(
        'estado' => '0',
        'tecnico' => '0'
    );
    
    echo "<p><strong>Simulando POST con:</strong></p>";
    echo "<pre>" . json_encode($postData, JSON_PRETTY_PRINT) . "</pre>";
    
    // 2. Probar el método getServicios() directamente
    echo "<div class='test-section'>";
    echo "<h3>2. Ejecución Directa del Método</h3>";
    
    // Simular la consulta SQL exacta del método getServicios()
    $ar = array();
    $cad_estado = $cad_tecnico = "";
    
    // Los parámetros son 0, así que no se agregan filtros
    if($postData['estado'] != '0' && $postData['estado'] != ''){
        $cad_estado = " and a.estado = '".$postData['estado']."'";
    }
    if($postData['tecnico'] != '0' && $postData['tecnico'] != ''){
        $ar[] = $postData['tecnico'];
        $cad_tecnico = " and a.tecnico_asignado = ?";
    }
    
    $cSql = "select a.id, a.codigo, a.cliente_nombre, a.cliente_telefono, 
                a.equipo_descripcion, a.estado, a.prioridad, 
                a.fecha_recepcion, ifnull(b.nombre,'Sin Asignar') tecnico_nombre,
                concat('<button onclick=\"editar(', a.id, ')\"><i class=\"glyphicon glyphicon-edit\"></i></button>',
                       '<button onclick=\"anular(', a.id, ')\" style=\"color:rgb(255,100,100)\" title=\"Anular\"><i class=\"glyphicon glyphicon-remove\"></i></button>') as acciones
            from tec_servicios_tecnicos a
            left join tec_tecnicos b on a.tecnico_asignado = b.id
            where a.activo='1'".$cad_estado.$cad_tecnico."
            order by a.id desc";
    
    echo "<p><strong>SQL que se ejecutará:</strong></p>";
    echo "<code style='background: #f5f5f5; padding: 10px; display: block; font-size: 12px; word-break: break-all;'>$cSql</code>";
    
    echo "<p><strong>Parámetros:</strong></p>";
    echo "<pre>" . json_encode($ar, JSON_PRETTY_PRINT) . "</pre>";
    
    try {
        $result = $conn->query($cSql, $ar);
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Resultados de la consulta: " . count($data) . " registros</strong></p>";
        
        if(!empty($data)) {
            // Formatear datos como lo hace el método
            $formatted_data = array();
            foreach($data as $row) {
                $row['fecha_recepcion'] = date('d/m/Y H:i', strtotime($row['fecha_recepcion']));
                $formatted_data[] = $row;
            }
            
            $response = array("data" => $formatted_data);
            $json_response = json_encode($response, JSON_PRETTY_PRINT);
            
            echo "<p><strong>JSON formateado para DataTables:</strong></p>";
            echo "<textarea style='width: 100%; height: 200px; font-family: monospace; font-size: 11px;'>$json_response</textarea>";
            
            // Verificar sintaxis JSON
            json_decode($json_response);
            if(json_last_error() === JSON_ERROR_NONE) {
                echo "<p style='color: green;'>✅ JSON válido</p>";
            } else {
                echo "<p style='color: red;'>❌ Error en JSON: " . json_last_error_msg() . "</p>";
            }
            
        } else {
            echo "<p style='color: orange;'>⚠️ No hay resultados en la consulta</p>";
        }
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Error en SQL: " . $e->getMessage() . "</p>";
        echo "<p><strong>SQL con error:</strong></p>";
        echo "<code style='background: #ffe6e6; padding: 10px; display: block;'>$cSql</code>";
    }
    
    echo "</div>";
    
    // 3. Verificar estructura de las tablas
    echo "<div class='test-section'>";
    echo "<h3>3. Verificación de Estructura de Tablas</h3>";
    
    // Verificar tabla tec_servicios_tecnicos
    echo "<p><strong>Estructura de tec_servicios_tecnicos:</strong></p>";
    $stmt = $conn->query("DESCRIBE tec_servicios_tecnicos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table class='table table-striped'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Key</th></tr>";
    foreach($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar tabla tec_tecnicos
    echo "<p><strong>Estructura de tec_tecnicos:</strong></p>";
    $stmt = $conn->query("DESCRIBE tec_tecnicos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table class='table table-striped'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Key</th></tr>";
    foreach($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 4. Verificar si hay registros
    echo "<div class='test-section'>";
    echo "<h3>4. Estado General de los Datos</h3>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo='1'");
    $total_servicios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_tecnicos WHERE activo='1'");
    $total_tecnicos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<table class='table table-striped'>";
    echo "<tr><th>Tabla</th><th>Total de registros activos</th></tr>";
    echo "<tr><td>tec_servicios_tecnicos</td><td>$total_servicios</td></tr>";
    echo "<tr><td>tec_tecnicos</td><td>$total_tecnicos</td></tr>";
    echo "</table>";
    
    if($total_servicios == 0) {
        echo "<p style='color: orange;'>⚠️ No hay servicios activos - esta podría ser la causa del error</p>";
        echo "<p>Los servicios podrían estar guardados con activo = NULL o ''</p>";
    }
    
    echo "</div>";
    
    // 5. Recomendaciones
    echo "<div class='test-section'>";
    echo "<h3>5. 🔧 Soluciones Recomendadas</h3>";
    echo "<div class='alert alert-info'>";
    echo "<h4>Posibles Causas del Error AJAX:</h4>";
    echo "<ol>";
    echo "<li><strong>Error PHP:</strong> Verificar logs de errores de Apache/XAMPP</li>";
    echo "<li><strong>Error JSON:</strong> La respuesta no es JSON válido</li>";
    echo "<li><strong>Error SQL:</strong> Hay algún problema en la consulta</li>";
    echo "<li><strong>Error de permisos:</strong> El controller no está accesible</li>";
    echo "<li><strong>Error de sintaxis:</strong> Hay algún error de PHP en el código</li>";
    echo "</ol>";
    
    echo "<h4>Pruebas a Realizar:</h4>";
    echo "<ol>";
    echo "<li><strong>Ver consola del navegador</strong> (F12) para ver errores JavaScript</li>";
    echo "<li><strong>Ver pestaña Network</strong> para ver la respuesta AJAX</li>";
    echo "<li><strong>Acceder directamente a la URL</strong> /servicios/getServicios</li>";
    echo "<li><strong>Ver logs de PHP</strong> para encontrar errores específicos</li>";
    echo "</ol>";
    echo "</div>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error de Conexión:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
.test-section {
    border: 1px solid #ddd;
    margin: 20px;
    padding: 15px;
    border-radius: 5px;
    background: #f9f9f9;
}

.table {
    background: white;
    margin: 10px 0;
    font-size: 12px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin: 10px 0;
}

.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
}

body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

code {
    word-break: break-all;
}
</style>

<div class="test-section">
    <h3>🚀 Acciones Inmediatas</h3>
    <div class="alert alert-success">
        <h4>Si la prueba anterior muestra resultados:</h4>
        <ol>
            <li><strong>1.</strong> <a href="../index.php/servicios" target="_blank">Abrir el Listado de Servicios</a></li>
            <li><strong>2.</strong> Abrir Consola de Desarrollador (F12)</li>
            <li><strong>3.</strong> Ir a pestaña Network</li>
            <li><strong>4.</strong> Recargar la página</li>
            <li><strong>5.</strong> Buscar la llamada AJAX a servicios/getServicios</li>
            <li><strong>6.</strong> Ver la Response y Headers</li>
        </ol>
    </div>
</div>