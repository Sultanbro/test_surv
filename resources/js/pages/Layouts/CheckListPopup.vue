<template>
	<div class="popup__con">
		<div class="check__title popup__content-title">
			{{ data.title }}
		</div>
		<div class="check__items">
			<div
				v-if="!data.items"
				class="check__items-empty"
			>
				У вас нет чеклиста
			</div>
			<CheckListPopupItem
				v-for="(item, index) in data.items"
				:key="index"
				:item="item"
				:index="index + 1"
			/>
		</div>
		<a
			v-if="data.items"
			href="javascript:void(0)"
			class="check__button"
		>
			Выполнить
		</a>
	</div>
</template>

<script>
import CheckListPopupItem from './CheckListPopupItem'
export default {
	name: 'CheckListPopup',
	components: {
		CheckListPopupItem
	},
	props: {
		data: {
			type: Object,
			default: null
		}
	},
	data() {
		return {}
	},
	created(){},
	methods: {}
};
</script>

<style lang="scss">
.check__title{
    padding-bottom: 2rem;
    border-bottom: 1px solid #E6E6E6;

    font-size: 2.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.check__items-empty{
    font-size: 1.6rem;
}

.check__button{
    display: flex;
    justify-content: center;
    align-items: center;
    background: #AEBEE0;
    color: #fff;
    min-height: 5rem;
    width: 100%;
    max-width: 26.5rem;
    font-size: 1.4rem;
    font-weight: 600;
}
</style>
