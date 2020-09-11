<template>
    <div>
        <b-modal id="modal-1" title="Mehrmalszahlung-Historie bearbeiten">
            <ongoingpaymenthistories-edit-formular size="lg" @closeModal="closeModal" v-bind:ongoingPaymentHistory="currentlySelectedItem"></ongoingpaymenthistories-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <h2 class="pb-2">Mehrmalszahlung-Historien</h2>
            </div>
            <div class="col-2">
                <label>Von:</label>
                <b-datepicker
                    v-model="startDate"
                    @input="$root.$emit('bv::refresh::table', 't1')"
                    id="startDate"
                    reset-button
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
                 :sort-desc.sync="sortDesc"
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(actions)="row">
                <img src="/svg/edit.svg" style="height: 20px;" class="pl-1 pr-1" @click="edit(row.item)">
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
        props: ['id'],
        data() {
            return {
                sortBy: 'dueDate',
                sortDesc: false,
                fields: [
                    {key: 'plannedPaymentDate', label: 'Geplantes Verausgabungsdatum', sortable: true},
                    {key: 'realPaymentDate', label: 'Tatsächliches Verausgabungsdatum', sortable: true},
                    { key: 'grantedFunds', label: 'gewährter Betrag', sortable: true},
                    { key: 'spentFunds', label: 'verausgabter Betrag', sortable: true},
                    { key: 'actions', label: 'Aktionen' },
                ],
                startDate: null,
                endDate: null,
                currentPage: 1,
                totalRows: 1,
                pageOptions: [10, 25, 50],
                perPage: 10,
                currentlySelectedItem: null,
            }
        },
        methods: {
            myProvider(ctx) {
                console.log('/ongoingPaymentHistories/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                    + '&startDate=' + this.startDate + '&endDate=' + this.endDate+ '&chargedFundsCenter=' + this.id)
                let promise = axios.get('/ongoingPaymentHistories/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                    + '&startDate=' + this.startDate + '&endDate=' + this.endDate+ '&ongoingPayment=' + this.id);
                return promise.then(response => {
                    console.log(response.data[0])
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage
                    return response.data[0] || [];
                })
                    .catch(errors => {
                    });
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('modal-1');
            },
            closeModal(){
                this.$bvModal.hide('modal-1');
            }
        }
    }
</script>


