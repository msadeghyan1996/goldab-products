const formatPersianNumber = (value) => new Intl.NumberFormat('fa-IR').format(value);
let livePriceRefreshInProgress = false;

const presenceDiscountAmount = (price) => Math.round(price * 0.01);

const initProductMagnifiers = (root = document) => {
    if (window.matchMedia('(hover: none), (pointer: coarse)').matches) return;

    root.querySelectorAll('.product-image-wrap img, .detail-main-image img').forEach((image) => {
        const frame = image.closest('.product-image-wrap, .detail-main-image');
        if (!frame || frame.dataset.magnifierReady === 'true') return;

        frame.dataset.magnifierReady = 'true';
        const lens = document.createElement('span');
        lens.className = 'product-magnifier-lens';
        const zoomImage = document.createElement('img');
        zoomImage.alt = '';
        zoomImage.decoding = 'async';
        lens.appendChild(zoomImage);
        frame.appendChild(lens);

        const zoom = frame.classList.contains('detail-main-image') ? 2.35 : 2.15;

        const updateLens = (event) => {
            const src = image.currentSrc || image.src;
            if (!src) return;
            if (zoomImage.src !== src) zoomImage.src = src;

            const rect = frame.getBoundingClientRect();
            const lensSize = lens.offsetWidth || 132;
            const radius = lensSize / 2;
            const x = Math.max(0, Math.min(event.clientX - rect.left, rect.width));
            const y = Math.max(0, Math.min(event.clientY - rect.top, rect.height));

            const lensLeft = Math.max(0, Math.min(x - radius, rect.width - lensSize));
            const lensTop = Math.max(0, Math.min(y - radius, rect.height - lensSize));

            lens.style.left = `${lensLeft}px`;
            lens.style.top = `${lensTop}px`;
            zoomImage.style.width = `${rect.width * zoom}px`;
            zoomImage.style.height = `${rect.height * zoom}px`;
            zoomImage.style.objectFit = getComputedStyle(image).objectFit || 'cover';
            zoomImage.style.left = `${radius - (x * zoom)}px`;
            zoomImage.style.top = `${radius - (y * zoom)}px`;
        };

        frame.addEventListener('pointerenter', (event) => {
            frame.classList.add('is-magnifying');
            updateLens(event);
        });
        frame.addEventListener('pointermove', updateLens);
        frame.addEventListener('pointerleave', () => frame.classList.remove('is-magnifying'));
    });
};

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
            document.querySelectorAll(`[data-live-presence-saving-id="${id}"]`).forEach((element) => {
                element.textContent = `${formatPersianNumber(presenceDiscountAmount(price))} تومان`;
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
                initProductMagnifiers(products);
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
                initProductMagnifiers(container);
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

    initProductMagnifiers();
    refreshLivePrices();
    window.setInterval(refreshLivePrices, 60_000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) refreshLivePrices();
    });
});
