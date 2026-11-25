/**
 * ESLint flat config. Extends the `@wordpress/scripts` default config.
 */

/**
 * WordPress dependencies
 */
import defaultConfig from '@wordpress/scripts/config/eslint.config.cjs';

export default [
	{
		ignores: [
			'assets/**',
			'dist/**',
			'node_modules/**',
			'vendor/**',
			'tests/**',
		],
	},
	...defaultConfig,
	{
		languageOptions: {
			globals: {
				adminAutoshareForTwitter: 'readonly',
				jQuery: 'readonly',
				wp: 'readonly',
				FormData: 'readonly',
				fetch: 'readonly',
				ajaxurl: 'readonly',
			},
		},
		settings: {
			'import/core-modules': [
				'@wordpress/api-fetch',
				'@wordpress/components',
				'@wordpress/compose',
				'@wordpress/data',
				'@wordpress/date',
				'@wordpress/edit-post',
				'@wordpress/element',
				'@wordpress/i18n',
				'@wordpress/plugins',
				'@wordpress/primitives',
			],
		},
	},
];
