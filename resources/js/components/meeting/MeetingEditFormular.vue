<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group
                id="input-group-1"
                label="Datum:"
                label-for="input1"
            >
                <b-form-datepicker
                    v-model="form.date"
                    id="date"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                ></b-form-datepicker>
                <div v-if="this.errors && this.errors.date" class="text-danger">{{ this.errors.date[0] }}</div>
            </b-form-group>

            <b-form-group id="input-group-2" label="Nummer der Sitzung im Jahr" label-for="number">
                <b-form-input
                    id="number"
                    v-model="form.number"
                    type="number"
                ></b-form-input>
                <div v-if="this.errors && this.errors.number" class="text-danger">{{ this.errors.number[0] }}</div>
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
    import './MeetingIndexTable.vue';
    export default {
        props: ['meeting'],
        data() {
            return {
                form: {},
                errors: {},
                otherError: '',
                success: false,
                loaded: true,
            }
        },
        created() {
                this.form.date = this.meeting.date;
                this.form.number =  this.meeting.number;
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/meeting/'+this.meeting.id, this.form)
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
                                this.otherError = "Datenbankfehler";
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
                axios.delete('/meeting/'+this.meeting.id) .then(response => {
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
                            this.otherError = 'Konnte nicht gelöscht werden: Es sind noch Anträge mit der Sitzung verknüpft';
                        }
                    });
            }
        },
    }
</script>


