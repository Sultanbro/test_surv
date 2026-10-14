<template>
	<div
		v-if="image && !imageError"
		:class="{
			'messenger__avatar': true,
			'messenger__avatar--small': inline,
		}"
	>
		<img
			:src="image"
			:alt="title"
			@error="imageError = true"
		>
		<span class="messenger_tooltip">{{ title }}</span>
	</div>
	<div
		v-else
		:class="{
			'messenger__avatar_container': !inline,
			'messenger__avatar_container--small': inline,
		}"
	>
		<div
			class="messenger__avatar_name"
		>
			{{ title[0] }}
		</div>
	</div>
</template>

<script>
export default {
	props: {
		image: {
			type: String,
			required: false,
			default: null
		},
		title: {
			type: String,
			required: true,
		},
		inline: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			imageError: false,
		};
	},
	watch: {
		imageError() {
			console.error('imageError', this.image, this.title);
		},
	},
}
</script>

<style scoped>
.messenger__avatar {
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  background-color: #ddd;
  height: 50px;
  width: 50px;
  min-height: 50px;
  min-width: 50px;
  margin-right: 10px;
  border-radius: 50%;
}

.messenger__avatar--small {
  display: inline-block;
  height: 25px;
  width: 25px;
  min-height: 25px;
  min-width: 25px;
  margin-right: 0;
}

.messenger__avatar img {
  height: 100%;
  width: 100%;
  border-radius: 50%;
}

.messenger__avatar_container {
  border-radius: 50%;
  height: 50px;
  width: 50px;
  min-height: 50px;
  min-width: 50px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #7ccaed;
  color: #fff;
  margin-right: 10px;
}

.messenger__avatar_container--small {
  display: block;
  height: 25px;
  width: 25px;
  min-height: 25px;
  min-width: 25px;
  border-radius: 50%;
  background-color: #7ccaed;
  color: #fff;
}

.messenger__avatar_container .messenger__avatar_name {
  font-size: 17px;
  font-weight: 700;
  line-height: 22px;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

.messenger__avatar_container--small .messenger__avatar_name {
  font-size: 12px;
  font-weight: 700;
  line-height: 25px;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
}

.messenger_tooltip {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.messenger__avatar:hover .messenger_tooltip {
  visibility: visible;
}

</style>
