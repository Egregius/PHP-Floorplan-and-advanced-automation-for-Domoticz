const urlParams = new URLSearchParams(self.location.search);
const VERSION = urlParams.get('v') || '1.0';
const CACHE_NAME = 'floorplan-cache-' + VERSION;
const PRE_CACHE_ASSETS = [
    '/',
    '/index.php',
    '/scripts/mqtt.min.js',
    '/scripts/floorplanjs.js',
    '/styles/floorplan.css',
    '/images/domoticzphp48.png',
    '/images/domoticzphp144.png',
    // We laten dynamische statussen zoals _On.png hier uit,
    // die pakt de 'fetch' handler on-the-fly wel op.
];

self.addEventListener('install', e => {
    console.log('SW Install: Caching assets naar ' + CACHE_NAME);
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRE_CACHE_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('SW Activate: Schoonmaak...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);

    // Strategie: Network-First voor de HTML pagina zelf
    if (e.request.mode === 'navigate' || url.pathname.endsWith('index.php') || url.pathname === '/') {
        e.respondWith(
            fetch(e.request)
                .then(response => {
                    // Update de cache met de nieuwste index
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(e.request, copy));
                    return response;
                })
                .catch(() => caches.match(e.request)) // Fallback naar cache als internet plat ligt
        );
        return;
    }

    // Bestaande logica voor rest (scripts, images, etc.) - Cache-First
    if (url.pathname.includes('ajax.php') || url.pathname.includes('d.php')) return;

    e.respondWith(
        caches.match(e.request, { ignoreSearch: true }).then(cached => {
            return cached || fetch(e.request).then(response => {
                const copy = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(e.request, copy));
                return response;
            });
        })
    );
});
