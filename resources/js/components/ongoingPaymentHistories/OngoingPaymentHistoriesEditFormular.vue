<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <div class="row d-flex">
                <div class="col-6">
                    <b-form-group
                        id="input-group-1"
                        label="Geplantes Bezahldatum:"
                        label-for="plannedPaymentDate"
                    >
                        <b-form-datepicker
                            v-model="form.plannedPaymentDate"
                            id="dueDate"
                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                            locale="de"
                            :disabled="true"
                        ></b-form-datepicker>
                        <div v-if="this.errors && this.errors.plannedPaymentDate" class="text-danger">{{ this.errors.plannedPaymentDate[0]
                            }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group
                        id="input-group-9"
                        label="Tatsächliches Bezahldatum"
                        label-for="realPaymentDate"
                    >
                        <b-form-datepicker
                            v-model="form.realPaymentDate"
                            reset-button
                            id="spentDate"
                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                            locale="de"
                        ></b-form-datepicker>
                        <div v-if="this.errors && this.errors.realPaymentDate" class="text-danger">{{ this.errors.realPaymentDate[0]
                            }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <div class="row d-flex">
                <div class="col-6">
                    <b-form-group id="input-group-2" label="gewährter Betrag" label-for="grantedFunds">
                        <b-form-input
                            id="grantedFunds"
                            type="number"
                            v-model="form.grantedFunds"
                            :disabled="true"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.grantedFunds" class="text-danger">{{
                            this.errors.grantedFunds[0] }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group id="input-group-10" label="verausgabter Betrag" label-for="spentFunds">
                        <b-form-input
                            id="spentFunds"
                            type="number"
                            v-model="form.spentFunds"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.spentFunds" class="text-danger">{{
                            this.errors.spentFunds[0] }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <div class="row d-flex pl-3 pt-3">
                <b-button type="submit" variant="primary" class="mr-auto ml-0">Speichern</b-button>
                <b-button type="button" variant="primary" class="ml-auto mr-3" @click.prevent="showDeleteDialog">Löschen</b-button>
            </div>
        </b-form>
    <hr/>
    <div class="text-center">
        <div v-if="this.otherError" class="text-danger pl-5">{{ this.otherError }}</div>
    </div>
    <b-modal id="delete-dialog" size="lg" title="Überprüfung">
        <delete-confirmation @closeModal="hideDeleteDialog" @delete="deleteItem"></delete-confirmation>
        <template v-slot:modal-footer="">
            <b></b>
        </template>
    </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['ongoingPaymentHistory'],
        data() {
            return {
                form: {},
                errors: {},
                otherError: '',
                success: false,
                loaded: true,
                claim_options: [],
                funds_options: [],
                costType_options: [],
            }
        },
        created() {
            this.form.grantedFunds = this.ongoingPaymentHistory.grantedFunds;
            this.form.spentFunds =  this.ongoingPaymentHistory.spentFunds;
            this.form.plannedPaymentDate = this.ongoingPaymentHistory.plannedPaymentDate;
            this.form.realPaymentDate = this.ongoingPaymentHistory.realPaymentDate;
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/ongoingPaymentHistory/'+this.ongoingPaymentHistory.id, this.form)
                        .then(response => {
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            this.$root.$emit('bv::refresh::table', 't1');
                            this.$emit('closeModal');
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status == 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                        });
                }
            },
            showDeleteDialog(){
                this.$bvModal.show('delete-dialog');
            },
            hideDeleteDialog(){
                this.$bvModal.hide('delete-dialog');
            },
            deleteItem(){
                axios.delete('/ongoingPaymentHistory/'+this.ongoingPaymentHistory.id) .then(response => {
                    this.$emit('closeModal');
                    this.$root.$emit('bv::refresh::table', 't1');
                })
                    .catch(errors => {
                        this.loaded = true;
                        if (errors.response.status == 401) {
                            window.location = '/login';
                        }
                        if (errors.response.status == 423) {
                            this.otherError = "Datenbankfehler";
                        }
                    });
            }
        }
    }
</script>


