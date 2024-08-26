<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css.css?v=1.1">
    <title>PC Monitor</title>
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
            <li><a href="../pc-monitor/index.php" class="active">Inicio</a></li>
            <li><a href="../pc-monitor/configuracion.php">Administrar</a></li>
            <li><a href="../pc-monitor/logs.php">Ver Logs</a></li>
        </ul>
    </nav>
    <div class="container" id="pcContainer">
	
    <?php
    date_default_timezone_set("America/Chihuahua");
    function ping($ip)
    {
        $output = shell_exec("ping -n 1 -w 1000 $ip");
        return strpos($output, "TTL") !== false ? "connected" : "disconnected";
    }
    $directorio = 'C:\xampp\htdocs\PC-monitor/';
    $archivos = glob($directorio . "*.txt");
    if (count($archivos) > 0) {
        foreach ($archivos as $archivo) {
            $nombre_archivo = basename($archivo, ".txt");
            echo "<div class='grupo-pc'>";
            echo "<h2 class='grupo-titulo'>$nombre_archivo</h2>";
            echo "<div class='grupo-contenido'>";

            $ips = file(
                $archivo,
                FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
            );
            foreach ($ips as $linea) {
                $partes = explode(" - ", $linea);

                if (count($partes) == 2) {
                    $nombre = $partes[0];
                    $ip = $partes[1];
                } else {
                    $partes = explode(" ", $linea, 2);

                    if (count($partes) == 2) {
                        $nombre = $partes[0];
                        $ip = $partes[1];
                    } else {
                        echo "<div class=\"pc error-formato\">Formato incorrecto $linea<p></p>\n</div>";
                        continue;
                    }
                }

                $estado = ping(trim($ip));
                echo "<div class=\"pc $estado\" data-ip=\"$ip\" data-archivo=\"$nombre_archivo\">\n";
                echo "    <h2>$nombre</h2>\n";
                echo "    <p>IP: $ip</p>\n";
                echo "</div>\n";
            }

            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No se encontraron archivos ips.txt en el directorio especificado.</p>\n";
    }
    ?>
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

    function actualizarEstadoPcs() {
        fetch('archivo.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(pc => {
                    const pcElement = document.querySelector(`.pc[data-ip="${pc.ip}"]`);
                    if (pcElement) {
                        pcElement.className = `pc ${pc.estado}`;
                    }
                });
                registrarDesconectadas();
            })
            .catch(error => {
                console.error('Error al actualizar el estado de las PCs:', error);
            });
    }

function registrarDesconectadas() {
    const desconectadas = document.querySelectorAll('.disconnected');
    if (desconectadas.length > 0) {
        let ipsDesconectadas = '';
        let ultimoGrupo = '';

        desconectadas.forEach(element => {
            const grupo = element.closest('.grupo-pc').querySelector('.grupo-titulo').textContent.trim();
            const nombre = element.querySelector('h2').textContent.trim();  
            const ip = element.getAttribute('data-ip');

            if (grupo !== ultimoGrupo) {
                if (ultimoGrupo !== '') {
                    ipsDesconectadas += '\n\n';  
                }
                ipsDesconectadas += `Area: ${grupo}\n`;
                ultimoGrupo = grupo;
            }

            ipsDesconectadas += `  ${nombre} (${ip})\n`;
        });

        const logData = `Equipos sin respuesta:\n${ipsDesconectadas}\n`;

        fetch('registrar_log.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `logData=${encodeURIComponent(logData)}`,
        }).then(response => {
            if (response.ok) {
                const fechaHoraActual = new Date().toLocaleString();
                const mensaje = `${ipsDesconectadas}\nsin respuesta\n`;
                mostrarNotificacion(mensaje);
            } else {
                mostrarNotificacion("Error al registrar equipos desconectados.", true);
            }
        }).catch(() => {
            mostrarNotificacion("Error al registrar equipos desconectados.", true);
        });
    }
}

function mostrarNotificacion(mensaje, esError = false) {
    const notificacion = document.createElement('div');
    notificacion.className = `toast-notificacion ${esError ? 'error' : ''}`;
    
    notificacion.innerHTML = mensaje.replace(/\n/g, '<br>');

    document.body.appendChild(notificacion);

    setTimeout(() => {
        notificacion.classList.add('show');
    }, 100);

    setTimeout(() => {
        notificacion.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notificacion);
        }, 500);
    }, 10000);
}



        function cerrarAlerta() {
            document.querySelector('.custom-alert').style.display = 'none';
            document.querySelector('.alert-overlay').style.display = 'none';
        }


    var intervalo = setInterval(actualizarEstadoPcs, 20000);

    window.onunload = function() {
        clearInterval(intervalo);
    };

    actualizarEstadoPcs();
    </script>
</body>
</html>