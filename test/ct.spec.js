const { it, expect, test } = require('@playwright/test');
const fs = require('fs')
const { loginMoodle, getCtUrl } = require('./utils.js')
const {
    MOODLE_URL,
    MOODLE_COURSE,
    MOODLE_SECTION,
    MOODLE_PAGE,
    BIG_VIEWPORT,
    STORAGE_FILE
} = require('./config.js')

const storageFileExists = fs.existsSync(STORAGE_FILE);

// test.use({ viewport: BIG_VIEWPORT, storageState: storageFileExists ? STORAGE_FILE : undefined });

test('Open codetest in new window', async ({ page }) => {

    const codetestUrl = await getCtUrl(page);
    
    await page.goto(codetestUrl);

    await page.pause();

})
