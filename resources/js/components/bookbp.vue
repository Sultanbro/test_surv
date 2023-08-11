<template>
	<div>
		<ul>
			<li
				v-for="(book, index) in categoryes.book"
				:key="index"
			>
				<a href="javascript:void(0)">{{ book.name }}</a>
			</li>
		</ul>

		<ul>
			<tree-item :item="categoryes" />
		</ul>
	</div>
</template>

<script>


export default {
	name: 'BookBP',
	props: {
		grouid: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			categoryes: {
				book: [
					{
						id: 1,
						name: 'Корневая книга 1',
						desk: 'описание 1',
					},
					{
						id: 2,
						name: 'Корневая книга 2',
						desk: 'описание 2',
					},
					{
						id: 3,
						name: 'Корневая книга 3',
						desk: 'описание 3',
					}
				],
				categoryes: [
					{ name: 'Категория 1', id:1,
						books:[
							{
								id: 1,
								name: 'Корневая книга 12',
								desk: 'описание 12',
							},
							{
								id: 2,
								name: 'Корневая книга 13',
								desk: 'описание 13',
							},
							{
								id: 3,
								name: 'Корневая книга 14',
								desk: 'описание 14',
							}
						]
					},
					{ name: 'Категория 3', id:3,
						books:[
							{
								id: 1,
								name: ' книга 23',
								desk: 'описание 23',
							},
							{
								id: 2,
								name: ' книга 22',
								desk: 'описание 22',
							},
							{
								id: 3,
								name: ' книга 24',
								desk: 'описание 24',
							}
						]
					},

				]
			}
		}
	},
	mounted: function () {
		this.glava = this.grouid;

	},
	methods: {

		activebooks(id, title, desk) {
			this.activebook = id
			this.editors = desk
			this.titlebook = title

		}
	}
}
</script>

<style scoped>
    .add_cat {
        margin-bottom: 20px;

    }

    .listbook {

        list-style: none;
        padding: 0;
        margin: 0;
    }

    .listbook li {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    .job h5 {
        font-size: 18px;
        display: block;
        margin: 15px 0 5px 0;
    }

    .job input, .job select {
        max-width: 350px;
    }
</style>
