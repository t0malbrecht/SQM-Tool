<template>
    <div>
    <img src="/svg/notes.svg" style="height: 20px;" class="pl-2" @click="edit">
    <b-modal size="lg" id="create-vn" title="Verwendungsnachweis erstellen">
        <proofofuse-create-formular @closeModal="closeModal" @updatePage="updatePage" v-bind:payment="payment1"></proofofuse-create-formular>
        <template v-slot:modal-footer="">
            <b></b>
        </template>
    </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['payment1'],
        data() {
            return {
                proofOfUse: null,
            }
        },
        created() {
            let promise = axios.get('/proofOfUses/get?find=' + this.payment1.unique_payment_id);
            return promise.then(response => {
                if(response.data.length === 0){
                    console.log(response.data.length)
                }else{
                    this.proofOfUse = response.data
                }
            }).catch(errors => {
            });
        },
        methods: {
            edit() {
                if(this.proofOfUse == null){
                    this.$bvModal.show('create-vn');
                }else{
                    window.location = '/proofOfUse/'+this.proofOfUse.id
                }
            },
            closeModal() {
                this.$bvModal.hide('create-vn');
            },
            updatePage(){
                window.location.reload()
            }
        }
    }

</script>
