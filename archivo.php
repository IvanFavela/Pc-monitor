<?php
header('Content-Type: application/json');

function ping($ip) {
    $output = shell_exec("ping -n 1 -w 1000 $ip");
    return (strpos($output, "TTL") !== false) ? "connected" : "disconnected";
}

$directorio = 'C:\xampp\htdocs\PC-monitor/';
$archivos = glob($directorio . '*.txt');
$resultado = [];

if (count($archivos) > 0) {
    foreach ($archivos as $archivo) {
        $ips = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($ips as $linea) {
            list($nombre, $ip) = explode(" - ", $linea);
            $estado = ping(trim($ip));
            $resultado[] = [
                'nombre' => $nombre,
                'ip' => trim($ip),
                'estado' => $estado
            ];
        }
    }
}

echo json_encode($resultado);
?>