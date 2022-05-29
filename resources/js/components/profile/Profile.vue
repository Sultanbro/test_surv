<template>
    <div class="profile-buttons">
        <a href="/"
            class="email d-flex align-items-center mr-2"
            title="Мой профиль"
            >
            
            <div class="d-flex align-items-center flex-column u-2">
                <div class="u-email">{{ u.email }}</div>
                <div class="u-name">{{ u.last_name + ' ' + u.name }}</div>        
            </div>
        </a>
        <a class="nav-link exit" title="Выход" href="/logout">
            <img src="https://cp.u-marketing.org/static/new/img/ico-exit.png" />
        </a>


    </div>
</template>

<script>
import 'ant-design-vue/dist/antd.css'
export default {
    name: "Profile",
    props: ['user'],
    data() {
        return {
            u: {},
        }
    },
    mounted() {
        this.u = JSON.parse(this.user)
    },
    methods: {

    }
}
</script>

<style lang="scss" scoped>
.email:hover {
    cursor: pointer;
    color: rgb(22, 61, 190);
}
.profile-buttons {
    display: flex;
    align-items: center;
    .photo {
        width: 32px;
        height: 32px;
        margin-right: 5px;
        border-radius: 50%;
    }
}
.exit {
    display: flex;
    width: 32px;
    background: #ededed;
    height: 32px;
    padding: 8px;
    border-radius: 50%;
    text-align: center;
    margin-left: 5px;
    &:hover {
        background: #ccd4eb;   
    }
    img {
        width: 100%;
    }
}
.flex-column {
    flex-direction: column;
}
.u-email {
    font-size: 14px;
}
.u-name {
    font-size: 11px;
    text-align: center;
}
.relative{
    position: relative;
}
i.fa {
    position: absolute;
    top: 13px;
    right: 27px;
    cursor: pointer;
}
.u-2 {
        width: 200px;
}


</style>
