<template>
	<div class="col-12">
		<div class="row">
			<div class="col-6 mt-5">
				<div style="width: 80%">
					<div
						v-if="showModal"
						style="position: relative;border: 1px solid #dcdcdc"
					>
						<div
							class="gen-role-class"
							style="position: absolute"
						>
							<a
								class="role_1"
								:class="selectedRoleClass.roleClass_1"
								@click="selectedRoles('1')"
								@mouseover="upHere.role_1 = true"
								@mouseleave="upHere.role_1 = false"
							>
								<span
									v-if="upHere.role_1"
									class="spanAction"
								>Роли_1</span>
								<i class="bi bi-card-text role_icon_false" />
							</a>


							<a
								class="role_2"
								:class="selectedRoleClass.roleClass_2"
								@click="selectedRoles('2')"
								@mouseover="upHere.role_2 = true"
								@mouseleave="upHere.role_2 = false"
							>
								<span
									v-if="upHere.role_2"
									class="spanAction"
								>Роли_2</span>
								<i class="bi bi-people role_icon_false" />
							</a>


							<a
								class="role_3"
								:class="selectedRoleClass.roleClass_3"
								@click="selectedRoles('3')"
								@mouseover="upHere.role_3 = true"
								@mouseleave="upHere.role_3 = false"
							>
								<span
									v-if="upHere.role_3"
									class="spanAction"
								>Роли_3</span>
								<i class="bi bi-box-arrow-right role_icon_false" />
							</a>
						</div>


						<div class="popupShowSelected">
							<div v-if="selectedRole.role_1">
								<p
									v-for="(item, index) in options_role_1"
									:key="index"
									class="list-role-1"
								>
									<a
										class="btn btn-block"
										style="display: flex"
									>
										<i class="bi bi-box-arrow-right style-icons" />
										<span style="margin-top: 5px; margin-left:15px;">{{ item.name }}</span>
									</a>
								</p>
							</div>

							<div v-if="selectedRole.role_2">
								<h1>Asdasd_role_2</h1>
								<h1>Asdasd_role_2</h1>
								<h1>Asdasd_role_2</h1>
								<h1>Asdasd_role_2</h1>
								<h1>Asdasd_role_2</h1>
							</div>


							<div v-if="selectedRole.role_3">
								<h1>Asdasd_role_3</h1>
								<h1>Asdasd_role_3</h1>
								<h1>Asdasd_role_3</h1>
								<h1>Asdasd_role_3</h1>
								<h1>Asdasd_role_3</h1>
							</div>
						</div>
					</div>
					<input
						value="asdasd dddd ssss"
						class="form-control"
						@click="toggle()"
					>
				</div>
			</div>

			<div class="col-6">
				<div>
					<div>
						<h1>gggg</h1>
						<label class="typo__label">Tagging</label>
						<multiselect
							v-model="value"
							tag-placeholder="Add this as new tag"
							placeholder="Search or add a tag"
							label="name"
							track-by="code"
							:options="options"
							:multiple="true"
							:taggable="true"
							@tag="addTag"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */

