const VERSION = '1.4';
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
	'/images/ST10_On.png',
	'/images/ST10_Off.png',
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
    'kodicontrol.php'
];
const CACHE_EXCLUDED = [
    'ajax.php',
    'd.php',
		'temp.php',
		'hum.php',
		
];
self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    if (CACHE_EXCLUDED.some(pattern => e.request.url.includes(pattern))) return;
    e.respondWith(
        caches.match(e.request).then(cachedResponse => {
            if (cachedResponse) return cachedResponse;
            return fetch(e.request)
                .then(networkResponse => {
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                        return networkResponse;
                    }
                    const clone = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(e.request, clone));
                    return networkResponse;
                })
                .catch(() => {
                    console.warn('[SW] Fetch mislukt en geen cache voor: ' + e.request.url);
                    return new Response('', { status: 503, statusText: 'Offline' });
                });
        })
    );
});
