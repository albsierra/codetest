const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('main', './assets/scripts/main.js')
    //.addEntry('page1', './assets/assets/page1.js')
    //.addEntry('page2', './assets/assets/page2.js')

    .addStyleEntry('codetest', './assets/styles/codetest.scss')
    .addStyleEntry('tailwind', './assets/styles/tailwind.scss')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()
    .disableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(false)
    // enables hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'entry';
        config.corejs = 3;
    })
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-transform-runtime');
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    .configureCssLoader((options) => {
        options.url = false
    })

    .copyFiles({
        from: './assets/images',
        pattern: /\.(png|jpg|jpeg|svg)$/,
        // to path is relative to the build directory
        to: 'images/[path][name].[ext]'
    })
    .copyFiles({
        from: './assets/scripts/codemirror',
        pattern: /.*/,
        // to path is relative to the build directory
        to: 'codemirror/[path][name].[ext]'
    })

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // enable PostCss
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
         // directory where the postcss.config.js file is stored
                path: './assets/postcss.config.js'
        };
    })

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    // .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer require api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/assets/admin.js')
;

module.exports = Encore.getWebpackConfig();