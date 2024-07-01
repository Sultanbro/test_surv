<script>
export default {
	name: 'Section1Popup',
	props: {
		lang: {
			type: String,
			default: ''
		},
		type: {
			type: String,
			default: ''
		},
	},
	data(){
		return {}
	},
	computed: {
		maintext(){
			if(!this.type) return ''
			return this.$lang(this.lang, `s1-${this.type}-main`)
		},
		employeetext(){
			if(!this.type) return ''
			return this.$lang(this.lang, `s1-${this.type}-employee`)
		},
		employertext(){
			if(!this.type) return ''
			return this.$lang(this.lang, `s1-${this.type}-employer`)
		},
	}
}
</script>

<template>
	<div class="Section1Popup">
		<div class="Section1Popup-maintext">
			{{ maintext }}
		</div>
		<div class="Section1Popup-subitems">
			<div class="Section1Popup-subitem Section1Popup-employee">
				<h4 class="Section1Popup-subtitle">
					{{ $lang(lang, 's1-for-worker') }}
				</h4>
				<p class="Section1Popup-subtext">
					{{ employeetext }}
				</p>
			</div>
			<div class="Section1Popup-subitem Section1Popup-employer">
				<h4 class="Section1Popup-subtitle">
					{{ $lang(lang, 's1-for-super') }}
				</h4>
				<p class="Section1Popup-subtext">
					{{ employertext }}
				</p>
			</div>
		</div>
	</div>
</template>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';
.Section1Popup{
    width: 100%;
    padding: 1rem;
    border-radius: 1rem;

    position: absolute;
    z-index: 100;

    background: #fff;
    box-shadow: 0px 0px 1rem rgba(#000, 0.07);
}
.Section1Popup-maintext{
    margin-bottom: 2rem;

    font-style: normal;
    font-weight: 400;
    font-size: 1.125rem;
    line-height: 1.875rem;

    text-align: center;
}
.Section1Popup-subitems{
    display: flex;
    flex-flow: column;
    gap: 2rem;
}
.Section1Popup-subitem{
    flex: 50% 1 1;
}
.Section1Popup-subtitle{
    font-style: normal;
    font-weight: 400;
    font-size: 1.25rem;
    line-height: 1.5625rem;

    color: #42B1F4;
}
.Section1Popup-subtext{
    font-style: normal;
    font-weight: 400;
    font-size: 1rem;
    line-height: 1.875rem;

  &:before {
    content: '';
    display: inline-block;
    width: 0.5rem;
    height: 0.75rem;
    margin-right: 0.5625rem;
    transform: matrix(0.83, 0.56, 0.56, -0.83, 0, 0);
    background-image: url("../../assets/img/s1-dot.svg");
    background-size: cover;
  }
}
@media screen and (min-width: $medium) {
    .Section1Popup{
        padding: 3rem 6rem;
    }
    .Section1Popup-subitems{
        flex-flow: row nowrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 6rem;
    }
}
</style>
