<template>
	<div class="TexesEditForm">
		<b-col
			cols="12"
			class="mb-3"
		>
			<InputText
				v-model="value.name"
				small
				primary
				placeholder="Название группы налогов"
			/>
		</b-col>
		<b-col cols="12">
			<Draggable
				:key="1"
				class="TexesEditForm-taxes dragArea"
				tag="ul"
				:handle="'.TexesEditForm-mover'"
				:group="{ name: 'g1' }"
			>
				<li
					v-for="item, index in value.items"
					:key="index"
					class="TexesEditForm-tax mb-3"
				>
					<div class="TexesEditForm-mover">
						<i class="fa fa-bars" />
					</div>
					<InputText
						v-model="item.name"
						small
						primary
						:placeholder="item.is_deduction ? 'Название вычета' : 'Название налога'"
					/>
					<InputText
						v-model="item.value"
						small
						primary
						placeholder="Сумма/процент"
						class="relative"
					>
						<template
							v-if="item.is_deduction"
							#after
						>
							<img
								v-b-popover.hover.html="'Вычет учитывается при расчете налога, но в саму сумму налога не попадает'"
								src="/images/dist/profit-info.svg"
								class="img-info TexesEditForm-infoInput"
								alt="info icon"
								width="20"
								tabindex="-1"
							>
						</template>
					</InputText>
					<InputPercentValue
						v-model="item.is_percent"
					/>
					<JobtronSwitch
						v-model="item.end_subtraction"
					/>
					<img
						v-b-popover.click.blur.html="'Данный процент или сумма будет вычитаться от остаточной суммы которая к начислению после вычетов указанных выше'"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
						width="20"
						tabindex="-1"
					>
					<div class="mx-a" />
					<JobtronButton
						small
						error
						@click="removeItem(index)"
					>
						<i class="fa fa-times" />
					</JobtronButton>
				</li>
			</Draggable>
			<JobtronButton
				small
				@click="addItem(0)"
			>
				+ Добавить налог
			</JobtronButton>
			<JobtronButton
				small
				@click="addItem(1)"
			>
				+ Добавить вычет
				<span class="TexesEditForm-infoBtn">
					<img
						v-b-popover.hover.html="'Вычет учитывается при расчете налога, но в саму сумму налога не попадает'"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
						width="20"
						tabindex="-1"
					>
				</span>
			</JobtronButton>
			<hr class="my-4">
			<div class="text-right">
				<JobtronButton
					small
					@click="$emit('submit', value)"
				>
					Сохранить
				</JobtronButton>
				<JobtronButton
					v-if="value.id"
					error
					small
					@click="$emit('remove', value)"
				>
					Удалить
				</JobtronButton>
			</div>
		</b-col>
	</div>
</template>

<script>
import Draggable from 'vuedraggable'
import InputText from '@ui/InputText.vue'
import InputPercentValue from '@ui/InputPercentValue.vue'
import JobtronSwitch from '@ui/Switch.vue'
import JobtronButton from '@ui/Button.vue'

// const total // сомма дохода
// let result = total
// taxes.items.sort((a,b) => a.order - b.order).forEach(tax => {
// 	if(!tax.value) return
// 	if(!tax.is_percent) {
// 		result -= tax.value
// 		return
// 	}
// 	result -= tax.end_subtraction ? Math.round(result * tax.value / 100) : Math.round(total * tax.value / 100)
// })

export default {
	name: 'TexesEditForm',
	components: {
		Draggable,
		InputText,
		InputPercentValue,
		JobtronSwitch,
		JobtronButton,
	},
	props: {
		tax: {
			type: Object,
			default: null,
		}
	},
	data(){
		return {
			value: JSON.parse(JSON.stringify(this.tax))
		}
	},
	computed: {},
	watch: {
		tax(){
			this.value = JSON.parse(JSON.stringify(this.tax))
		}
	},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		addItem(isDeduction = 0){
			/* eslint-disable camelcase */
			this.value.items.push({
				name: '',
				value: 0,
				is_percent: 0,
				end_subtraction: 0,
				is_deduction: isDeduction,
				order: this.value.items.length,
			})
			/* eslint-enable camelcase */
		},
		removeItem(index){
			this.value.items.splice(index, 1)
		},
	},
}
</script>

<style lang="scss">
.TexesEditForm{
	&-tax{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;
		.InputText{
			width: 150px;
			&-input{
				&:first-child{
					padding-left: 0;
				}
			}
		}
	}
	&-infoBtn{
		display: inline-flex;
		align-items: center;
		height: 12px;
		.img-info{
			background-color: #fff;
			border-radius: 999rem;
		}
	}
	&-infoInput{
		position: absolute;
		right: 10px;
		top: 50%;
		transform: translateY(-50%);
	}
}
</style>
