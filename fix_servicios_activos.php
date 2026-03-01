<?php
// Fix existing servicios records - ensure activo='1' for active records
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 Fixing Servicios Records</h2>";
    
    // Count records with activo = '' (empty string)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo = '' OR activo IS NULL");
    $empty_count = $stmt->fetchColumn();
    echo "<p>Records with empty/NULL activo: <strong>$empty_count</strong></p>";
    
    // Count records with activo = '1'
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo = '1'");
    $active_count = $stmt->fetchColumn();
    echo "<p>Records with activo = '1': <strong>$active_count</strong></p>";
    
    if ($empty_count > 0) {
        // Fix records - set activo='1' where it's empty or NULL
        $stmt = $conn->exec("UPDATE tec_servicios_tecnicos SET activo = '1' WHERE activo = '' OR activo IS NULL");
        echo "<p style='color: green;'>✅ Fixed $stmt records</p>";
        
        // Verify fix
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo = '1'");
        $new_active = $stmt->fetchColumn();
        echo "<p>New total active records: <strong>$new_active</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No records need fixing</p>";
    }
    
    // Show some sample records
    $stmt = $conn->query("SELECT id, codigo, cliente_nombre, activo, estado FROM tec_servicios_tecnicos ORDER BY id DESC LIMIT 5");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Sample Records:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Código</th><th>Cliente</th><th>Activo</th><th>Estado</th></tr>";
    foreach ($records as $record) {
        $color = $record['activo'] == '1' ? 'green' : 'red';
        echo "<tr>";
        echo "<td>{$record['id']}</td>";
        echo "<td>{$record['codigo']}</td>";
        echo "<td>{$record['cliente_nombre']}</td>";
        echo "<td style='color: $color;'>{$record['activo']}</td>";
        echo "<td>{$record['estado']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green;'><strong>✅ Fix completed! Try the DataTable now.</strong></p>";
    
} catch(PDOException $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
