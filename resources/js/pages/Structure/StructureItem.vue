<template>
	<div
		class="structure-item"
		:class="[{'grouped' : department.group}, 'lvl' + level]"
	>
		<div
			class="structure-card"
			:style="'background-color:' + bgColor"
			:class="{'no-result' : !department.hasOwnProperty('departmentChildren') && !department.hasOwnProperty('result')}"
		>
			<div class="structure-card-header">
				<p class="department">
					{{ department.department }}
				</p>
				<p
					class="count"
					v-if="department.employeesCount > 0"
				>
					{{ department.employeesCount }} сотрудников
				</p>
				<p
					class="count"
					v-else
				>
					Нет сотрудников
				</p>
			</div>
			<div
				class="structure-card-body"
				v-if="department.employeesCount > 0"
			>
				<template v-if="department.director">
					<img
						:src="department.director.photo"
						alt="photo"
						class="director-photo"
					>
					<p class="position">
						{{ department.director.position }}
					</p>
					<p class="full-name">
						{{ department.director.fullName }}
					</p>
				</template>
				<template v-if="department.users">
					<div class="users-group">
						<img
							:src="user.photo"
							alt="photo"
							v-for="(user, usrIdx) in department.users"
							:key="usrIdx"
						>
					</div>
				</template>
			</div>
			<i
				class="fa fa-plus-circle structure-add"
				@click="addNew"
			/>
		</div>
		<template v-if="department.hasOwnProperty('departmentChildren')">
			<div
				class="structure-group"
				ref="group"
				v-if="department.departmentChildren.length > 1"
				:style="[{'--start-line' : startLine}, {'--end-line' : endLine}]"
			>
				<StructureItem
					v-for="(child, index) in department.departmentChildren"
					:department="child"
					:bgc="bgColor"
					:level="level + 1"
					:key="index"
				/>
			</div>
			<StructureItem
				class="lonely"
				v-for="(child, index) in department.departmentChildren"
				:department="child"
				:bgc="bgColor"
				:level="level + 1"
				:key="index"
				v-else
			/>
		</template>
		<div
			class="structure-card-result"
			v-if="department.hasOwnProperty('result')"
			:style="{ backgroundColor: bgColor, width: resultWidth > 0 ? `${resultWidth}px` : null }"
		>
			<p class="result-title">
				Результаты
			</p>
			<p class="result-text">
				{{ department.result }}
			</p>
		</div>
	</div>
</template>

<script>
export default {
	name: 'StructureItem',
	props: {
		department: Object,
		level: Number,
		bgc: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			startLine: '1px',
			endLine: '2px',
			resultWidth: '0'

		}
	},
	computed: {
		bgColor() {
			return this.department.bgc ? this.department.bgc : this.bgc.length ? this.bgc : '';
		}
	},
	mounted() {
		this.drawLines();
	},
	methods: {
		drawLines() {
			if (this.$refs.group) {
				const children = this.$refs.group.children;
				this.resultWidth = this.$refs.group.offsetWidth - (children.length * 5);
				this.startLine = `${(children[0].offsetWidth / 2) + 8}px`;
				this.endLine = `${(children[children.length - 1].offsetWidth / 2) + 8}px`;
			}
		},
		addNew() {
			const obj = {
				department: 'Новый департамент',
				employeesCount: 111,
				director: {
					fullName: 'Кто-то',
					position: 'Просто директор',
					birthday: '10.10.1985',
					phone: '+7(700)5654323',
					email: 'test.test@gmail.com',
					photo: 'https://avatars.mds.yandex.net/i?id=3699f557f15d3fdb99c1602219555d62731d4f73-6959765-images-thumbs&n=13'
				}
			};
			this.department.departmentChildren.push(obj);
			this.drawLines();
		}
	}
}
</script>
