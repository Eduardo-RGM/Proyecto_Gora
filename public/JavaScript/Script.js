const elementoAnio = document.getElementById("anioActual");
if (elementoAnio) {
    elementoAnio.textContent = new Date().getFullYear();
}

const formularioContacto = document.getElementById("formularioContacto");
if (formularioContacto) {
    formularioContacto.addEventListener("submit", function (evento) {
        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const mensaje = document.getElementById("mensaje").value.trim();

        if (nombre === "" || correo === "" || mensaje === "") {
            evento.preventDefault();
            alert("Por favor llena tu nombre, correo y mensaje antes de enviar.");
        }
    });
}

const formularioCotizacion = document.getElementById("formularioCotizacion");
if (formularioCotizacion) {
    formularioCotizacion.addEventListener("submit", function (evento) {
        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const cantidad = document.getElementById("cantidad").value.trim();

        if (nombre === "" || correo === "" || cantidad === "") {
            evento.preventDefault();
            alert("Por favor llena tu nombre, correo y la cantidad aproximada.");
        }
    });
}

