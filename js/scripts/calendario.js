(function () {
    'use strict';
    const elemento = document.querySelector('#calendario-operaciones');
    if (!elemento || !window.FullCalendar) {
        return;
    }
    try {
        const eventos = JSON.parse(elemento.dataset.eventos || '[]');
        const calendario = new FullCalendar.Calendar(elemento, {
            initialView: 'dayGridMonth',
            height: 520,
            events: eventos,
            buttonText: { today: 'Hoy', month: 'Mes', list: 'Lista' }
        });
        calendario.render();
    } catch (error) {
        console.error('No se pudo inicializar el calendario:', error);
    }
})();
