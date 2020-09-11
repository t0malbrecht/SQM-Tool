<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-1" label="Termin der Sitzung" label-for="meeting_id">
            <b-form-select v-model="form.meeting_id" id="meeting_id" :disabled=true :options="options"></b-form-select>
            <div v-if="this.errors && this.errors.meeting_id" class="text-danger">{{ this.errors.meeting_id[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-7">
                <b-form-checkbox
                    id="decided"
                    v-model="form.decided"
                    name="checkbox-1"
                    value="true"
                    unchecked-value="false"
                >
                    Antrag wurde beschlossen
                </b-form-checkbox>
                <div v-if="this.errors && this.errors.decided" class="text-danger">{{ this.errors.decided[0] }}</div>
            </b-form-group>
            <div class="row d-flex">
                <div class="col-6">
            <b-form-group id="input-group-3" label="Tagesordnungspunkt" label-for="ioa">
                <b-form-input
                    id="ioa"
                    v-model="form.ioa"
                ></b-form-input>
                <div v-if="this.errors && this.errors.ioa" class="text-danger">{{ this.errors.ioa[0] }}</div>
            </b-form-group>
                </div>
                <div class="col-6">
            <b-form-group id="input-group-4" label="Drucksachennummer" label-for="printNumber">
                <b-form-input
                    id="printNumber"
                    v-model="form.printNumber"
                ></b-form-input>
                <div v-if="this.errors && this.errors.printNumber" class="text-danger">{{ this.errors.printNumber[0] }}</div>
            </b-form-group>
                </div>
            </div>
            <b-form-group id="input-group-5" label="Antragssteller" label-for="claimant">
                <b-form-input
                    id="claimant"
                    v-model="form.claimant"
                ></b-form-input>
                <div v-if="this.errors && this.errors.claimant" class="text-danger">{{ this.errors.claimant[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-2" label="Antragstitel" label-for="title">
                <b-form-textarea
                    id="textarea-state"
                    v-model="form.title"
                    size="textarea-default"
                ></b-form-textarea>
                <div v-if="this.errors && this.errors.title" class="text-danger">{{ this.errors.title[0] }}</div>
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
        props: ['meeting'],
        data() {
            return {
                form: {},
                errors: {},
                combinedError: '',
                success: false,
                loaded: true,
                options: [],
                file: null,
            }
        },
        created() {
            axios.get('/meetings/get?sortBy=date').then(response => {
                let array = [];
                let i;
                console.log(response.data[0])
                for (i=0; i<response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i].date, value: response.data[0][i].id, disabled: false};
                }
                this.options = array || [];
                this.form.meeting_id = this.meeting.id;
            }).catch(errors => {});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.form.document = this.file;
                    let formData = new FormData();
                    for (let key in this.form) {
                        formData.append(key, (this.form[key]));
                    }
                    axios.post('/claim', formData, { headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }})
                        .then(response => {
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            this.$emit('closeModal');
                            this.$root.$emit('bv::refresh::table', 't1');
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status === 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                            if (errors.response.status === 423) {
                                this.combinedError = "Datenbankfehler";
                            }
                        });
                }
            }
        }
        }
</script>


