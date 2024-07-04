<template>
	<main class="article-news">
		<div
			class="article-news__wrapper"
			@click="navigate('1')"
		>
			<div class="article-news__card">
				<div class="article-news__image">
					<img
						src="/images/bg-login.jpg"
						alt=""
					>
				</div>
				<div class="article-news__content">
					<h2 class="article-news__title">
						Статья 1
					</h2>
					<p class="article-news__subtitle">
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus
						modi, molestiae iste dignissimos odio et voluptatum accusamus eaque
						a! Perferendis, dolore facere minus natus laborum praesentium esse
						enim ab ipsa.
					</p>
					<button class="article-news__button">
						Читать дальше
					</button>
					<div class="article-news__date">
						03.08.2003
					</div>
				</div>
			</div>
		</div>
	</main>
</template>

<script>
export default {
	name: 'ArticleView',
	data() {
		return {};
	},
	mounted() {
		this.$nextTick(() => {
			this.setMetaViewport();
		});
	},
	methods: {
		setMetaViewport() {
			const meta = document.createElement('meta');
			meta.setAttribute('name', 'viewport');
			meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
			document.head.appendChild(meta);
		},
		navigate(id) {
			this.$router.push({
				name: 'Article',
				params: { id },
			});
		},
	},
};
</script>

<style scoped lang="scss">
.article-news {
	font-size: 16px;
	&__wrapper {
		cursor: pointer;
		transition: all ease 100ms;
		&:hover {
			background-color: #54c9ff4c;
		}
		.article-news__card {
			max-width: 1200px;
			margin: 0 auto;
			padding: 1%;
			display: flex;
			align-items: center;
			gap: 4%;
			margin-top: 1%;

			.article-news__image {
				img {
					width: 200px;
					height: 200px;
				}
			}
			.article-news__content {
				.article-news__title {
					font-size: 18px;
					color: #205899;
					font-weight: bold;
				}

				.article-news__subtitle {
					line-height: 34px;
					margin-top: 2%;
				}

				.article-news__button {
					background-color: #00aeff;
					color: white;
					border-radius: 30px;
					padding: 1% 2%;
					margin-top: 2%;
					outline: none;
				}

				.article-news__date {
					margin-top: 3%;
					font-weight: bold;
				}
			}
		}
	}
}

@media (max-width: 890px) {
	.article-news {
		font-size: 12px;
		&__wrapper {
			.article-news__card {
				display: flex;
				align-items: center;
				.article-news__image {
					img {
						width: 90px;
						height: 90px;
					}
				}
				.article-news__content {
					.article-news__title {
					}

					.article-news__subtitle {
						line-height: 17px;
					}

					.article-news__button {
					}

					.article-news__date {
					}
				}
			}
		}
	}
}
</style>
