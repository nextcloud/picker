module.exports = {
	globals: {
		appVersion: true
	},
	parserOptions: {
		requireConfigFile: false
	},
	extends: [
		'@nextcloud'
	],
	rules: {
		'jsdoc/require-jsdoc': 'off',
		'jsdoc/tag-lines': 'off',
		'vue/first-attribute-linebreak': 'off',
		'no-console': 'off',
		"import/no-unresolved": ["error", { "ignore": ["\\?raw"] }],
		'import/extensions': 'off'
	}
}
