<template>
  <div id="app">
    <Navbar/>
    <Section1/>
    <Section2/>
    <Reviews/>
    <Tariffs/>
    <Section3/>
    <Section4/>
    <Footer/>
  </div>
</template>

<script>
import Navbar from './components/sections/Navbar.vue'
import Section1 from './components/sections/Section1.vue'
import Section2 from './components/sections/Section2.vue'
import Section3 from './components/sections/Section3.vue'
import Section4 from './components/sections/Section4.vue'
import Reviews from './components/sections/Reviews.vue'
import Tariffs from './components/sections/Tariffs.vue'
import Footer from './components/sections/Footer.vue'

export default {
  name: 'app',
  components: {
    Navbar,
    Section1,
    Section2,
    Section3,
    Section4,
    Reviews,
    Tariffs,
    Footer
  }
}
</script>
<style lang="scss">
@import 'assets/scss/app.variables.scss';
@import 'assets/scss/app.global.scss';
</style>
