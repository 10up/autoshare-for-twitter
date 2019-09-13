const path = require('path');

module.exports = {
	entry: {
		autotweet: './src/js/index.js',
		'api-fetch': './src/js/externals/api-fetch'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules)/,
				use: {
					loader: 'babel-loader',
				},
			},
		],
	},
	output: {
		filename: '[name].js',
		path: path.resolve('./dist'),
	},
	externals: {
		'admin-autotweet': 'adminAutotweet',
		'@wordpress/api-fetch': 'wp.apiFetch',
		'@wordpress/components': 'wp.components',
		'@wordpress/compose': 'wp.compose',
		'@wordpress/data': 'wp.data',
		'@wordpress/edit-post': 'wp.editPost',
		'@wordpress/element': 'wp.element',
		'@wordpress/i18n': 'wp.i18n',
		'@wordpress/plugins': 'wp.plugins',
		'lodash': 'lodash',
	}
};
