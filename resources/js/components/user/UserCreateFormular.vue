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
            <b-form-group id="input-group-5" label="Passwort" label-for="password">
                <b-form-input
                    id="password"
                    type="text"
                    v-model="form.password"
                ></b-form-input>
                <div v-if="this.errors && this.errors.password" class="text-danger">{{ this.errors.password[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-6" label="Rolle" label-for="role">
            <b-form-select v-model="form.role" id="role" :options="options"></b-form-select>
                <div v-if="this.errors && this.errors.role" class="text-danger">{{ this.errors.role[0] }}</div>
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
            this.options = [{text: 'Studiendekan', value: 0, disabled: false},
                            {text: 'Sachbearbeiter', value: 1, disabled: false}];
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    axios.post('/user', this.form)
                        .then(response => {
                            console.log(response)
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
            }
        },
        }
</script>


