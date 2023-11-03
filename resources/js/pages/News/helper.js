export function getEmptyQuestion(){
	return {
		question: '',
		variants: [
			'',
			'',
		],
		config: {
			manyanswers: false,
			changeanswers: false,
			public: true,
		},
	}
}
