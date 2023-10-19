
document.addEventListener("DOMContentLoaded", function() {
  const chatbotToggler = document.querySelector(".chatbot-toggler");
  const closeBtn = document.querySelector(".close-btn");
  const chatbox = document.querySelector(".chatbox");
  const chatInput = document.querySelector(".chat-input textarea");
  const sendButton = document.querySelector(".chatbot .chat-input #send-btn");
  const userInfoForm = document.getElementById("user-info-form");
  const inputInitHeight = chatInput.scrollHeight;
  // Variable para controlar la reproducción del sonido
  let isSoundPlaying = false;
  const createChatLi = (message, className) => {
    const chatLi = document.createElement("li");
    chatLi.classList.add("chat", className);
    chatLi.innerHTML = `<span class="material-symbols-outlined"></span><p>${message}</p>`;
    return chatLi;
  };

  document.addEventListener("DOMContentLoaded", function() {
    const closeIcon = document.querySelector(".close-icon");
    closeIcon.addEventListener("click", function() {
      // Eliminar la cookie "id_unico" estableciendo su valor a una cadena vacía y una fecha de expiración pasada
      document.cookie = "id_unico=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      // Redireccionar a la página principal u otra página deseada
      window.location.href = "index.php";
    });
  });

  // Define una función para determinar si un mensaje es reciente (menos de 2 minutos de antigüedad)
  function esMensajeReciente(fecha) {
    const fechaMensaje = new Date(fecha); // Convierte la fecha del mensaje en un objeto Date
    const fechaActual = new Date(); // Obtiene la fecha y hora actuales
    const tiempoTranscurrido = (fechaActual - fechaMensaje) / 1000; // Calcula el tiempo transcurrido en segundos
    return tiempoTranscurrido <= 120; // Devuelve true si el mensaje es reciente (menos de 2 minutos)
  }
  // Define una función para determinar si un mensaje es una respuesta a un mensaje específico
  function esRespuestaAMensajeEspecifico(mensaje, idUnico, idUnicoRespuesta) {
    return mensaje.startsWith(idUnico) && !mensaje.startsWith(idUnicoRespuesta);
    // Devuelve true si el mensaje comienza con el ID del mensaje, pero no con el ID del mensaje de respuesta
  }
  const handleChat = () => {
    const userMessage = chatInput.value.trim();
    if (!userMessage) return;
    chatInput.value = "";
    chatInput.style.height = `${inputInitHeight}px`;
    const outgoingChatLi = createChatLi(userMessage, "outgoing");
    chatbox.appendChild(outgoingChatLi);
    chatbox.scrollTo(0, chatbox.scrollHeight);
    fetch("./guardar_mensaje.php", {
      method: "POST",
      body: new URLSearchParams({ message: userMessage }),
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
    })
      .then((response) => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error("Error inserting message");
        }
      })
      .then((result) => {
        console.log(result);
        // Aquí es donde se recibe el mensaje de respuesta desde el servidor
        // Debes asignar el mensaje de respuesta a la variable "responseMessage"
        const responseMessage = result;
        // Después de agregar un mensaje de respuesta al chatbox
        const incomingChatLi = createChatLi(responseMessage, "incoming");
        chatbox.appendChild(incomingChatLi);
        chatbox.scrollTo(0, chatbox.scrollHeight);
        // Llama a la función para reproducir el sonido de notificación
        playNotificationSound();
      })
      .catch((error) => {
        console.error(error);
      });
  };
  if (sendButton) {
    sendButton.addEventListener("click", handleChat);
  }
  closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
  chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
  userInfoForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const fullName = document.getElementById("full-name").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    document.cookie = `full_name=${encodeURIComponent(fullName)}; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/`;
    document.cookie = `email=${encodeURIComponent(email)}; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/`;
    document.cookie = `phone=${encodeURIComponent(phone)}; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/`;
    document.cookie = `user_info_submitted=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/`;
    userInfoForm.style.display = "none";
    document.querySelector(".chatbot").style.display = "block";
    showChatbot();
  });
  if (userInfoForm.style.display === "none") {
    document.body.classList.add("show-chatbot");
  }
  const closeIcon = document.querySelector(".close-icon");
  closeIcon.addEventListener("click", function() {
    document.cookie = "full_name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "phone=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "messages=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "user_info_submitted=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // Eliminar la cookie "id_unico" estableciendo su valor a una cadena vacía y una fecha de expiración pasada
    document.cookie = "id_unico=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    location.reload();
  });
  
  function obtenerHoraDelMensaje(responseHTML) {
    // Utiliza DOMParser para convertir la cadena HTML en un documento HTML
    const parser = new DOMParser();
    const htmlDoc = parser.parseFromString(responseHTML, 'text/html');
    
    // Aquí debes escribir el código para extraer la hora del mensaje desde htmlDoc
    // Por ejemplo, si la hora está contenida en un elemento con una clase "hora-mensaje":
    const horaMensajeElement = htmlDoc.querySelector(".hora-mensaje");
    
    if (horaMensajeElement) {
      return horaMensajeElement.textContent;
    }
    // Si no puedes obtener la hora, regresa un valor predeterminado o maneja el error de acuerdo a tu caso.
    return ""; // Valor predeterminado
  }

  function obtenerMensajesNuevos() {
    const idUnicoInput = document.getElementById("id_ticket");
    if (idUnicoInput) {
      const idUnico = idUnicoInput.value;
      const xhr = new XMLHttpRequest();
      xhr.open("GET", "../api/obtener_mensaje.php?id=" + idUnico, true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const responseHTML = xhr.responseText;
          chatbox.innerHTML = responseHTML; // Actualiza el chatbox con los mensajes nuevos
          const numMensajesSpan = document.getElementById("num-mensajes");
          const horaMensaje = obtenerHoraDelMensaje(responseHTML);
          // Verifica si se ha recibido un mensaje nuevo
          if (responseHTML.trim() !== "") {
            numMensajesSpan.textContent = "";
            // Comprueba si el mensaje es reciente y no es una respuesta a un mensaje específico
            if (esMensajeReciente(horaMensaje, responseHTML)) {
              playNotificationSound(); // Reproduce el sonido solo si cumple las condiciones
            }
            lastMessageTime = Date.now(); // Actualiza el marcador de tiempo
          } else {
            // No hay mensajes nuevos
            const currentTime = Date.now();
            // Si han pasado más de 5 segundos desde el último mensaje, detén el sonido
            if (currentTime - lastMessageTime >= 5000) {
              stopNotificationSound();
            }
          }
        }
      };
      xhr.send();
    }
  }
  
  
  function esMensajeReciente(horaMensaje) {
    const fechaMensaje = new Date(horaMensaje);
    const fechaActual = new Date();
    const tiempoTranscurrido = (fechaActual - fechaMensaje) / 1000;
    return tiempoTranscurrido <= 120;
  }
  
  
  function playNotificationSound() {
    // Reproduce el sonido de notificación solo si se ha permitido la reproducción por interacción del usuario
    const notificationSound = document.getElementById("notification-sound");
    if (notificationSound) {
      notificationSound.play().catch(function (error) {
        console.error("Error al reproducir el sonido de notificación:", error);
      });
    }
  }

  let lastMessageTime = 0;
  function stopNotificationSound() {
    // Detén la reproducción del sonido si se estaba reproduciendo
    if (isSoundPlaying) {
      isSoundPlaying = false;
      const notificationSound = document.querySelector("#notification-sound");
      if (notificationSound) {
        notificationSound.pause();
        notificationSound.currentTime = 0;
      }
    }
  }
  setInterval(obtenerMensajesNuevos, 5000);
  function getCookie(name) {
    const cookies = document.cookie.split("; ");
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i].split("=");
      if (cookie[0] === name) {
        return decodeURIComponent(cookie[1]);
      }
    }
    return "";
  }

});