<template>
	<div class="sidebar-course">
		<button
			v-for="item in links"
			:key="item.id"
			class="sidebar-course-content"
			:class="{ active: isActive(item.id) }"
			@click="handleClickActiveMain(item.id)"
		>
			<component :is="item.icon" />
			<div
				class="sidebar-course-title"
				:class="{ active: isActive(item.id) }"
			>
				{{ item.title }}
			</div>
			<div class="sidebar-course-hint">
				<HintIcon />
			</div>
		</button>
	</div>
</template>

<script>
import AppearanceIcon from '../../../assets/icons/AppearanceIcon.vue';
import MaterialsIcon from '../../../assets/icons/MaterialsIcon.vue';
import ProfileIcon from '../../../assets/icons/ProfileIcon.vue';
import SettingIcon from '../../../assets/icons/SettingIcon.vue';
import CommentIcon from '../../../assets/icons/CommentIcon.vue';
import JobIcon from '../../../assets/icons/JobIcon.vue';
import HintIcon from '../../../assets/icons/HintIcon.vue';

export default {
	name: 'SideBarCourse',
	components: { HintIcon },
	props: {
		activeLink: {
			type: Number,
			default: 1
		}
	},
	data() {
		return {
			links: [
				{ id: 1, title: 'Внешний вид', icon: AppearanceIcon, hint: false },
				{ id: 2, title: 'Материалы курса', icon: MaterialsIcon, hint: true },
				{ id: 3, title: 'Курс проходят', icon: ProfileIcon, hint: true },
				{ id: 4, title: 'Настройки', icon: SettingIcon, hint: true },
				{ id: 5, title: 'Комментарии', icon: CommentIcon, hint: false },
				{ id: 6, title: 'Заработать на курсе', icon: JobIcon, hint: false },
			],
		};
	},
	computed: {
		isActive() {
			return (index) => this.activeLink === index;

		}
	},
	methods: {
		handleClickActiveMain(index) {
			this.$emit('update:activeLink', index);
		},

	},
};
</script>
<style scoped>
.sidebar-course{
		max-width: 284px;
		width: 100%;
		padding: 28px 19px;
		display: flex;
		gap: 25px;
		flex-direction: column;

		background-color: white;
		color: #8DA0C1;
}
.sidebar-course .active{
		background-color: #658CDA;
	color: white;

}
.sidebar-course-title .active{
		color: white;
}

.sidebar-course-content:focus,
.sidebar-course-content:active {

	outline: none;
}

.sidebar-course-content{
	background-color: white;
	display: flex;
		gap: 10px;
		padding: 11px;
	border-radius: 8px;
}

.sidebar-course-title{
font-size: 16px;
		font-weight: 400;
		line-height:20px;

}

.sidebar-course-hint{

}

</style>
