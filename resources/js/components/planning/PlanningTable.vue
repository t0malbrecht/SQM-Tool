<template>
    <div>
        <b-modal id="edit-onetime-modal" size="lg" title="Einmalzahlung bearbeiten">
            <onetimepayment-edit-formular @closeModal="closeEditModal" v-bind:payment="currentlySelectedItem"></onetimepayment-edit-formular>
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
                <p v-text="allAllFundsFormatted"></p>
            </div>
            <div class="col-2">
                <p>verplant:</p>
                <p v-text="allGrantedFundsFormatted"></p>
            </div>
            <div class="col-2">
                <p>verausgabt:</p>
                <p v-text="allSpentFundsFormatted"></p>
            </div>
            <div class="col-2">
                <p>verfügbar:</p>
                <p v-text="allAvailableFundsFormatted"></p>
            </div>
        </div>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <h2 class="pb-2">Planungsübersicht</h2>
                    <b-form-select v-model="chargedFundsCenter" id="chargedFundsCenter" @input="$root.$emit('bv::refresh::table', 't1')" :options="options"></b-form-select>
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
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(grantedFunds)="data">
                <a v-text="formatGrantedFunds(data)"></a>
            </template>

            <template v-slot:cell(spentFunds)="data">
                <a id="spentFunds" v-text="formatSpentFunds(data)"></a>
            </template>

            <template v-slot:cell(actions)="row">
                <img src="/svg/show.svg" style="height: 20px;" class="pl-1 pr-1" @click="show(row.item)">
                <img src="/svg/edit.svg" :hidden=isOneTime(row.item) style="height: 20px;" class="pl-1 pr-1" @click="edit(row.item)">
                <img src="/svg/notes.svg" :hidden=hasNotes(row.item) style="height: 20px;" class="pl-1 pr-1" @click="row.toggleDetails">
            </template>

            <template v-slot:row-details="row">
                <b-card>
                    <ul>
                        <li>Titel: {{row.item.title}}</li>
                        <li>Notizen: {{row.item.notes}}</li>
                    </ul>
                </b-card>
            </template>
        </b-table>
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
                    { key: 'title', label: 'Titel', class: 'table-col-max-250'},
                    { key: 'grantedFunds', label: 'gewährter Betrag', sortable: true},
                    { key: 'spentFunds', label: 'verausgabter Betrag', sortable: true},
                    { key: 'actions', label: 'Aktionen' },
                ],
                startDate: null,
                endDate: null,
                chargedFundsCenter: null,
                items: null,
                currentlySelectedItem: null,
                result: [],
                allAllFunds: 0,
                allGrantedFunds: 0,
                allSpentFunds: 0,
                allAvailableFunds: 0,
                allAllFundsFormatted: null,
                allGrantedFundsFormatted: null,
                allSpentFundsFormatted: null,
                allAvailableFundsFormatted: null,
                options: [],
                errors: [],
            }
        },
        created() {
            axios.get('/fundsCenters/get?level=0').then(response => {
                let array = [];
                let i;
                let prof;
                for (i = 0; i < response.data[0].length; i++) {
                    prof = response.data[0][i]['professor']
                    console.log(prof)
                    if (prof === null) {
                        prof = ''
                    } else {
                        prof = ' - ' + prof
                    }
                    array[i] = {
                        text: response.data[0][i]['fundsCenterNumber'] + ' - ' + response.data[0][i]['description'] + prof,
                        value: response.data[0][i]['id'],
                        disabled: false
                    };
                }
                this.options = array;
            }).catch(errors => {console.log(errors)});
        },
        methods: {
            myProvider(ctx) {
                let promise = axios.get('/oneTimePayments/get?chargedFundsCenter=' + this.chargedFundsCenter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    let i
                    for (i = 0; i < response.data[0].length; i++) {
                        response.data[0][i].plannedDate = response.data[0][i].dueDate;
                        response.data[0][i].favoredFundsCenter = response.data[0][i].favored_funds_center.description;
                        response.data[0][i].title = response.data[0][i].claim.title;
                        response.data[0][i].type = 'Einmalzahlung'
                        response.data[0][i].spentFunds = response.data[0][i].spentFunds || 0;
                    }
                    this.result = response.data[0] || [];
                    return this.myProvider2(ctx)
                }).catch(errors => {
                });
            },
            myProvider2(ctx){
                let promise2 = axios.get('/ongoingPaymentHistories/get?planning='+ 'yes' + '&chargedFundsCenter=' + this.chargedFundsCenter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise2.then(response => {
                    let i
                    for (i = 0; i < response.data[0].length; i++) {
                        console.log(response.data[0][i]);
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
                const formatter = new Intl.NumberFormat('de', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 0
                })
                    let promise = axios.get('/sqmPayments/get?chargedFundsCenter=' + this.chargedFundsCenter
                        + '&startDate=' + this.startDate + '&endDate=' + this.endDate + '&sum=yes');
                    return promise.then(response => {
                        this.allAllFunds = response.data[0] || 0;
                        this.allAllFundsFormatted = formatter.format(this.allAllFunds);
                         console.log(this.items)
                        let i
                        this.allGrantedFunds = 0;
                        this.allSpentFunds = 0;
                        for (i = 0; i < this.items.length; i++) {
                            this.allGrantedFunds = this.allGrantedFunds + this.items[i].grantedFunds - this.items[i].spentFunds;
                            this.allGrantedFundsFormatted = formatter.format(this.allGrantedFunds);
                            this.allSpentFunds = this.allSpentFunds + this.items[i].spentFunds;
                            this.allSpentFundsFormatted = formatter.format(this.allSpentFunds);
                        }
                        this.allAvailableFunds = this.allAllFunds - this.allGrantedFunds - this.allSpentFunds;
                        this.allAvailableFundsFormatted = formatter.format(this.allAvailableFunds);
                    })
                        .catch(errors => {
                            console.log(errors)
                        });
            },
            formatGrantedFunds(data){
                const formatter = new Intl.NumberFormat('de', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 0
                })
                return formatter.format(data.item.grantedFunds);
            },
            formatSpentFunds(data){
                const formatter = new Intl.NumberFormat('de', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 0
                })
                if(data.item.spentFunds > data.item.grantedFunds){
                    if(document.getElementById("spentFunds") != null){
                        document.getElementById("spentFunds").style.backgroundColor = "red";
                    }
                }
                return formatter.format(data.item.spentFunds);
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('edit-onetime-modal');
            },
            isOneTime(item){
              return item.type != 'Einmalzahlung'
            },
            hasNotes(item){
              return item.notes == null
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
            },
            updateFundsCenter(){
                this.myProvider(ctx)
            }
        }
    }
</script>


