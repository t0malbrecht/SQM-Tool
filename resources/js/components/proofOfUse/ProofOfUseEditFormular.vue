<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-5" label="Eingereicht von" label-for="claimant">
                <b-form-input
                    id="submitter"
                    v-model="form.submitter"
                ></b-form-input>
                <div v-if="this.errors && this.errors.submitter" class="text-danger">{{ this.errors.submitter[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-2" label="Einreichungsdatum" label-for="title">
                <b-form-datepicker
                    v-model="form.submitDate"
                    id="submitDate"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                ></b-form-datepicker>
                <div v-if="this.errors && this.errors.submitDate" class="text-danger">{{ this.errors.submitDate[0] }}</div>
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
        props: ['proofofuse1'],
        data() {
            return {
                form: {},
                errors: {},
                success: false,
                loaded: true,
                options: [],
                file: null,
                otherError: '',
            }
        },
        created() {
            this.form.grantedClaim_id = this.proofofuse1.grantedClaim_id;
            this.form.submitter = this.proofofuse1.submitter;
            this.form.submitDate = this.proofofuse1.submitDate;

        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    this.form.unique_payment_id = this.form.grantedClaim_id;
                    let formData = new FormData();
                    for (let key in this.form) {
                        formData.append(key, (this.form[key]));
                    }
                    axios.patch('/proofOfUse/'+this.proofofuse1.id, this.form)
                        .then(response => {
                            console.log(response.data)
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            this.$emit('closeModal');
                            //window.location = '/sqmPayments'
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status == 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                            if (errors.response.status == 423) {
                                this.otherError = "Datenbankfehler";
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
                console.log('test')
                axios.delete('/proofOfUse/'+this.proofofuse1.id) .then(response => {
                    this.$emit('closeModal');
                    if(window.location.href.split('/')[3] == 'proofOfUse'){
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


