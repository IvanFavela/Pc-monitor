<?php
function pingHost($hostname) {
    // Ejecutar el comando ping
    $output = [];
    $return_var = 0;

    // En sistemas Windows, usa el comando 'ping -n 1', y en Unix usa 'ping -c 1'
    $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "ping -n 1 $hostname" : "ping -c 1 $hostname";
    exec($command, $output, $return_var);

    // Verifica si el comando de ping fue exitoso
    return $return_var === 0;
}

$hostname = 'NANB-9HBCPL3';  // Reemplaza con el nombre de host que deseas verificar

if (pingHost($hostname)) {
    echo "$hostname está disponible.";
} else {
    echo "$hostname no está disponible.";
}
?>
