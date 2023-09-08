const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'dist'),
	},
	entry: {
		'autoshare-for-twitter': './src/js/index.js',
		'api-fetch': './src/js/externals/api-fetch.js'
	},
	externals: {
		'admin-autoshare-for-twitter': 'adminAutoshareForTwitter',
		'@wordpress/api-fetch': 'wp.apiFetch',
		'@wordpress/components': 'wp.components',
		'@wordpress/compose': 'wp.compose',
		'@wordpress/data': 'wp.data',
		'@wordpress/edit-post': 'wp.editPost',
		'@wordpress/element': 'wp.element',
		'@wordpress/i18n': 'wp.i18n',
		'@wordpress/plugins': 'wp.plugins',
		lodash: 'lodash',
		react: 'React',
	},
};
