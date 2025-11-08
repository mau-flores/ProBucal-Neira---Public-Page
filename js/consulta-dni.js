const consultarDNI = async () => {
  const dni = document.getElementById("dni").value;
  if (dni.length !== 8) {
    alert("El DNI debe tener 8 dígitos");
    return;
  }

  try {
    const response = await fetch(
      `./php/controllers/consultar_dni.php?dni=${dni}`,
      {
        method: "GET",
      }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    console.log("Respuesta de la API:", data); // Para depuración

    if (data.success) {
      document.getElementById("nombres").value = data.nombres;
      document.getElementById("apellidos").value = data.apellidos;
    } else {
      console.error("Error detallado:", data); // Para ver más detalles del error
      alert("Error: " + data.message);
    }
  } catch (error) {
    console.error("Error completo:", error);
    alert("Error al consultar el DNI: " + error.message);
  }
};

// Agregar el evento click al botón de consulta
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("btnConsultarDni")
    .addEventListener("click", consultarDNI);
});
