<?php
// Script para reparar permisos del módulo Servicio Técnico
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Reparando Permisos - Servicio Técnico</h2>";
    
    // 1. Verificar si existe la tabla tec_modulos
    $stmt = $conn->query("SHOW TABLES LIKE 'tec_modulos'");
    if($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La tabla tec_modulos no existe. Creándola...</p>";
        
        // Crear tabla tec_modulos
        $sql = "CREATE TABLE IF NOT EXISTS tec_modulos (
            id int(11) NOT NULL AUTO_INCREMENT,
            modulo varchar(100) NOT NULL UNIQUE,
            descripcion text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY modulo (modulo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $conn->exec($sql);
        echo "<p style='color: green;'>✅ Tabla tec_modulos creada</p>";
    }
    
    // 2. Insertar módulo de servicios si no existe
    $stmt = $conn->query("SELECT id FROM tec_modulos WHERE modulo = 'servicios'");
    if($stmt->rowCount() == 0) {
        $sql = "INSERT INTO tec_modulos (modulo, descripcion) VALUES ('servicios', 'Servicio Técnico')";
        $conn->exec($sql);
        echo "<p style='color: green;'>✅ Módulo 'servicios' agregado</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Módulo 'servicios' ya existe</p>";
    }
    
    // 3. Verificar si existe la tabla tec_grupo_modulos
    $stmt = $conn->query("SHOW TABLES LIKE 'tec_grupo_modulos'");
    if($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La tabla tec_grupo_modulos no existe. Creándola...</p>";
        
        // Crear tabla tec_grupo_modulos
        $sql = "CREATE TABLE IF NOT EXISTS tec_grupo_modulos (
            id int(11) NOT NULL AUTO_INCREMENT,
            grupo_id int(11) NOT NULL,
            modulo_id int(11) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY grupo_id (grupo_id),
            KEY modulo_id (modulo_id),
            UNIQUE KEY grupo_modulo (grupo_id, modulo_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $conn->exec($sql);
        echo "<p style='color: green;'>✅ Tabla tec_grupo_modulos creada</p>";
    }
    
    // 4. Obtener el ID del módulo servicios
    $stmt = $conn->query("SELECT id FROM tec_modulos WHERE modulo = 'servicios'");
    $modulo_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    
    // 5. Agregar permisos a todos los grupos existentes
    $stmt = $conn->query("SELECT DISTINCT grupo_id FROM tec_grupo_modulos");
    $grupos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Si no hay grupos, obtener todos los grupos de usuarios
    if(empty($grupos)) {
        $stmt = $conn->query("SELECT DISTINCT group_id FROM tec_users WHERE group_id IS NOT NULL");
        $grupos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    if(empty($grupos)) {
        echo "<p style='color: orange;'>⚠️ No se encontraron grupos de usuarios. Asignando permiso al grupo 1 (administrador)</p>";
        $grupos = array(1);
    }
    
    foreach($grupos as $grupo_id) {
        // Verificar si ya tiene permiso
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tec_grupo_modulos WHERE grupo_id = ? AND modulo_id = ?");
        $stmt->execute([$grupo_id, $modulo_id]);
        
        if($stmt->fetchColumn() == 0) {
            $sql = "INSERT INTO tec_grupo_modulos (grupo_id, modulo_id) VALUES (?, ?)";
            $conn->prepare($sql)->execute([$grupo_id, $modulo_id]);
            echo "<p style='color: green;'>✅ Permiso agregado para grupo_id: $grupo_id</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Grupo $grupo_id ya tiene permiso para servicios</p>";
        }
    }
    
    // 6. Verificación final
    echo "<h3>Verificación Final</h3>";
    
    // Verificar tabla tec_modulos
    $stmt = $conn->query("SELECT * FROM tec_modulos WHERE modulo = 'servicios'");
    if($stmt->rowCount() > 0) {
        $modulo = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color: green;'>✅ Módulo registrado: ID={$modulo['id']}, Nombre={$modulo['modulo']}</p>";
    }
    
    // Verificar permisos
    $stmt = $conn->query("SELECT gm.grupo_id, m.modulo FROM tec_grupo_modulos gm INNER JOIN tec_modulos m ON gm.modulo_id = m.id WHERE m.modulo = 'servicios'");
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Permisos configurados:</strong></p>";
    echo "<ul>";
    foreach($permisos as $permiso) {
        echo "<li style='color: green;'>✅ Grupo {$permiso['grupo_id']} -> {$permiso['modulo']}</li>";
    }
    echo "</ul>";
    
    // 7. Mostrar consulta que debería funcionar en el template
    echo "<h3>Consulta de Prueba</h3>";
    if(!empty($grupos)) {
        $grupo_test = $grupos[0];
        $sql_test = "select trim(lower(b.modulo)) modulo from tec_grupo_modulos a inner join tec_modulos b on a.modulo_id = b.id where a.grupo_id = $grupo_test";
        echo "<p><strong>SQL para grupo $grupo_test:</strong></p>";
        echo "<code style='background: #f5f5f5; padding: 10px; display: block;'>$sql_test</code>";
        
        $stmt = $conn->query($sql_test);
        $modulos_permitidos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<p><strong>Resultado:</strong></p>";
        echo "<ul>";
        foreach($modulos_permitidos as $modulo) {
            echo "<li>$modulo</li>";
        }
        echo "</ul>";
    }
    
    echo "<div class='alert alert-success'>";
        echo "<h4>✅ ¡Permisos configurados correctamente!</h4>";
        echo "<p>El módulo 'Servicio Técnico' debería aparecer ahora en el menú.</p>";
        echo "<p>Si aún no aparece, verifica:</p>";
        echo "<ul>";
        echo "<li>Que hayas iniciado sesión correctamente</li>";
        echo "<li>Que tu grupo de usuarios tenga los permisos asignados</li>";
        echo "<li>Que la variable de sesión \$_SESSION['group_id'] esté definida</li>";
        echo "</ul>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
</body>
</html>