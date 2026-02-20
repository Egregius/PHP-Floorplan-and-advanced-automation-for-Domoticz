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
    if (url.pathname.includes('ajax.php') || url.pathname.includes('d.php')) return;
    e.respondWith(
        caches.match(e.request, { ignoreSearch: true }).then(cached => {
            if (cached) return cached;
            return fetch(e.request).then(response => {
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }
                const copy = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(e.request, copy));
                return response;
            });
        })
    );
});
