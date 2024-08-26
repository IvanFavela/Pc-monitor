<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ip"]) && isset($_POST["nombre"])) {
    $ip = trim($_POST["ip"]);
    $nombre = trim($_POST["nombre"]);

    $pattern_with_dash = '/^[a-zA-Z0-9_]+\s*-\s*(?:\d{1,3}\.){3}\d{1,3}$/';
    $pattern_without_dash = '/^[a-zA-Z0-9_]+\s+(?:\d{1,3}\.){3}\d{1,3}$/';

    if (!empty($ip) && !empty($nombre) && (preg_match($pattern_with_dash, "$nombre - $ip") || preg_match($pattern_without_dash, "$nombre $ip"))) {
        $directorio = 'C:\xampp\htdocs\PC-monitor/';
        $archivo = $directorio . basename($nombre) . ".txt";

        if (file_exists($archivo)) {
            $formato_ip = preg_match($pattern_with_dash, "$nombre - $ip") ? "$nombre - $ip" : "$nombre $ip";
            file_put_contents($archivo, "$formato_ip\n", FILE_APPEND | LOCK_EX);
        } else {
            $formato_ip = preg_match($pattern_with_dash, "$nombre - $ip") ? "$nombre - $ip" : "$nombre $ip";
            file_put_contents($archivo, "$formato_ip\n");
        }

        $mensaje = "IP y nombre guardados correctamente.";
    } else {
        $mensaje = "Formato de IP o nombre incorrecto. Debe seguir el formato: Palabra - dirección IP o Palabra dirección IP";
    }

    header("Location: configuracion.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>