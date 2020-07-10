<template>
    <div>
        <div class="row">
            <div class="col-3 border rounded text-center offset-3" @click="showOneTimePayment">
                <div><img src="/svg/oneTime.svg" style="height: 30px;" class=""></div>
                <div class="pt-1">Einmalzahlung</div>
            </div>
            <div class="col-3 border rounded text-center" @click="showOngoingPayment">
                <div><img src="/svg/onGoing.svg" style="height: 30px;" class=""></div>
                <div class="pt-1">Mehrmalszahlung</div>
            </div>
        </div>
        <div>
            <onetimepayment-create-formular v-bind:claim="claim" :hidden=oneTimePaymentHidden></onetimepayment-create-formular>
            <ongoingpayment-create-formular v-bind:claim="claim" :hidden=ongoingPaymentHidden></ongoingpayment-create-formular>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['claim'],
        data() {
            return {
                oneTimePaymentHidden: true,
                ongoingPaymentHidden: true,
                oneTimePayment: null,
                ongoingPayment: null,
                payment: null
            }
        },
        mounted(){
            this.$emit('closeModal');
        },
        methods: {
            showOneTimePayment() {
                this.selectionHidden = true;
                this.oneTimePaymentHidden = false;
            },
            showOngoingPayment() {
                this.selectionHidden = true;
                this.ongoingPaymentHidden = false;
            }
        }
    }
</script>
