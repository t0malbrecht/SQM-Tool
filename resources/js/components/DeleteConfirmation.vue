<template>
    <div>
        Möchten Sie das Objekt wirklich löschen?
        <div class="row d-flex pl-3 pt-3">
            <b-button type="button" variant="primary" class="mr-auto ml-0" @click.prevent="yes">Ja</b-button>
            <b-button type="button" variant="primary" class="ml-auto mr-3" @click.prevent="no">Nein</b-button>
        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return {

            }
        },
        mounted(){
            this.$emit('closeModal');
        },
        methods: {
            no() {
                this.$emit('closeModal');
            },
            yes() {
                this.$emit('closeModal');
                this.$emit('delete');
            }
        }
    }
</script>
