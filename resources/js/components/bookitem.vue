<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<div>
		<a
			href="#"
			class="chapter"
			@click.prevent="opener(), active(tre)"
		>
			<i
				v-if="open == true"
				class="fa fa-folder-open"
				aria-hidden="true"
			/>
			<i
				v-if="open == false"
				class="fa fa-folder"
				aria-hidden="true"
			/>
			{{ tre.name }}
		</a>

		<i
			class="fa fa-file"
			aria-hidden="true"
			@click.prevent="bookshow"
		/>
		<i
			class="fa fa-plus"
			aria-hidden="true"
			@click.prevent="bookshowbook"
		/>
		<i
			title="удалить"
			class="fa fa-trash-o"
			aria-hidden="true"
			@click="deletecat(tre)"
		/>
		<i
			title="переименовать"
			class="fa fa-pencil"
			aria-hidden="true"
			@click.prevent="namefile(tre)"
		/>
		<i
			title="переместить"
			class="fa fa-long-arrow-right"
			aria-hidden="true"
			@click.prevent="moveto(tre)"
		/>


		<i
			aria-hidden="true"
			class="fa fa-arrows-v"
		/>



		<ul v-if="open == true">
			<template v-if="books">
				<draggable
					v-model="books"
					:options="{handle:'.fa-arrows-v'}"
					@end="onEndSort(books,tre.id)"
				>
					<template v-for="book in books">
						<li
							v-if="book.category_id == tre.id"
							:key="book.id"
							class="chapter-item"
						>
							<i
								class="fa fa-arrows-v"
								aria-hidden="true"
							/>
							<a
								href="#"
								@click.prevent="activebook(book)"
							>
								<i
									class="fa fa-file-text"
									aria-hidden="true"
								/>
								{{ book.title }}
							</a>
							<i
								class="fa fa-trash-o"
								aria-hidden="true"
								@click="deletebook(book)"
							/>

							<i
								class="fa fa-pencil"
								aria-hidden="true"
								@click.prevent="renames(book)"
							/>
							<input
								v-if="renamebook"
								ref="bookrename"
								v-model="book.title"
								@keyup.enter="renamebooks(book)"
							>
						</li>
					</template>
				</draggable>
			</template>

			<draggable
				v-model="tree"
				:options="{handle:'.fa-arrows-v'}"
				@end="onEndSortcat(tree)"
			>
				<template v-for="trez in tree">
					<bookitem
						v-if="trez.parent_cat_id == tre.id"
						:key="trez.id"
						:tre="trez"
						:tree="tree"
						:books="books"
						@moveto="moveto"
						@rename="rename"
						@renamebooks="renamebooks"
						@onEndSort="onEndSort"
						@onEndSortcat="onEndSortcat"
						@deletebook="deletebook"
						@active="active"
						@deletecat="deletecat"
						@addcat="addcat"
						@addpage="addpage"
						@activebook="activebook"
					/>
				</template>
			</draggable>
		</ul>

		<input
			v-if="renamefile"
			ref="renamefile"
			v-model="tre.name"
			@keyup.enter="rename(tre)"
		>



		<input
			v-if="showaddbook"
			ref="adddglabook"
			v-model="newbook"
			@keyup.enter="addcat(tre.id,newbook)"
		>

		<input
			v-if="showbk"
			ref="showbk"
			v-model="newpage"
			@keyup.enter="addpage(tre.id,newpage)"
		>
	</div>
</template>

<script>
export default {
	name: 'BookItem',
	props: ['tre', 'tree', 'books'],
	data() {
		return {
			renamebook:false,
			renamefile:false,
			open: false,
			showaddbook: false,
			newbook: 'Новая книга',
			showbk: false,
			newpage: 'Новая страница'
		}
	},
	computed: {},
	mounted() {

	},
	methods: {
		moveto(tre){

			this.$emit('moveto', tre)
		},
		renamebooks(book){
			if(book.name != ''){
				this.$emit('renamebooks', book)
				this.renamebook = false
			} else { alert('Введите название')}
		},
		renames(){
			this.renamebook = !this.renamebook
			setTimeout(()=>{
				this.$refs.bookrename[0].select();
			}, 500);
		},
		rename(tre){
			if(tre.name != ''){
				this.$emit('rename', tre)
				this.renamefile = false
			} else { alert('Введите название')}
		},
		namefile(){
			this.renamefile = !this.renamefile
			setTimeout(()=>{
				this.$refs.renamefile.select();
			}, 500);
		},
		bookshow(){
			this.showbk = !this.showbk

			setTimeout(()=>{
				this.$refs.showbk.select();
			}, 500);

		},
		bookshowbook(){

			this.showaddbook = !this.showaddbook
			setTimeout(()=>{
				this.$refs.adddglabook.select();
			}, 500);

		},
		onEndSortcat(tree){
			this.$emit('onEndSortcat', tree)
		},
		onEndSort(books, id) {

			this.$emit('onEndSort', books, id)
		},
		deletebook(book) {
			this.$emit('deletebook', book)
		},
		deletecat(cat) {
			this.$emit('deletecat', cat)
		},
		addpage(id, name) {
			this.$emit('addpage', id, name)
			this.newpage = 'Новая страница'
			this.showbk = false
			this.open = true
		},
		addcat(id, name) {
			this.$emit('addcat', id, name)
			this.newbook = 'Новая книга'
			this.showaddbook = false
			this.open = true
		},
		opener() {
			this.open = !this.open
		},
		activebook(book) {
			this.$emit('activebook', book)
		},
		active(tre) {
			this.$emit('active', tre)
		}
	}
}
</script>
<style scoped>
    .bookendlist {
        list-style: none;
        margin: 0;
        padding: 0;
        margin-left: 10px;
    }

    .flip-list-move {
        transition: transform 0.2s;
    }

    .no-move {
        transition: transform 0s;
    }

    .white {
        color: white;
    }

    input {
        color: black;
    }

    .bookendlist a {
        display: block;
        padding: 10px 0 10px 20px;
        color: white;
    }

    ul {
        margin-left: 35px;
        list-style: none;
    }

.chapter {

}
.chapter-item {
        padding: 5px 0;

}
</style>
