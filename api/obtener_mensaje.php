<?php
$fullName = $_COOKIE["full_name"] ?? "";
?>

<li class="chat incoming"><span class="material-symbols-outlined">smart_toy</span>
    <p>Hola <?php echo $fullName; ?> 👋<br>Aqui podrás hablar con agentes de Tecnología de XYBooster Unicor Series. ¿En qué puedo ayudarte hoy?</p>
  </li>
<?php
include("../conexion/conexion.php");

// Obtén el id_unico del usuario actual
$idUnico = $_COOKIE["id_unico"] ?? "";

// Verifica si el id_unico existe
if (!empty($idUnico)) {
  // Realizar una consulta a la base de datos para obtener los mensajes actualizados
  $query = "SELECT * FROM datos_chat WHERE id_unico = '$idUnico' OR id_unico_respuesta = '$idUnico' ORDER BY fecha ASC";
  $resultado = $con->query($query);
  // Inicializar una variable para almacenar los mensajes con formato HTML
  $mensajesHTML = "";
  while ($row = $resultado->fetch_assoc()) {
    $fecha = new DateTime($row["fecha"]);
    $hora = $fecha->format('H:i'); // Formato de hora en 24 horas
    $idUnicoRespuesta = $row["id_unico_respuesta"];
    
    // Calcula la diferencia de tiempo entre el mensaje y la hora actual
    $fechaMensaje = $fecha->getTimestamp();
    $fechaActual = time();
    $diferenciaTiempo = $fechaActual - $fechaMensaje;

    if ($row["id_unico"] == $idUnico) {
        $mensajesHTML .= '<li class="chat outgoing" data-fecha="' . $hora . '">
            <span class="material-symbols-outlined"></span>
            <p>' . $row["preguntas"] . ' <label id="tiempo2">' . $hora . '</label>
        </li>';
    } else {
        $mensajesHTML .= '<li class="chat incoming" data-fecha="' . $hora . '" data-respuesta="' . $idUnico . '">
            <span class="material-symbols-outlined">smart_toy</span><p><label id="cliente">' . $row["nombre"] . ':</label> <br>' . $row["respuestas"] . ' <br><label id="tiempo">' . $hora . '</label> </p>
            </li>';

        // Reproduce el sonido de notificación si el mensaje es reciente y no es una respuesta específica
        if ($diferenciaTiempo <= 120 && substr($row["preguntas"], 0, strlen($idUnico)) === $idUnico && substr($row["preguntas"], 0, strlen($idUnicoRespuesta)) !== $idUnicoRespuesta) {
            // Reproduce el sonido de notificación si el mensaje es reciente y cumple otras condiciones
            echo '<script>playNotificationSound();</script>';
        }        
    }
}
  // Enviar los mensajes formateados como respuesta en formato HTML
  echo $mensajesHTML;
}
$con->close();
?>
