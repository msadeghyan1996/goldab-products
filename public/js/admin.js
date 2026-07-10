const toPersianDigits = (value) => value.replace(/[0-9]/g, (digit) => '۰۱۲۳۴۵۶۷۸۹'[Number(digit)]);

const localizeAdminNumbers = () => {
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
        acceptNode(node) {
            const parent = node.parentElement;
            if (!parent || ['SCRIPT', 'STYLE', 'TEXTAREA', 'INPUT', 'SELECT', 'OPTION'].includes(parent.tagName)) {
                return NodeFilter.FILTER_REJECT;
            }

            return /[0-9]/.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
        },
    });

    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach((node) => { node.nodeValue = toPersianDigits(node.nodeValue); });
};

document.addEventListener('DOMContentLoaded', () => {
    localizeAdminNumbers();

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();

            const result = await Swal.fire({
                title: 'از حذف مطمئن هستید؟',
                text: form.dataset.confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                confirmButtonColor: '#dc3545',
            });

            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.requestSubmit();
            }
        });
    });

    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        document.querySelector('.sidebar')?.classList.toggle('show');
    });
});
