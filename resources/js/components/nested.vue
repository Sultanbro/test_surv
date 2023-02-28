<template>
	<Draggable
		class="dragArea"
		tag="ul"
		:handle="'.fa-bars'"
		:list="tasks"
		:group="{ name: 'g1' }"
		:id="parent_id"
		@end="saveOrder"
	>
		<template v-for="el in tasks">
			<li
				:key="el.id"
				:id="el.id"
				class="chapter"
				:class="{'opened':opened}"
			>
				<div class="d-flex">
					<div class="handles">
						<i
							class="fa fa-bars mover"
							v-if="mode == 'edit'"
						/>
						<i
							class="fa fa-circle mover"
							v-else
						/>
						<div class="shower">
							<i
								class="fa fa-chevron-down pointer"
								v-if="el.children.length > 0 && el.opened"
							/>
							<i
								class="fa fa-chevron-right pointer"
								v-else-if="el.children.length > 0"
							/>
							<i
								class="fa fa-circle pointer"
								v-else
							/>
						</div>
					</div>
					<p
						@click.stop="toggleOpen(el)"
						class="mb-0"
					>
						{{ el.title }}
					</p>
					<div
						class="chapter-btns"
						v-if="mode == 'edit'"
					>
						<i
							class="fa fa-plus mr-1"
							@click.stop="addPage(el)"
						/>
					</div>
				</div>
				<NestedDraggable
					:tasks="el.children"
					@showPage="showPage"
					@addPage="addPage"
					:parent_id="el.id"
					:auth_user_id="auth_user_id"
					:opened="el.opened"
					:mode="mode"
				/>
			</li>
		</template>
	</Draggable>
</template>
<script>
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
		}
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

		log(e) {
			console.log(e)
		}
	},
};
NestedDraggable.components = {
	Draggable,
	NestedDraggable,
}
export default NestedDraggable
</script>
