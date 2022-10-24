<template>
<div class="popup__con">
    ????????
</div>
</template>

<script>
export default {
    name: "MailPopup", 
    props: {},
    data: function () {
        return {
            data: []
        };
    },
    created(){
        // this.fetchData()
    },
    methods: {
        fetchData() {
            let loader = this.$loading.show();
            
            axios.post("/checklist", {})
                .then((response) => {
                    this.data = response.data
                    loader.hide();
                });
        },
    }
};
</script>