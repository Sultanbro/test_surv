<script>
export default {
	name: 'UserEditDocuments',
	props: {
		user: {
			type: Object,
			default: null
		},
	},
	data(){
		return{
			files: [0,0,0,0,0,0]
		}
	},
	methods: {
		hasFile(idx) {
			this.$set(this.files, idx, 1)
		}
	}
}
</script>
<template>
	<div
		id="iphones4"
		class="xtab-pane xfade none-block"
	>
		<div class="row file-uploads-container">
			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-5"
						name="file5"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(0)"
					>
					<template v-if="(user && user.downloads && user.downloads.ud_lich) || files[0] === 1">
						<label
							class="active"
							for="file-5"
						>
							<i class="fa fa-check" />
							<span>Удостоверение личности</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.ud_lich"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}/ud_lich/${user.downloads.ud_lich}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-5"
					>
						<i class="fa fa-upload" />
						<span>Удостоверение личности</span>
					</label>
				</div>
			</div>
			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-1"
						name="file1"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(1)"
					>
					<template v-if="(user && user.downloads && user.downloads.dog_okaz_usl) || files[1] === 1">
						<label
							for="file-1"
							class="active"
						>
							<i class="fa fa-check" />
							<span>Договор оказания услуг</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.dog_okaz_usl"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}/dog_okaz_usl/${user.downloads.dog_okaz_usl}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-1"
					>
						<i class="fa fa-upload" />
						<span>Договор оказания услуг</span>
					</label>
				</div>
			</div>
			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-2"
						name="file2"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(2)"
					>
					<template v-if="(user && user.downloads && user.downloads.sohr_kom_tainy) || files[2] === 1">
						<label
							for="file-2"
							class="active"
						>
							<i class="fa fa-check" />
							<span>Сохранение комм. тайны</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.sohr_kom_tainy"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}}/sohr_kom_tainy/${user.downloads.sohr_kom_tainy}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-2"
					>
						<i class="fa fa-upload" />
						<span>Сохранение комм. тайны</span>
					</label>
				</div>
			</div>
			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-3"
						name="file3"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(3)"
					>
					<template v-if="(user && user.downloads && user.downloads.dog_o_nekonk) || files[3] === 1">
						<label
							for="file-3"
							class="active"
						>
							<i class="fa fa-check" />
							<span>Договор о неконкуренции</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.dog_o_nekonk"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}/dog_o_nekonk/${user.downloads.dog_o_nekonk}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-3"
					>
						<i class="fa fa-upload" />
						<span>Договор о неконкуренции</span>
					</label>
				</div>
			</div>
			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-4"
						name="file4"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(4)"
					>
					<template v-if="(user && user.downloads && user.downloads.trud_dog) || files[4] === 1">
						<label
							for="file-4"
							class="active"
						>
							<i class="fa fa-check" />
							<span>Трудовой <br> договор</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.trud_dog"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}/trud_dog/${user.downloads.trud_dog}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-4"
					>
						<i class="fa fa-upload" />
						<span>Трудовой <br> договор</span>
					</label>
				</div>
			</div>

			<div class="col-12 col-md-4 mb-4">
				<div class="file-item">
					<input
						id="file-7"
						name="file7"
						type="file"
						class="inputfile inputfile-1 d-none"
						data-multiple-caption="{count} files selected"
						@change="hasFile(5)"
					>
					<template v-if="(user && user.downloads && user.downloads.archive) || files[5] === 1">
						<label
							for="file-7"
							class="active"
						>
							<i class="fa fa-check" />
							<span>Архив <br>документов</span>
						</label>
						<a
							v-if="user && user.downloads && user.downloads.archive"
							download
							class="btn btn-success btn-block"
							:href="`/static/profiles/${user.id}/archive/${user.downloads.archive}`"
						>Скачать</a>
					</template>
					<label
						v-else
						for="file-7"
					>
						<i class="fa fa-upload" />
						<span>Архив <br> документов</span>
					</label>
				</div>
			</div>
			<!-- может лишний -->
			<input
				id="file-6"
				name="logo"
				type="file"
				accept="image/*,.pdf"
				class="inputfile inputfile-1"
				data-multiple-caption="{count} files selected"
				style="display: none;"
			>
			<!-- -------- -->
		</div>
	</div>
</template>
