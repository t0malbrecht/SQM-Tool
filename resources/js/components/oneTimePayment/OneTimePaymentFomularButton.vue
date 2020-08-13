<template>
    <div>
        <b-button variant="outline-primary" @click="showModal">Formular erzeugen</b-button>
    <b-modal size="lg" id="select-formular" title="Formular auswählen">
        <b-button variant="outline-primary" @click="tranferFormular">Antrag auf Budgetumbuchung</b-button>
        <b-button variant="outline-primary" @click="bskFormular">Beschluss der Studienkommission</b-button>
        <b-button :hidden="vnFormularButtonHidden" variant="outline-primary" @click="vnFormular">Verwendungsnachweis</b-button>
        <template v-slot:modal-footer="">
            <b></b>
        </template>
    </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['id'],
        data() {
            return {
                oneTimePayment: null,
            }
        },
        methods: {
            tranferFormular() {
                window.open('/oneTimePayment/createTransferFormular/' + this.id);
            },
            bskFormular(){
                window.open('/oneTimePayment/createBskFormular/' + this.id);
            },
            vnFormular(){
                window.open('/oneTimePayment/createVnFormular/' + this.id);
            },
            showModal() {
                this.$bvModal.show('select-formular');
            },
            closeModal() {
                this.$bvModal.hide('select-formular');
            }
        }
    }

</script>