export default {
	name: 'PermissionLocal',
	data() {
		return {
			active: 'google',
			showModal: true,
			upHere : {
				role_1:false,
				role_2:false,
				role_3:false,
			},
			selectedRole:{
				role_1:true,
				role_2:false,
				role_3:false,
			},
			selectedRoleClass:{
				roleClass_1:''  ,
				roleClass_2:'' ,
				roleClass_3:'' ,
			},

			options_role_1: [
				{ name: 'Ответственный', id: '1' },
				{ name: 'Автор', id: '2' },
				{ name: 'Администраторы', id: '3' },
				{ name: 'Создатель лида', id: '4' },
			],
			value: [
				{ name: 'Javascript', code: 'js' }
			],
			options: [
				{ name: 'Vue.js', code: 'vu' },
				{ name: 'Javascript', code: 'js' },
				{ name: 'Open Source', code: 'os' }
			],


		};
	},
	created() {


	},
	methods: {

		selectedRoles(type){

			if (type == 1){
				this.selectedRole.role_1 = true
				this.selectedRole.role_2 = false
				this.selectedRole.role_3 = false



				this.selectedRoleClass.roleClass_1 = 'active_role';
				this.selectedRoleClass.roleClass_2 = false
				this.selectedRoleClass.roleClass_3 = false;



			}else if (type == 2){
				this.selectedRole.role_1 = false
				this.selectedRole.role_2 = true
				this.selectedRole.role_3 = false


				this.selectedRoleClass.roleClass_1 = false;
				this.selectedRoleClass.roleClass_2 = 'active_role';
				this.selectedRoleClass.roleClass_3 = false;

			}else if (type == 3){
				this.selectedRole.role_1 = false
				this.selectedRole.role_2 = false
				this.selectedRole.role_3 = true

				this.selectedRoleClass.roleClass_1 = false;
				this.selectedRoleClass.roleClass_2 = false;
				this.selectedRoleClass.roleClass_3 = 'active_role';
			}


			// this.selectedRole.type = !this.selectedRole.type

		},
		toggle() {
			this.showModal = !this.showModal
		},

		addTag (newTag) {
			const tag = {
				name: newTag,
				code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
			}
			this.options.push(tag)
			this.value.push(tag)
		}

	},
}

</script>

<style scoped>


 .active_role{
     border: 1px solid red;
     background-color: #1a202c;
 }
 .style-icons > span{
   margin-top: 5px;
   margin-left: 15px;
 }

 .style-icons{
     background-color: #e4ebed;
     align-items: center;
     text-align: center;
     margin-left: 0px;
     border-radius: 100%;
     padding-right: 8px;
     padding-left: 10px;
     padding-bottom: 5px;
     /* padding-top: 0px; */
     font-size: 20px
 }


 .list-role-1{
     margin: 0px;
     border: 1px solid white;
 }

 .list-role-1 a:hover{
     background-color: #e0f6fe;
     border: 1px solid white;
 }

 .popupShowSelected{
     max-height:  250px;
     overflow: scroll;
 }

 .role_1{
     padding: 10px 20px 15px 20px;
     background-color: #e4ebed;
     margin-left: -60px;
     border-top-left-radius: 100%;
     border-bottom-left-radius: 100%;
     position: absolute;
     cursor: pointer;

     /*background-color: #2fc6f6;*/

     /*padding: 9px 110px 15px 20px;*/
     /*background-color: #2fc6f6;*/
     /*margin-left: -150px;*/
     /*border-top-left-radius: 30%;*/
     /*border-bottom-left-radius: 30%;*/
     /*position: absolute;*/
     /*cursor: pointer;*/

 }

 .role_2{
     padding: 15px 20px 18px 20px;
     background-color: #e4ebed;
     margin-left: -60px;
     border-top-left-radius: 100%;
     border-bottom-left-radius: 100%;
     top: 75px;
     position: relative;
     cursor: pointer;

 }

 .role_3{
     padding: 15px 20px 18px 20px;
     background-color: #e4ebed;
     margin-left: -60px;
     border-top-left-radius: 100%;
     border-bottom-left-radius: 100%;
     top: 140px;
     position: relative;
     cursor: pointer;

 }

 .role_icon_false{
     font-size: 20px;
     color: #abb1b8;
     font-weight: bold;
 }

 .role_icon_true{
     font-size: 20px;
     color: #ffffff;
     font-weight: bold;
 }

 .gen-role-class a:hover{
     padding: 10px 110px 12px 20px;
     background-color: #2fc6f6;
     margin-left: -150px;
     border-top-left-radius: 30%;
     border-bottom-left-radius: 30%;
     position: absolute;
     cursor: pointer;
 }

 .gen-role-class a:hover i{
     font-size: 20px;
     color: #ffffff;
     font-weight: bold;
 }

 .spanAction{
     position: absolute;
     font-weight: bold;
     font-size: 20px;
     padding-left: 30px;
     color: white;
     top: 10px;
 }




</style>
