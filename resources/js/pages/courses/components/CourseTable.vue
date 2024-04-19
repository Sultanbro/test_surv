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
				<tr v-for="item in items" :key="item.id">
					<td>
						<input type="checkbox" v-model="item.checked">
					</td>
					<td>{{ item.name }}</td>
					<td>{{ item.short }}</td>
					<td>
						<div class="section__type">
							<span :class="getType(item.type)">{{ getTypeTitle(item.type) }}</span>
						</div>
					</td>
					<td>{{ item.author_id }}</td>
					<td>{{ item.created_at }}</td>
					<td>
						<div class="section__edit">
							<button class="section__edit__btn" @click="toggleMenu(item.id)">
								<img src="/course/dots.svg" alt="More Options">
							</button>
							<div v-if="showMenuId === item.id" class="section__edit__menu">
								<ul>
									<li>
										<button>
											<img src="/course/eye.svg" alt="More Options">
											<span>Посмотреть курс</span>
										</button>
									</li>
									<li>
										<button>
											<img src="/course/plus.svg" alt="More Options">
											<span>Назначить</span>
										</button>
									</li>
									<li>
										<button @click="openModal(item)">
											<img src="/course/trash.svg" alt="More Options">
											<span>Удалить</span>
										</button>
									</li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<div v-if="modalVisible" class="modal">
			<div class="modal__content">
				<img src="/course/delete.svg" alt="delete">
				<h3 class="modal__title">Удалить курс?</h3>
				<p class="modal__desc">Вы уверены, что хотите удалить курс? Это действия нельзя будет отменить.</p>
				<div class="modal__action">
					<button class="modal__action__cancel" @click="modalVisible = false">Отмена</button>
					<button class="modal__action__delete" @click="deleteItem(currentItem)">Удалить</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable*/
export default {
	props: {
		items: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			showMenuId: null,
			modalVisible: false,
			currentItem: null
		};
	},
	computed: {
		allChecked: {
			get() {
				return this.items.length && this.items.every(item => item.checked);
			},
			set(value) {
				this.items.forEach(item => {
					item.checked = value;
				});
			}
		}
	},
	watch: {
		showMenuId(newVal, oldVal) {
			if (newVal !== null) {
				document.addEventListener('click', this.handleClickOutside);
			} else if (oldVal !== null) {
				document.removeEventListener('click', this.handleClickOutside);
			}
		}
	},
	methods: {
		toggleAll(event) {
			this.allChecked = event.target.checked;
		},
		toggleMenu(itemId) {
			this.showMenuId = this.showMenuId === itemId ? null : itemId;
		},
		handleClickOutside(event) {
			if (this.showMenuId !== null && !this.$el.contains(event.target)) {
				this.showMenuId = null;
			}
		},
		openModal(item) {
			this.currentItem = item;
			this.modalVisible = true;
			this.showMenuId = null;
		},

		getType(status) {
			switch (status) {
				case 1:
					return "default";
				case 2:
					return "default";
				case 3:
					return "purchased";
				default:
					return "";
			}
		},
		getTypeTitle(status) {
			switch (status) {
				case 1:
					return "автоматический";
				case 2:
					return "индивидуальный";
				case 3:
					return "куплен";
				default:
					return "";
			}
		},
		async deleteItem(item) {
			try {
				const res = await this.axios.delete(`/v2/courses/delete/${item.id}`);
				this.$emit('child-event');
				console.log(res)
				this.modalVisible = false

			} catch (e) {
				console.error(e)
			}

		},
	}
};
/* eslint-enable */
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
}

tbody tr:hover td:first-child {
	border-left: 5px solid #9F9F9F;
	/* Gray left border on hover */
}

.section__edit {
	position: relative;
}

.section__edit__btn {
	background: none;
	outline: none;
}



.section__edit__menu {
	position: absolute;
	background-color: white;
	box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
	z-index: 1;
	width: 230px;
	left: -230px;
	border-radius: 20px;
	overflow: hidden;
	/* Adjust based on layout */
}

.section__edit__menu ul {
	list-style: none;
	padding: 0;
	margin: 0;
}

.section__edit__menu li button {
	width: 100%;
	display: flex;
	align-items: center;
	padding: 10px 16px;
	text-decoration: none;
	color: black;
	font-family: "Inter", sans-serif;
	font-size: 16px;
	background: none;
	outline: none;
}

.section__edit__menu li button:focus {
	outline: none !important;
}

.section__edit__menu li button img {
	margin-right: 10px;
}

.section__edit__menu li button:hover {
	background-color: #8DA0C1;
	color: #fff;
}
</style>

<style scoped lang="scss">
/* Add your existing styles here */
.modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;

	&__content {
		font-family: "Inter", sans-serif;
		background: white;
		padding: 20px;
		border-radius: 10px;
		width: 400px;
	}

	&__title {
		margin-top: 20px;
		margin-bottom: 10px;
		font-size: 18px;
		font-weight: 600;
	}

	&__desc {
		font-size: 14px;
		font-weight: 400;
	}

	&__action {
		width: 100%;
		display: flex;
		justify-content: space-between;
		margin-top: 20px;

		button {
			font-size: 16px;
			font-weight: 600;
			padding: 15px 20px;
			width: 48%;
			border-radius: 5px;
		}

		&__cancel {
			border: 1px solid #D0D5DD;
			color: #000000;
			background: #fff;
		}

		&__delete {
			background: #D92D20;
			color: #fff;
		}
	}
}



.section__table {
	height: calc(550px);
	overflow-y: auto;
	scrollbar-width: thin;
}
</style>


<style scoped lang="scss">
.section__type {
	span {
		border-radius: 50px;
		padding: 5px 10px;
	}

	.default {
		background: #EBEBEB;
	}

	.purchased {
		background: #41DFB8;
		color: #fff;
	}
}
</style>
