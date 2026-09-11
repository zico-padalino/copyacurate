<script>
(() => {
    const digitsOnly = (value) => String(value ?? '').replace(/\D/g, '');

    const formatRupiah = (value) => {
        const digits = digitsOnly(value);
        if (!digits) {
            return '';
        }

        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    const bindRupiahInput = (input) => {
        if (!(input instanceof HTMLInputElement) || input.dataset.rupiahBound === '1') {
            return;
        }

        input.dataset.rupiahBound = '1';
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('autocomplete', 'off');

        if (input.value) {
            input.value = formatRupiah(input.value);
        }

        input.addEventListener('input', () => {
            const cursor = input.selectionStart ?? input.value.length;
            const beforeLength = input.value.length;
            input.value = formatRupiah(input.value);
            const afterLength = input.value.length;
            const nextCursor = Math.max(0, cursor + (afterLength - beforeLength));
            input.setSelectionRange(nextCursor, nextCursor);
        });

        input.addEventListener('blur', () => {
            input.value = formatRupiah(input.value);
        });
    };

    document.querySelectorAll('.js-rupiah').forEach(bindRupiahInput);

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll('.js-rupiah').forEach((input) => {
                input.value = digitsOnly(input.value);
            });
        });
    });
})();
</script>
