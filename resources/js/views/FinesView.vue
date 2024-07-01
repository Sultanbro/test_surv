<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'

export default {
	name: 'FinesView',
	components: {
		DefaultLayout,
	},
	data(){
		return {
			fines: [],
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/fines').then(data => {
			this.fines = data.fines || []
		}).catch(error => {
			console.error('useAsyncPageData', error)
		});
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<div class="container p-4">
				<div class="card p-3 content">
					<h5 class="mb-3">
						<strong>Система депремирования&nbsp;</strong>
					</h5>
					<table
						cellspacing="0"
						cellpadding="0"
						class="table table-striped"
					>
						<tbody>
							<tr
								v-for="fine in fines"
								:key="fine.name"
							>
								<td class="pr-5 text-left">
									<span><strong>{{ fine.name }}</strong></span>
								</td>
								<td
									class="p-3 text-right primary"
									style="background: #dc354573;font-weight: 700;"
								>
									- {{ fine.penalty_amount }} тенге
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style scoped>
ul,
ol {
    padding-left: 30px;
}

.content p {
    color: #000;
}

.content a {
    color: #007bff;
    text-decoration: dashed;
}

.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 7rem !important;
    }
}
</style>
