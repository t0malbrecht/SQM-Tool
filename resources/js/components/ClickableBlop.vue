<template>
    <div class="col-2 border rounded text-center" @click="openLink">
            <div><img :src=this.newIcon style="height: 70px;" class=""></div>
            <div class="pt-1" v-text="this.title"></div>
    </div>
</template>


<script>
    export default {
        props: ['icon', 'link', 'title'],
        data() {
            return {
                newIcon: []
            }
        },
        created() {
            this.newIcon = '/svg/'+this.icon+'.svg'
        },
        methods: {
            openLink() {
                window.location = '/'+this.link
            }
        }
    }
</script>
