import { defineStore } from 'pinia'
import {
	structureGet,
	structureCreate,
	structureUpdate,
	structureDelete,
} from '@/stores/api/structure'

function recursiveToFlat(struct, result = []){
	if(!struct) return result
	result.push({
		id: struct.id,
		name: struct.name,
		parent_id: struct.parent_id,
		description: struct.description,
		color: struct.color,
		created_at: struct.created_at,
		updated_at: struct.updated_at,
		group_id: struct.group_id,
		status: struct.status,
		users: struct.users,
		manager: struct.manager,
		is_group: struct.is_group,
	})
	if(struct.childrens && struct.childrens.length){
		struct.childrens.forEach(child => recursiveToFlat(child, result))
	}
	return result
}

export const useStructureStore = defineStore('structure', {
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		cards: [],
		isEditMode: false,
		newId: -1,
	}),
	actions: {
		async structureGet(){
			this.isLoading = true
			try{
				const {structure_card} = await structureGet()
				// update state here
				this.cards = recursiveToFlat(structure_card)
				this.isReady = true
			}
			catch(error){
				console.error('fetchApi', error)
			}
			this.isLoading = false
		},
		toggleEdit(){
			this.isEditMode = !this.isEditMode
		},
		addCard(parentId){
			const card = this.cards.find(card => card.id === parentId)
			const empty = this.getEmptyCard()
			empty.parent_id = parentId
			if(card){
				empty.color = card.color
			}

			this.cards.push(empty)
		},
		async createCard(card){
			const data = await structureCreate(card)
			return data
		},
		async updateCard(card){
			const data = await structureUpdate(card)
			return data
		},
		async deleteCard(cardId){
			if(cardId === null) return
			if(cardId > 0) {
				await structureDelete(cardId)
			}

			const index = this.cards.findIndex(card => card.id === cardId)
			this.cards.splice(index, 1)

			return true
		},
		getEmptyCard(){
			return {
				id: --this.newId,
				name: '',
				parent_id: 0,
				description: '',
				color: '#7CAEF3',
				group_id: 0,
				status: 1,
				users: [],
				manager: null,
				is_group: 0,
				isNew: true
			}
		}
	},
	getters: {
		rootCard(){
			return this.cards.find(card => !card.parent_id)
		}
	}
})
