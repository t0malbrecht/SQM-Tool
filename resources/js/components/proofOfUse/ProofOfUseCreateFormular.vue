<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-1" label="Titel der Zahlung" label-for="unique_payment_id">
            <b-form-select v-model="form.claim_id" id="claim_id" :disabled=true :options="options"></b-form-select>
            <div v-if="this.errors && this.errors.claim_id" class="text-danger">{{ this.errors.claim_id[0] }}</div>
            </b-form-group>
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
            <b-form-group id="input-group-6" label="Dokument" label-for="document">
                <b-form-file
                    v-model="file"
                    :state="Boolean(file)"
                    placeholder="Datei auswählen oder hier ablegen..."
                    drop-placeholder="Hier ablegen..."
                ></b-form-file>
                <div v-if="this.errors && this.errors.document" class="text-danger">{{ this.errors.document[0] }}</div>
            </b-form-group>

            <b-button type="submit" variant="primary">Speichern</b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        props: ['payment'],
        data() {
            return {
                form: {},
                errors: {},
                success: false,
                loaded: true,
                options: [],
                file: null,
            }
        },
        created() {
            let promise = axios.get('/claims/get?find=' + this.payment.claim_id);
            return promise.then(response => {
                let array = [];
                let i;
                console.log('test');
                console.log(response.data)
                this.options = [{text: response.data.title, value: response.data.id, disabled: false}];
                this.form.claim_id = this.payment.claim_id;
            }).catch(errors => {
            });
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.form.document = this.file;
                    this.form.unique_payment_id = this.payment.unique_payment_id;
                    let formData = new FormData();
                    for (let key in this.form) {
                        formData.append(key, (this.form[key]));
                    }
                    axios.post('/proofOfUse', formData, { headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }})
                        .then(response => {
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            this.$emit('closeModal');
                            this.$emit('updatePage')
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status == 422) {
                                console.log("Errors")
                                console.log(errors.response)
                                this.errors = errors.response.data.errors || {};
                            }
                            if (errors.response.status == 423) {
                                this.combinedError = "Datenbankfehler";
                            }
                        });
                }
            }
        }
        }
</script>


