<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <div class="row d-flex">
                <div class="col-6">
                    <b-form-group
                        id="input-group-1"
                        label="Nutzbar bis:"
                        label-for="input1"
                    >
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
            <b-form-group id="input-group-3" label="Antrag" label-for="ot_claim_id">
                <b-form-select v-model="form.claim_id" id="ot_claim_id" :disabled="true" :options="claim_options"></b-form-select>
                <div v-if="this.errors && this.errors.claim_id" class="text-danger">{{ this.errors.claim_id[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Begünstigte Finanzstelle" label-for="ot_favoredFundsCenter_id">
                <b-form-select v-model="form.favoredFundsCenter_id" id="ot_favoredFundsCenter_id"
                               :options="funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.favoredFundsCenter_id" class="text-danger">{{
                    this.errors.favoredFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-5" label="Belastete Finanzstelle" label-for="ot_chargedFundsCenter_id">
                <b-form-select v-model="form.chargedFundsCenter_id" id="ot_chargedFundsCenter_id"
                               :options="funds_options"></b-form-select>
                <div v-if="this.errors && this.errors.chargedFundsCenter_id" class="text-danger">{{
                    this.errors.chargedFundsCenter_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-6" label="Kostenart" label-for="ot_costType_id">
                <b-form-select v-model="form.costType_id" id="ot_costType_id"
                               :options="costType_options"></b-form-select>
                <div v-if="this.errors && this.errors.costType_id" class="text-danger">{{
                    this.errors.costType_id[0] }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-7" label="Beschreibung" label-for="ot_description">
                <b-form-input
                    id="ot_description"
                    v-model="form.description"
                ></b-form-input>
                <div v-if="this.errors && this.errors.description" class="text-danger">{{ this.errors.description[0]
                    }}
                </div>
            </b-form-group>
            <b-form-group id="input-group-8" label="Auflagen" label-for="requirements">
                <b-form-input
                    id="requirements"
                    v-model="form.requirements"
                ></b-form-input>
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
                funds_options: [],
                costType_options: [],
            }
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    console.log(this.form)
                    axios.post('/oneTimePayment', this.form)
                        .then(response => {
                            console.log(response.data);
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
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
                            console.log(errors.response.data);
                        });
                }
            }
        },
        created() {
            axios.get('/fundsCenters/get').then(response => {
                let array = [];
                let i;
                for (i = 0; i < response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i]['description'], value: response.data[0][i]['id'], disabled: false};
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
        }
    }
</script>


