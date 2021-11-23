const { it, expect, test } = require('@playwright/test');
const fs = require('fs')
// const { loginMoodle } = require('./utils.js')
const {
    MOODLE_URL,
    MOODLE_COURSE,
    MOODLE_SECTION,
    BIG_VIEWPORT,
    MOODLE_PAGE,
    STORAGE_FILE
} = require('./config.js')

const storageFileExists = fs.existsSync(STORAGE_FILE);

// test.use({ viewport: BIG_VIEWPORT, storageState: storageFileExists ? STORAGE_FILE : undefined });


test('Delete all codetest instances in a section @clean-moodle', async ({ page }) => {

    await page.goto(MOODLE_PAGE)
    const turnEditOn = await page.locator('text=Turn editing on')
    if(await turnEditOn.count()){
        await turnEditOn.click();
    }

    const editButtons = await page.$$(`${MOODLE_SECTION} ul .dropdown-toggle >> text=Edit`);

    for (const el of editButtons) {
        await el.scrollIntoViewIfNeeded();
        await el.click();
        
        const dropDown = await el.evaluateHandle((node) => node.nextElementSibling)

        const deleteItem = await dropDown.$('text=Delete')

        await deleteItem.click();

        const modalBody = await page.waitForSelector('text=Are you sure that you want to delete the External tool')

        const modalActions = await modalBody.evaluateHandle((node) => node.nextElementSibling);
        const confirmButton = await modalActions.$("text=Yes")
        await confirmButton.click();

        modalActions.dispose();
        dropDown.dispose();
    }

})


test('Create codetest instance @init-moodle', async ({ page }) => {

    await page.goto(MOODLE_PAGE)
    const turnEditOn = await page.locator('text=Turn editing on')
    if(await turnEditOn.count()){
        await turnEditOn.click();
    }

    await page.click(`${MOODLE_SECTION} >> text=Add an activity or resource`)
    await page.click('[aria-label="External tool"] >> text=External tool');
    await page.click('input[name="name"]');
    await page.fill('input[name="name"]', `CodeTest - ${new Date().toISOString()}`);
    await page.selectOption('select[name="typeid"]', { label: 'Code test local' });
    await page.click('text=Save and return to course');

})

