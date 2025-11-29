import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

function detectLocalIPv4() {
    const ifaces = os.networkInterfaces();
    const candidates = [];
    const nameLower = (n) => (n || '').toLowerCase();
    for (const name of Object.keys(ifaces)) {
        for (const iface of ifaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal && iface.address) {
                // skip obvious APIPA
                if (iface.address.startsWith('169.254')) continue;
                candidates.push({ name, addr: iface.address });
            }
        }
    }

    // prefer private LAN ranges and avoid virtual adapters by name heuristics
    const isLan = (a) => /^(10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/.test(a);
    const virtualNameRx = /virtual|vbox|vmware|hyper|docker|nat|adapter/i;

    // 1) LAN addresses on non-virtual adapters
    for (const c of candidates) {
        if (isLan(c.addr) && !virtualNameRx.test(nameLower(c.name))) return c.addr;
    }
    // 2) any LAN address
    for (const c of candidates) {
        if (isLan(c.addr)) return c.addr;
    }
    // 3) first non-virtual candidate
    for (const c of candidates) {
        if (!virtualNameRx.test(nameLower(c.name))) return c.addr;
    }
    // 4) fallback to first candidate
    if (candidates.length) return candidates[0].addr;
    return '127.0.0.1';
}

const detectedHost = detectLocalIPv4();
console.log(`Vite HMR host detected: ${detectedHost}`);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/explore.css',
                'resources/css/nightMode.css',
                'resources/js/app.js',
                'resources/js/dashBoard.js',
                'resources/js/searchFlights.js',
                'resources/js/formTrip.js',
                'resources/js/hotels.js',
                'resources/js/nightMode.js',
                'resources/js/ia-place-modal.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // listen on all interfaces so network devices can connect
        host: true,
        hmr: {
                // use detected IP for HMR so browsers on the same LAN can connect
                host: detectedHost || 'localhost',
        },
    },
});
