<?php
$mensaje = "";
$contenido_archivo = "";
$archivo_seleccionado = "";

function verificar_y_corregir_formato($lineas) {
    $lineas_corregidas = [];
    foreach ($lineas as $linea) {
        $linea = trim($linea);
        $linea = preg_replace('/_/', '-', $linea);
        if (preg_match('/^[a-zA-Z0-9]+\s*-\s*(?:\d{1,3}\.){3}\d{1,3}$/', $linea)) {
            $lineas_corregidas[] = $linea;
        } else {
            if (preg_match('/^([a-zA-Z0-9]+)\s+(?:(\d{1,3}\.){3}\d{1,3})$/', $linea, $matches)) {
                $nombre = $matches[1];
                $ip = trim(str_replace($nombre, '', $linea));
                $lineas_corregidas[] = "$nombre - $ip";
            } else {
                $lineas_corregidas[] = $linea;
            }
        }
    }
    return $lineas_corregidas;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $directorio = 'C:\xampp\htdocs\PC-monitor/';

    if (isset($_POST["editar"]) && !empty($_POST["archivo"])) {
        $archivo_seleccionado = $_POST["archivo"];
        if (!empty($archivo_seleccionado) && file_exists($archivo_seleccionado)) {
            $contenido_archivo = file_get_contents($archivo_seleccionado);
        } else {
            $mensaje = "No se pudo cargar el archivo, no existe.";
            $archivo_seleccionado = ""; 
        }
    }

    if (isset($_POST["guardar_cambios"])) {
        $archivo_seleccionado = $_POST["archivo"];
        $nuevo_contenido = $_POST["contenido_archivo"];
        if (!empty($archivo_seleccionado) && file_exists($archivo_seleccionado)) {
            $lineas = explode("\n", $nuevo_contenido);
            $lineas_corregidas = verificar_y_corregir_formato($lineas);
            $nuevo_contenido_corregido = implode("\n", $lineas_corregidas);
            $resultado = file_put_contents($archivo_seleccionado, $nuevo_contenido_corregido);
            if ($resultado !== false) {
                $mensaje = "Archivo editado correctamente.";
                $contenido_archivo = ""; 
                $archivo_seleccionado = "";
            } else {
                $mensaje = "No se pudo guardar el archivo. Verifica los permisos de escritura.";
            }
        } else {
            $mensaje = "Archivo no seleccionado o no existe.";
        }
    }

    if (isset($_POST["crear_archivo"])) {
        $nuevo_nombre_archivo = trim($_POST["nuevo_archivo"]);
        $archivo_path = $directorio . $nuevo_nombre_archivo . '.txt';

        if (!empty($nuevo_nombre_archivo) && !file_exists($archivo_path)) {
            if (file_put_contents($archivo_path, '') !== false) {
                $mensaje = "Nuevo archivo creado correctamente.";
                $archivo_seleccionado = "";
                $contenido_archivo = ''; 
            } else {
                $mensaje = "No se pudo crear el archivo. Verifica los permisos.";
            }
        } else {
            $mensaje = "Nombre de archivo inválido o el archivo ya existe.";
        }
    }

    if (isset($_POST["borrar_archivo"])) {
        $archivo_a_borrar = $_POST["archivo"];
        if (!empty($archivo_a_borrar) && file_exists($archivo_a_borrar)) {
            if (unlink($archivo_a_borrar)) {
                $mensaje = "Archivo borrado correctamente.";
                $archivo_seleccionado = "";
                $contenido_archivo = '';
            } else {
                $mensaje = "No se pudo borrar el archivo. Verifica los permisos.";
            }
        } else {
            $mensaje = "Archivo no seleccionado o no existe.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css.css?v=1.0">
    <title>PC Monitor</title>
</head>
<body>
    <nav class="top-menu">
	<div id="fecha-hora" class="fecha-hora"></div>
        <div class="logo"></div>
        <ul>
            <li><a href="../pc-monitor/index.php">Inicio</a></li>
            <li><a href="../pc-monitor/configuracion.php"class="active">Administrar</a></li>
            <li><a href="../pc-monitor/logs.php">Ver Logs</a></li>
        </ul>
    </nav>

<span class="configuracion-message <?php echo $mensaje == "Seccion borrada correctamente." ? "success" : "error"; ?>"><?php echo $mensaje; ?></span><br><br>
<span class="configuracion-message <?php echo $mensaje == "Archivo editado correctamente." ? "success" : "error"; ?>"><?php echo $mensaje; ?></span><br><br>

    <div class="container" id="pcContainer">
        <div class="configuracion-container">
            
            <form action="configuracion.php" method="post" class="configuracion-form">
                <h2>Crear Nueva Área</h2>
                <br>
                <input type="text" id="nuevo_archivo" name="nuevo_archivo" placeholder="Nombre del archivo"><br><br>
                <input type="submit" name="crear_archivo" value="Crear" class="configuracion-form-submit">
                <br>
            </form>

            <form action="configuracion.php" method="post" class="configuracion-form">
                <h2>Editar Área</h2>
                <br><br>
                <select name="archivo" id="archivo">
                    <option value="">Seleccione un archivo</option>
                    <?php
                    $archivos = glob($directorio . '*.txt');
                    foreach ($archivos as $archivo) {
                        $nombre_archivo = basename($archivo);
                        echo "<option value=\"$archivo\"" . ($archivo_seleccionado === $archivo ? " selected" : "") . ">$nombre_archivo</option>";
                    }
                    ?>
                </select>
                <br><br>
                <input type="submit" name="editar" value="Abrir" class="configuracion-form-submit">
                <input type="submit" name="actualizar_lista" value="Actualizar Lista" class="configuracion-form-submit">
                <br><br>
                <?php if (!empty($archivo_seleccionado) && file_exists($archivo_seleccionado)): ?>
                <h2>Contenido:</h2><br>
                <textarea name="contenido_archivo" rows="10" cols="50" class="configuracion-form-textarea"><?php echo htmlspecialchars($contenido_archivo); ?></textarea><br>
                <input type="hidden" name="archivo" value="<?php echo htmlspecialchars($archivo_seleccionado); ?>">
                <input type="submit" name="guardar_cambios" value="Guardar Cambios" class="configuracion-form-submit">
                <?php endif; ?>
            </form>

            <form action="configuracion.php" method="post" class="configuracion-form">
                <h2>Borrar Área</h2>
                <br><br>
                <select name="archivo" id="archivo">
                    <option value="">Seleccione un area</option>
                    <?php
                    $archivos = glob($directorio . '*.txt');
                    foreach ($archivos as $archivo) {
                        $nombre_archivo = basename($archivo);
                        echo "<option value=\"$archivo\">" . htmlspecialchars($nombre_archivo) . "</option>";
                    }
                    ?>
                </select>
                <br><br>
                <input type="submit" name="borrar_archivo" value="Borrar Area" class="configuracion-form-submit">
            </form>
        </div>
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
