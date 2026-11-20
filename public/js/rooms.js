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

document.querySelectorAll('[data-room-image-input]').forEach((input) => {
    input.addEventListener('change', () => {
        const preview = input.closest('form').querySelector('[data-room-image-preview]');
        const image = preview.querySelector('img');
        const file = input.files[0];

        if (!file) {
            return;
        }

        image.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
        image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
    });
});
