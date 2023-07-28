<template>
	<div class="StructureInfo additional-info">
		<img
			:src="info.avatar ? info.avatar : '/user.png'"
			alt="photo"
			class="addi-director-photo"
		>
		<div class="addi-content">
			<div
				v-if="info.name || info.last_name"
				class="addi-fullname"
			>
				{{ info.name || '' }} {{ info.last_name || '' }}
			</div>
			<p
				v-if="info.position"
				class="addi-item"
			>
				<span>Должность: </span> {{ info.position }}
			</p>
			<p
				v-if="info.birthday"
				class="addi-item"
			>
				<span>Дата рождения: </span>{{ $moment(info.birthday).format('DD.MM.YYYY') }}
			</p>
			<p
				v-if="info.phone"
				class="addi-item"
			>
				<span>Телефон: </span> {{ info.phone }}
			</p>
			<p
				v-if="info.email"
				class="addi-item addi-email"
			>
				<span>E-mail: </span> {{ info.email }}
			</p>
		</div>
	</div>
</template>

<script>
export default {
	name: 'StructureInfo',
	components: {},
	props: {
		info: {
			type: Object,
			default: () => ({})
		}
	}
}
</script>

<style>
</style>
