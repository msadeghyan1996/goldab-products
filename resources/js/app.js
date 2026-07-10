document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();
            const result = await Swal.fire({
                title: 'از حذف مطمئن هستید؟', text: form.dataset.confirm,
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'بله، حذف شود', cancelButtonText: 'انصراف',
                confirmButtonColor: '#dc3545',
            });
            if (result.isConfirmed) { form.dataset.confirmed = 'true'; form.requestSubmit(); }
        });
    });

    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        document.querySelector('.sidebar')?.classList.toggle('show');
    });
});
