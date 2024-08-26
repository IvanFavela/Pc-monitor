<?php
date_default_timezone_set('America/Chihuahua');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logData'])) {
    $logData = $_POST['logData'];
    $logFile = 'C:/xampp/htdocs/PC-monitor/Log/desconectadas.log';

    // Obtener la fecha y hora del servidor y restarle 12 horas
    $fechaHora = date('Y-m-d H:i:s', strtotime('-12 hours'));
    $logEntry = "[$fechaHora] $logData" . PHP_EOL;

    $existingLog = file_exists($logFile) ? file_get_contents($logFile) : '';

    $newLogContent = $logEntry . $existingLog;

    if (file_put_contents($logFile, $newLogContent, LOCK_EX) === false) {
        error_log("No se pudo escribir en el archivo de log: $logFile");
    }
}
?>
