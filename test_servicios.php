<?php
// Script para probar el módulo Servicio Técnico
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Diagnóstico del Módulo Servicio Técnico</h2>";
    
    // 1. Verificar tablas
    echo "<div class='test-section'>";
    echo "<h3>1. Verificación de Tablas</h3>";
    
    $tables = ['tec_servicios_tecnicos', 'tec_tecnicos'];
    foreach($tables as $table) {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Tabla '$table': $count registros</p>";
    }
    echo "</div>";
    
    // 2. Verificar servicios guardados recientemente
    echo "<div class='test-section'>";
    echo "<h3>2. Servicios Guardados Recientemente</h3>";
    
    $stmt = $conn->query("SELECT id, codigo, cliente_nombre, estado, fecha_recepcion 
                          FROM tec_servicios_tecnicos 
                          ORDER BY id DESC LIMIT 5");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(!empty($servicios)) {
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
        echo "<p style='color: orange;'>⚠️ No hay servicios guardados</p>";
    }
    echo "</div>";
    
    // 3. Probar inserción directa
    echo "<div class='test-section'>";
    echo "<h3>3. Prueba de Inserción Directa</h3>";
    
    // Generar código de prueba
    $stmt = $conn->query("SELECT max(id)+1 nuevo FROM tec_servicios_tecnicos");
    $nuevo_id = $stmt->fetchColumn();
    $codigo = "ST-" . str_pad($nuevo_id, 3, "0", STR_PAD_LEFT);
    
    $sql = "INSERT INTO tec_servicios_tecnicos 
            (codigo, cliente_nombre, cliente_telefono, problema_reportado, estado, usuario_registra) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $codigo,
        'CLIENTE PRUEBA',
        '999888777',
        'PROBLEMA DE PRUEBA DESDE SCRIPT',
        'RECIBIDO',
        1 // Asumiendo usuario_id = 1
    ]);
    
    if($result) {
        echo "<p style='color: green;'>✅ Inserción exitosa: $codigo</p>";
        
        // Verificar que se guardó
        $stmt = $conn->query("SELECT * FROM tec_servicios_tecnicos WHERE codigo = '$codigo'");
        if($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Verificación exitosa: el servicio existe</p>";
            
            $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='alert alert-success'>";
            echo "<h4>Datos guardados:</h4>";
            echo "<p><strong>ID:</strong> {$servicio['id']}</p>";
            echo "<p><strong>Código:</strong> {$servicio['codigo']}</p>";
            echo "<p><strong>Cliente:</strong> {$servicio['cliente_nombre']}</p>";
            echo "<p><strong>Problema:</strong> {$servicio['problema_reportado']}</p>";
            echo "<p><strong>Estado:</strong> {$servicio['estado']}</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error en inserción</p>";
    }
    echo "</div>";
    
    // 4. Verificar URLs
    echo "<div class='test-section'>";
    echo "<h3>4. URLs del Módulo</h3>";
    echo "<p><strong>Para probar el módulo:</strong></p>";
    echo "<ul>";
    echo "<li><a href='../index.php/servicios' target='_blank'>Listado de Servicios</a></li>";
    echo "<li><a href='../index.php/servicios/add' target='_blank'>Nuevo Servicio</a></li>";
    echo "</ul>";
    echo "</div>";
    
    // 5. Diagnóstico de sesión
    echo "<div class='test-section'>";
    echo "<h3>5. Verificación de Variables de Sesión</h3>";
    echo "<p><strong>Variables requeridas:</strong></p>";
    echo "<ul>";
    echo "<li>\$_SESSION['user_id']: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NO DEFINIDA') . "</li>";
    echo "<li>\$_SESSION['group_id']: " . (isset($_SESSION['group_id']) ? $_SESSION['group_id'] : 'NO DEFINIDA') . "</li>";
    echo "<li>\$_SESSION['store_id']: " . (isset($_SESSION['store_id']) ? $_SESSION['store_id'] : 'NO DEFINIDA') . "</li>";
    echo "</ul>";
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
</style>

<div class="test-section">
    <h3>🔧 Si el módulo no funciona correctamente:</h3>
    <ol>
        <li><strong>Verifica que las tablas existan</strong> - Revisa phpMyAdmin</li>
        <li><strong>Inicia sesión correctamente</strong> - Sin sesión no funciona</li>
        <li><strong>Revisa los permisos</strong> - Ejecuta fix_permissions.php</li>
        <li><strong>Revisa la configuración de Apache</strong> - Que permita .htaccess</li>
        <li><strong>Verifica URL rewriting</strong> - Que index.php funcione correctamente</li>
    </ol>
    
    <p><strong>Flujo de prueba recomendado:</strong></p>
    <ol>
        <li>Ingresa a <a href="../index.php/servicios/add">Nuevo Servicio</a></li>
        <li>Llena todos los campos obligatorios</li>
        <li>Haz clic en "Guardar Servicio"</li>
        <li>Debería redirigir al listado y mostrar mensaje</li>
        <li>Verifica en phpMyAdmin que se guardó correctamente</li>
    </ol>
</div>