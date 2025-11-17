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

    if (data.success) {
      document.getElementById("nombres").value = data.nombres || "";
      document.getElementById("apellidos").value = data.apellidos || "";
    } else {
      alert("Error: " + (data.message || "No se encontraron datos"));
    }
  } catch (error) {
    alert("Error al consultar el DNI: " + error.message);
  }
};

// Los campos de tratamiento y odontólogo se envían con valor 1 por defecto
// (campos hidden en el formulario)

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
    if (data.success) {
      alert("Reserva guardada exitosamente");
      window.location.href = "index.html";
    } else {
      alert("Error: " + (data.message || "No se pudo guardar la reserva"));
    }
  } catch (e) {
    alert("Error al enviar la reserva");
  }
}

// Agregar eventos
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("btnConsultarDni")
    .addEventListener("click", consultarDNI);
  document.querySelector("form").addEventListener("submit", enviarReserva);
});
