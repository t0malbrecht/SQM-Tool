<template>
    <div>
        <b-modal id="modal-2" size="lg" title="Finanzstelle erstellen">
            <fundscenter-create-formular @closeModal="closeAddModal"></fundscenter-create-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="modal-1" size="lg" title="Finanzstelle bearbeiten">
            <fundscenter-edit-formular @closeModal="closeEditModal" v-bind:fundsCenter="currentlySelectedItem"></fundscenter-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <div class="d-flex">
                    <h2 class="pb-2">Finanzstellen</h2>
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
                sortBy: 'division',
                sortDesc: false,
                fields: [
                    {key: 'division', label: 'Bereich', sortable: true},
                    {key: 'description', label: 'Beschreibung', sortable: true},
                    { key: 'fundsCenterNumber', label: 'Finanzstelle' },
                    { key: 'costCenter', label: 'Kostenstelle' },
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
                let promise = axios.get('/fundsCenters/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&filterOn' + this.filterOn + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                                        + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage;
                    console.log(response.data[0])
                    return response.data[0] || [];
                })
                    .catch(errors => {
                    });
            },
            edit(item){
                this.currentlySelectedItem = item;
                this.$bvModal.show('modal-1');
            },
            add(item){
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
