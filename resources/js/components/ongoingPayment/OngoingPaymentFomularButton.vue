<template>
    <div>
        <b-button variant="outline-primary" @click="showModal">Formular erzeugen</b-button>
        <b-modal size="lg" id="select-formular" title="Formular auswählen">
            <b-button :hidden="tranferFormularButtonHidden" variant="outline-primary" @click="tranferFormular">Antrag auf Budgetumbuchung</b-button>
            <b-button :hidden="bskFormularButtonHidden" variant="outline-primary" @click="bskFormular">Beschluss der Studienkommission</b-button>
        <div :hidden="datePickerHidden">
            <b-form @submit.prevent="onSubmit" v-if="true">
                <b-form-group id="input-group-1" label="Von:" label-for="meeting_id">
                    <b-form-datepicker
                        :required=true
                        v-model="startDate"
                        id="startDate"
                        :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                        locale="de"
                    ></b-form-datepicker>
                    <div v-if="this.errors && this.errors.meeting_id" class="text-danger">{{ this.errors.meeting_id[0] }}</div>
                </b-form-group>
                <b-form-group id="input-group-2" label="Bis:" label-for="endDate">
                    <b-form-datepicker
                        :required=true
                        v-model="endDate"
                        id="endDate"
                        :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                        locale="de"
                    ></b-form-datepicker>
                    <div v-if="this.errors && this.errors.meeting_id" class="text-danger">{{ this.errors.meeting_id[0] }}</div>
                </b-form-group>

                <b-button type="submit" variant="primary">Formular erzeugen</b-button>
            </b-form>
        </div>
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
                transfer: false,
                startDate: null,
                endDate: null,
                oneTimePayment: null,
                datePickerHidden: true,
                tranferFormularButtonHidden: false,
                bskFormularButtonHidden: false,
            }
        },
        mounted() {
            this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
                this.transfer = false;
                this.startDate = null;
                    this.endDate = null;
                    this.oneTimePayment = null;
                    this.datePickerHidden = true;
                    this.tranferFormularButtonHidden = false;
                    this.bskFormularButtonHidden = false;
            })
        },
        methods: {
            tranferFormular() {
                this.datePickerHidden = false;
                this.tranferFormularButtonHidden = true;
                this.bskFormularButtonHidden = true;
                this.transfer = true;
            },
            bskFormular() {
                this.datePickerHidden = false;
                this.tranferFormularButtonHidden = true;
                this.bskFormularButtonHidden = true;
                this.transfer = false;
            },
            onSubmit(){
                if(this.transfer){
                    window.open('/ongoingPayment/createTransferFormular?id=' + this.id + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                }else{
                    window.open('/ongoingPayment/createBskFormular?id=' + this.id + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                }
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
