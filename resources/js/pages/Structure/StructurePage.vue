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
			v-if="$can('structure_edit')"
			class="structure-company-controls"
			@mousemove.stop
			@mousedown.stop
		>
			<div class="actions">
				<button
					v-if="false"
					class="remove-demo"
					@click="deleteDemoData"
				>
					Удалить демо данные
				</button>
				<button
					class="icon-btn"
					:class="{'active': isEditMode}"
					@click="toggleEdit"
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
			@mousemove.stop
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
			:style="{
				zoom: zoom / 100,
				'-moz-transform': `scale(${zoom / 100})`
			}"
		>
			<div
				class="departments-area"
				ref="departmentsArea"
			>
				<template v-if="rootCard">
					<StructureItem
						:card="rootCard"
						:level="0"
						:dictionaries="dictionaries"
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
import {mapState, mapActions} from 'pinia'
import StructureItem from './StructureItem';
// import {users, positions, departments, structure} from './mockApi';
import {useCompanyStore} from '@/stores/Company.js'
import {useStructureStore} from '@/stores/Structure.js'

export default {
	name: 'StructurePage',
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
		}
	},
	computed: {
		...mapState(useCompanyStore, ['dictionaries']),
		...mapState(useStructureStore, ['cards', 'isEditMode']),
		cardsOrFirst(){
			if(this.cards && this.cards.length){
				return this.cards
			}
			return [{
				...this.getEmptyCard(),
				id: null
			}]
		},
		rootCard(){
			return this.cardsOrFirst.find(card => !card.parentId)
		},
	},
	watch: {
		openEditCard(val) {
			if (val) {
				this.stopDrag();
			}
		},
	},
	async mounted() {
		this.fetchDictionaries()
		await this.structureGet()
		this.drawLines()
		window.addEventListener('wheel', this.scrollArea, { passive: false })
		window.addEventListener('storage', this.checkTabEvents, false)
	},
	beforeUnmount() {
		window.removeEventListener('wheel', this.scrollArea)
		window.removeEventListener('storage', this.checkTabEvents, false)
	},
	methods: {
		...mapActions(useCompanyStore, ['fetchDictionaries']),
		...mapActions(useStructureStore, [
			'structureGet',
			'toggleEdit',
			'getEmptyCard',
		]),
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
			// this.structure.push(obj);
			this.scrollToBlock(obj.id)
		},
		scrollToBlock(id){
			this.$nextTick(() => {
				const addedDepartment = document.querySelector(`#id-${id}`)
				addedDepartment.scrollIntoView({ behavior: 'smooth', block: 'center' })
				this.drawLines()
			})
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
			// this.structure = [];
			this.$nextTick(() => {
				this.drawLines();
			})
		},
		drawLines() {
			if(!this.$refs.departmentsArea) return
			const children = [...this.$refs.departmentsArea.children];
			if(!children.length){
				this.leftMarginMainCard = 0;
				return;
			}
			let sumWidth = 0;
			children.forEach(c => sumWidth += c.offsetWidth);
			this.leftMarginMainCard = `${Math.round((sumWidth / 2) - 167)}px`;
		},
		updateLines() {
			this.$nextTick(() => {
				this.drawLines();
			})
			this.$forceUpdate();
		},
		startDrag(event) {
			if(this.openEditCard) return

			this.isDragging = true;
			this.startX = event.clientX;
			this.startY = event.clientY;
			this.scrollLeft = this.$refs.container.scrollLeft;
			this.scrollTop = this.$refs.container.scrollTop;
		},
		stopDrag() {
			this.isDragging = false;
		},
		onDrag(event) {
			if (!this.isDragging) return

			const deltaX = event.clientX - this.startX;
			const deltaY = event.clientY - this.startY;

			this.$refs.container.scrollLeft = this.scrollLeft - deltaX;
			this.$refs.container.scrollTop = this.scrollTop - deltaY;
		},
		async checkTabEvents(event){
			if (event.key !== 'event.updatePositions') return
			const message = JSON.parse(event.newValue);
			if (!message) return

			if (message.command) {
				const loader = this.$loading.show()
				await this.fetchDictionaries(true)
				loader.hide()
			}
		}
	}
}
</script>
