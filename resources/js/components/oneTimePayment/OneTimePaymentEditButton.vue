<template>
    <div>
    <img src="/svg/edit.svg" style="height: 20px;" class="pl-2" @click="edit">
    <b-modal size="lg" id="edit-payment" title="Zahlung bearbeiten">
        <onetimepayment-edit-formular @closeModal="closeModal" v-bind:payment="payment"></onetimepayment-edit-formular>
        <template v-slot:modal-footer="">
            <b></b>
        </template>
    </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['onetimepayment'],
        data() {
            return {
                payment: null,
            }
        },
        methods: {
            edit(item) {
                let promise = axios.get('/oneTimePayments/get?find=' + this.onetimepayment)
                return promise.then(response => {
                    this.payment = response.data || [];
                    this.$bvModal.show('edit-payment');
                })
                    .catch(errors => {
                        console.log(errors)
                    });
            },
            closeModal() {
                this.$bvModal.hide('edit-payment');
            }
        }
    }

</script>
