const { chromium } = require('playwright');

const { expect } = require('@playwright/test');

(async () => {
  const browser = await chromium.launch({
    headless: false,
    slowMo: 400
  });
  const context = await browser.newContext({
    storageState: 'storageState.json',
    recordVideo: {
      dir: 'videos/',
    }
  });

  // Open new page
  const page = await context.newPage();

  page.viewportSize({
    width: 1080,
    height: 920
  })

  /*

  
  // Login
  await page.goto('http://bryan-pc.internal:8081/moodle/official/course/view.php?id=2#section-2');
  await page.click('[placeholder="Username"]');
  await page.fill('[placeholder="Username"]', 'bryan_admin');
  await page.press('[placeholder="Username"]', 'Tab');
  await page.fill('[placeholder="Password"]', 'Sakai.12');
  await page.click('text=Log in');
  
  // - SAVE STORAGE
  await page.context().storageState({ path: 'storageState.json' });
  
  // await browser.close();

  */

  /*

  // Create LTI activity
  await page.click('text=Turn editing on');
  await page.waitForLoadState('networkidle');
  await page.click('#section-5 >> text=Add an activity or resource')
  await page.click('[aria-label="External tool"] >> text=External tool');
  await page.click('input[name="name"]');
  await page.fill('input[name="name"]', `Testing - Codetest - ${new Date().toISOString()}`);
  await page.selectOption('select[name="typeid"]', '1');
  await page.click('text=Save and display');

  */

  const moodleSection = '#section-5';
  const codetesInstance = 'Testing - Codetest - 2021-11-18T22:55:25.838Z';

  await page.goto('http://bryan-pc.internal:8081/moodle/official/course/view.php?id=2');

  await page.click(`${moodleSection} >> text=${codetesInstance}`)

  await page.waitForLoadState('networkidle');

  /*

  // Inside LTI
  const ltiIframe = page.frame({
    url: /splash.php/
  });
  await ltiIframe.click('text=Get Started');
  await ltiIframe.waitForURL(/instructor-home.php/)

  */

  const ltiIframe = page.frame({
    url: /instructor-home.php/
  });

  await ltiIframe.waitForURL(/instructor-home.php/, {
    waitUntil: 'networkidle'
  })

  /*

  Por si quiero abrir codetest en ventana

  .navbar-brand -> href

  /*


   /*

  await ltiIframe.click('#toolTitleEditLink span');

  await ltiIframe.click('textarea:has-text("Code Test")');

  await ltiIframe.fill('textarea:has-text("Code Test")', 'Code Test - Testing');

  await ltiIframe.selectOption('select[name="mainType"]', { label: 'PROGRAMMING' });

  await ltiIframe.click('text=Title Text Code Test Save Title Text Cancel Title Text >> span');

  await expect(ltiIframe.locator('#flashmessages >> text=Title saved')).toHaveCount(1)

  */


  await ltiIframe.click('text=Create a exercise');

  await ltiIframe.waitForURL(/create-exercise.php/, {
    waitUntil: 'networkidle'
  })

  // await page.pause();

  await ltiIframe.click('[placeholder="Exercise title"]');
  await ltiIframe.type('[placeholder="Exercise title"]', `Pregunta de prueba - ${new Date().toISOString()}`);
  await ltiIframe.press('[placeholder="Exercise title"]', 'Tab');
  await ltiIframe.type('[placeholder="Keywords"]', 'keyword');
  await ltiIframe.press('[placeholder="Keywords"]', 'Tab');
  await ltiIframe.selectOption('select[name="exercise[difficulty]"]', { label: 'Medium'});
  await ltiIframe.type('textarea[name="exercise[exercise_input_test]"]', 'Input para el estudiante');
  await ltiIframe.press('textarea[name="exercise[exercise_input_test]"]', 'Tab');
  await ltiIframe.type('textarea[name="exercise[exercise_input_grade]"]', 'Input para el grade');
  await ltiIframe.press('textarea[name="exercise[exercise_input_grade]"]', 'Tab');
  await ltiIframe.selectOption('select[name="exercise[exercise_language]"]', { label: 'Java'});
  await ltiIframe.press('select[name="exercise[exercise_language]"]', 'Tab');
  await ltiIframe.type('textarea[name="exercise[exercise_solution]"]', 'La solución');
  await ltiIframe.press('textarea[name="exercise[exercise_solution]"]', 'Tab');
  await ltiIframe.type('textarea[name="exercise[exercise_must]"]', 'Debe contener');
  await ltiIframe.press('textarea[name="exercise[exercise_must]"]', 'Tab');
  await ltiIframe.type('textarea[name="exercise[exercise_musnt]"]', 'No debe contener');
  await ltiIframe.click('text=Save Exercise');

  await expect(ltiIframe.locator('#flashmessages >> text=Exercise Saved')).toHaveCount(1)
  
  await browser.close();
})();