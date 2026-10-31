import { test, expect } from '@playwright/test';

const pages = [
    ['home', '/'],
    ['story', '/story'],
    ['help', '/help'],
    ['updates', '/updates'],
    ['update', '/updates/sample-update-1-demo-only'],
    ['transparency', '/transparency'],
    ['prayers', '/prayer-wall'],
    ['contact', '/contact'],
];

/** Fails the test on any console error or uncaught exception. */
function watchConsole(page) {
    const errors = [];
    page.on('console', (msg) => msg.type() === 'error' && errors.push(msg.text()));
    page.on('pageerror', (err) => errors.push(err.message));
    return errors;
}

for (const [name, url] of pages) {
    test(`${name} page loads cleanly`, async ({ page }, testInfo) => {
        const errors = watchConsole(page);
        const response = await page.goto(url);

        expect(response.status()).toBe(200);
        await expect(page.locator('h1').first()).toBeVisible();
        await expect(page.getByText('Website made with love and offered for free by a volunteer.')).toBeVisible();

        // No sideways scrolling on any screen size.
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
        expect(overflow).toBeLessThanOrEqual(0);

        // Every image has alt text (empty alt is allowed for decorative images).
        expect(await page.locator('img:not([alt])').count()).toBe(0);

        // Load every lazy image now, then check none of them is broken.
        const broken = await page.evaluate(async () => {
            const imgs = [...document.images];
            imgs.forEach((img) => (img.loading = 'eager'));
            await Promise.all(imgs.map((img) => img.decode().catch(() => {})));
            return imgs.filter((img) => img.naturalWidth === 0).map((img) => img.src);
        });
        expect(broken).toEqual([]);

        await page.screenshot({ path: `e2e/screenshots/${testInfo.project.name}-${name}.png`, fullPage: true });
        expect(errors).toEqual([]);
    });
}

test('progress bar shows the admin amounts', async ({ page }) => {
    await page.goto('/');
    const bar = page.getByRole('progressbar');
    await expect(bar).toHaveAttribute('aria-valuenow', '27'); // 137,500 of 500,000
    await expect(page.getByText('₱137,500').first()).toBeVisible();
});

test('copy number button copies the GCash number', async ({ page }) => {
    const errors = watchConsole(page);
    await page.goto('/help');
    await page.getByRole('button', { name: 'Copy number' }).click();
    await expect(page.locator('#gcash').getByText('Copied!')).toBeVisible();
    expect(errors).toEqual([]);
});

test('language toggle switches to Filipino and back', async ({ page }) => {
    await page.goto('/story');
    await page.getByRole('link', { name: 'FIL', exact: true }).first().click();
    await expect(page.locator('h1')).toHaveText('Ang Kanyang Kuwento');
    await expect(page.locator('html')).toHaveAttribute('lang', 'fil');
    await page.getByRole('link', { name: 'EN', exact: true }).first().click();
    await expect(page.locator('h1')).toHaveText('His Story');
});

test('visitor can leave a prayer and it waits for approval', async ({ page }) => {
    const errors = watchConsole(page);
    await page.goto('/prayer-wall');
    await page.getByLabel('Your country').fill('Kenya');
    await page.getByLabel('Your prayer or message').fill('E2E prayer: stay strong, David.');
    await page.getByRole('button', { name: 'Send my prayer' }).click();
    await expect(page.getByText('Your message will appear after the family has seen it.')).toBeVisible();
    await page.reload();
    await expect(page.getByText('E2E prayer: stay strong, David.')).toHaveCount(0);
    expect(errors).toEqual([]);
});

test('contact form shows validation errors, then sends', async ({ page }) => {
    const errors = watchConsole(page);
    await page.goto('/contact');
    await page.getByRole('button', { name: 'Send message' }).click();
    await expect(page.getByText('The your name field is required.').or(page.locator('.field-error').first())).toBeVisible();

    await page.getByLabel('Your name').fill('E2E Tester');
    await page.getByLabel('Your email').fill('tester@example.com');
    await page.getByLabel('Your message').fill('I know a doctor who may be able to help David.');
    await page.getByRole('button', { name: 'Send message' }).click();
    await expect(page.getByText('Your message has been sent to the family.')).toBeVisible();
    expect(errors).toEqual([]);
});

test('mobile menu opens and closes', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'phone', 'Phone only');
    await page.goto('/');
    await page.getByRole('button', { name: 'Menu' }).click();
    const menu = page.locator('#mobile-menu');
    await expect(menu).toBeVisible();
    await menu.getByRole('link', { name: 'Transparency' }).click();
    await expect(page.locator('h1')).toHaveText('Transparency');
});

test('admin can sign in, approve a prayer and update the goal', async ({ page }, testInfo) => {
    const errors = watchConsole(page);
    const prayer = `E2E prayer from the ${testInfo.project.name} test.`;

    // A visitor leaves a prayer first, so each run has its own message to approve.
    await page.goto('/prayer-wall');
    await page.getByLabel('Your country').fill('Peru');
    await page.getByLabel('Your prayer or message').fill(prayer);
    await page.getByRole('button', { name: 'Send my prayer' }).click();
    await expect(page.getByText('Your message will appear after the family has seen it.')).toBeVisible();

    await page.goto('/admin');
    await expect(page).toHaveURL(/admin\/login/);
    await page.getByLabel('Email').fill('e2e@example.com');
    await page.getByLabel('Password').fill('e2e-password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await expect(page.getByRole('heading', { name: /Hello/ })).toBeVisible();
    await page.screenshot({ path: `e2e/screenshots/${testInfo.project.name}-admin-dashboard.png`, fullPage: true });

    await page.getByRole('link', { name: /Prayer messages/ }).click();
    const pending = page.locator('li', { hasText: prayer });
    await pending.getByRole('button', { name: 'Approve' }).click();
    await expect(page.getByRole('status').filter({ hasText: 'Approved' })).toBeVisible();
    await page.goto('/prayer-wall');
    await expect(page.getByText(prayer)).toBeVisible();

    // Same amount as the sample data (typed with a comma) so other tests are unaffected.
    await page.goto('/admin/fundraising');
    await page.getByLabel('Amount raised so far').fill('137,500');
    await page.getByRole('button', { name: 'Save' }).click();
    await expect(page.getByRole('status').filter({ hasText: 'Saved' })).toBeVisible();
    await page.screenshot({ path: `e2e/screenshots/${testInfo.project.name}-admin-fundraising.png`, fullPage: true });

    for (const screen of ['updates', 'transparency', 'donations', 'needs', 'shop', 'messages', 'story']) {
        const response = await page.goto(`/admin/${screen}`);
        expect(response.status()).toBe(200);
        await expect(page.locator('h1')).toBeVisible();
    }
    await page.screenshot({ path: `e2e/screenshots/${testInfo.project.name}-admin-story.png`, fullPage: true });

    expect(errors).toEqual([]);
});
