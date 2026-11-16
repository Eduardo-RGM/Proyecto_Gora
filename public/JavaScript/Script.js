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

//APIS

const urlApi = "../admin/api/";
const rutaImagenes = "../images/uploads/";

async function pedirApi(recurso) {
    const respuesta = await fetch(urlApi + recurso + "/index.php", { headers: { Accept: "application/json" } });
    if (!respuesta.ok) {
        throw new Error("La API respondió con HTTP " + respuesta.status);
    }

    return respuesta.json();
}

function crearElemento(etiqueta, clases, texto) {
    const elemento = document.createElement(etiqueta);
    if (clases) elemento.className = clases;
    if (texto) elemento.textContent = texto;
    return elemento;
}

const tablaPreciosSeccion = document.getElementById("tablaPreciosSeccion");
if (tablaPreciosSeccion) {
    const cuerpoPrecios = tablaPreciosSeccion.querySelector("tbody");
    pedirApi("precio").then(function (precios) {
        cuerpoPrecios.innerHTML = "";
        precios.forEach(function (precio) {
            const fila = crearElemento("tr");
            fila.append(
                crearElemento("td", "", precio.material),
                crearElemento("td", "", "$" + Number(precio.precio_compra).toFixed(2)),
                crearElemento("td", "", precio.fecha_actualizacion || "Sin actualizar")
            );
            cuerpoPrecios.appendChild(fila);
        });
        if (precios.length === 0) {
            const fila = crearElemento("tr");
            const celda = crearElemento("td", "", "No hay precios disponibles.");
            celda.colSpan = 3;
            fila.appendChild(celda);
            cuerpoPrecios.appendChild(fila);
        }
    }).catch(function () {
        cuerpoPrecios.innerHTML = "";
        const fila = crearElemento("tr");
        const celda = crearElemento("td", "", "No fue posible cargar los precios.");
        celda.colSpan = 3;
        fila.appendChild(celda);
        cuerpoPrecios.appendChild(fila);
    });
}

function crearCarrusel(id, elementos, crearContenido) {
    const contenedor = document.getElementById(id);
    if (!contenedor || elementos.length === 0) return;

    contenedor.innerHTML = "";
    elementos.forEach(function (elemento, indice) {
        const item = crearElemento("div", "carousel-item" + (indice === 0 ? " active" : ""));
        item.appendChild(crearContenido(elemento));
        contenedor.appendChild(item);
    });
}

// Materiales.html
const listaMateriales = document.getElementById("listaMateriales");
if (listaMateriales) {
    pedirApi("material").then(function (materiales) {
        crearCarrusel("listaMaterialesCarousel", materiales, function (material) {
            const columna = crearElemento("div", "col-md-8 mx-auto");
            const tarjeta = crearElemento("div", "card h-100 tarjetaServicio border-0 shadow-sm");
            const marco = crearElemento("div", "ratio ratio-4x3 bg-light");
            const imagen = crearElemento("img", "w-100 h-100 imagenMaterial");
            imagen.src = rutaImagenes + encodeURIComponent(material.foto);
            imagen.alt = material.material;
            imagen.loading = "lazy";
            marco.appendChild(imagen);
            const cuerpo = crearElemento("div", "card-body");
            cuerpo.append(
                crearElemento("h2", "h5 card-title text-capitalize", material.material),
                crearElemento("p", "card-text", material.descripcion)
            );
            tarjeta.append(marco, cuerpo);
            columna.appendChild(tarjeta);
            return columna;
        });
        listaMateriales.classList.add("d-none");
    }).catch(function () {
        listaMateriales.innerHTML = "";
        listaMateriales.appendChild(crearElemento("p", "text-center text-secondary", "No fue posible cargar los materiales. Intenta más tarde."));
    });
}

// Historia.html: equipo
const listaEquipo = document.getElementById("listaEquipo");
if (listaEquipo) {
    pedirApi("empleado").then(function (empleados) {
        empleados.forEach(function (empleado) {
            const columna = crearElemento("div", "col-6 col-md-4 text-center");
            const foto = crearElemento("img", "fotoEmpleado shadow-sm mb-2");
            foto.src = rutaImagenes + encodeURIComponent(empleado.foto);
            foto.alt = empleado.nombre;
            foto.loading = "lazy";
            const nombre = [empleado.nombre, empleado.pri_apellido, empleado.seg_apellido].join(" ").trim();
            columna.append(foto, crearElemento("p", "fw-bold mb-0", nombre), crearElemento("small", "text-secondary", empleado.puesto));
            listaEquipo.appendChild(columna);
        });
    }).catch(function () {
        listaEquipo.appendChild(crearElemento("p", "text-secondary", "No fue posible cargar al equipo."));
    });
}

// Index.html: contadores y proveedores
const totalEmpleados = document.getElementById("totalEmpleados");
if (totalEmpleados) {
    pedirApi("empleado").then(function (empleados) {
        totalEmpleados.textContent = empleados.length;
    }).catch(function () {});
}

const totalMateriales = document.getElementById("totalMateriales");
if (totalMateriales) {
    pedirApi("material").then(function (materiales) {
        totalMateriales.textContent = materiales.length;
    }).catch(function () {});
}

const seccionProveedores = document.getElementById("seccionProveedores");
if (seccionProveedores) {
    const listaProveedores = document.getElementById("listaProveedores");
    pedirApi("proveedor").then(function (proveedores) {
        if (proveedores.length === 0) {
            seccionProveedores.remove();
            return;
        }
        const contenedor = document.getElementById("listaProveedores");
        contenedor.innerHTML = "";
        for (let inicio = 0; inicio < proveedores.length; inicio += 3) {
            const item = crearElemento("div", "carousel-item" + (inicio === 0 ? " active" : ""));
            const fila = crearElemento("div", "row g-3 justify-content-center");
            proveedores.slice(inicio, inicio + 3).forEach(function (proveedor) {
                const columna = crearElemento("div", "col-12 col-md-4 text-center");
                const tarjeta = crearElemento("div", "tarjetaProveedor h-100");
                const logo = crearElemento("img", "logoProveedor mb-2");
                logo.src = rutaImagenes + encodeURIComponent(proveedor.logo);
                logo.alt = proveedor.razon_social;
                logo.loading = "lazy";
                tarjeta.append(logo, crearElemento("p", "small mb-0", proveedor.razon_social));
                columna.appendChild(tarjeta);
                fila.appendChild(columna);
            });
            item.appendChild(fila);
            contenedor.appendChild(item);
        }
    }).catch(function () {
        seccionProveedores.remove();
    });
}

function enviarFormulario(formulario, tipo) {
    formulario.addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const boton = formulario.querySelector("button[type='submit']");
        const estado = crearElemento("div", "alert mt-3");
        boton.disabled = true;

        try {
            const datos = new FormData(formulario);
            datos.set("tipo", tipo);
            const respuesta = await fetch("api/formularios.php", {
                method: "POST",
                body: datos,
                headers: { Accept: "application/json" }
            });
            const resultado = await respuesta.json();
            if (!respuesta.ok) throw new Error(resultado.error || "No fue posible enviar el formulario.");
            estado.classList.add("alert-success");
            estado.textContent = resultado.mensaje;
            formulario.reset();
        } catch (error) {
            estado.classList.add("alert-danger");
            estado.textContent = error.message;
        } finally {
            boton.disabled = false;
            formulario.appendChild(estado);
        }
    });
}

if (formularioContacto) enviarFormulario(formularioContacto, "contacto");
if (formularioCotizacion) enviarFormulario(formularioCotizacion, "cotizacion");

