<template>
    <div>
        <b-modal id="edit-onetime-modal" title="Sitzung bearbeiten">
            <onetimepayment-edit-formular @closeModal="closeEditModal" v-bind:payment="currentlySelectedItem"></onetimepayment-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="edit-ongoing-modal" title="Sitzung bearbeiten">
            <ongoingpayment-edit-formular @closeModal="closeEditModal" v-bind:payment="currentlySelectedItem"></ongoingpayment-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row">
            <div class="col-2 offset-2">
                <h4>Gesamt</h4>
            </div>
            <div class="col-2">
                <p>gewährt:</p>
                <p v-text="allAllFunds"></p>
            </div>
            <div class="col-2">
                <p>verplant:</p>
                <p v-text="allGrantedFunds"></p>
            </div>
            <div class="col-2">
                <p>verausgabt:</p>
                <p v-text="allSpentFunds"></p>
            </div>
            <div class="col-2">
                <p>verfügbar:</p>
                <p v-text="allAvailableFunds"></p>
            </div>
        </div>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <h2 class="pb-2">Einmalzahlungen</h2>
                <b-form-group>
                    <b-input-group size="sm">
                        <b-form-input
                            v-model="filter"
                            type="search"
                            id="filterInput"
                            placeholder="Suchbegriff eingeben"
                        ></b-form-input>
                    </b-input-group>
                </b-form-group>
            </div>
            <div class="col-2">
                <label>Von:</label>
                <b-datepicker
                    v-model="startDate"
                    @input="$root.$emit('bv::refresh::table', 't1')"
                    id="startDate"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                    placeholder="Datum auswählen"
                ></b-datepicker>
            </div>
            <div class="col-2">
                <label>Bis:</label>
                <b-datepicker
                    v-model="endDate"
                    @input="$root.$emit('bv::refresh::table', 't1')"
                    label="Bis:"
                    id="endDate"
                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }"
                    locale="de"
                    placeholder="Datum auswählen"
                ></b-datepicker>
            </div>
        </div>
        <b-table ref="MeetingIndexTable" id="t1"
                 :items="myProvider"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
                 :filter="filter"
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(actions)="row">
                <img src="/svg/show.svg" style="height: 20px;" class="pl-1 pr-1" @click="show(row.item)">
                <img src="/svg/edit.svg" :hidden=isOneTime(row.item) style="height: 20px;" class="pl-1 pr-1" @click="edit(row.item)">
            </template>
        </b-table>
        <div class="row">
            <div class="col-4">
                <b-form-group
                    label="Per page"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    label-size="sm"
                    label-for="perPageSelect"
                    class="mb-0"
                >
                    <b-form-select
                        v-model="perPage"
                        @input="$root.$emit('bv::refresh::table', 't1')"
                        id="perPageSelect"
                        size="sm"
                        :options="pageOptions"
                    ></b-form-select>
                </b-form-group>
            </div>
            <div class="col-8">
                <b-pagination
                    v-model="currentPage"
                    @input="$root.$emit('bv::refresh::table', 't1')"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    align="fill"
                    size="sm"
                    class="my-0"
                    limit="3"
                ></b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['meeting'],
        data() {
            return {
                sortBy: null,
                sortDesc: false,
                fields: [
                    {key: 'type', label: 'Art', sortable: false},
                    { key: 'favoredFundsCenter', label: 'Begünstigte Finanzstelle', sortable: false},
                    { key: 'description', label: 'Beschreibung' },
                    { key: 'grantedFunds', label: 'gewährter Betrag', sortable: true},
                    { key: 'spentFunds', label: 'verausgabter Betrag', sortable: true},
                    { key: 'actions', label: 'Aktionen' },
                ],
                startDate: null,
                endDate: null,
                filter: null,
                items: null,
                currentPage: 1,
                totalRows: 1,
                pageOptions: [10, 25, 50],
                perPage: 10,
                currentlySelectedItem: null,
                result: [],
                allAllFunds: 0,
                allGrantedFunds: 0,
                allSpentFunds: 0,
                allAvailableFunds: 0
            }
        },
        methods: {
            myProvider(ctx) {
                let promise = axios.get('/oneTimePayments/get?page=' + '&filter=' + ctx.filter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    let i
                    for (i = 0; i < response.data[0].length; i++) {
                        response.data[0][i].plannedDate = response.data[0][i].dueDate;
                        response.data[0][i].favoredFundsCenter = response.data[0][i].favored_funds_center.description;
                        response.data[0][i].type = 'Einmalzahlung'
                        response.data[0][i].spentFunds = response.data[0][i].spentFunds || 0;
                    }
                    this.result = response.data[0] || [];
                    return this.myProvider2(ctx)
                }).catch(errors => {
                });
            },
            myProvider2(ctx){
                let promise2 = axios.get('/ongoingPaymentHistories/get?planning='+ 'yes' + '&filter=' + this.filter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise2.then(response => {
                    let i
                    for (i = 0; i < response.data[0].length; i++) {
                        response.data[0][i].type = 'Mehrmalszahlung'
                    }
                    this.result = this.result.concat(response.data[0]);
                    this.calculateFunds(ctx);
                    this.items = this.result;
                    return this.result;
                }).catch(errors => {
                    console.log(errors)
                });
            },
            calculateFunds(ctx){
                console.log('called');
                    let promise = axios.get('/sqmPayments/get?filter=' + ctx.filter + '&filterOn' + this.filterOn
                        + '&startDate=' + this.startDate + '&endDate=' + this.endDate + '&sum=yes');
                    return promise.then(response => {
                         this.allAllFunds = response.data[0] || 0;
                         console.log(this.items)
                        let i
                        this.allGrantedFunds = 0;
                        this.allSpentFunds = 0;
                        for (i = 0; i < this.items.length; i++) {
                            this.allGrantedFunds = this.allGrantedFunds + this.items[i].grantedFunds - this.items[i].spentFunds;
                            this.allSpentFunds = this.allSpentFunds + this.items[i].spentFunds;
                        }
                        this.allAvailableFunds = this.allAllFunds - this.allGrantedFunds - this.allSpentFunds;
                    })
                        .catch(errors => {
                            console.log(errors)
                        });
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('edit-onetime-modal');
            },
            isOneTime(item){
              return item.type != 'Einmalzahlung'
            },
            closeEditModal(){
                this.$bvModal.hide('edit-onetime-modal');
            },
            show(item){
                if(item.type == 'Einmalzahlung'){
                    window.open('/oneTimePayment/'+item.id);
                }else{
                    window.open('/ongoingPayment/'+item.ongoing_payment_id);
                }
            }
        }
    }
</script>


