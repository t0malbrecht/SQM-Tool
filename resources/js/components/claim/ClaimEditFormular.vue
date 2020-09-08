<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-1" label="Termin der Sitzung" label-for="meeting_id">
                <b-form-select v-model="form.meeting_id" id="meeting_id" :options="options"></b-form-select>
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
            <b-form-group id="input-group-2" label="Antragstitel" label-for="title">
                <b-form-input
                    id="title"
                    v-model="form.title"
                ></b-form-input>
                <div v-if="this.errors && this.errors.title" class="text-danger">{{ this.errors.title[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-3" label="Tagesordnungspunkt" label-for="ioa">
                <b-form-input
                    id="ioa"
                    v-model="form.ioa"
                ></b-form-input>
                <div v-if="this.errors && this.errors.ioa" class="text-danger">{{ this.errors.ioa[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Drucksachnummer" label-for="printNumber">
                <b-form-input
                    id="printNumber"
                    v-model="form.printNumber"
                ></b-form-input>
                <div v-if="this.errors && this.errors.printNumber" class="text-danger">{{ this.errors.printNumber[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-5" label="Antragssteller" label-for="claimant">
                <b-form-input
                    id="claimant"
                    v-model="form.claimant"
                ></b-form-input>
                <div v-if="this.errors && this.errors.claimant" class="text-danger">{{ this.errors.claimant[0] }}</div>
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
        props: ['claim'],
        data() {
            return {
                form: {},
                errors: {},
                otherError: '',
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
                for (i=0; i<response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i].date, value: response.data[0][i].id, disabled: false};
                }
                this.options = array || [];
                this.form.meeting_id = this.claim.meeting_id;
                this.form.title =  this.claim.title;
                this.form.ioa = this.claim.ioa
                this.form.printNumber = this.claim.printNumber;
                this.form.claimant =  this.claim.claimant;
                this.form.decided = this.claim.decided === 1;
            }).catch(errors => {});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/claim/'+this.claim.id, this.form)
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
                            if (errors.response.status === 423) {
                                this.combinedError = "Datenbankfehler";
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
                axios.delete('/claim/'+this.claim.id) .then(response => {
                    this.$emit('closeModal');
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
                            this.otherError = 'Konnte nicht gelöscht werden: Es sind noch Zahlungen mit dem Antrag verknüpft';
                        }
                        if (errors.response.status === 500) {
                            this.otherError = 'Interner Fehler';
                        }
                    });
            }
        }
    }
</script>


