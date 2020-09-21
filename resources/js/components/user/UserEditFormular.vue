<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-3" label="Nutzername" label-for="username">
                <b-form-input
                    id="username"
                    type="text"
                    v-model="form.username"
                ></b-form-input>
                <div v-if="this.errors && this.errors.username" class="text-danger">{{ this.errors.username[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-4" label="Email" label-for="email">
                <b-form-input
                    id="email"
                    type="text"
                    v-model="form.email"
                ></b-form-input>
                <div v-if="this.errors && this.errors.email" class="text-danger">{{ this.errors.email[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-6" label="Rolle" label-for="role">
                <b-form-select v-model="form.role" id="role" :options="options"></b-form-select>
                <div v-if="this.errors && this.errors.role" class="text-danger">{{ this.errors.role[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-5" label="Neues Passwort vergeben" label-for="password">
                <b-form-input
                    id="password"
                    type="text"
                    v-model="form.password"
                ></b-form-input>
                <div v-if="this.errors && this.errors.password" class="text-danger">{{ this.errors.password[0] }}</div>
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
        props: ['user'],
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
            this.options = [{text: 'Studiendekan', value: 0, disabled: false},
                {text: 'Sachbearbeiter', value: 1, disabled: false}];
            this.form.username = this.user.username;
            this.form.email = this.user.email;
            this.form.role = this.user.role;
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    this.otherError = '';
                    axios.patch('/user/'+this.user.id, this.form)
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
                                console.log(this.errors);
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
                axios.delete('/user/'+this.user.id) .then(response => {
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
                        if (errors.response.status === 500) {
                            this.otherError = "Interner Server Fehler";
                        }
                    });
            }
        }
    }
</script>


