<template>
	<div
		class="structure-container"
		ref="container"
		@mousedown="startDrag"
		@mouseup="stopDrag"
		@mousemove="onDrag"
		:class="[{'is-dragging': isDragging}, {'overflow-hidden': editedCard}]"
	>
		<div
			v-if="$can('structure_edit')"
			class="structure-company-controls"
			@mousemove.stop
			@mousedown.stop
		>
			<div class="actions">
				<button
					v-if="isDemo"
					class="remove-demo"
					@click="removeDemo"
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
				<!-- <button class="icon-btn">
					<i class="icon-nd-settings" />
				</button> -->
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
						:dictionaries="isDemo ? demo.dictionaries : actualDictionaries"
						@scrollToBlock="scrollToBlock"
						@updateLines="drawLines"
					/>
				</template>
			</div>
		</div>

		<StructureEditCard
			v-if="editedCard"
			:card="editedCard"
			:users="isDemo ? demo.dictionaries.users : actualDictionaries.users"
			:positions="isDemo ? demo.dictionaries.users : actualDictionaries.positions"
			:departments-list="isDemo ? demo.dictionaries.users : actualDictionaries.profile_groups"
			@close="closeEditCard"
		/>


		<StructureUsersMore
			v-if="moreUsers"
			:users="moreUsers"
		/>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import StructureItem from './StructureItem';
import StructureEditCard from './StructureEditCard'
import StructureUsersMore from './StructureUsersMore'
// import {users, positions, departments, structure} from './mockApi';
import {useCompanyStore} from '@/stores/Company.js'
import {useStructureStore} from '@/stores/Structure.js'

import {
	fetchSettings,
	updateSettings,
} from '@/stores/api.js'

export default {
	name: 'StructurePage',
	components: {
		StructureItem,
		StructureEditCard,
		StructureUsersMore,
	},
	data() {
		return {
			isDragging: false,
			startX: 0,
			startY: 0,
			scrollLeft: 0,
			scrollTop: 0,
			zoom: 100,
			editStructure: false,
			leftMarginMainCard: 0,
		}
	},
	computed: {
		...mapState(useCompanyStore, [
			'dictionaries',
			'centralOwner',
		]),
		...mapState(useStructureStore, [
			'cards',
			'editedCard',
			'isEditMode',
			'isDemo',
			'demo',
			'moreUsers',
		]),
		actualDictionaries(){
			return {
				users: this.dictionaries.users.filter(user => {
					return !user.deleted_at && user.last_seen
				}),
				profile_groups: this.dictionaries.profile_groups.filter(group => {
					return group.active
				}),
				positions: this.dictionaries.positions.filter(pos => {
					return !pos.deleted_at
				})
			}
		},
		owner(){
			if(!this.centralOwner) return null
			return this.dictionaries.users.find(user => user.email === this.centralOwner.email)
		},
		cardsOrFirst(){
			if(this.cards && this.cards.length){
				return this.cards
			}
			const ownerCard = {
				...this.getEmptyCard(),
				id: null,
				parent_id: null,
				name: 'Генеральный директор',
				is_vacant: false,
			}
			if(this.owner){
				ownerCard.manager = {
					user_id: this.owner.id,
					position_id: this.owner.position_id
				}
				ownerCard.users = [
					{
						id: this.owner.id
					}
				]
			}
			return [ownerCard]
		},
		rootCard(){
			if(this.isDemo) return this.demo.structure.find(card => !card.parentId)
			return this.cardsOrFirst.find(card => !card.parentId)
		},
	},
	watch: {
		editedCard(val) {
			if (val) {
				this.stopDrag();
			}
		},
	},
	async mounted() {
		await this.checkDemo()
		await this.fetchDictionaries()
		await this.fetchCentralOwner()
		await this.structureGet()
		this.$nextTick(this.checkFirstCard)
		this.drawLines()
		window.addEventListener('wheel', this.scrollArea, { passive: false })
		window.addEventListener('storage', this.checkTabEvents, false)
	},
	beforeUnmount() {
		window.removeEventListener('wheel', this.scrollArea)
		window.removeEventListener('storage', this.checkTabEvents, false)
	},
	methods: {
		...mapActions(useCompanyStore, [
			'fetchDictionaries',
			'fetchCentralOwner',
		]),
		...mapActions(useStructureStore, [
			'structureGet',
			'toggleEdit',
			'getEmptyCard',
			'closeEditCard',
			'setDemo',
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
		scrollToBlock(id){
			this.$nextTick(() => {
				const addedDepartment = document.querySelector(`#id-${id}`)
				addedDepartment.scrollIntoView({ behavior: 'smooth', block: 'center' })
				this.drawLines()
			})
		},
		scrollArea(event) {
			if (event.ctrlKey) {
				event.preventDefault();
				this.zoom = Math.min(Math.max(this.zoom + (event.deltaY > 0 ? -10 : 10), 10), 200);
			}
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
			if(this.editedCard) return

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
		},
		checkFirstCard(){},
		async checkDemo(){
			const {settings} = await fetchSettings('structure_demo_removed')
			if(!parseInt(settings.custom_structure_demo_removed)){
				this.setDemo(true)
			}
		},
		async removeDemo(){
			await updateSettings({
				type: 'structure_demo_removed',
				custom_structure_demo_removed: 1
			})
			this.$toast.success('Демо данные удалены')
			this.setDemo(false)
		},
	}
}
</script>
