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
    const url = e.request.url;

    // 1. Sluit jouw specifieke API calls uit!
    // Je gebruikt 'ajax.php' en 'd.php' in je js, niet 'api.php'.
    if (url.includes('ajax.php') || url.includes('d.php') || e.request.method !== 'GET') {
        return; // Laat de browser dit oplossen via het netwerk
    }

    e.respondWith(
        // 2. ignoreSearch is cruciaal!
        caches.match(e.request, { ignoreSearch: true }).then(cachedResponse => {
            if (cachedResponse) {
                return cachedResponse;
            }
            return fetch(e.request).then(networkResponse => {
                // Sla alleen geldige responses op
                if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    return networkResponse;
                }
                const responseToCache = networkResponse.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(e.request, responseToCache);
                });
                return networkResponse;
            }).catch(() => {
                // Hier zou je evt. een offline afbeelding kunnen teruggeven
            });
        })
    );
});
