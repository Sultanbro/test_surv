<template>
	<div class="StructureInfo additional-info">
		<slot>
			<JobtronAvatar
				:image="info.avatar ? info.avatar : '/user.png'"
				:title="`${info.name || ''} ${info.last_name || ''}`"
				:size="info.avatarSize"
				class="StructureInfo-avatar"
			/>
			<div class="StructureInfo-content addi-content">
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
		</slot>
	</div>
</template>

<script>
import JobtronAvatar from '@/components/ui/Avatar.vue';

export default {
	name: 'StructureInfo',
	components: {
		JobtronAvatar,
	},
	props: {
		info: {
			type: Object,
			default: () => ({})
		}
	}
}
</script>

<style lang="scss">
.StructureInfo{
	&-avatar{
		flex: 0 0 auto;
	}
	&-content{
		flex: 1;
	}
}
</style>
