<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <div class="row d-flex">
                <div class="col-6">
            <b-form-group
                id="input-group-1"
                label="Nutzbar ab:"
                label-for="input1"
            >
                <b-form-datepicker
                    v-model="form.startDate"
                    id="startDate"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                ></b-form-datepicker>
                <div v-if="this.errors && this.errors.startDate" class="text-danger">{{ this.errors.startDate[0] }}</div>
            </b-form-group>
                </div>
                <div class="col-6">
            <b-form-group
                id="input-group-2"
                label="Nutzbar bis:"
                label-for="input1"
            >
                <b-form-datepicker
                    label="Test"
                    v-model="form.dueDate"
                    id="dueDate"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                ></b-form-datepicker>
                <div v-if="this.errors && this.errors.dueDate" class="text-danger">{{ this.errors.dueDate[0] }}</div>
            </b-form-group>
                </div>
            </div>
            <b-form-group id="input-group-3" label="Betrag" label-for="amount">
                <b-form-input
                    id="amount"
                    type="number"
                    v-model="form.amount"
                ></b-form-input>
                <div v-if="this.errors && this.errors.amount" class="text-danger">{{ this.errors.amount[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Begünstigte Finanzstelle" label-for="fundsCenter">
            <b-form-select v-model="form.fundsCenter" id="fundsCenter" :options="options"></b-form-select>
                <div v-if="this.errors && this.errors.fundsCenter" class="text-danger">{{ this.errors.fundsCenter[0] }}</div>
            </b-form-group>

            <b-button type="submit" variant="primary">Speichern</b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {},
                errors: {},
                combinedError: '',
                success: false,
                loaded: true,
                options: []
            }
        },
        created() {
            axios.get('/fundsCenters/get?level=0').then(response => {
                let array = [];
                let i;
                for (i=0; i<response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i]['description'], value: response.data[0][i]['id'], disabled: false};
                }
                this.options = array || [];
            }).catch(errors => {});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    axios.post('/sqmPayment', this.form)
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
                            if (errors.response.status === 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                        });
                }
            }
        },
        }
</script>


