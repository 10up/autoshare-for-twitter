const path = require( 'path' );

module.exports = {
	entry: {
		autoshare: './src/js/index.js',
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
		path: path.resolve( './dist' ),
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
	},
};
