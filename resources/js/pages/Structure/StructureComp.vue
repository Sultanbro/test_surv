<template>
	<div
		class="structure-container"
		ref="container"
		@mousedown="startDrag"
		@mouseup="stopDrag"
		@mousemove="onDrag"
		:class="[{'is-dragging': isDragging}, {'overflow-hidden': openEditCard}]"
	>
		<div
			class="structure-company-controls"
			mousemove.stop
			@mousedown.stop
		>
			<div class="actions">
				<button
					class="remove-demo"
					@click="deleteDemoData"
				>
					Удалить демо данные
				</button>
				<button
					class="icon-btn"
					:class="{'active': editStructure}"
					@click="editStructure = !editStructure"
				>
					<i class="fa fa-pen" />
				</button>
				<button class="icon-btn">
					<i class="icon-nd-settings" />
				</button>
			</div>
		</div>
		<div
			class="range-zoom"
			mousemove.stop
			@mousedown.stop
		>
			<input
				id="range-input"
				class="range-input"
				v-model.number="zoom"
				min="10"
				max="200"
				step="1"
				type="range"
			>
		</div>
		<div
			class="structure-company-area"
			:style="{zoom: zoom / 100}"
		>
			<div
				class="structure-card ceo-card"
				:style="{marginLeft: leftMarginMainCard}"
				:class="{'no-result' : structure.length === 0}"
			>
				<div class="structure-card-header">
					<p class="department">
						Коммерческий департамент
					</p>
					<p class="count">
						999 сотрудников
					</p>
				</div>
				<div class="structure-card-body">
					<img
						src="https://randomuser.me/api/portraits/men/1.jpg"
						alt="photo"
						class="director-photo"
					>
					<p class="position">
						Генеральный директор
					</p>
					<p class="full-name">
						Адиль Каримов
					</p>
				</div>
				<i
					class="fa fa-plus-circle structure-add"
					v-if="editStructure"
					@click="addDepartment"
				/>
			</div>
			<div
				class="departments-area"
				ref="departmentsArea"
			>
				<template v-for="department in structure">
					<StructureItem
						:department="department"
						:key="department.id"
						:level="1"
						:edit-structure="editStructure"
						:bgc="''"
						:users="users"
						:positions="positions"
						:departments-list="departments"
						@isOpenEditCard="isOpenEditCard"
						@scrollToBlock="scrollToBlock"
						@updateLines="drawLines"
					/>
				</template>
			</div>
		</div>
	</div>
</template>

<script>
import StructureItem from './StructureItem';
import {users, positions, departments, structure} from './mockApi';
export default {
	name: 'StructureComp',
	components: {
		StructureItem,
	},
	data() {
		return {
			isDragging: false,
			startX: 0,
			startY: 0,
			scrollLeft: 0,
			scrollTop: 0,
			zoom: 100,
			openEditCard: false,
			editStructure: false,
			leftMarginMainCard: 0,
			positions: positions,
			departments: departments,
			users: users,
			structure: structure
		}
	},
	mounted() {
		this.drawLines();
		window.addEventListener('wheel', this.scrollArea, { passive: false });
	},
	beforeDestroy() {
		window.removeEventListener('wheel', this.scrollArea);
	},
	watch: {
		openEditCard(val) {
			if (val) {
				this.stopDrag();
			}
		},
		structure: {
			deep: true,
			handler() {
				this.$children.forEach(childComponent => {
					this.recursiveUpdate(childComponent);
				});
			}
		}
	},
	methods: {
		recursiveUpdate(component) {
			if (component.drawLines) {
				component.drawLines();
			}
			if (component.$children) {
				component.$children.forEach(childComponent => {
					this.recursiveUpdate(childComponent);
				});
			}
		},
		addDepartment(){
			const obj = {
				id: Math.floor(Math.random() * 10000),
				department: 'Новый департамент',
				employeesCount: 0,
				isNew: true
			};
			this.structure.push(obj);
			this.scrollToBlock(obj.id);
		},
		scrollToBlock(id){
			this.$nextTick(() => {
				const addedDepartment = document.querySelector(`#id-${id}`);
				addedDepartment.scrollIntoView({ behavior: 'smooth', block: 'center' });
				this.drawLines();
			});

		},
		isOpenEditCard(bool) {
			this.openEditCard = bool;
		},
		scrollArea(event) {
			if (event.ctrlKey) {
				event.preventDefault();
				this.zoom = Math.min(Math.max(this.zoom + (event.deltaY > 0 ? -10 : 10), 10), 200);
			}
		},
		deleteDemoData(){
			this.structure = [];
			this.$nextTick(() => {
				this.drawLines();
			})
		},
		drawLines() {
			if (this.$refs.departmentsArea) {
				const children = [...this.$refs.departmentsArea.children];
				if(!children.length){
					this.leftMarginMainCard = 0;
					return;
				}
				let sumWidth = 0;
				children.forEach(c => sumWidth += c.offsetWidth);
				this.leftMarginMainCard = `${Math.round((sumWidth / 2) - 167)}px`;
			}
		},
		updateLines() {
			this.$nextTick(() => {
				this.drawLines();
			})
			this.$forceUpdate();
		},
		startDrag(event) {
			if (!this.openEditCard) {
				this.isDragging = true;
				this.startX = event.clientX;
				this.startY = event.clientY;
				this.scrollLeft = this.$refs.container.scrollLeft;
				this.scrollTop = this.$refs.container.scrollTop;
			}

		},
		stopDrag() {
			this.isDragging = false;
		},
		onDrag(event) {
			if (!this.isDragging) {
				return;
			}

			const deltaX = event.clientX - this.startX;
			const deltaY = event.clientY - this.startY;

			this.$refs.container.scrollLeft = this.scrollLeft - deltaX;
			this.$refs.container.scrollTop = this.scrollTop - deltaY;
		},
	}
}
</script>
