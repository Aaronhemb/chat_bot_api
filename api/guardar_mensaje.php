<?php
require("../conexion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
  $message = $_POST["message"];

  // Obtén los mensajes almacenados en la cookie actual (si existen)
  $storedMessages = json_decode($_COOKIE["messages"] ?? '[]', true);
  // Decodifica los mensajes almacenados antes de agregar el nuevo mensaje
  $storedMessages = array_map('urldecode', $storedMessages);
  // Agrega el nuevo mensaje al array de mensajes
  $storedMessages[] = $message;
  // Codifica los mensajes antes de guardarlos en la cookie
  $storedMessages = array_map('urlencode', $storedMessages);
  // Guarda el array de mensajes en la cookie
  setcookie("messages", json_encode($storedMessages), time() + 86400, "/", $_SERVER["HTTP_HOST"]);

  $fullName = $_COOKIE["full_name"] ?? "";
  $email = $_COOKIE["email"] ?? "";
  $phone = $_COOKIE["phone"] ?? "";

  // Verificar si el usuario ya tiene un ID único almacenado en una cookie
  $storedIdUnico = $_COOKIE["id_unico"] ?? "";
  if (empty($storedIdUnico)) {
    // Si el usuario no tiene un ID único, generar uno nuevo
    // Combinar los datos de nombre, correo y teléfono
    $dataToHash = $fullName . $email . $phone;
    // Generar un ID único utilizando la función md5()
    $idUnico = md5($dataToHash);
    // Limitar el ID único a los primeros 6 dígitos
    $idUnico = substr($idUnico, 0, 6);
    // Generar un número o letra aleatorio
    $septimoValor = rand(0, 9); // Genera un número aleatorio del 0 al 9
    // O puedes usar letras aleatorias
    // $septimoValor = chr(rand(65, 90)); // Genera una letra mayúscula aleatoria
    // Concatenar el séptimo valor aleatorio con el ID único existente
    $idUnico .= $septimoValor;
    // Guardar el nuevo ID único en una cookie
    setcookie("id_unico", $idUnico, time() + 86400, "/", $_SERVER["HTTP_HOST"]);
  } else {
    // Si el usuario ya tiene un ID único, usarlo en lugar de generar uno nuevo
    $idUnico = $storedIdUnico;
  }

  $query = "INSERT INTO datos_chat (preguntas, respuestas, fecha, nombre, correo, telefono, id_unico, fecha_cierre, id_unico_respuesta) VALUES ('$message', '', now(), '$fullName', '$email', '$phone', '$idUnico', '0000-00-00', '')";
  $result = mysqli_query($con, $query);

  if ($result) {
    echo "...";
  } else {
    echo "Error al insertar el mensaje en la base de datos";
  }
  $con->close();
} else {
  echo "Invalid request.";
}
