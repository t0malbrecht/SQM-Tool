<template>
    <div>
        <b-form @submit.prevent="onSubmit" v-if="true">
            <b-form-group id="input-group-1" label="Beschlossen in Sitzung:" label-for="meeting_id">
                <b-form-select v-model="form.meeting_id" id="meeting_id" :options="options"></b-form-select>
                <div v-if="this.errors && this.errors.meeting_id" class="text-danger">{{ this.errors.meeting_id[0] }}</div>
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

            <b-button type="submit" variant="primary">Speichern</b-button>
        </b-form>
    </div>
</template>

<script>
    export default {
        props: ['claim'],
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
                for (i=0; i<response.data[0].length; i++) {
                    array[i] = {text: response.data[0][i].yearNumberCombined, value: response.data[0][i].id, disabled: false};
                }
                console.log(this.claim)
                this.options = array || [];
                this.form.meeting_id = this.claim.meeting_id;
                this.form.title =  this.claim.title;
                this.form.ioa = this.claim.ioa
                this.form.printNumber = this.claim.printNumber;
                this.form.claimant =  this.claim.claimant;
            }).catch(errors => {});
        },
        methods: {
            onSubmit() {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
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
                        });
                }
            }
        }
    }
</script>


