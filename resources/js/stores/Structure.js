/* eslint-disable camelcase */

import { defineStore } from 'pinia'
import {
	structureGet,
	structureCreate,
	structureUpdate,
	structureDelete,
} from '@/stores/api/structure'
import {
	dictionaries,
	structure,
} from '@/pages/Structure/mockApi.js'

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
		is_vacant: struct.is_vacant,
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

function cardToRequest(card){
	const request = {
		parent_id: card.parent_id,
		description: card.description,
		color: card.color,
		user_ids: card.users?.map(user => user.id),
		position_id: card.manager?.position_id,
		manager_id: card.manager?.user_id,
		status: card.status,
		is_group: card.is_group,
		is_vacant: card.is_vacant,
	}
	if(!request.position_id) request.position_id = null
	if(card.group_id) request.group_id = card.group_id
	if(card.name) request.name = card.name
	return request
}

// function responseToCard(response){
// 	const card = response.structure_card
// 	card.users = response.structure_card_user.map(id => ({id}))
// 	card.manager = response.structure_card_manager
// 	return card
// }

export const useStructureStore = defineStore('structure', {
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		cards: [],
		isEditMode: false,
		newId: -1,
		editedCard: null,
		moreUsers: null,
		demo: {
			dictionaries,
			structure,
			id: 1000,
		},
		isDemo: false,
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
			if(this.isDemo){
				const card = this.demo.structure.find(card => card.id === parentId)
				const empty = this.getEmptyCard()
				empty.parent_id = parentId
				if(card){
					empty.color = card.color
				}

				this.demo.structure.push(empty)
				return
			}
			const card = this.cards.find(card => card.id === parentId)
			const empty = this.getEmptyCard()
			empty.parent_id = parentId
			if(card){
				empty.color = card.color
			}

			this.cards.push(empty)
		},
		editCard(card){
			this.editedCard = card
		},
		showMoreUsers(users){
			this.moreUsers = users
		},
		closeEditCard(){
			this.editedCard = null
		},
		async createCard(card){
			if(this.isDemo){
				const index = this.demo.structure.findIndex(c => c.id === card.id)
				this.demo.structure.splice(index, 1)
				card.id = ++this.demo.id
				this.demo.structure.push(card)
				return card
			}

			const request = cardToRequest(card)
			const data = await structureCreate(request)
			// const created = responseToCard(data)
			// console.log(card)
			// this.cards.push(created)
			// const temp = this.cards.findIndex(c => c.id = card.id)
			// if(~temp) this.cards.splice(temp, 1)
			await this.structureGet()
			this.closeEditCard()
			return data
		},
		async updateCard(card){
			if(this.isDemo){
				const index = this.demo.structure.findIndex(c => c.id === card.id)
				this.demo.structure.splice(index, 1, card)
				if(card.color !== this.demo.structure[index].color) this.updateColor(card.id, card.color)
				return card
			}

			const request = cardToRequest(card)
			const data = await structureUpdate(card.id, request)
			const old = this.cards.findIndex(c => c.id === card.id)
			if(~old && card.color !== this.cards[old].color) await this.updateColor(card.id, card.color)
			if(~old) this.cards.splice(old, 1, card)
			this.closeEditCard()
			return data
		},
		async deleteCard(cardId){
			if(this.isDemo){
				const index = this.demo.structure.findIndex(card => card.id === cardId)
				this.demo.structure.splice(index, 1)
				return true
			}

			if(cardId === null) return
			if(cardId > 0) {
				await structureDelete(cardId)
			}

			const index = this.cards.findIndex(card => card.id === cardId)
			this.cards.splice(index, 1)
			this.closeEditCard()
			return true
		},
		async updateColor(cardId, color){
			const topCard = this.cards.find(card => card.id === cardId)
			if(!topCard) return
			for(const card of this.cards){
				if(card.parent_id === cardId){
					await this.updateCard({
						...card,
						color,
					})
				}
			}
		},
		getEmptyCard(){
			return {
				id: JSON.parse(JSON.stringify(--this.newId)),
				name: '',
				parent_id: 0,
				description: '',
				color: '#7CAEF3',
				group_id: 0,
				status: 1,
				users: [],
				manager: null,
				is_group: 0,
				is_vacant: 0,
				isNew: true
			}
		},
		setDemo(toggle){
			this.isDemo = toggle
			return this.demo
		}
	}
})
