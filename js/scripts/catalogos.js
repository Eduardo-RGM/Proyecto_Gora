(function () {
    'use strict';

    const tabla = document.querySelector('#tablaEmpleados');
    const archivoFoto = document.querySelector('input[type="file"][name="data[foto]"]');
    if (archivoFoto && window.FilePond && window.FilePondPluginImagePreview) {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.create(archivoFoto, {
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'],
            maxFileSize: '2MB',
            storeAsFile: true,
            allowProcess: false,
            labelIdle: 'Arrastra una foto o <span class="filepond--label-action">selecciónala</span>',
            labelFileTypeNotAllowed: 'Tipo de archivo no permitido',
            labelMaxFileSizeExceeded: 'La imagen no puede superar 2 MB'
        });
    }

    const exportarExcel = document.querySelector('#exportarExcel');
    if (exportarExcel && window.XLSX && tabla) {
        exportarExcel.addEventListener('click', function () {
            const libro = XLSX.utils.table_to_book(tabla, { sheet: 'Empleados' });
            XLSX.writeFile(libro, 'empleados.xlsx');
        });
    }

    document.querySelectorAll('.js-select-buscable').forEach(function (select) {
        if (window.Choices) {
            new Choices(select, {
                searchEnabled: true,
                shouldSort: false,
                itemSelectText: 'Seleccionar'
            });
        }
    });

    const modalQr = document.querySelector('#modalQrEmpleado');
    if (modalQr && window.QRCode) {
        modalQr.addEventListener('show.bs.modal', function (evento) {
            const boton = evento.relatedTarget;
            const contenedor = modalQr.querySelector('#qrEmpleado');
            contenedor.innerHTML = '';
            new QRCode(contenedor, {
                text: boton.dataset.qr,
                width: 220,
                height: 220,
                correctLevel: QRCode.CorrectLevel.M
            });
            modalQr.querySelector('.modal-title').textContent = 'QR de ' + boton.dataset.nombre;
        });
    }
})();
