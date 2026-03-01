<?php
// Test script to simulate the DataTable AJAX call
echo "<h2>🧪 Testing AJAX Call: servicios/getServicios/0/0</h2>";

// Test direct SQL query first
echo "<h2>🔍 Direct SQL Test</h2>";

try {
    $conn = new PDO("mysql:host=localhost;dbname=erp_surcoc", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'tec_servicios_tecnicos'");
    if ($stmt->rowCount() === 0) {
        echo "<p style='color: red;'>❌ Table 'tec_servicios_tecnicos' does not exist</p>";
    } else {
        echo "<p style='color: green;'>✅ Table 'tec_servicios_tecnicos' exists</p>";
        
        // Count all records
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos");
        $total = $stmt->fetchColumn();
        echo "<p><strong>Total records (all):</strong> $total</p>";
        
        // Count active records
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo='1'");
        $active = $stmt->fetchColumn();
        echo "<p><strong>Active records (activo='1'):</strong> $active</p>";
        
        // Check the actual SQL query
        $sql = "select a.id, a.codigo, a.cliente_nombre, a.cliente_telefono, 
                    a.equipo_descripcion, a.estado, a.prioridad, 
                    a.fecha_recepcion, ifnull(b.nombre,'Sin Asignar') tecnico_nombre,
                    concat('<button onclick=editar(', a.id, ')><i class=\'glyphicon glyphicon-edit\'></i></button>',
                    '<button onclick=anular(', a.id, ') style=\'color:rgb(255,100,100)\' title=\'Anular\'><i class=\'glyphicon glyphicon-remove\'></i></button>') as acciones
                from tec_servicios_tecnicos a
                left join tec_tecnicos b on a.tecnico_asignado = b.id
                where a.activo='1'
                order by a.id desc";
        
        $stmt = $conn->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>📊 SQL Results:</h4>";
        echo "<p><strong>Total records returned:</strong> " . count($results) . "</p>";
        
        if (!empty($results)) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Código</th><th>Cliente</th><th>Estado</th><th>Técnico</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['codigo'] . "</td>";
                echo "<td>" . $row['cliente_nombre'] . "</td>";
                echo "<td>" . $row['estado'] . "</td>";
                echo "<td>" . $row['tecnico_nombre'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No records found with activo='1'</p>";
        }
        
        // Check if there are any records at all
        $stmt = $conn->query("SELECT id, codigo, cliente_nombre, activo FROM tec_servicios_tecnicos ORDER BY id DESC LIMIT 5");
        $all_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($all_records)) {
            echo "<h4>📋 Recent Records (all states):</h4>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Código</th><th>Cliente</th><th>Activo</th></tr>";
            foreach ($all_records as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['codigo'] . "</td>";
                echo "<td>" . $row['cliente_nombre'] . "</td>";
                echo "<td style='color: " . ($row['activo'] == '1' ? 'green' : 'red') . ";'>" . ($row['activo'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // Check tecnicos table
    echo "<h3>🔍 Checking tec_tecnicos table:</h3>";
    $stmt = $conn->query("SHOW TABLES LIKE 'tec_tecnicos'");
    if ($stmt->rowCount() === 0) {
        echo "<p style='color: red;'>❌ Table 'tec_tecnicos' does not exist</p>";
    } else {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_tecnicos WHERE activo='1'");
        $tecnicos_count = $stmt->fetchColumn();
        echo "<p><strong>Active technicians:</strong> $tecnicos_count</p>";
        
        if ($tecnicos_count > 0) {
            $stmt = $conn->query("SELECT id, nombre FROM tec_tecnicos WHERE activo='1' ORDER BY nombre");
            $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Available Technicians:</h4>";
            echo "<ul>";
            foreach ($tecnicos as $tecnico) {
                echo "<li>ID: " . $tecnico['id'] . " - " . $tecnico['nombre'] . "</li>";
            }
            echo "</ul>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h4>❌ Database Error:</h4>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
}
h2, h3, h4 {
    color: #333;
}
table {
    margin: 10px 0;
}
th {
    background: #f0f0f0;
    font-weight: bold;
}
</style>
