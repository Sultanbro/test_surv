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
		'no-prototype-builtins': 'warn',
		'no-console': ['error', {allow: ['warn', 'error']}],
		'no-duplicate-imports': 'error',
		'no-promise-executor-return': 'error',
		'no-self-compare': 'error',
		'no-template-curly-in-string': 'error',
		'no-unmodified-loop-condition': 'error',
		'no-use-before-define': 'error',
		'require-atomic-updates': 'error',
		'block-scoped-var': 'error',
		'camelcase': 'error',

		'vue/no-v-for-template-key': 'error',
		'vue/no-v-for-template-key-on-child': 'off',
		'vue/no-deprecated-scope-attribute': 'error',
		'vue/no-deprecated-slot-attribute': 'error',
		'vue/no-deprecated-slot-scope-attribute': 'error',
		'vue/valid-v-is': 'error',
		'vue/valid-v-memo': 'error',
		'vue/this-in-template': ['error', 'never'],
		'vue/order-in-components': 'error',
		'vue/require-prop-types': 'error',
		'vue/require-default-prop': 'error',
		'vue/prop-name-casing': 'error',
		'vue/no-v-html': 'error',
		'vue/no-template-shadow': 'error',
		'vue/no-lone-template': 'error',
		'vue/attributes-order': 'error',
		'vue/html-indent': ['error', 'tab', {
			'attribute': 1,
			'baseIndent': 1,
			'closeBracket': 0,
			// 'alignAttributesVertically': true,
			'ignores': []
		}],
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
