const urlParams = new URLSearchParams(self.location.search);
const VERSION = urlParams.get('v') || '1.0';
const CACHE_NAME = 'floorplan-cache-' + VERSION;
const PRE_CACHE_ASSETS = [
    '/',
    '/index.php',
    '/icon.png',
    '/scripts/mqtt.min.js',
    '/scripts/floorplanjs.js',
    '/styles/floorplan.css',
    '/images/HomeZw.png',
	'/images/temp.png',
	'/images/l_On.png',
	'/images/l_Off.png',
	'/images/p_On.png',
	'/images/p_Off.png',
	'/images/ST30_On.png',
	'/images/ST30_Off.png',
	'/images/Thuis.png',
	'/images/weg.png',
	'/images/slapen.png',
	'/images/arrowdown.png'
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
