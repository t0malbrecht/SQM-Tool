<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <div class="d-flex row">
                <div class="col-5">
                    <b-form-group id="input-group-5" label="Fond" label-for="fond">
                        <b-form-input
                            id="fond"
                            type="text"
                            v-model="form.fond"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.fond" class="text-danger">{{ this.errors.fond[0] }}</div>
                    </b-form-group>
                </div>
                <div class="col-5">
                    <b-form-group id="input-group-6" label="Lehreinheit" label-for="teachingUnit">
                        <b-form-input
                            id="teachingUnit"
                            type="number"
                            v-model="form.teachingUnit"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.teachingUnit" class="text-danger">{{
                            this.errors.teachingUnit[0] }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-2">
                    <b-form-group id="level" label="Level" label-for="level">
                        <b-form-input
                            id="level"
                            type="number"
                            v-model="form.level"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.level" class="text-danger">{{ this.errors.level[0] }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <div class="d-flex row">
                <div class="col-6">
                    <b-form-group id="input-group-1" label="Bereich" label-for="division">
                        <b-form-input
                            id="division"
                            type="text"
                            v-model="form.division"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.division" class="text-danger">{{ this.errors.division[0]
                            }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group id="input-group-2" label="Beschreibung" label-for="description">
                        <b-form-input
                            id="description"
                            type="text"
                            v-model="form.description"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.description" class="text-danger">{{
                            this.errors.description[0] }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <div class="d-flex row">
                <div class="col-6">
                    <b-form-group id="input-group-7" label="Fakultät" label-for="faculty">
                        <b-form-input
                            id="faculty"
                            type="text"
                            v-model="form.faculty"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.faculty" class="text-danger">{{ this.errors.faculty[0]
                            }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group id="input-group-8" label="Professor" label-for="professor">
                        <b-form-input
                            id="professor"
                            type="text"
                            v-model="form.professor"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.professor" class="text-danger">{{ this.errors.professor[0]
                            }}
                        </div>
                    </b-form-group>
                </div>
            </div>
            <div class="d-flex row">
                <div class="col-6">
                    <b-form-group id="input-group-3" label="Finanzstelle" label-for="fundsCenterNumber">
                        <b-form-input
                            id="fundsCenterNumber"
                            type="number"
                            v-model="form.fundsCenterNumber"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.fundsCenterNumber" class="text-danger">{{
                            this.errors.fundsCenterNumber[0] }}
                        </div>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group id="input-group-4" label="Kostenstelle" label-for="costCenter">
                        <b-form-input
                            id="costCenter"
                            type="number"
                            v-model="form.costCenter"
                        ></b-form-input>
                        <div v-if="this.errors && this.errors.costCenter" class="text-danger">{{
                            this.errors.costCenter[0] }}
                        </div>
                    </b-form-group>
                </div>
            </div>
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
            }
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    axios.post('/fundsCenter', this.form)
                        .then(response => {
                            console.log(response.data);
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            window.location = '/fundsCenters'
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
        }
    }
</script>


