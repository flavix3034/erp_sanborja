<?php
// Script para verificar la solución rápida
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>✅ Verificación de Solución Rápida</h2>";
    
    // 1. Verificar que hay registros activos
    echo "<div class='test-section'>";
    echo "<h3>1. Registros Activos en Base de Datos</h3>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo='1'");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p><strong>Total de servicios activos:</strong> $total</p>";
    
    if($total > 0) {
        echo "<p style='color: green;'>✅ Hay servicios activos para mostrar</p>";
        
        // Mostrar últimos 3 servicios
        $stmt = $conn->query("SELECT id, codigo, cliente_nombre, estado, fecha_recepcion 
                              FROM tec_servicios_tecnicos 
                              WHERE activo='1' 
                              ORDER BY id DESC LIMIT 3");
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table class='table table-striped'>";
        echo "<tr><th>ID</th><th>Código</th><th>Cliente</th><th>Estado</th><th>Fecha</th></tr>";
        foreach($servicios as $servicio) {
            echo "<tr>";
            echo "<td>{$servicio['id']}</td>";
            echo "<td><strong>{$servicio['codigo']}</strong></td>";
            echo "<td>{$servicio['cliente_nombre']}</td>";
            echo "<td><span class='label label-info'>{$servicio['estado']}</span></td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($servicio['fecha_recepcion'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No hay servicios activos</p>";
    }
    echo "</div>";
    
    // 2. Probar consulta SQL manualmente
    echo "<div class='test-section'>";
    echo "<h3>2. Prueba de Consulta SQL</h3>";
    
    $cSql = "SELECT a.id, a.codigo, a.cliente_nombre, a.cliente_telefono, 
                    a.equipo_descripcion, a.estado, a.prioridad, 
                    a.fecha_recepcion, ifnull(b.nombre,'Sin Asignar') tecnico_nombre
                FROM tec_servicios_tecnicos a
                LEFT JOIN tec_tecnicos b ON a.tecnico_asignado = b.id
                WHERE a.activo='1'
                ORDER BY a.id DESC 
                LIMIT 3";
    
    $stmt = $conn->query($cSql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>SQL Query:</strong></p>";
    echo "<code style='background: #f5f5f5; padding: 10px; display: block; font-size: 12px;'>$cSql</code>";
    
    echo "<p><strong>Resultados:</strong></p>";
    if(!empty($result)) {
        echo "<table class='table table-striped'>";
        echo "<tr><th>ID</th><th>Código</th><th>Cliente</th><th>Técnico</th><th>Estado</th></tr>";
        foreach($result as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['codigo']}</td>";
            echo "<td>{$row['cliente_nombre']}</td>";
            echo "<td>{$row['tecnico_nombre']}</td>";
            echo "<td><span class='label label-info'>{$row['estado']}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color: green;'>✅ Consulta SQL funciona correctamente</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ La consulta no devolvió resultados</p>";
    }
    echo "</div>";
    
    // 3. Simular respuesta JSON
    echo "<div class='test-section'>";
    echo "<h3>3. Simulación de Respuesta JSON</h3>";
    
    if(!empty($result)) {
        // Formatear como lo haría el método getServicios()
        $data = array();
        foreach($result as $row) {
            $row['fecha_recepcion'] = date('d/m/Y H:i', strtotime($row['fecha_recepcion']));
            $data[] = $row;
        }
        
        $response = array("data" => $data);
        $json = json_encode($response, JSON_PRETTY_PRINT);
        
        echo "<p><strong>JSON para DataTables:</strong></p>";
        echo "<textarea style='width: 100%; height: 200px; font-family: monospace; font-size: 11px;'>$json</textarea>";
        echo "<p style='color: green;'>✅ JSON formatiado correctamente</p>";
    }
    echo "</div>";
    
    // 4. Verificar técnicos
    echo "<div class='test-section'>";
    echo "<h3>4. Verificación de Técnicos para Filtros</h3>";
    
    $stmt = $conn->query("SELECT id, nombre FROM tec_tecnicos WHERE activo='1' ORDER BY nombre LIMIT 3");
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Técnicos disponibles:</strong></p>";
    if(!empty($tecnicos)) {
        echo "<ul>";
        foreach($tecnicos as $tec) {
            echo "<li>ID {$tec['id']}: {$tec['nombre']}</li>";
        }
        echo "</ul>";
        echo "<p style='color: green;'>✅ Técnicos disponibles para filtros</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No hay técnicos disponibles</p>";
    }
    echo "</div>";
    
    // 5. Resumen
    echo "<div class='test-section'>";
    echo "<h3>5. ✅ Resumen de Cambios Aplicados</h3>";
    echo "<div class='alert alert-success'>";
    echo "<h4>¡SOLUCIÓN RÁPIDA IMPLEMENTADA!</h4>";
    echo "<p><strong>Correcciones aplicadas:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Model: Se asegura activo='1' en nuevos registros</li>";
    echo "<li>✅ Model: getServicios() usa JSON nativo (eliminado json_datatable)</li>";
    echo "<li>✅ Controller: Maneja parámetros POST correctamente</li>";
    echo "<li>✅ View: Filtros dinámicos con AJAX</li>";
    echo "<li>✅ View: Botones para aplicar/limpiar filtros</li>";
    echo "<li>✅ View: DataTables configurado para JSON.data</li>";
    echo "</ul>";
    echo "<p><strong>El módulo debería funcionar perfectamente ahora:</strong></p>";
    echo "<ol>";
    echo "<li>1. Ingresa a <a href='../index.php/servicios'>Listado de Servicios</a></li>";
    echo "<li>2. Los datos deberían mostrarse en el DataTable</li>";
    echo "<li>3. Los filtros deberían funcionar sin recargar página</li>";
    echo "<li>4. Prueba cambiar Estado y Técnico</li>";
    echo "</ol>";
    echo "</div>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error:</h3>";
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
}

.label {
    font-size: 11px;
    padding: 3px 6px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin: 10px 0;
}

.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #3c763d;
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
    <h3>🚀 ¡PRUEBA AHORA!</h3>
    <div class="alert alert-info">
        <h4>Instrucciones para probar:</h4>
        <ol>
            <li><strong>1.</strong> <a href="../index.php/servicios" target="_blank">Abrir Listado de Servicios</a></li>
            <li><strong>2.</strong> Verificar que aparezcan los datos en la tabla</li>
            <li><strong>3.</strong> Cambiar el filtro de Estado</li>
            <li><strong>4.</strong> Click en "Aplicar" y verificar que filtre</li>
            <li><strong>5.</strong> Probar con filtro de Técnico</li>
            <li><strong>6.</strong> Click en "Nuevo Servicio" y crear uno de prueba</li>
            <li><strong>7.</strong> Verificar que aparezca inmediatamente en el listado</li>
        </ol>
        <p><strong>Si algo no funciona, revisa:</strong></p>
        <ul>
            <li>Consola de JavaScript (F12)</li>
            <li>Pestaña Network en las herramientas de desarrollador</li>
            <li>Logs de errores de Apache/XAMPP</li>
        </ul>
    </div>
</div>