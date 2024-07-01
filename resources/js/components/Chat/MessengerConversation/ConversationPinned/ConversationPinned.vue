<template>
	<div
		v-if="pinnedMessage"
		class="messenger__chat-wrapper"
	>
		<div
			class="messenger__info-wrapper messenger__text-ellipsis"
			@click="goto"
		>
			<div class="messenger__pin-body">
				{{ pinnedMessage.body }}
			</div>
			<div
				class="messenger__pin-close"
				@click="unpin"
			>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 512 512"
					width="24"
					height="24"
				>
					<path
						fill="#5f5d5d"
						d="M175 175C184.4 165.7 199.6 165.7 208.1 175L255.1 222.1L303 175C312.4 165.7 327.6 165.7 336.1 175C346.3 184.4 346.3 199.6 336.1 208.1L289.9 255.1L336.1 303C346.3 312.4 346.3 327.6 336.1 336.1C327.6 346.3 312.4 346.3 303 336.1L255.1 289.9L208.1 336.1C199.6 346.3 184.4 346.3 175 336.1C165.7 327.6 165.7 312.4 175 303L222.1 255.1L175 208.1C165.7 199.6 165.7 184.4 175 175V175zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"
					/>
				</svg>
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
	computed: {
		...mapGetters(['chat', 'pinnedMessage'])
	},
	methods: {
		...mapActions(['unpinMessage', 'loadMessages', 'setLoading']),
		goto(event) {
			event.stopPropagation();
			this.setLoading(true);
			this.loadMessages({
				reset: false, goto: this.pinnedMessage.id, callback: () => {
					// after a second
					setTimeout(() => {
						this.setLoading(false);
					}, 1000);
				}
			});
		},
		unpin(event) {
			event.stopPropagation();
			this.unpinMessage();
		}
	}
}
</script>

<style scoped>

.messenger__info-wrapper {
  cursor: pointer;
}

.messenger__pin-body {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.messenger__pin-close {
  margin-left: auto;
  cursor: pointer;
  padding: 10px;
}

</style>
