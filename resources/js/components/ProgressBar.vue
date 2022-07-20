<template>
<div class="progress-bar mb-1">
    <div class="background-bar">
        <div class="info">
            <label>{{label}}</label>
            <label class="percentage">{{percentage}}%   </label>
        </div>
    </div>
    <transition appear @before-appear="beforeEnter" @after-appear="enter" >
        <div class="tracker-bar" id="mytrans"></div>
    </transition>
</div>
</template>

<script>
export default {
    name: "ProgressBar",
    props: {
        percentage: Number,
        label: String,
    },
    watch: {
        // whenever question changes, this function will run
        percentage: {
            handler (val, oldVal) {
                var trans = document.getElementById("mytrans");
                trans.style.width = `${val}%`;
                trans.style.transition = `width 0.3s linear`;
            }
        }
      },
    methods: {
        beforeEnter (el) {
            el.style.width = 40;
        },
        enter (el) {
            el.style.width = `${this.percentage}%`
            el.style.transition = `width 1s linear`
        }
    }
}
</script>
