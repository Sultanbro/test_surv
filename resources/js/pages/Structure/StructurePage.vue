<template>
	<div
		ref="container"
		class="structure-container"
		:class="[{'is-dragging': isDragging}, {'overflow-hidden': editedCard}]"
		@mousedown="startDrag"
		@mouseup="stopDrag"
		@mousemove="onDrag"
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
				<button
					class="icon-btn"
					@click="toggleSettings"
				>
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
				v-model.number="zoom"
				class="range-input"
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
				ref="departmentsArea"
				class="departments-area"
			>
				<template v-if="rootCard">
					<StructureItem
						ref="rootCard"
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

		<SimpleSidebar
			title="Настройки"
			:open="isSettings"
			width="30%"
			@close="isSettings = false"
		>
			<template #body>
				<b-form-checkbox
					v-model="settings.autoManager"
					switch
					size="lg"
				>
					Обновлять автоматически информацию о&nbsp;руководителях отделов
				</b-form-checkbox>
			</template>
			<template #footer>
				<JobtronButton
					@click="onSaveSettings"
				>
					Сохранить
				</JobtronButton>
			</template>
		</SimpleSidebar>
	</div>
</template>

<script>
import StructureItem from './StructureItem';
import StructureEditCard from './StructureEditCard'
import StructureUsersMore from './StructureUsersMore'
import SimpleSidebar from '@ui/SimpleSidebar.vue'
import JobtronButton from '@ui/Button.vue'

import {mapState, mapActions} from 'pinia'
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
		SimpleSidebar,
		JobtronButton,
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
			isSettings: false,
			settings: {
				autoManager: false
			}
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
				/* eslint-disable-next-line camelcase */
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
			if(this.cards && this.cards.lengtkh){
				return this.cards
			}
			/* eslint-disable camelcase */
			const ownerCard = {
				...this.getEmptyCard(),
				id: null,
				parent_id: null,
				name: 'Генеральный директор',
				is_vacant: false,
			}
			/* eslint-enable camelcase */

			if(this.owner){
				/* eslint-disable camelcase */
				ownerCard.manager = {
					user_id: this.owner.id,
					position_id: this.owner.position_id
				}
				/* eslint-enable camelcase */
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
		await this.fetchSettings()
		await this.fetchDictionaries()
		await this.fetchCentralOwner()
		await this.structureGet()
		this.drawLines()
		this.autoZoom()
		window.addEventListener('wheel', this.scrollArea, { passive: false })
		window.addEventListener('storage', this.checkTabEvents, false)

		// if(!this.isDemo) await this.autoDeleteCards()
		if(this.settings.autoManager && !this.isDemo) this.updateManagers()
	},
	beforeDestroy() {
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
			'updateCard',
			'deleteCard',
		]),

		// ScrollZoom
		autoZoom(){
			this.$nextTick(() => {
				if(!this.$refs.container) return
				if(!this.$refs.rootCard) return
				const widthAwailable = this.$refs.container.clientWidth - 40
				const heightAwailable = this.$refs.container.clientHeight - 40
				const zoom = this.zoom / 100
				const cardsWidth = this.$refs.rootCard.$el.clientWidth * zoom
				const cardsHeight = this.$refs.rootCard.$el.clientHeight * zoom
				if((cardsWidth > widthAwailable || cardsHeight > heightAwailable) && this.zoom > 10){
					this.zoom -= 2
					requestAnimationFrame(this.autoZoom)
				}
			})
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
		// ScrollZoom

		// Lines
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
		drawLines() {
			if(!this.$refs.departmentsArea) return
			const children = [...this.$refs.departmentsArea.children];
			if(!children.length){
				this.leftMarginMainCard = 0;
				return;
			}
			let sumWidth = 0;
			children.forEach(c => sumWidth += c.offsetWidth);
			this.leftMarginMainCard = `${Math.round((sumWidth / 2) - 164)}px`;
		},
		updateLines() {
			this.$nextTick(() => {
				this.drawLines();
			})
			this.$forceUpdate();
		},
		// Lines

		// DND
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
		// DND

		// Settings
		async fetchSettings(){
			const {settings} = await fetchSettings('structure_auto_manager')
			this.settings.autoManager = !!parseInt(settings.custom_structure_auto_manager)
		},
		async updateSettings(){
			await updateSettings({
				type: 'structure_auto_manager',
				// eslint-disable-next-line camelcase
				custom_structure_auto_manager: this.settings.autoManager
			})
			this.$toast.success('Настройки сохранены')
		},
		toggleSettings(){
			this.isSettings = !this.isSettings
		},
		onSaveSettings(){
			this.isSettings = false
			this.updateSettings()
		},
		// Settings

		// Demo
		async checkDemo(){
			const {settings} = await fetchSettings('structure_demo_removed')
			if(!parseInt(settings.custom_structure_demo_removed)){
				this.setDemo(true)
			}
		},
		async removeDemo(){
			/* eslint-disable camelcase */
			await updateSettings({
				type: 'structure_demo_removed',
				custom_structure_demo_removed: 1
			})
			/* eslint-enable camelcase */
			this.$toast.success('Демо данные удалены')
			this.setDemo(false)
		},
		// Demo

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

		async autoDeleteCards(){
			if(!this.dictionaries?.profile_groups?.length) return
			for(const card of this.cards){
				if(!card.group_id) continue
				const cardGroup = this.dictionaries.profile_groups.find(group => group.id === card.group_id)
				if(!cardGroup || !cardGroup.active) await this.deleteCard(card.id)
			}
		},

		async updateManagers(parent = null, parentManagers = [this.owner.id]){
			if(!this.dictionaries.users) return
			for(const card of this.cards){
				if(card.parent_id !== parent) continue
				if(!card.group_id) {
					// if(card.manager.user_id) parentManagers.push(card.manager.user_id)
					this.updateManagers(card.id, parentManagers)
					continue
				}

				const manager = this.dictionaries.users.find(user => {
					if(!user.profile_group) return false
					const group = user.profile_group.find(group => group.id === card.group_id)
					if(group) return group.is_head
					return false
				})

				if(parentManagers.includes(manager?.id)){
					// if(card.manager.user_id) parentManagers.push(card.manager.user_id)
					this.updateManagers(card.id, parentManagers)
					continue
				}

				if(!manager ?? card.manager?.user_id){
					/* eslint-disable camelcase */
					await this.updateCard({
						...card,
						manager: {
							user_id: null,
							position_id: card.manager?.position_id
						},
						is_vacant: true
					})
					/* eslint-enable camelcase */
				}
				if(manager && manager.id !== card.manager?.user_id){
					/* eslint-disable camelcase */
					await this.updateCard({
						...card,
						manager: {
							user_id: manager.id,
							position_id: manager.position_id
						},
						is_vacant: false
					})
					// parentManagers.push(manager.id)
					/* eslint-enable camelcase */
				}
				this.updateManagers(card.id, parentManagers)
			}
		}
	}
}
</script>
