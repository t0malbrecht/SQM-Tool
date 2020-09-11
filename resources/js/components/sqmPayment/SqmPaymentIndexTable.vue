<template>
    <div>
        <b-modal id="modal-2" size="lg" title="SQM-Zahlung hinzufügen">
            <sqmpayment-create-formular @closeModal="closeAddModal"></sqmpayment-create-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="modal-1" size="lg" title="SQM-Zahlung bearbeiten">
            <sqmpayment-edit-formular size="lg" @closeModal="closeEditModal" v-bind:sqmPayment="currentlySelectedItem"></sqmpayment-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <div class="d-flex">
                    <h2 class="pb-2">SQM-Zahlungen</h2>
                    <img src="/svg/add.svg" style="height: 20px;" class="pl-2" @click="add">
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
            </template>
            <template v-slot:cell(fundsCenterDescription)="row">
                {{getItem['description']}}
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
        data() {
            return {
                sortBy: 'startDate',
                sortDesc: false,
                fields: [
                    {key: 'startDate', label: 'Nutzbar ab', sortable: true},
                    {key: 'dueDate', label: 'Nutzbar bis', sortable: true},
                    { key: 'amount', label: 'Betrag' },
                    { key: 'funds_center_description', label: 'Finanzstelle' },
                    { key: 'actions', label: 'Aktionen' },
                ],
                startDate: null,
                endDate: null,
                filter: null,
                filterOn: [],
                currentPage: 1,
                totalRows: 1,
                pageOptions: [10, 25, 50],
                perPage: 10,
                currentlySelectedItem: null,
                combinedError: '',
            }
        },
        methods: {
            myProvider(ctx) {
                let promise = axios.get('/sqmPayments/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&filterOn' + this.filterOn + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                    + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage
                    let i;
                    for (i = 0; i < response.data[0].length; i++) {
                        response.data[0][i].funds_center_description = response.data[0][i].funds_center.description;
                    }
                    return response.data[0] || [];
                })
                    .catch(errors => {
                    });
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('modal-1');
            },
            add(){
                this.$bvModal.show('modal-2');
            },
            closeEditModal(){
                this.$bvModal.hide('modal-1');
            },
            closeAddModal(){
                this.$bvModal.hide('modal-2');
            }
        }
    }
</script>


