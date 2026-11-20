document.querySelectorAll('[data-price-filter]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const minimum = form.querySelector('[name="min_price"]');
        const maximum = form.querySelector('[name="max_price"]');
        const error = form.querySelector('[data-price-error]');

        error.textContent = '';

        if (minimum.value && maximum.value && Number(maximum.value) < Number(minimum.value)) {
            event.preventDefault();
            error.textContent = 'El precio máximo debe ser igual o mayor que el mínimo.';
            maximum.focus();
        }
    });
});

document.querySelectorAll('[data-delete-room]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('¿Deseas eliminar esta habitación?')) {
            event.preventDefault();
        }
    });
});
