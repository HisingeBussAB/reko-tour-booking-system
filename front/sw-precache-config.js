module.exports = {
  staticFileGlobs: [
    'build/static/css/**.css',
    'build/static/js/**.js',
    'build/*.html',
    'build/manifest.json',
    'build/asset-manifest.json',
    'build/robots.txt',
    'build/favicon.ico'
  ],
  staticFileGlobsIgnorePatterns: [/\.map$/, /asset-manifest\.json$/],
  swFilePath                   : './build/service-worker.js',
  stripPrefix                  : 'build/',
  runtimeCaching               : [
    {
      urlPattern: /api/,
      handler   : 'networkOnly'
    }, {
      urlPattern: /updatefirewall/,
      handler   : 'networkOnly'
    }
  ]
}
