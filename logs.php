<?php
date_default_timezone_set('America/Chihuahua');
$logDirectory = 'C:/xampp/htdocs/PC-monitor/Log';

$logFiles = glob($logDirectory . '/*.log');

function readLogFile($filePath) {
    if (file_exists($filePath)) {
        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
    return [];
}

function clearLogFile($filePath) {
    if (file_exists($filePath)) {
        file_put_contents($filePath, '');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_logs'])) {
    foreach ($logFiles as $logFile) {
        clearLogFile($logFile);
    }
    header('Location: logs.php');
    exit;
}

$logs = [];
foreach ($logFiles as $logFile) {
    $logContent = readLogFile($logFile);
    $logs[basename($logFile)] = $logContent;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css.css?v=1.0">
    <title>PC Monitor - Logs</title>
</head>
<body>
    <nav class="top-menu">
	<div id="fecha-hora" class="fecha-hora"></div>
        <div class="custom-alert">
            <p id="alert-message">Las siguientes IPs están desconectadas:</p>
            <br><button onclick="cerrarAlerta()">Aceptar</button>
        </div>
        <div class="logo"></div>
        <ul>
            <li><a href="../pc-monitor/index.php">Inicio</a></li>
            <li><a href="../pc-monitor/configuracion.php">Administrar</a></li>
            <li><a href="../pc-monitor/logs.php" class="active">Ver Logs</a></li>
			<li><form method="post" action="">
					<button type="submit" name="clear_logs" class="clear-button">Limpiar Log</button>
					</form></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Registro de Eventos</h1>
		

        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $fileName => $logEntries): ?>
                <div class="log-file">
					<?php echo "<br>"; echo "<br>" ; ?>
                    <div class="log-entries">
                        <?php foreach ($logEntries as $entry): ?>
                            <p><?php echo htmlspecialchars($entry); ?></p>
                        <?php endforeach; ?>
                    </div>
					
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron archivos de log.</p>
        <?php endif; ?>
    </div>
	
<script>
	function actualizarFechaHora() {
    const ahora = new Date();
    const opciones = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'America/Chihuahua'
    };
    const fechaHoraFormateada = ahora.toLocaleString('es-MX', opciones);
    document.getElementById('fecha-hora').textContent = fechaHoraFormateada;
}

setInterval(actualizarFechaHora, 1000);

actualizarFechaHora();
</script>
</body>
</html>
