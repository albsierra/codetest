const {
    MOODLE_SECTION,
    MOODLE_PAGE,
    STORAGE_FILE
} = require('./config.js')


const getCtUrl = async (page, codetestInstance = 'CodeTest - ') => {

    await page.goto(MOODLE_PAGE)
    const turnEditOn = await page.locator('text=Turn editing on')
    if(await turnEditOn.count()){
        await turnEditOn.click();
    }

    let mainPageLink = null

    
    await page.click(`${MOODLE_SECTION} >> text=${codetestInstance}`);
    await page.waitForResponse(
        resp => {
            const url = resp.url();
            let isNeedle = url.includes('/?PHPSESSID=') && resp.request().method() === 'GET'
            if(isNeedle){
                mainPageLink = url
            }
            return isNeedle;
        }
        , { timeout: 5 * 1000 }
    )
    
    return mainPageLink;
}

module.exports = {
    getCtUrl
}