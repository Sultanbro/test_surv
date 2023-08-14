<template>
	<li>
		<span :value="cat.id"><i
				class="fa fa-folder"
				aria-hidden="true"
			/>
			{{ cat.name }}
			<i
				class="fa fa-plus-circle"
				title="новая книга"
				aria-hidden="true"
				@click="addbookk(cat)"
			>Добавить</i>

			<!-- <i @click="deletebook(cat,indexcat)" class="fa fa-trash-o" aria-hidden="true" ></i>
<i @click="addbook(cat)" title="Добавить подкатегорию" class="fa fa-plus-circle" aria-hidden="true">Добавить</i>
<i @click="addbookk(cat)" class="fa fa-plus-circle" title="новая книга" aria-hidden="true">Книга</i> -->

		</span>
		<template v-if="cat.categoryes">
			<ul>
				<template v-for="(cats, index) in cat.categoryes">
					<selectgroup
						:key="index"
						:cat="cats"
						:indexcat="index"
						@addbook="addbook"
						@deletebook="deletebook"
						@addbookk="addbookk"
					/>

					<!--  <ul v-if="cats.books"" class="bookendlist" v-for="book in cats.books">
                         <li> <i class="fa fa-book" aria-hidden="true"></i> {{book.title}}</li>
                      </ul> -->
				</template>
			</ul>
		</template>
	</li>
</template>

<script>
export default {
	name: 'SelectGroupBook',
	props: {
		cat: {
			type: Object,
			required: true
		},
		indexcat: {
			type: Number,
			default: 0
		}
	},
	methods: {
		addbook(cat) {
			this.$emit('addbook', cat)
		},
		deletebook(cat, index) {
			this.$emit('deletebook', cat, index)
		},

		addbookk(cat) {
			this.$emit('addbookk', cat)
		}
	}

}
</script>

<style scoped>
    ul {
        margin-left: 20px;
    }

</style>
