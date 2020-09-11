<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <div class="row d-flex">
                <div class="col-6">
                    <b-form-group
                        id="input-group-1"
                        label="Nutzbar bis:"
                        label-for="dueDate">
                    <b-form-datepicker
                        v-model="form.dueDate"
                        id="startDate"
                        :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                        locale="de"
                    ></b-form-datepicker>
                    <div v-if="this.errors && this.errors.dueDate" class="text-danger">{{ this.errors.dueDate[0]
                        }}
                    </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group
                        id="input-group-9"
                        label="Ausgabedatum"
                        label-for="spentDate"
                    >
                        <b-form-datepicker
                            v-model="form.spentDate"
                            id="spentDate"
                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                            locale="de"
                        ></b-form-datepicker>
                        <div v-if="this.errors && this.errors.spentDate" class="text-danger">{{ this.errors.spentDate[0]
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
            <b-form-group id="input-group-3" label="Antrag" label-for="claim_id">
                <b-form-select v-model="form.claim_id" id="claim_id" :options="claim_options"></b-form-select>
                <div v-if="this.errors && this.errors.claim_id" class="text-danger">{{ this.errors.claim_id[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Begünstigte Finanzstelle" label-for="favoredFundsCenter_id">
                <b-form-select v-model="form.favoredFundsCenter_id" id="favoredFundsCenter_id"
                               :options="funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.favoredFundsCenter_id" class="text-danger">{{
                    this.errors.favoredFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-5" label="Belastete Finanzstelle" label-for="chargedFundsCenter_id">
                <b-form-select v-model="form.chargedFundsCenter_id" id="chargedFundsCenter_id"
                               :options="charged_funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.chargedFundsCenter_id" class="text-danger">{{
                    this.errors.chargedFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-6" label="Kostenart" label-for="costType_id">
                <b-form-select v-model="form.costType_id" id="costType_id"
                               :options="costType_options"></b-form-select>
                <div v-if="this.errors && this.errors.costType_id" class="text-danger">{{
                    this.errors.costType_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-7" label="Notizen" label-for="notes">
                <b-form-textarea
                    id="notes"
                    v-model="form.notes"
                    size="textarea-default"
                ></b-form-textarea>
                <div v-if="this.errors && this.errors.notes" class="text-danger">{{ this.errors.notes[0]
                    }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-8" label="Auflagen" label-for="requirements">
                <b-form-textarea
                    id="requirements"
                    v-model="form.requirements"
                    size="textarea-default"
                ></b-form-textarea>
                <div v-if="this.errors && this.errors.requirements" class="text-danger">{{ this.errors.requirements[0]
                    }}
                </div>
            </b-form-group>
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
        props: ['payment'],
        data() {
            return {
                form: {},
                errors: {},
                otherError: '',
                success: false,
                loaded: true,
                claim_options: [],
                funds_options: [],
                charged_funds_options: [],
                costType_options: [],
            }
        },
        created() {
            axios.get('/fundsCenters/get').then(response => {
                let array = [];
                let i;
                let prof;
                for (i = 0; i < response.data[0].length; i++) {
                    prof = response.data[0][i]['professor']
                    if(prof === null){
                        prof = ''
                    }else{
                        prof = ' - ' + prof
                    }
                    array[i] = {text: response.data[0][i]['fundsCenterNumber']+' - '+response.data[0][i]['description'] + prof, value: response.data[0][i]['id'], disabled: false};
                }
                this.funds_options = array || [];
            }).catch(errors => {
            });
            axios.get('/claims/get').then(response => {
                let array = [];
                let i;
                for (i = 0; i < response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i]['title'], value: response.data[0][i]['id'], disabled: false};
                }
                this.claim_options = array || [];
            }).catch(errors => {
            });
            axios.get('/costTypes/get').then(response => {
                let array = [];
                let i;
                for (i = 0; i < response.data.length; i++) {
                    array[i] = {text: response.data[i]['name'], value: response.data[i]['id'], disabled: false};
                }
                this.costType_options = array || [];
                this.options = array || [];
                this.form.dueDate = this.payment.dueDate;
                this.form.spentDate =  this.payment.spentDate;
                this.form.grantedFunds = this.payment.grantedFunds;
                this.form.spentFunds = this.payment.spentFunds;
                this.form.claim_id = this.payment.claim.id;
                this.form.favoredFundsCenter_id =  this.payment.favored_funds_center.id;
                this.form.chargedFundsCenter_id = this.payment.charged_funds_center.id;
                this.form.costType_id = this.payment.cost_type.id;
                this.form.notes = this.payment.notes;
                this.form.requirements = this.payment.requirements;
            }).catch(errors => {
            });
            axios.get('/fundsCenters/get?level=0').then(response => {
                let array = [];
                let i;
                let prof;
                for (i = 0; i < response.data[0].length; i++) {
                    prof = response.data[0][i]['professor']
                    console.log(prof)
                    if (prof === null) {
                        prof = ''
                    } else {
                        prof = ' - ' + prof
                    }
                    array[i] = {
                        text: response.data[0][i]['fundsCenterNumber'] + ' - ' + response.data[0][i]['description'] + prof,
                        value: response.data[0][i]['id'],
                        disabled: false
                    };
                }
                this.charged_funds_options = array;
            }).catch(errors => {console.log(errors)});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/oneTimePayment/'+this.payment.id, this.form)
                        .then(response => {
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            this.$root.$emit('bv::refresh::table', 't1');
                            this.$emit('closeModal');
                            //window.location = '/sqmPayments'
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status === 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                            console.log(errors)
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
                axios.delete('/oneTimePayment/'+this.payment.id) .then(response => {
                    this.$emit('closeModal');
                    if(window.location.href.split('/')[3] === 'oneTimePayment'){
                        console.log('close')
                        window.close()
                    }
                    this.$root.$emit('bv::refresh::table', 't1');
                })
                    .catch(errors => {
                        this.loaded = true;
                        if (errors.response.status == 401) {
                            window.location = '/login';
                        }
                        if (errors.response.status === 423) {
                            this.otherError = "Datenbankfehler";
                        }
                        if (errors.response.status === 412) {
                            this.otherError = 'Konnte nicht gelöscht werden: Es ist noch ein Verwendungsnachweis mit der Zahlung verknüpft';
                        }
                    });
            }
        }
    }
</script>


