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
                    placeholder="Datum auswählen"
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
                <div class="text-danger">{{ this.combinedError }}</div>
            </b-form-group>

            <b-button type="submit" variant="primary">Submit</b-button>
        </b-form>
    </div>
</template>

<script>
    import './MeetingIndexTable.vue';
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
                    let yearNumberCombined = this.form.date.split('-', 1)[0].concat('-',this.form.number)
                    this.form.yearNumberCombined = yearNumberCombined;
                    axios.post('/meeting', this.form)
                        .then(response => {
                            this.fields = {}; //Clear input fields.
                            this.loaded = true;
                            this.success = true;
                            //this.$refs.MeetingIndexTable.refresh()
                            this.$root.$emit('bv::refresh::table', 't1')
                            window.location = '/meetings'
                        })
                        .catch(errors => {
                            this.loaded = true;
                            if (errors.response.status == 401) {
                                window.location = '/login';
                            }
                            if (errors.response.status === 422) {
                                this.errors = errors.response.data.errors || {};
                            }
                            if (errors.response.status === 433) {
                                this.combinedError = 'Diese Sitzungsnummer wurde für dieses Jahr bereits vergeben';
                            }
                        });
                }
            }
        }
    }
</script>


