<?php require("../conexion/conexion.php"); ?>


<?php
$fullName = $_COOKIE["full_name"] ?? "";
$email = $_COOKIE["email"] ?? "";
$phone = $_COOKIE["phone"] ?? "";
$storedMessages = json_decode($_COOKIE["messages"] ?? '[]', true); // Obtén los mensajes almacenados en la cookie
$userInfoSubmitted = isset($_COOKIE["user_info_submitted"]) && $_COOKIE["user_info_submitted"] === "true";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Chatbot XYTECNOLOGY</title>
  <link rel="stylesheet" href="./css/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Google Fonts Link For Icons -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
  <!--Iconos animations-->
  <script src="https://cdn.lordicon.com/bhenfmcm.js"></script>
</head>

<body>
  <audio id="notification-sound" src="./sounds/int1.mp3"></audio>
  <button class="chatbot-toggler">
    <span class="material-symbols-rounded">mode_comment</span>
    <span class="material-symbols-outlined">close</span>
  </button>
  <div class="chatbot">
    <header>
      <img src="../api/img/logo.jfif" id="logo" alt="Imagen del chatbot XYTech">
      <h2>Chatbot XYTech </h2>
      <span class="close-btn material-symbols-outlined">close</span>
    </header>
    <?php if (empty($fullName)): ?>
      <form id="user-info-form">
        <div id="close" style="display:none;"><span class="close-icon material-symbols-rounded">close</span></div>
        <label for="full-name">Nombre completo:</label>
        <input type="text" id="full-name" name="full-name" placeholder="Escribe tu nombre" required>
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="Escribe tu correo electronico" required>
        <label for="phone">Teléfono:</label>
        <input type="tel" id="phone" name="phone" placeholder="Ingresa tu numero" required>
        <button type="submit" id="submit-btn">Iniciar conversación</button>
      </form>
      <!-- Agrega un elemento para mostrar el mensaje de error -->
      <p id="error-message" style="color: red;"></p>
      <ul class="chatbox">
        <?php
        if (!empty($storedMessages)) {
        } else {
          // echo '<li class="chat incoming"><span class="material-symbols-outlined">smart_toy</span><p>Hola Bro 👋<br>Aqui podras hablar con agentes de Tecnologia de XYBooster Unicor Series En qué puedo ayudarte hoy?</p></li>';
        }
        ?>
      </ul>
      <div class="chat-input">
        <textarea disabled placeholder="Necesitas registrarte para poder iniciar conversacion..." spellcheck="false"
          required></textarea>
        <span disabled id="send-btn" class="material-symbols-rounded">send</span>
      </div>
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const submitBtn = document.getElementById("submit-btn");
          submitBtn.onclick = function () {
            location.reload();
          };
        });
      </script>
      <script src="./js/script_form.js" defer></script>
    <?php elseif (!empty($fullName)): ?>
      <div id="close"><span class="close-icon material-symbols-rounded">close</span></div>
      <form id="user-info-form" style="background: #ffffff00;">
        <button style="display:none;" type="submit" id="submit-btn">Iniciar conversación</button>
      </form>
      <ul class="chatbox">
        <li class="chat incoming"><span class="material-symbols-outlined">smart_toy</span>
          <p>Hola <?php echo $fullName; ?> 👋<br>Aqui podras hablar con agentes de Tecnologia de XYBooster Unicor Series. En qué puedo ayudarte hoy?</p>
        </li>
        <?php
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
            if ($row["id_unico"] == $idUnico) {
              $mensajesHTML .= '<li class="chat outgoing" data-fecha="' . $hora . '">
            <span class="material-symbols-outlined"></span>
            <p>' . $row["preguntas"] . ' <label id="tiempo2">' . $hora . '</label>
        </li>';
            } else {
              $mensajesHTML .= '<li class="chat incoming" data-fecha="' . $hora . '" data-respuesta="' . $idUnicoRespuesta . '">
            <span class="material-symbols-outlined">smart_toy</span><p><label id="cliente">' . $row["nombre"] . ':</label> <br>' . $row["respuestas"] . ' <br><label id="tiempo">' . $hora . '</label> </p>
        </li>';
            }
          }
          // Enviar los mensajes formateados como respuesta en formato HTML
          echo $mensajesHTML;
        }
        $con->close();
        ?>
      </ul>
      <span id="num-mensajes"></span>
      <div class="chat-input">
        <input style="display:none;" type="text" id="id_ticket" value="<?php echo $idUnico; ?> ">
        <textarea placeholder="Escribe tu mensaje..." spellcheck="false" required></textarea>
        <span id="send-btn" class="material-symbols-rounded">send</span>
      </div>
    <?php endif; ?>
  </div>
  <script defer>
    const fullName = "<?php echo $_COOKIE['full_name'] ?? ''; ?>";
    const email = "<?php echo $_COOKIE['email'] ?? ''; ?>";
    const phone = "<?php echo $_COOKIE['phone'] ?? ''; ?>";
    // Función para verificar y mostrar el chatbot
    const showChatbot = () => {
      if (<?php echo $userInfoSubmitted ? 'true' : 'false'; ?>) {
        document.body.classList.add("show-chatbot");
      }
    };
    // Verificar y mostrar el chatbot al cargar la página
    showChatbot();
  </script>
  <script src="./js/script.js" defer></script>
  <script>
    function playNotificationSound() {
      const notificationSound = new Audio("./sounds/int1.mp3");
      notificationSound.play();
    }
  </script>

</body>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const submitBtn = document.getElementById("submit-btn");

    // Función para generar un número aleatorio de 6 dígitos
    function generateRandomNumber() {
      return Math.floor(100000 + Math.random() * 900000); // Genera un número entre 100000 y 999999
    }

    // Función para establecer la cookie "id_unico"
    function setCookie(name, value, days) {
      const expires = new Date();
      expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    submitBtn.onclick = function () {
      // Genera un número aleatorio de 6 dígitos
      const idUnico = generateRandomNumber();

      // Establece la cookie "id_unico" con el número aleatorio
      setCookie("id_unico", idUnico, 1); // La cookie expira en 1 día

      // Recarga la página para que los cambios surtan efecto
      location.reload();
    };
  });
</script>


</html>