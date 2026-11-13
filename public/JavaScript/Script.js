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

// Materiales.html
const listaMateriales = document.getElementById("listaMateriales");
if (listaMateriales) {
    pedirApi("material").then(function (materiales) {
        listaMateriales.innerHTML = "";
        materiales.forEach(function (material) {
            const columna = crearElemento("div", "col-md-4");
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
            listaMateriales.appendChild(columna);
        });
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
        proveedores.forEach(function (proveedor) {
            const columna = crearElemento("div", "col-6 col-md-3");
            const logo = crearElemento("img", "logoProveedor mb-2");
            logo.src = rutaImagenes + encodeURIComponent(proveedor.logo);
            logo.alt = proveedor.razon_social;
            logo.loading = "lazy";
            columna.append(logo, crearElemento("p", "small text-secondary mb-0", proveedor.razon_social));
            listaProveedores.appendChild(columna);
        });
    }).catch(function () {
        seccionProveedores.remove();
    });
}

