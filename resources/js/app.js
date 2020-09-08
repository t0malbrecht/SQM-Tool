/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import BootstrapVue from 'bootstrap-vue' //Importing

window.Vue = require('vue');

Vue.use(BootstrapVue); // Telling Vue to use this in whole application


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('meeting-create-formular', require('./components/meeting/MeetingCreateFormular.vue').default);
Vue.component('meeting-edit-formular', require('./components/meeting/MeetingEditFormular.vue').default);
Vue.component('meeting-index-table', require('./components/meeting/MeetingIndexTable.vue').default);

Vue.component('fundscenter-create-formular', require('./components/fundsCenter/FundsCenterCreateFormular.vue').default);
Vue.component('fundscenter-edit-formular', require('./components/fundsCenter/FundsCenterEditFormular.vue').default);
Vue.component('fundscenter-index-table', require('./components/fundsCenter/FundsCenterIndexTable.vue').default);

Vue.component('sqmpayment-create-formular', require('./components/sqmPayment/SqmPaymentCreateFormular.vue').default);
Vue.component('sqmpayment-edit-formular', require('./components/sqmPayment/SqmPaymentEditFormular.vue').default);
Vue.component('sqmpayment-index-table', require('./components/sqmPayment/SqmPaymentIndexTable.vue').default);

Vue.component('onetimepayment-create-formular', require('./components/oneTimePayment/OneTimePaymentCreateFormular.vue').default);
Vue.component('onetimepayment-edit-formular', require('./components/oneTimePayment/OneTimePaymentEditFormular.vue').default);
Vue.component('onetimepayment-edit-button', require('./components/oneTimePayment/OneTimePaymentEditButton.vue').default);
Vue.component('onetimepayment-forumlar-button', require('./components/oneTimePayment/OneTimePaymentFomularButton.vue').default);

Vue.component('ongoingpayment-create-formular', require('./components/ongoingPayment/OngoingPaymentCreateFormular.vue').default);
Vue.component('ongoingpayment-edit-formular', require('./components/ongoingPayment/OngoingPaymentEditFormular.vue').default);
Vue.component('ongoingpayment-edit-button', require('./components/ongoingPayment/OngoingPaymentEditButton.vue').default);
Vue.component('ongoingpayment-forumlar-button', require('./components/ongoingPayment/OngoingPaymentFomularButton.vue').default);

Vue.component('ongoingpaymenthistories-edit-formular', require('./components/ongoingPaymentHistories/OngoingPaymentHistoriesEditFormular.vue').default);
Vue.component('ongoingpaymenthistories-index-table', require('./components/ongoingPaymentHistories/OngoingPaymentHistoriesIndexTable.vue').default);

Vue.component('claim-create-formular', require('./components/claim/ClaimCreateFormular.vue').default);
Vue.component('claim-edit-formular', require('./components/claim/ClaimEditFormular.vue').default);
Vue.component('claim-index-table', require('./components/claim/ClaimIndexTable.vue').default);

Vue.component('clickable-blop', require('./components/ClickableBlop.vue').default);

Vue.component('onetime-or-ongoingpayment-picker', require('./components/OneTimeOrOgoingPaymentPicker.vue').default);

Vue.component('planning-table', require('./components/planning/PlanningTable.vue').default);

Vue.component('delete-confirmation', require('./components/DeleteConfirmation.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
