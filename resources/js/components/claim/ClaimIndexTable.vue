<template>
    <div>
        <b-modal id="payment-modal" size="lg" title="Zahlung zum Antrag">
            <onetime-or-ongoingpayment-picker @closeModal="closePaymentModal" v-bind:claim="currentlySelectedItem"></onetime-or-ongoingpayment-picker>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="edit-modal" size="lg" title="Antrag bearbeiten">
            <claim-edit-formular @closeModal="closeEditModal" v-bind:claim="currentlySelectedItem"></claim-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="create-modal" size="lg" title="Antrag hinzufügen">
            <claim-create-formular @closeModal="closeCreateModal" v-bind:meeting="meeting"></claim-create-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m">
            <div class="col-8 align-items-end">
                <div class="d-flex">
                    <h2 class="pb-2">Anträge</h2>
                    <img src="/svg/add.svg" style="height: 20px;" class="pl-2" @click="addClaim">
                    <img src="/svg/export.svg" style="height: 20px;" class="pl-2 ml-auto mr-3" @click="exportXlsx">
                </div>
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
            </div>
        <b-table ref="MeetingIndexTable" id="t1"
                 :items="myProvider"
                 :fields="fields"
                 :sort-by.sync="sortBy"
                 :sort-desc.sync="sortDesc"
                 :filter="filter"
                 :filterIncludedFields="filterOn"
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(actions)="row">
                <img src="/svg/edit.svg" style="height: 20px;" class="pl-1 pr-1" @click="edit(row.item)">
                <img :src=getIcon(row.item) :hidden=isDecided(row.item) style="height: 20px;" class="pl-1 pr-1" @click="addPayment(row.item)">
            </template>
            <template v-slot:cell(showDocument)="row">
                    <img src="/svg/show.svg" style="height: 20px;" class="pl-1 pr-1" @click="openDocument(row.item)">
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
                sortBy: 'ioa',
                sortDesc: false,
                fields: [
                    {key: 'meeting_date', label: 'Sitzungsdatum', sortable: true},
                    {key: 'ioa', label: 'Top', sortable: true},
                    { key: 'claimant', label: 'Antragsteller' },
                    { key: 'showDocument', label: 'Dokument' },
                    { key: 'actions', label: 'Aktionen' },
                ],
                filter: null,
                filterOn: [],
                currentPage: 1,
                totalRows: 1,
                pageOptions: [10, 25, 50],
                perPage: 10,
                currentlySelectedItem: null,
                combinedError: '',
                paymentModalHidden: true
            }
        },
        methods: {
            myProvider(ctx) {
                let promise = axios.get('/claims/get?id=' + this.meeting.id + '&page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&filterOn' + this.filterOn + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc);
                return promise.then(response => {
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage
                    let i
                    for(i=0; i<response.data[0].length; i++){
                        response.data[0][i].meeting_date = response.data[0][i].meeting.date;
                    }
                    return response.data[0] || [];
                })
                    .catch(errors => {
                    });
            },
            addClaim(){
                this.$bvModal.show('create-modal');
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('edit-modal');
            },
            isDecided(item){
              return item.decided !== 1;
            },
            addPayment(item){
                if(item.one_time_payment ==  null && item.ongoing_payment == null){
                    this.currentlySelectedItem = item;
                    this.$bvModal.show('payment-modal')
                }else{
                    if(item.ongoing_payment == null){
                        window.open("/oneTimePayment/"+item.one_time_payment.id)
                    }else{
                        window.open("/ongoingPayment/"+item.ongoing_payment.id)
                    }
                }
            },
            openDocument(item){
                window.open('/claims/showDocument?path=' + item.document);
            },
            closeEditModal(){
                this.$bvModal.hide('edit-modal');
            },
            closeCreateModal(){
                this.$bvModal.hide('create-modal');
            },
            closePaymentModal(){
                this.$bvModal.hide('payment-modal');
            },
            getIcon(item){
                if(item.one_time_payment ==  null && item.ongoing_payment == null){
                    return '/svg/addPayment.svg'
                }else{
                    return '/svg/show.svg'

                }
            },
            exportXlsx(){
                window.open('/meeting/exportXlsx/' + this.meeting.id);
            }
        }
    }
</script>


