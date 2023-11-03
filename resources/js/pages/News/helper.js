export function getEmptyQuestion(){
	return {
		multiAnswer: false,
		question: '',
		order: 0,
		answers: [
			{
				answer: '',
				order: 0,
			},
			{
				answer: '',
				order: 1,
			},
		],
	}
}
