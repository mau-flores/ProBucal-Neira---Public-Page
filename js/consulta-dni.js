const consultarDNI = async () => {
  const dni = document.getElementById("dni").value;
  if (dni.length !== 8) {
    alert("El DNI debe tener 8 dígitos");
    return;
  }

  try {
    const response = await fetch(
      `./php/controllers/consultar_dni.php?dni=${dni}`,
      { method: "GET" }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    console.log("Respuesta de la API:", data); // Para depuración

    if (data.success) {
      document.getElementById("nombres").value = data.nombres || "";
      document.getElementById("apellidos").value = data.apellidos || "";
    } else {
      console.error("Error detallado:", data); // Para ver más detalles del error
      alert("Error: " + (data.message || "No se encontraron datos"));
    }
  } catch (error) {
    console.error("Error completo:", error);
    alert("Error al consultar el DNI: " + error.message);
  }
};

async function cargarOpciones() {
  // Cargar tratamientos
  try {
    const resT = await fetch("./php/controllers/get_tratamientos.php");
    if (!resT.ok) throw new Error("Error de red al cargar tratamientos");
    const dataT = await resT.json();
    const selT = document.getElementById("id_tratamiento");
    selT.innerHTML = "";
    if (dataT.success && Array.isArray(dataT.data) && dataT.data.length) {
      selT.appendChild(new Option("Seleccione un tratamiento", ""));
      dataT.data.forEach((t) => {
        if (t && t.id_tratamiento && t.nombre) {
          selT.appendChild(new Option(t.nombre, t.id_tratamiento));
        }
      });
    } else {
      console.warn("Respuesta tratamientos:", dataT);
      selT.appendChild(new Option("No hay tratamientos disponibles", ""));
    }
  } catch (e) {
    console.error("Error cargando tratamientos:", e);
    const selT = document.getElementById("id_tratamiento");
    selT.innerHTML = "";
    selT.appendChild(
      new Option("Error al cargar tratamientos - Intente más tarde", "")
    );
  }

  // Cargar odontólogos
  try {
    const resO = await fetch("./php/controllers/get_odontologos.php");
    if (!resO.ok) throw new Error("Error de red al cargar odontólogos");
    const dataO = await resO.json();
    const selO = document.getElementById("id_odontologo");
    selO.innerHTML = "";
    if (dataO.success && Array.isArray(dataO.data) && dataO.data.length) {
      selO.appendChild(new Option("Seleccione un odontólogo", ""));
      dataO.data.forEach((o) => {
        if (o && o.id_odontologo && o.nombre_completo) {
          selO.appendChild(new Option(o.nombre_completo, o.id_odontologo));
        }
      });
    } else {
      console.warn("Respuesta odontólogos:", dataO);
      selO.appendChild(new Option("No hay odontólogos disponibles", ""));
    }
  } catch (e) {
    console.error("Error cargando odontólogos:", e);
    const selO = document.getElementById("id_odontologo");
    selO.innerHTML = "";
    selO.appendChild(
      new Option("Error al cargar odontólogos - Intente más tarde", "")
    );
  }
}

async function enviarReserva(event) {
  event.preventDefault();

  const payload = {
    dni: document.getElementById("dni").value,
    nombres: document.getElementById("nombres").value,
    apellidos: document.getElementById("apellidos").value,
    telefono: document.getElementById("telefono").value,
    email: document.getElementById("email").value,
    edad: document.getElementById("edad").value,
    id_tratamiento: document.getElementById("id_tratamiento").value,
    id_odontologo: document.getElementById("id_odontologo").value,
    fecha: document.getElementById("fecha").value,
    hora: document.getElementById("hora").value,
    notas: document.getElementById("otros").value,
  };

  try {
    const res = await fetch("./php/controllers/procesar_reserva.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const data = await res.json();
    console.log("Respuesta reserva:", data);
    if (data.success) {
      alert("Reserva guardada exitosamente");
      window.location.href = "index.html";
    } else {
      alert("Error: " + (data.message || "No se pudo guardar la reserva"));
    }
  } catch (e) {
    console.error("Error enviando reserva", e);
    alert("Error al enviar la reserva");
  }
}

// Agregar eventos
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("btnConsultarDni")
    .addEventListener("click", consultarDNI);
  cargarOpciones();
  document.querySelector("form").addEventListener("submit", enviarReserva);
});
