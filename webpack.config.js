const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'dist' ),
	},
	entry: {
		'autoshare-for-twitter': './src/js/index.js',
	},
};
