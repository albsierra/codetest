const config = {
    reporter: [ ['html', { outputFolder: 'playwright-report' }] ],
    use: {
        trace: 'on',
        storageState: 'storageState.json',
        viewport: {
            width: 1890,
            height: 930
        }        
    },
    globalSetup: require.resolve('./init.js'),
};
  
module.exports = config;