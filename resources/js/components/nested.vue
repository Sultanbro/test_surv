<template>
	<Draggable
		:id="parent_id"
		class="dragArea"
		tag="ul"
		:handle="'.fa-bars'"
		:list="tasks"
		:group="{ name: 'g1' }"
		@end="saveOrder"
	>
		<template v-for="el in tasks">
			<li
				:id="el.id"
				:key="el.id"
				class="chapter"
				:class="{
					'opened': opened,
					'active': active == el.id,
				}"
			>
				<div class="d-flex">
					<div class="handles">
						<i
							v-if="mode == 'edit'"
							class="fa fa-bars mover"
						/>
						<i
							v-else
							class="fa fa-circle mover"
						/>
						<div class="shower">
							<i
								v-if="el.children.length > 0 && el.opened"
								class="fa fa-chevron-down pointer"
							/>
							<i
								v-else-if="el.children.length > 0"
								class="fa fa-chevron-right pointer"
							/>
							<i
								v-else
								class="fa fa-circle pointer"
							/>
						</div>
					</div>
					<p
						class="mb-0"
						@click.stop="toggleOpen(el)"
					>
						{{ el.title }}
					</p>
					<div
						v-if="mode == 'edit'"
						class="chapter-btns"
					>
						<i
							class="fa fa-plus mr-1"
							@click.stop="addPage(el)"
						/>
					</div>
				</div>
				<NestedDraggable
					:tasks="el.children"
					:parent_id="el.id"
					:active="active"
					:auth_user_id="auth_user_id"
					:opened="el.opened"
					:mode="mode"
					@showPage="showPage"
					@addPage="addPage"
				/>
			</li>
		</template>
	</Draggable>
</template>

<script>
/* eslint-disable camelcase */

import Draggable from 'vuedraggable'
const NestedDraggable = {
	name: 'NestedDraggable',
	props: {
		tasks: {
			required: true,
			type: Array
		},
		parent_id: {
			default: null
		},
		opened: {
			default: false
		},
		auth_user_id: {
			type: Number
		},
		mode: {
			type: String
		},
		active: {
			default: 0
		},
	},
	data() {
		return {
			hover: false,
			handle: '.fa-t',
		}
	},
	created() {
		// if(this.mode == 'edit') {
		//   this.handle = '.fa-bars';
		// }
	},
	methods: {
		toggleOpen(el) {
			this.showPage(el.id, false, true);
			el.opened = !el.opened
		},
		showPage(id) {
			this.$emit('showPage', id);
		},

		addPage(el) {
			this.$emit('addPage', el);
		},
		saveOrder(event) {
			this.axios.post('/kb/page/save-order', {
				id: event.item.id,
				order: event.newIndex, // oldIndex
				parent_id: event.to.id
			})
				.then(() => {
					this.$toast.success('Очередь сохранена');
				})
		},
	},
};
NestedDraggable.components = {
	Draggable,
	NestedDraggable,
}
export default NestedDraggable
</script>
