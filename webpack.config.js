const path = require( 'path' );

module.exports = {
	entry: {
		'autoshare-for-twitter': './src/js/index.js',
		'api-fetch': './src/js/externals/api-fetch',
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
};
