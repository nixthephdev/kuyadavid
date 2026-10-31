import { defineConfig } from '@playwright/test';
import path from 'node:path';

const port = 8123;
const database = path.resolve('database/e2e.sqlite');

// Browser tests run against a separate SQLite database filled with sample content.
export default defineConfig({
    testDir: './e2e',
    fullyParallel: false,
    workers: 1,
    reporter: 'list',
    use: {
        baseURL: `http://127.0.0.1:${port}`,
        permissions: ['clipboard-read', 'clipboard-write'],
    },
    projects: [
        { name: 'phone', use: { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true } },
        { name: 'desktop', use: { viewport: { width: 1280, height: 800 } } },
    ],
    webServer: {
        command: `node e2e/setup-db.js && php artisan migrate:fresh --seed --force && php artisan db:seed --class=DemoContentSeeder --force && php artisan serve --port=${port}`,
        url: `http://127.0.0.1:${port}/up`,
        reuseExistingServer: false,
        timeout: 120_000,
        env: {
            APP_ENV: 'testing',
            APP_URL: `http://127.0.0.1:${port}`,
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: database,
            CACHE_STORE: 'array',
            MAIL_MAILER: 'array',
            ADMIN_EMAIL: 'e2e@example.com',
            ADMIN_PASSWORD: 'e2e-password',
        },
    },
});
