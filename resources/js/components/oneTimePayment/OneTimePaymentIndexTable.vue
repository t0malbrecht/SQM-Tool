<template>
    <div>
        <b-modal id="modal-1" title="Sitzung bearbeiten">
            <onetimepayment-edit-formular @closeModal="closeModal" v-bind:payment="currentlySelectedItem"></onetimepayment-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
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
                 :sort-desc.sync="sortDesc"
                 :filter="filter"
                 sort-icon-left
                 responsive="sm"
                 :no-sort-reset=true
        >
            <template v-slot:cell(actions)="row">
                <b-button size="sm" @click="edit(row.item)">
                    Edit
                </b-button>
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
                sortBy: 'dueDate',
                sortDesc: false,
                fields: [
                    {key: 'dueDate', label: 'Nutzbar bis', sortable: true},
                    {key: 'spentDate', label: 'Ausgabe-Datum', sortable: true},
                    { key: 'favoredFundsCenter', label: 'Begünstigte Finanzstelle', sortable: false},
                    { key: 'description', label: 'Beschreibung' },
                    { key: 'grantedFunds', label: 'gewährter Betrag', sortable: true},
                    { key: 'spentFunds', label: 'verausgabter Betrag', sortable: true},
                    { key: 'actions', label: 'Aktionen' },
                ],
                startDate: null,
                endDate: null,
                filter: null,
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
                let promise = axios.get('/oneTimePayments/get?page=' + this.currentPage + '&perPage=' + this.perPage + '&filter=' + ctx.filter + '&filterOn' + this.filterOn + '&sortBy=' + ctx.sortBy + '&sortDesc=' + ctx.sortDesc
                    + '&startDate=' + this.startDate + '&endDate=' + this.endDate);
                return promise.then(response => {
                    console.log(response.data[0])
                    if(ctx.filter != null || this.startDate != null || this.endDate != null)
                        this.currentPage = 1;
                    this.totalRows = response.data[1]*this.perPage
                    let i
                    for(i=0; i<response.data[0].length; i++){
                        response.data[0][i].chargedFundsCenter = response.data[0][i].charged_funds_center.description;
                        response.data[0][i].favoredFundsCenter = response.data[0][i].favored_funds_center.description;
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
            closeModal(){
                this.$bvModal.hide('modal-1');
            }
        }
    }
</script>


