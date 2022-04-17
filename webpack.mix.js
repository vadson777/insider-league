const mix = require('laravel-mix');

mix.disableNotifications()
	.options({
		processCssUrls: false, // very slow if true
		terser: {extractComments: false},
	})
	.js('resources/js/app.js', 'public/js').vue()
	.sass('resources/sass/app.scss', 'public/css')
	.version()
	.sourceMaps(false);

