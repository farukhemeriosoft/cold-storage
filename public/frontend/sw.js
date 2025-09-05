// Service Worker for Offline Support
const CACHE_NAME = 'cold-storage-v1'
const OFFLINE_URL = '/offline.html'

// Files to cache for offline use
const CACHE_FILES = [
  '/',
  '/frontend/',
  '/frontend/index.html',
  '/frontend/assets/index.css',
  '/frontend/assets/index.js',
  '/manifest.json'
]

// Install event - cache essential files
self.addEventListener('install', (event) => {
  console.log('Service Worker installing...')
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Caching essential files...')
        return cache.addAll(CACHE_FILES)
      })
      .then(() => {
        console.log('Service Worker installed successfully')
        return self.skipWaiting()
      })
      .catch((error) => {
        console.error('Service Worker installation failed:', error)
      })
  )
})

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker activating...')
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('Deleting old cache:', cacheName)
              return caches.delete(cacheName)
            }
          })
        )
      })
      .then(() => {
        console.log('Service Worker activated')
        return self.clients.claim()
      })
  )
})

// Fetch event - serve from cache when offline
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return
  }

  // Skip API requests (they should be handled by the app)
  if (event.request.url.includes('/api/')) {
    return
  }

  event.respondWith(
    caches.match(event.request)
      .then((cachedResponse) => {
        // Return cached version if available
        if (cachedResponse) {
          console.log('Serving from cache:', event.request.url)
          return cachedResponse
        }

        // Otherwise, fetch from network
        return fetch(event.request)
          .then((response) => {
            // Don't cache if not a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response
            }

            // Clone the response
            const responseToCache = response.clone()

            // Cache the response for future use
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache)
              })

            return response
          })
          .catch(() => {
            // If offline and no cache, show offline page for navigation requests
            if (event.request.destination === 'document') {
              return caches.match(OFFLINE_URL)
            }
          })
      })
  )
})

// Background sync for offline data
self.addEventListener('sync', (event) => {
  console.log('Background sync triggered:', event.tag)

  if (event.tag === 'sync-offline-data') {
    event.waitUntil(syncOfflineData())
  }
})

// Sync offline data when connection is restored
async function syncOfflineData() {
  try {
    console.log('Syncing offline data...')

    // Get offline data from IndexedDB
    const offlineData = await getOfflineData()

    if (offlineData && offlineData.length > 0) {
      console.log(`Found ${offlineData.length} offline operations to sync`)

      // Process each offline operation
      for (const operation of offlineData) {
        try {
          await syncOperation(operation)
          // Remove from offline queue after successful sync
          await removeOfflineOperation(operation.id)
        } catch (error) {
          console.error('Failed to sync operation:', operation, error)
        }
      }

      console.log('Offline data sync completed')
    }
  } catch (error) {
    console.error('Offline data sync failed:', error)
  }
}

// Sync individual operation
async function syncOperation(operation) {
  const response = await fetch(operation.url, {
    method: operation.method,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': operation.headers?.Authorization || ''
    },
    body: operation.body ? JSON.stringify(operation.body) : undefined
  })

  if (!response.ok) {
    throw new Error(`Sync failed: ${response.status}`)
  }

  return response.json()
}

// Get offline data from IndexedDB
async function getOfflineData() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('ColdStorageOffline', 1)

    request.onerror = () => reject(request.error)
    request.onsuccess = () => {
      const db = request.result
      const transaction = db.transaction(['offlineQueue'], 'readonly')
      const store = transaction.objectStore('offlineQueue')
      const getAllRequest = store.getAll()

      getAllRequest.onsuccess = () => resolve(getAllRequest.result)
      getAllRequest.onerror = () => reject(getAllRequest.error)
    }
  })
}

// Remove offline operation after successful sync
async function removeOfflineOperation(id) {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('ColdStorageOffline', 1)

    request.onerror = () => reject(request.error)
    request.onsuccess = () => {
      const db = request.result
      const transaction = db.transaction(['offlineQueue'], 'readwrite')
      const store = transaction.objectStore('offlineQueue')
      const deleteRequest = store.delete(id)

      deleteRequest.onsuccess = () => resolve()
      deleteRequest.onerror = () => reject(deleteRequest.error)
    }
  })
}
