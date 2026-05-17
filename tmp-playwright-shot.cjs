const { chromium } = require('playwright');
(async() => {
  const browser = await chromium.launch({headless:true, executablePath:'C:/Program Files/Google/Chrome/Application/chrome.exe'});
  const page = await browser.newPage({ viewport: { width: 1440, height: 1400 } });
  await page.goto('http://127.0.0.1:8001', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'C:/nwp0203/docs/screenshots/rg-visual-check-2026-04-30-home-full.png', fullPage: true });
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight * 0.55));
  await page.waitForTimeout(700);
  await page.screenshot({ path: 'C:/nwp0203/docs/screenshots/rg-visual-check-2026-04-30-home-mid.png' });
  await browser.close();
})();
