<?php
echo "<h2>🔧 Complete Servicio Técnico Module Fix</h2>";

// Step 1: Fix existing data
echo "<h3>Step 1: Fixing existing data...</h3>";
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_surcoc';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count records before fix
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos");
    $total = $stmt->fetchColumn();
    echo "<p>Total records: $total</p>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo = '1'");
    $active = $stmt->fetchColumn();
    echo "<p>Active records (activo='1'): $active</p>";
    
    // Fix records
    $stmt = $conn->exec("UPDATE tec_servicios_tecnicos SET activo = '1' WHERE activo = '' OR activo IS NULL");
    $fixed = $stmt;
    
    if ($fixed > 0) {
        echo "<p style='color: green;'>✅ Fixed $fixed records</p>";
    }
    
    // Verify
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tec_servicios_tecnicos WHERE activo = '1'");
    $new_active = $stmt->fetchColumn();
    echo "<p>New active records: $new_active</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Step 2: Check file changes
echo "<h3>Step 2: Verifying file changes...</h3>";

$model_file = 'application/models/Servicios_model.php';
$controller_file = 'application/controllers/Servicios.php';

// Check model
$model_content = file_get_contents($model_file);
if (strpos($model_content, "$data['activo'] = '1';") !== false) {
    echo "<p style='color: green;'>✅ Model file has been updated</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Model file needs manual update</p>";
}

// Check controller
$controller_content = file_get_contents($controller_file);
if (strpos($controller_content, '$this->input->post(\'estado\')') !== false) {
    echo "<p style='color: green;'>✅ Controller file has been updated</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Controller file needs manual update</p>";
}

echo "<hr>";

// Step 3: Instructions
echo "<h3>Step 3: Next Steps</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #b0d4f1; border-radius: 5px;'>";
echo "<h4>🎯 To complete the fix:</h4>";
echo "<ol>";
echo "<li><strong>Test the DataTable:</strong> Go to <a href='../index.php/servicios' target='_blank'>Servicios List</a></li>";
echo "<li><strong>Test filters:</strong> Try changing Estado and Técnico filters</li>";
echo "<li><strong>Test new service:</strong> Create a new service to verify it shows up</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";

// Step 4: Troubleshooting
echo "<h3>🔍 Troubleshooting</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<p><strong>If DataTable still shows empty:</strong></p>";
echo "<ul>";
echo "<li>Open browser developer console (F12)</li>";
echo "<li>Go to Network tab</li>";
echo "<li>Reload the page and look for 'servicios/getServicios' request</li>";
echo "<li>Check the Response - should show JSON data</li>";
echo "<li>If you see an error, check the response details</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-top: 10px;'>";
echo "<h4>✅ Expected Result:</h4>";
echo "<p>The DataTable should now show all services with activo='1', and filters should work dynamically without page reloads.</p>";
echo "</div>";
?>
