const formatPersianNumber = (value) => new Intl.NumberFormat('fa-IR').format(value);
let livePriceRefreshInProgress = false;

const refreshLivePrices = async () => {
    const endpoint = document.body.dataset.livePricesEndpoint;
    if (!endpoint || livePriceRefreshInProgress || document.hidden) return;

    livePriceRefreshInProgress = true;
    const url = new URL(endpoint, window.location.origin);
    const productIds = [...new Set(
        [...document.querySelectorAll('[data-live-product-id]')].map((element) => element.dataset.liveProductId),
    )];
    productIds.forEach((id) => url.searchParams.append('product_ids[]', id));

    try {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Live price request failed');
        const data = await response.json();

        if (data.rate) {
            document.querySelectorAll('[data-live-gold-rate]').forEach((element) => {
                element.textContent = formatPersianNumber(data.rate.gram_price);
                element.classList.add('price-refreshed');
            });
            document.querySelectorAll('[data-live-updated-at]').forEach((element) => {
                element.textContent = data.rate.updated_at.replace(/[0-9]/g, (digit) => '۰۱۲۳۴۵۶۷۸۹'[Number(digit)]);
            });
        }

        Object.entries(data.products || {}).forEach(([id, price]) => {
            if (price === null) return;
            document.querySelectorAll(`[data-live-product-id="${id}"]`).forEach((element) => {
                const suffix = element.closest('.product-card-price') ? '' : ' تومان';
                element.textContent = `${formatPersianNumber(price)}${suffix}`;
                element.classList.add('price-refreshed');
            });
        });

        window.setTimeout(() => {
            document.querySelectorAll('.price-refreshed').forEach((element) => element.classList.remove('price-refreshed'));
        }, 900);
    } catch (error) {
        // Keep the last successfully rendered price when the network is temporarily unavailable.
    } finally {
        livePriceRefreshInProgress = false;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const section = document.querySelector('[data-category-section]');

    if (section) {
        const buttons = section.querySelectorAll('[data-category]');
        const products = section.querySelector('[data-category-products]');
        const loader = section.querySelector('[data-category-loader]');
        const allLink = section.querySelector('[data-category-all]');

        buttons.forEach((button) => button.addEventListener('click', async () => {
            if (button.classList.contains('active')) return;
            buttons.forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            products.classList.add('loading-fade');
            loader.classList.remove('d-none');

            const category = button.dataset.category;
            const url = new URL(section.dataset.endpoint, window.location.origin);
            url.searchParams.set('section', 'category');
            url.searchParams.set('per_page', '8');
            if (category) url.searchParams.set('category_id', category);

            try {
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Request failed');
                const data = await response.json();
                products.innerHTML = data.html;
                const catalogUrl = new URL('/', window.location.origin);
                catalogUrl.searchParams.set('all', '1');
                if (category) catalogUrl.searchParams.set('category_id', category);
                allLink.href = catalogUrl.pathname + catalogUrl.search;
                refreshLivePrices();
            } catch (error) {
                products.innerHTML = '<div class="col-12 empty-products"><p>دریافت محصولات با خطا مواجه شد.</p></div>';
            } finally {
                products.classList.remove('loading-fade');
                loader.classList.add('d-none');
            }
        }));
    }

    const container = document.querySelector('[data-infinite-products]');
    const status = document.querySelector('[data-infinite-loader]');

    if (container && status && status.dataset.nextUrl) {
        let loading = false;
        const observer = new IntersectionObserver(async (entries) => {
            if (!entries[0].isIntersecting || loading || !status.dataset.nextUrl) return;
            loading = true;
            try {
                const response = await fetch(status.dataset.nextUrl, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Request failed');
                const data = await response.json();
                container.insertAdjacentHTML('beforeend', data.html);
                status.dataset.nextUrl = data.next_page_url || '';
                refreshLivePrices();
                if (!data.next_page_url) {
                    status.innerHTML = '';
                    observer.disconnect();
                }
            } catch (error) {
                status.innerHTML = '<span>دریافت محصولات بیشتر با خطا مواجه شد.</span>';
            } finally {
                loading = false;
            }
        }, { rootMargin: '350px' });
        observer.observe(status);
    }

    refreshLivePrices();
    window.setInterval(refreshLivePrices, 60_000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) refreshLivePrices();
    });
});
