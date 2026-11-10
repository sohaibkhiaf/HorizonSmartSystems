import { chromium } from 'playwright';

const BASE = 'http://127.0.0.1:8000';
const ADMIN_EMAIL = 'salemamine@gmail.com';
const ADMIN_PASS = 'password';

const results = [];
const pageErrors = [];
let httpErrors = [];

function log(name, ok, info = '') {
  results.push({ name, ok, info });
  console.log(`${ok ? 'PASS' : 'FAIL'}  ${name}${info ? '  -> ' + info : ''}`);
}

const browser = await chromium.launch({ channel: 'chrome' });
const context = await browser.newContext();
const page = await context.newPage();

page.on('pageerror', (e) => pageErrors.push(`[JS] ${page.url()} :: ${e.message}`));
page.on('console', (m) => { if (m.type() === 'error') pageErrors.push(`[console] ${page.url()} :: ${m.text()}`); });
page.on('response', (r) => { if (r.status() >= 400) httpErrors.push(`${r.status()} ${r.url()}`); });

async function goto(path) {
  const resp = await page.goto(BASE + path, { waitUntil: 'networkidle' });
  return resp ? resp.status() : 0;
}

try {
  // ---------- PUBLIC PAGES ----------
  for (const p of ['/', '/about', '/services', '/achievements', '/contact', '/services/webdev']) {
    const s = await goto(p);
    log(`public GET ${p}`, s === 200, `HTTP ${s}`);
  }

  // ---------- LANGUAGE SWITCHER ----------
  await goto('/');
  for (const lang of ['ar', 'en', 'fr']) {
    const s = (await page.goto(`${BASE}/language/${lang}`, { waitUntil: 'networkidle' })).status();
    const htmlLang = await page.getAttribute('html', 'lang');
    log(`language switch ${lang}`, s === 200 && htmlLang === lang, `html lang=${htmlLang}`);
  }

  // ---------- CONTACT FORM ----------
  await goto('/contact');
  const stamp = Date.now();
  await page.fill('input[name="name"]', 'Playwright Tester');
  await page.fill('input[name="email"]', `pw${stamp}@test.com`);
  await page.fill('input[name="phone"]', '0561000000');
  await page.fill('input[name="subject"]', `PW Subject ${stamp}`);
  await page.fill('textarea[name="message"]', `Automated test message ${stamp}`);
  await page.click('button.btn-submit');
  await page.waitForLoadState('networkidle');
  const contactOk = await page.locator('.form-alert.success').count();
  log('contact form submit', contactOk > 0, contactOk > 0 ? 'success banner shown' : 'no success banner');

  // ---------- ADMIN LOGIN (wrong password) ----------
  await goto('/admin/login');
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', 'wrongpass');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  log('admin login rejects wrong password', page.url().includes('/admin/login') && (await page.locator('.alert.danger').count()) > 0);

  // ---------- ADMIN LOGIN (correct) ----------
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASS);
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  log('admin login success -> dashboard', /\/admin\/?$/.test(page.url()) || page.url().endsWith('/admin'), `url=${page.url()}`);

  // ---------- DASHBOARD ----------
  await goto('/admin');
  log('dashboard renders stats', (await page.locator('.stat-card').count()) >= 4);

  // ---------- CREATE REALISATION ----------
  await goto('/admin/realisations/create');
  const title = `PW Realisation ${stamp}`;
  await page.fill('input[name="title"]', title);
  await page.fill('input[name="type"]', 'Web');
  await page.fill('input[name="short_description"]', 'Created by Playwright automated test.');
  await page.fill('textarea[name="description"]', 'Full description for the test realisation.');
  await page.fill('input[name="project_url"]', 'https://example.com');
  await page.locator('.content-card button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  const created = await page.locator('.cards-grid', { hasText: title }).count();
  log('create realisation', page.url().includes('/admin/realisations') && created > 0, created > 0 ? 'appears in list' : 'not found in list');

  // ---------- EDIT REALISATION ----------
  const editLink = page.locator('.admin-card', { hasText: title }).locator('a.edit');
  await editLink.first().click();
  await page.waitForLoadState('networkidle');
  const onEdit = page.url().includes('/edit');
  const newTitle = title + ' EDITED';
  await page.fill('input[name="title"]', newTitle);
  await page.locator('.content-card button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  const edited = await page.locator('.admin-card', { hasText: newTitle }).count();
  log('edit realisation', onEdit && edited > 0, edited > 0 ? 'title updated' : 'edit failed');

  // ---------- PUBLIC LINKING: realisation shows on /achievements ----------
  await goto('/achievements');
  const onPublic = await page.locator('.acheivement-card', { hasText: newTitle }).count();
  log('admin->public linking (achievement visible)', onPublic > 0, onPublic > 0 ? 'visible on public page' : 'NOT visible');

  // ---------- DELETE REALISATION ----------
  await goto('/admin/realisations');
  page.once('dialog', (d) => d.accept());
  await page.locator('.admin-card', { hasText: newTitle }).locator('button.delete').first().click();
  await page.waitForLoadState('networkidle');
  const stillThere = await page.locator('.admin-card', { hasText: newTitle }).count();
  log('delete realisation', stillThere === 0, stillThere === 0 ? 'removed' : 'still present');

  // ---------- MESSAGES: waiting list shows the contact message ----------
  await goto('/admin/messages/waiting');
  const msgCount = await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).count();
  log('message appears in waiting list', msgCount > 0);

  // ---------- MESSAGES: mark seen ----------
  const msgCard = page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).first();
  await msgCard.locator('button.success-action').click();
  await page.waitForLoadState('networkidle');
  await goto('/admin/messages/seen');
  const inSeen = await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).count();
  log('mark message seen', inSeen > 0);

  // ---------- MESSAGES: edit (update status + note) ----------
  await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).first().locator('a.edit').click();
  await page.waitForLoadState('networkidle');
  await page.selectOption('select[name="seen"]', '0');
  await page.fill('textarea[name="admin_note"]', 'Reviewed by automated test.');
  await page.locator('.content-card button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  const backToWaiting = await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).count();
  log('edit message (back to waiting)', page.url().includes('/messages/waiting') && backToWaiting > 0);

  // ---------- MESSAGES: delete ----------
  page.once('dialog', (d) => d.accept());
  await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).first().locator('button.delete').click();
  await page.waitForLoadState('networkidle');
  const msgGone = await page.locator('.message-card', { hasText: `PW Subject ${stamp}` }).count();
  log('delete message', msgGone === 0);

  // ---------- LOGOUT ----------
  await goto('/admin');
  await page.click('.logout-form button');
  await page.waitForLoadState('networkidle');
  log('logout', page.url().includes('/admin/login'), `url=${page.url()}`);

  // ---------- ADMIN ROUTE PROTECTION ----------
  const s = await goto('/admin');
  log('admin route protected after logout', page.url().includes('/admin/login'), `landed=${page.url()}`);

} catch (e) {
  log('SCRIPT EXCEPTION', false, e.message + '\n' + e.stack);
} finally {
  console.log('\n===== JS / CONSOLE ERRORS =====');
  console.log(pageErrors.length ? [...new Set(pageErrors)].join('\n') : 'none');
  console.log('\n===== HTTP >=400 =====');
  console.log(httpErrors.length ? [...new Set(httpErrors)].join('\n') : 'none');
  const failed = results.filter((r) => !r.ok);
  console.log(`\n===== SUMMARY: ${results.length - failed.length}/${results.length} passed =====`);
  await browser.close();
  process.exit(failed.length ? 1 : 0);
}
