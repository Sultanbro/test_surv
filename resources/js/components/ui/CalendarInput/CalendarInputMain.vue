<template>
	<div class="CalendarInputMain">
		<ol class="CalendarInputMain-header">
			<li
				v-for="weekday in weekdays"
				:key="weekday"
				class="CalendarInputMain-weekday"
			>
				{{ weekday }}
			</li>
		</ol>
		<ol class="CalendarInputMain-row">
			<li
				v-for="empty in emptyDays"
				:key="'e' + empty"
				class="CalendarInputMain-empty"
			/>
			<li
				v-for="day in daysInMonth"
				:key="day"
				class="CalendarInputMain-day"
				:class="{
					'CalendarInputMain-day_today': datesInMonth[day] === today,
					'CalendarInputMain-day_selected': range ? false : tsInMonth[day] === tsValue[0],
					'CalendarInputMain-day_start': range ? tsInMonth[day] === minDate : false,
					'CalendarInputMain-day_end': range ? tsInMonth[day] === maxDate : false,
					'CalendarInputMain-day_inrange': range && tsValue.length > 1 ? minDate < tsInMonth[day] && tsInMonth[day] < maxDate : false,
				}"
				@click="onDateClick(day)"
			>
				<span class="CalendarInputMain-inner">
					{{ day }}
				</span>
			</li>
		</ol>
	</div>
</template>

<script>
export default {
	name: 'CalendarInputMain',
	components: {},
	inject: [
		'getValue',
		'getTSValue',
		'getFormat',
		'getMonth',
		'getYear',
		'getRange',
		'getDaysInMonth',
		'setValue',
		'setMonth',
	],
	data(){
		return {
			weekdays: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
			today: this.$moment(Date.now()).format('DD.MM.YYYY'),
		}
	},
	computed: {
		value(){
			return this.getValue()
		},
		tsValue(){
			return this.getTSValue()
		},
		format(){
			return this.getFormat()
		},
		month(){
			return this.getMonth()
		},
		year(){
			return this.getYear()
		},
		range(){
			return this.getRange()
		},
		monthString(){
			return ('' + (this.month + 1)).padStart(2, '0')
		},
		fullMonthString(){
			return `${this.monthString}.${this.year}`
		},
		emptyDays(){
			return this.$moment(`01.${this.fullMonthString}`, 'DD.MM.YYYY').isoWeekday() - 1
		},
		daysInMonth(){
			return this.getDaysInMonth()
		},
		datesInMonth(){
			return new Array(this.daysInMonth + 1).fill(0).map((wtf, index) => {
				return ('' + index).padStart(2, '0') + '.' + this.fullMonthString
			})
		},
		tsInMonth(){
			return this.datesInMonth.map(el => this.$moment(el, this.format).valueOf())
		},
		minDate(){
			if(!this.range) return 0
			return Math.min(...this.tsValue)
		},
		maxDate(){
			if(!this.range) return 0
			return Math.max(...this.tsValue)
		}
	},
	methods: {
		onDateClick(day){
			const moment = this.$moment([this.year, this.month, day])
			const ts = moment.valueOf()
			this.setValue(ts)
		},
	}
}
</script>

<style lang="scss">
.CalendarInputMain{
	&-header,
	&-row{
		display: flex;
		align-items: center;
		min-width: 23.8rem;
		padding: 0;
		margin: 0;
	}
	&-header{
		flex-flow: row nowrap;
		margin-bottom: 1rem;
		border-radius: 0.5rem;
		background: rgba(228, 236, 247, 0.5);
	}
	&-row{
		flex-flow: row wrap;
		padding-bottom: 1rem;
	}
	&-weekday,
	&-empty,
	&-day{
		display: flex;
		flex: 0 0 14.2857142857%;
		align-items: center;
		justify-content: center;
		width: 3.2rem;
		height: 3.2rem;
		list-style: none;
		font-size: 11px;
		line-height: 20px;
		text-align: center;
		user-select: none;
	}
	&-empty,
	&-day{
		padding: 0.1rem;
		margin-bottom: 0.5rem;
	}
	&-weekday{
		color: #8DA0C1;
	}
	&-day{
		position: relative;
		font-weight: 600;
		color: #3361FF;
		cursor: pointer;
		&:hover{
			.CalendarInputMain-inner{
				background-color: #F1F5FB;
			}
		}
		&_today{
			color: #fff;
			.CalendarInputMain-inner{
				background-color: #8BABD8;
			}
			&:hover{
				.CalendarInputMain-inner{
					background-color: lighten(#8BABD8, 5);
				}
			}
		}
		&_selected{
			color: #fff;
			.CalendarInputMain-inner{
				background-color: #3361FF;
			}
			&:hover{
				.CalendarInputMain-inner{
					background-color: lighten(#3361FF, 5);
				}
			}
		}
		&_start{
			border-radius: 3.2rem 0 0 3.2rem;
			color: #fff;
			background-color: rgba(226, 232, 255, 0.25);
			.CalendarInputMain-inner{
				background-color: #3361FF;
			}
			&:hover{
				.CalendarInputMain-inner{
					background-color: lighten(#3361FF, 5);
				}
			}
		}
		&_end{
			border-radius: 0 3.2rem 3.2rem 0;
			color: #fff;
			background-color: rgba(226, 232, 255, 0.25);
			.CalendarInputMain-inner{
				background-color: #3361FF;
			}
			&:hover{
				.CalendarInputMain-inner{
					background-color: lighten(#3361FF, 5);
				}
			}
		}
		&_inrange{
			background-color: rgba(226, 232, 255, 0.25);
		}
	}
	&-inner{
		display: flex;
		align-items: center;
		justify-content: center;
		width: 3.2rem;
		height: 3.2rem;
		border-radius: 3.2rem;
	}
}
</style>
