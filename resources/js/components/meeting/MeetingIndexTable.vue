<template>
    <div>
        <b-modal id="modal-2" size="lg" title="Sitzung hinzufügen">
            <meeting-create-formular @closeModal="closeAddModal"></meeting-create-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="modal-1" size="lg" title="Sitzung bearbeiten">
            <meeting-edit-formular @closeModal="closeEditModal" v-bind:meeting="currentlySelectedItem"></meeting-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <div class="d-flex">
                    <h2 class="pb-2">Sitzungen</h2>
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
                 :filterIncludedFields="filterOn"
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(actions)="row">
                <img src="/svg/show.svg" style="height: 20px;" class="pl-1 pr-1" @click="show(row.item)">
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
                sortBy: 'date',
                sortDesc: false,
                fields: [
                    {key: 'date', label: 'Datum', sortable: true},
                    {key: 'number', label: 'Nummer', sortable: true},
                    {key: 'actions', label: 'Aktionen' },
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
                let promise = axios.get('/meetings/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&filterOn' + this.filterOn + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                                        + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage;
                    return response.data[0] || [];
                })
                    .catch(errors => {
                    });
            },
            show(item){
              window.open('/meeting/'+item.id);
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
