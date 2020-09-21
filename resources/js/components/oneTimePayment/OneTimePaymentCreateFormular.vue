<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-3" label="Antrag" label-for="ot_claim_id">
                <b-form-select v-model="form.claim_id" id="ot_claim_id" :disabled="true" :options="claim_options"></b-form-select>
                <div v-if="this.errors && this.errors.claim_id" class="text-danger">{{ this.errors.claim_id[0] }}</div>
            </b-form-group>
            <div class="row d-flex">
                <div class="col-6">
                    <b-form-group
                        id="input-group-1"
                        label="Nutzbar bis:"
                        label-for="dueDate">
                        <b-dropdown id="dropdown-form" text="Datum wählen" variant="bg-white" ref="dropdown" style="width: 100% !important; border: 1px solid #ced4da; border-radius: 0.25rem;">
                            <b-dropdown-form class="d-flex align-items-start">
                                <b-form-group label="Semester">
                                    <b-form-radio v-model="dueDate" @submit.prevent @change="changeDueDateText" value="09-30">Sommersemester</b-form-radio>
                                    <b-form-radio v-model="dueDate" @submit.prevent @change="changeDueDateText" value="03-31">Wintersemester</b-form-radio>
                                </b-form-group>

                                <b-dropdown-divider></b-dropdown-divider>

                                <b-form-group label="Jahr" label-for="dropdown-form-year">
                                    <b-form-input @change="changeDueDateText" @submit.prevent v-on:keydown.enter.prevent
                                        type="number"
                                        v-model="dueYear"
                                    ></b-form-input>
                                </b-form-group>
                            </b-dropdown-form>
                        </b-dropdown>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group id="input-group-2" label="gewährter Betrag" label-for="ot_grantedFunds">
                        <b-form-input
                            id="ot_grantedFunds"
                            type="number"
                            v-model="form.grantedFunds"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.grantedFunds" class="text-danger">{{
                            this.errors.grantedFunds[0] }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <b-form-group id="input-group-5" label="Belastete Finanzstelle" label-for="ot_chargedFundsCenter_id">
                <b-form-select v-model="form.chargedFundsCenter_id" id="ot_chargedFundsCenter_id"
                               :options="charged_funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.chargedFundsCenter_id" class="text-danger">{{
                    this.errors.chargedFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Begünstigte Finanzstelle" label-for="ot_favoredFundsCenter_id">
                <b-form-select v-model="form.favoredFundsCenter_id" id="ot_favoredFundsCenter_id"
                               :options="funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.favoredFundsCenter_id" class="text-danger">{{
                    this.errors.favoredFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-6" label="Kostenart" label-for="ot_costType_id">
                <b-form-select v-model="form.costType_id" id="ot_costType_id"
                               :options="costType_options"></b-form-select>
                <div v-if="this.errors && this.errors.costType_id" class="text-danger">{{
                    this.errors.costType_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-7" label="Notizen" label-for="ot_notes">
                <b-form-textarea
                    id="ot_notes"
                    v-model="form.notes"
                    size="textarea-default"
                ></b-form-textarea>
                <div v-if="this.errors && this.errors.notes" class="text-danger">{{ this.errors.notes[0]
                    }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-8" label="Auflagen" label-for="ot_requirements">
                <b-form-textarea
                    id="ot_requirements"
                    v-model="form.requirements"
                    size="textarea-default"
                ></b-form-textarea>
                <div v-if="this.errors && this.errors.requirements" class="text-danger">{{ this.errors.requirements[0]
                    }}
                </div>
            </b-form-group>
            <b-button type="submit" variant="primary">Speichern</b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        props: ['claim'],
        data() {
            return {
                form: {},
                errors: {},
                combinedError: '',
                success: false,
                loaded: true,
                claim_options: [],
                charged_funds_options: [],
                funds_options: [],
                costType_options: [],
                dueDate: 'Nichts ausgewählt',
                dueYear: ''
            }
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.form.dueDate = this.dueYear + '-' + this.dueDate;
                    axios.post('/oneTimePayment', this.form)
                        .then(response => {
                            console.log(response.data);
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
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
                            console.log(errors.response.data);
                        });
                }
            },
            changeDueDateText(){
                setTimeout(()=> {document.getElementById("dropdown-form__BV_toggle_").firstChild.data = this.dueYear + '-' + this.dueDate; }, 500);
            }
        },
        created() {
            axios.get('/fundsCenters/get').then(response => {
                let array = [];
                let i;
                let prof;
                for (i = 0; i < response.data[0].length; i++) {
                    prof = response.data[0][i]['professor']
                    if(prof == null){
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
                this.form.claim_id = this.claim.id
            }).catch(errors => {
            });
            axios.get('/fundsCenters/get?level=0').then(response => {
                let array = [];
                let i;
                let prof;
                for (i = 0; i < response.data[0].length; i++) {
                    prof = response.data[0][i]['professor']
                    if (prof == null) {
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
        }
    }
</script>


