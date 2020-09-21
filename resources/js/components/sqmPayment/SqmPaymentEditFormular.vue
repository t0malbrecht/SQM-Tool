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
        props: ['sqmPayment'],
        data() {
            return {
                form: {},
                errors: {},
                otherError: '',
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
                this.form.startDate = this.sqmPayment.startDate;
                this.form.dueDate =  this.sqmPayment.dueDate;
                this.form.amount = this.sqmPayment.amount
                this.form.fundsCenter = this.sqmPayment.funds_center.id;
            }).catch(errors => {console.log(errors)});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/sqmPayment/'+this.sqmPayment.id, this.form)
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
                axios.delete('/sqmPayment/'+this.sqmPayment.id) .then(response => {
                    this.$emit('closeModal');
                    if(window.location.href.split('/')[3] == 'sqmPayment'){
                        window.close()
                    }
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
                        if (errors.response.status == 412) {
                            this.otherError = 'Konnte nicht gelöscht werden: Es sind noch Zahlungen oder Verwendungsnachweise mit der Zahlung verknüpft';
                        }
                    });
            }
        }
    }
</script>


