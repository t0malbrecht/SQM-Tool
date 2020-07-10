<template>
    <div>
        <label>Choose a date</label>
                <b-form-datepicker
                    v-model="value"
                    id="datepicker-dateformat1"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                ></b-form-datepicker>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                value: ''
            }
        }
    }
</script>
