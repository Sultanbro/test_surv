<!-- eslint-disable -->
<template>
	<div class="section__table">
		<table>
			<thead>
				<tr>
					<th><input type="checkbox" :checked="allChecked" @change="toggleAll"></th>
					<th>Название</th>
					<th>Краткое описание</th>
					<th>Тип</th>
					<th>Автор</th>
					<th>Добавлено</th>
					<th />
				</tr>
			</thead>
			<tbody>
				<tr
					v-for="(item, index) in items"
					:key="index"
				>
					<td>
						<input
							v-model="item.checked"
							type="checkbox"
							@change="handleCheckChange"
						>
					</td>
					<td>{{ item.title }}</td>
					<td>{{ item.description }}</td>
					<td>{{ item.type }}</td>
					<td>{{ item.author }}</td>
					<td>{{ item.createdAt }}</td>
					<td>
						img
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
export default {
	data() {
		return {
			items: [
				{ title: 'Название курса', description: 'Сайт рыбатекст поможт дизайнеру, верстальщику', type: 'куплен', author: 'Петров Иван', createdAt: '12.03.24', checked: false },
				{ title: 'Название курса', description: 'Сайт рыбатекст поможт дизайнеру, верстальщику', type: 'автоматический', author: 'Петров Иван', createdAt: '12.03.24', checked: false },
				{ title: 'Название курса', description: 'Сайт рыбатекст поможт дизайнеру, верстальщику', type: 'автоматический', author: 'Петров Иван', createdAt: '12.03.24', checked: false },
				{ title: 'Название курса', description: 'Сайт рыбатекст поможт дизайнеру, верстальщику', type: 'куплен', author: 'Петров Иван', createdAt: '12.03.24', checked: false },
			]
		};
	},
	computed: {
		allChecked: {
			get() {
				return this.items.every(item => item.checked);
			},
			set(value) {
				this.items.forEach(item => item.checked = value);
			}
		}
	},
	methods: {
		toggleAll() {
			this.allChecked = !this.allChecked;
		},
		handleCheckChange() {
			this.$nextTick(() => {
				this.allChecked = this.items.every(item => item.checked);
			});
		}
	}
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0 10px;
}

th,
td {
	padding: 12px 15px;
	text-align: left;
	background-color: transparent;
	/* Ensuring that cells do not override row color */
	font-family: "Inter", sans-serif !important;
	font-size: 14px;
	font-weight: 300;
}

th {
	font-weight: 300;
}

td {
	padding: 20px 15px;
}

thead tr {
	background-color: #EBEBEB;
	/* Setting the background at the row level */
	font-weight: 300 !important;
	color: #000000;
}

thead tr th:first-child {
	border-top-left-radius: 10px;
	border-bottom-left-radius: 10px;
}

thead tr th:last-child {
	border-top-right-radius: 10px;
	border-bottom-right-radius: 10px;
}

tbody tr td:first-child {
	border-left: 5px solid #358BDC;
	/* Blue left border */
	border-bottom-left-radius: 16px;
	border-top-left-radius: 16px;
}

tbody tr td:last-child {
	border-bottom-right-radius: 16px;
	border-top-right-radius: 16px;
}

tbody tr {
	background-color: white;
	/* Explicit setting for tbody cells */
}

tbody tr:hover {
	background-color: #658CDA;
	color: #fff;
}

tbody tr:hover td:first-child {
	border-left: 5px solid #9F9F9F;
	/* Gray left border on hover */
}
</style>
