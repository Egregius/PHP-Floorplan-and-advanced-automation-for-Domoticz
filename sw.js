const VERSION = '1.3';
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
	'/images/Slapen.png',
	'/images/arrowdown.png'
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRE_CACHE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    console.log('SW Activate: Oude caches verwijderen...');
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
const NETWORK_FIRST = [
	'index.php',
    'temp.php',
    'tempbig.php',
    'hum.php',
    'log.php',
    'floorplan.cache.php',
    'floorplan.doorsensors.php',
    'kodi.php',
    'kodicontrol.php',
];
const CACHE_EXCLUDED = [
    'ajax.php',
    'd.php',
];
self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    const url = e.request.url;
    if (CACHE_EXCLUDED.some(pattern => url.includes(pattern))) return;
    if (NETWORK_FIRST.some(pattern => url.includes(pattern))) {
        e.respondWith(
            fetch(e.request).then(networkResponse => {
                if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    return networkResponse;
                }
                const responseToCache = networkResponse.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(e.request, responseToCache);
                });
                return networkResponse;
            }).catch(() => {
                return caches.match(e.request);
            })
        );
        return;
    }
    e.respondWith(
        caches.match(e.request).then(cachedResponse => {
            if (cachedResponse) return cachedResponse;
            return fetch(e.request).then(networkResponse => {
                if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    return networkResponse;
                }
                const responseToCache = networkResponse.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(e.request, responseToCache);
                });
                return networkResponse;
            }).catch(() => {});
        })
    );
});
