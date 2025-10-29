<?php
// Archivo de prueba para verificar acceso a extras.php
echo "🔍 PROBANDO ACCESO A EXTRAS.PHP\n";
echo "================================\n\n";

// Verificar si podemos incluir init.php
echo "📋 Verificando includes/init.php...\n";
if (file_exists('../includes/init.php')) {
    echo "✅ Archivo init.php encontrado\n";
} else {
    echo "❌ Archivo init.php NO encontrado\n";
    echo "Directorio actual: " . getcwd() . "\n";
    echo "Archivos en directorio actual:\n";
    $files = scandir('.');
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "  - $file\n";
        }
    }
}

// Verificar si podemos incluir db_connect.php
echo "\n📋 Verificando includes/db_connect.php...\n";
if (file_exists('../includes/db_connect.php')) {
    echo "✅ Archivo db_connect.php encontrado\n";
} else {
    echo "❌ Archivo db_connect.php NO encontrado\n";
}

// Intentar incluir init.php
echo "\n📋 Intentando incluir init.php...\n";
try {
    require_once '../includes/init.php';
    echo "✅ init.php incluido exitosamente\n";
} catch (Exception $e) {
    echo "❌ Error al incluir init.php: " . $e->getMessage() . "\n";
}

echo "\n🎯 Prueba completada\n";
?>
