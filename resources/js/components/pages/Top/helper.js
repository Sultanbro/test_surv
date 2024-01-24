import salaryCellType from '@/composables/salaryCellType'

export function calcGroupFOT(data, lastDay = 31, daysInMonth = 31){
	/* eslint-disable camelcase */
	let items = [];
	let daySalariesSum = [];

	data.users.forEach(item => {
		var daySalaries = [];
		var daySalariesOnly = [];
		var personalTotal = 0;
		var personalFinal = 0;
		var personalAvanses = 0;
		var personalFines = 0;
		var personalBonuses = 0;
		var personalTaxes = 0;

		item.dayType = []
		item.salaries.forEach(tt => {
			let salary = 0;
			let total = 0;
			if(+tt.day > lastDay) return

			if(item.earnings[tt.day] !== null) {
				salary = Number(item.earnings[tt.day]);
				total = salary;
			}

			// salary earned to total
			if(Number(salary) != 0) personalTotal += parseInt(salary);

			if(tt.paid !== null) {
				personalAvanses += parseInt(tt.paid, 0);
			}

			if(item.bonuses[tt.day] !== null) {
				personalBonuses += Number(item.bonuses[tt.day]);
				total += Number(item.bonuses[tt.day]);
			}

			if(item.awards[tt.day] !== null) {
				personalBonuses += Number(item.awards[tt.day]);
				total += Number(item.awards[tt.day]);
			}

			if(item.test_bonus[tt.day] !== null) {
				personalBonuses += Number(item.test_bonus[tt.day]);
				total += Number(item.test_bonus[tt.day]);
			}

			if(item.fine[tt.day] !== null) {
				let fine_for_day = 0;
				item.fine[tt.day].forEach(el => {
					Object.values(el).forEach(fine_sum => fine_for_day += Number(fine_sum));
				})

				total -= Number(fine_for_day);
			}

			daySalaries[tt.day] = Number(total) != 0
				? Number(total).toFixed(0)
				: '';

			daySalariesOnly[tt.day] = Number(salary) != 0
				? Number(salary).toFixed(0)
				: '';

			const isFine = item.fine[tt.day] && item.fine[tt.day].length ? salaryCellType.FINE : 0
			const isTraining = item.trainings[tt.day] ? salaryCellType.TRAINING : 0
			const isBonus = item.bonuses[tt.day] || item.awards[tt.day] || item.test_bonus[tt.day] ? salaryCellType.BONUS : 0
			const isAvans = item.avanses[tt.day] ? salaryCellType.AVANS : 0
			item.dayType[tt.day] = isFine | isTraining | isBonus | isAvans
		});

		item.taxes.forEach(t => {
			personalTaxes = personalTaxes + t.amount;
		});

		let personalKpi =  Number(item.kpi);
		if(item.edited_kpi) {
			personalKpi = item.edited_kpi.amount
		}

		if(item.edited_bonus) {
			personalBonuses = item.edited_bonus.amount
		}

		personalFines = Number(item.fines_total);
		personalFinal = personalTotal - personalAvanses + personalBonuses - personalFines + personalKpi - personalTaxes;

		if(item.edited_salary) {
			personalFinal = item.edited_salary.amount
		}

		daySalaries.bonus = Number(personalBonuses).toFixed(0);
		daySalaries.avans = Number(personalAvanses).toFixed(0);
		daySalaries.fines = Number(personalFines).toFixed(0);
		daySalaries.total = Number(personalTotal).toFixed(0);
		daySalaries.taxes = Number(personalTaxes).toFixed(0);
		daySalaries.final = Number(personalFinal).toFixed(0);

		daySalaries.forEach((amount, day) => {
			if(isNaN(amount) || isNaN(Number(amount))) {
				amount = 0;
			}

			if (typeof daySalariesSum[day] === 'undefined') {
				daySalariesSum[day] = 0;
			}

			daySalariesSum[day] = parseInt(daySalariesSum[day]) + Number(amount);
		});

		let obj = {
			kpi: item.kpi,
			fine: item.fine,
			user_id: item.id,
			hours: item.hours,
			awards: item.awards,
			name: `${item.last_name} ${item.name}`,
			avanses: item.avanses,
			bonuses: item.bonuses,
			user_type: item.user_type,
			trainings: item.trainings,
			history: item.track_history,
			edited_kpi: item.edited_kpi,
			test_bonus: item.test_bonus,
			hourly_pays: item.hourly_pays,
			edited_bonus: item.edited_bonus,
			edited_salary: item.edited_salary,
			dayType: item.dayType,
			salaries: daySalariesOnly,
			groupUsers: item.group_users,
			...daySalaries,
		};
		items.push(obj);
	})

	const final = items.reduce((result, item) => result + (Number(item.final)|| 0), 0)
	const kpi = items.reduce((result, item) => result + (Number(item.kpi)|| 0), 0)
	const bonus = items.reduce((result, item) => result + (Number(item.bonus)|| 0), 0)
	const avans = items.reduce((result, item) => result + (Number(item.avans)|| 0), 0)
	const fines = items.reduce((result, item) => result + (Number(item.fines)|| 0), 0)
	const taxes = items.reduce((result, item) => result + (Number(item.taxes)|| 0), 0)
	const total = items.reduce((result, item) => result + (Number(item.total)|| 0), 0)

	const totals = {
		name: data.currentGroup.name,
		final,
		kpi,
		bonus,
		avans,
		fines,
		taxes,
		total,
	}

	for(let i = 1; i < 32; ++i){
		totals[i] = items.reduce((result, item) => result + (Number(item[i]) || 0), 0)
	}

	totals.predict = (daysInMonth - lastDay) * (totals[lastDay] || 0)

	return totals
	/* eslint-enable camelcase */
}
