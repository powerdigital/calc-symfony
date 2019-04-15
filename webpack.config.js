var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './public/assets/js/app.jsx')
    .enableReactPreset()
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();
