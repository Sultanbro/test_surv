// eslint-disable-next-line no-undef
module.exports = {
	root: true,
	extends: [
		'plugin:vue/essential',
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
		'vue/no-mutating-props': 'warn',
		'vue/no-side-effects-in-computed-properties': 'warn'
	},
	ignorePatterns: [
		'resouces/js/admin/*',
		'resources/js/plugins/datepicker/*',
		'resources/js/plugins/playerjs/*',
		'resources/js/profile.js',
		'resources/js/videos.js',
	],
	globals: {
		require: 'readonly',
		jQuery: 'readonly'
	}
}