// eslint-disable-next-line no-undef
module.exports = {
	root: true,
	extends: [
		'plugin:vue/recommended',
		'eslint:recommended'
	],
	parserOptions: {
		ecmaVersion: 'latest',
	},
	rules: {
		'indent': ['error', 'tab'],
		'quotes': ['error', 'single'],
		'vue/no-v-for-template-key': 'error',
		'vue/no-v-for-template-key-on-child': 'off',
		// 'vue/no-side-effects-in-computed-properties': 'warn',
		'vue/no-child-content': 'warn',
		'no-prototype-builtins': 'warn',
		'vue/html-indent': ['error', 'tab', {
			'attribute': 1,
			'baseIndent': 1,
			'closeBracket': 0,
			// 'alignAttributesVertically': true,
			'ignores': []
		}],
		'no-console': ['error', {allow: ['warn', 'error']}]
	},
	ignorePatterns: [
		'resources/js/plugins/datepicker/*',
		'resources/js/plugins/playerjs/*',
		'resources/js/profile.js',
		'resources/js/videos.js',
		'resources/js/header.js',
		'resources/js/helper.js',
		'resources/js/admin',
	],
	globals: {
		require: 'readonly',
		jQuery: 'readonly'
	}
}
