const path = require('path');

module.exports = {
	entry: {
		autotweet: './src/js/index.js',
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
};
