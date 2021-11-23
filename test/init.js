const { chromium } = require('@playwright/test');

module.exports = async config => {
  const browser = await chromium.launch({headless: true});
  const page = await browser.newPage();
  console.log('Loggin in...');
  await page.goto('http://bryan-pc.internal:8081/moodle/official/login/index.php');


  await page.fill('[placeholder="Username"]', 'bryan_admin');
  await page.fill('[placeholder="Password"]', 'Sakai.12');
  

  await Promise.all([
      page.click('text=Log in'),
      page.waitForNavigation()
  ])

  await page.context().storageState({ path: 'storageState.json' });
  console.log('Session saved!');
  await browser.close();
};