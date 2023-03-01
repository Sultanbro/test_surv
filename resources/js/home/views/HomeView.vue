<template>
	<div id="HomeView">
		<Navbar />
		<Section1 />
		<Section2 />
		<Reviews />
		<Tariffs />
		<Section3 />
		<Section4 />
		<SectionFooter />
	</div>
</template>

<script>
import Navbar from '../components/sections/Navbar.vue'
import Section1 from '../components/sections/Section1.vue'
import Section2 from '../components/sections/Section2.vue'
import Section3 from '../components/sections/Section3.vue'
import Section4 from '../components/sections/Section4.vue'
import Reviews from '../components/sections/Reviews.vue'
import Tariffs from '../components/sections/Tariffs.vue'
import SectionFooter from '../components/sections/Footer.vue'

export default {
	name: 'HomeView',
	components: {
		Navbar,
		Section1,
		Section2,
		Section3,
		Section4,
		Reviews,
		Tariffs,
		SectionFooter
	}
}
</script>
