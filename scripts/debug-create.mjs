import { chromium } from 'playwright';

const BASE = 'http://127.0.0.1:8000';

const browser = await chromium.launch({ channel: 'chrome' });
const context = await browser.newContext();
const page = await context.newPage();

// Login
await page.goto(`${BASE}/admin/login`);
await page.fill('input[name="email"]', 'salemamine@gmail.com');
await page.fill('input[name="password"]', 'password');
await page.click('button[type="submit"]');
await page.waitForLoadState('networkidle');
console.log('After login URL:', page.url());

// Go to create
await page.goto(`${BASE}/admin/realisations/create`);
await page.waitForLoadState('networkidle');
console.log('Create page URL:', page.url());
console.log('Create page title:', await page.title());

// Check for errors on create page
const errors = await page.locator('.alert.danger').count();
if (errors > 0) {
  console.log('Errors on create page:', await page.locator('.alert.danger').textContent());
}

// Fill form
await page.fill('input[name="title"]', 'Debug Test 12345');
await page.fill('input[name="type"]', 'Web');
await page.fill('input[name="short_description"]', 'Short debug description here');
await page.fill('textarea[name="description"]', 'Full description text');
await page.fill('input[name="project_url"]', 'https://example.com');

// Print form action
const formAction = await page.locator('form').getAttribute('action');
console.log('Form action:', formAction);

// Submit
console.log('Submitting...');
await page.click('form button[type="submit"]');
await page.waitForLoadState('networkidle');

console.log('After submit URL:', page.url());
const afterErrors = await page.locator('.alert.danger').count();
if (afterErrors > 0) {
  console.log('ERROR after submit:', await page.locator('.alert.danger').textContent());
}
const afterSuccess = await page.locator('.alert.success').count();
if (afterSuccess > 0) {
  console.log('SUCCESS after submit:', await page.locator('.alert.success').textContent());
}

const cards = await page.locator('.admin-card').count();
console.log('Cards on page:', cards);
const matchCard = await page.locator('.admin-card', { hasText: 'Debug Test 12345' }).count();
console.log('Matching card found:', matchCard);

await browser.close();
