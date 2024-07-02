import axios from 'axios'

export async function fetchWorkChartList(){
	const { data } = await axios.get('/work-chart')
	return data
}
