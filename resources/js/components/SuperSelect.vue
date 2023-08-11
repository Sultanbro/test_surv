<template>
	<div
		ref="select"
		v-click-outside="close"
		class="super-select"
		:class="posClass"
	>
		<div
			class="selected-items flex-wrap noscrollbar"
			@click="toggleShow"
		>
			<div
				v-for="(value, i) in valueList"
				:key="i"
				class="selected-item"
				:class="'value' + value.type"
			>
				{{ value.name }}
				<i
					v-if="!one_choice_made"
					class="fa fa-times"
					@click.stop="removeValue(i, value.id)"
				/>
			</div>
			<div
				id="placeholder"
				class="selected-item placeholder"
			>
				{{ placeholder }}
			</div>
		</div>
		<div
			v-if="show"
			class="show"
		>
			<div class="search">
				<input
					ref="search"
					v-model="searchText"
					type="text"
					placeholder="Поиск..."
					@keyup="onSearch()"
				>
			</div>

			<div class="options-window">
				<div class="types">
					<div
						v-if="disable_type !== 1"
						class="type"
						:class="{'active': type == 1}"
						@click="changeType(1)"
					>
						<div class="text">
							Сотрудники
						</div>
						<i class="fa fa-user" />
					</div>
					<div
						v-if="disable_type !== 2"
						class="type"
						:class="{'active': type == 2}"
						@click="changeType(2)"
					>
						<div class="text">
							Отделы
						</div>
						<i class="fa fa-users" />
					</div>
					<div
						v-if="disable_type !== 3"
						class="type"
						:class="{'active': type == 3}"
						@click="changeType(3)"
					>
						<div class="text">
							Должности
						</div>
						<i class="fa fa-briefcase" />
					</div>

					<div
						v-if="select_all_btn && !single"
						class="type mt-5 active all"
						@click="selectAll"
					>
						<div class="text">
							Все
						</div>
						<i class="fa fa-check" />
					</div>
				</div>


				<div class="options">
					<template v-for="(option, index) in filtered_options">
						<div
							v-if="option.shownProfile"
							:key="index"
							class="option shown-profile"
						>
							<i
								v-if="option.type == 1"
								class="fa fa-user"
							/>
							<i
								v-if="option.type == 2"
								class="fa fa-users"
							/>
							<i
								v-if="option.type == 3"
								class="fa fa-briefcase"
							/>
							{{ option.name }}
							<i
								v-if="option.selected"
								class="fa fa-times"
								@click.stop="removeValueFromList(index)"
							/>
						</div>
						<div
							v-else
							:key="index + 'a'"
							class="option"
							:class="{'selected': option.selected}"
							@click="addValue(index)"
						>
							<i
								v-if="option.type == 1"
								class="fa fa-user"
							/>
							<i
								v-if="option.type == 2"
								class="fa fa-users"
							/>
							<i
								v-if="option.type == 3"
								class="fa fa-briefcase"
							/>
							{{ option.name }}
							<i
								v-if="option.selected"
								class="fa fa-times"
								@click.stop="removeValueFromList(index)"
							/>
						</div>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'SuperSelect',
	props: {
		pre_build: {
			type: Boolean,
			default: false
		},
		value_id: {
			type: Number,
			default: null
		},
		disable_type: {
			type: Number,
			default: null
		},
		onlytype:{
			type: Number,
			default: 0
		},
		placeholder:{
			type: String,
			default: '',
		},
		values: {
			type: Array,
			default: Array
		},
		single: {
			type: Boolean,
			default: false
		},
		select_all_btn: {
			type: Boolean,
			default: false
		},
		ask_before_delete: {
			type: String,
			default: ''
		},
		one_choice: {
			type: Boolean,
			default: false
		},
		available_courses: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			valueList: [],
			show_placeholder: true,
			options: [],
			filtered_options: [],
			type: 1,
			show: false,
			posClass: 'top',
			searchText: '',
			first_time: true,
			selected_all: false,
			one_choice_made: false
		};
	},
	created() {
		if(this.pre_build){
			this.axios
				.get('/superselect/get', {})
				.then(response => {
					if(this.disable_type){
						response.data.options.forEach(item =>{
							if(this.disable_type !== item.type){
								this.options.push(item);
							}
						})
					} else{
						this.options = response.data.options;
					}
					if(this.available_courses.length > 0){
						this.available_courses.forEach(item => {
							this.options.filter((option, index) => {
								if(option.id === item.id && option.type === item.type){
									this.options[index].shownProfile = true;
								}
							})
						})
					}
					if(this.value_id){
						this.valueList.push(this.options.find(x => x.id === this.value_id));
						document.getElementById('placeholder').style.display = 'none';
					}
					this.first_time = false;
					this.filterType();
					this.addSelectedAttr();
				})
		}
		this.valueList = this.values;
		if(this.one_choice && this.valueList.length > 0) this.one_choice_made = true;
		this.checkSelectedAll();
		if(this.onlytype > 0){
			this.changeType(2);
		}
	},
	methods: {
		hidePlaceholder(){
			this.show_placeholder = !this.show_placeholder;
		},
		checkSelectedAll() {
			if(this.valueList.length == 1
                && this.valueList[0]['id']== 0
                && this.valueList[0]['type'] == 0) {
				this.selected_all = true;
				//  console.log('okay');
			} else {
				// console.log('wtf');
			}
		},

		filterType() {
			this.filtered_options = this.options.filter(el => {
				return el.type == this.type
			});
		},

		addSelectedAttr() {
			this.filtered_options.forEach(el => {
				el.selected = this.valueList.findIndex(v => v.id == el.id && v.type == el.type) != -1
			});
		},

		toggleShow() {
			if(this.one_choice_made) {
				return;
			}

			this.show = !this.show;
			if(this.show){
				document.getElementById('placeholder').style.display = 'none';
			}else{
				document.getElementById('placeholder').style.display = 'block';
			}

			if(this.first_time) {
				this.fetch();
			}

			this.$nextTick(() => {
				if(this.$refs.search !== undefined) this.$refs.search.focus();
			});
			this.setPosClass();
		},

		setPosClass() {
			let pos = this.$refs['select'].getBoundingClientRect();
			let viewport_h = document.documentElement.clientHeight;
			this.posClass = (viewport_h - pos.top > 450) ? 'bottom' : 'top';
		},

		changeType(i) {
			this.type = i;
			this.searchText = '';
			this.filterType();
			this.addSelectedAttr();
		},

		addValue(index) {
			if(this.single) this.show = false;
			if(this.single && this.valueList.length > 0) {
				return;
			}


			if(this.selected_all) return;

			let item = this.filtered_options[index];

			if(this.valueList.findIndex(v => v.id == item.id && v.type == item.type) == -1) {

				let value = {
					name: item.name,
					id: item.id,
					type: item.type
				};

				this.$emit('choose', value);
				this.valueList.push(value);

				item.selected = true
			}
		},

		removeValue(i, id) {
			if(this.ask_before_delete != '') {
				if(!confirm(this.ask_before_delete)) return;
			}

			let v = this.valueList[i];
			// console.log(v);
			if(v.id == 0 && v.type == 0 && v.name == 'Все') this.selected_all = false;

			this.valueList.splice(i, 1);

			this.filtered_options.filter((option, index) => {
				if(option.id === id){
					this.filtered_options[index].selected = false;
					this.filtered_options[index].shownProfile = false;
				}
			});
			this.$emit('remove');

			// let index = this.filtered_options.findIndex(o => v.id == o.id && v.type == o.type);
			// if(index != -1) {
			// this.filtered_options.splice(index, 1);
			// this.$emit('remove');
			// }
		},

		removeValueFromList(i) {
			let fo = this.filtered_options[i];
			let index = this.valueList.findIndex(v => v.id == fo.id && v.type == fo.type);
			if(index != -1) {
				this.valueList.splice(index, 1);
				fo.selected = false;
			}
		},

		onSearch() {

			if(this.searchText == '') {
				this.filtered_options = this.options;
			} else {
				this.filtered_options = this.options.filter(el => {
					return el.name.toLowerCase().indexOf(this.searchText.toLowerCase()) > -1
				});
			}

			this.addSelectedAttr();
		},

		close() {
			this.show = false;
		},

		fetch() {
			this.axios
				.get('/superselect/get', {})
				.then(response => {
					if(this.disable_type){
						response.data.options.forEach(item =>{
							if(this.disable_type !== item.type){
								this.options.push(item);
							}
						})
					} else{
						this.options = response.data.options;
					}
					this.first_time = false;
					this.filterType();
					this.addSelectedAttr();
				})
				.catch((error) => {
					alert(error,'111');
				});
		},

		selectAll() {
			if(this.selected_all) return;
			this.valueList.splice(0, this.valueList.length);
			this.valueList.push({
				name: 'Все',
				id: 0,
				type: 0
			});
			this.show = false;
			this.selected_all = true;

		}
	},

}
</script>
<style scoped lang="scss">
    .placeholder{
       color: rgba(0,0,0,0.5);
        background-color: transparent!important;
    }
    .options{
        .shown-profile{
            background-color: #f2f2f2;
            color: #999;
            position: relative;
            cursor: default;
            i{
                opacity: 0.3;
            }
            &:before{
                content: 'Уже создан';
                position: absolute;
                top: 10px;
                right: 5px;
                font-size: 12px;
                color: #666;
                font-weight: 600;
            }
            &:hover{
                background-color: #f2f2f2;
                cursor: default;
            }
        }
    }
</style>
