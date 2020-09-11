<template>
    <div>
        <b-modal id="modal-2" size="lg" title="Nutzer hinzufügen">
            <user-create-formular @closeModal="closeAddModal"></user-create-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <b-modal id="modal-1" size="lg" title="Nutzer bearbeiten">
            <user-edit-formular size="lg" @closeModal="closeEditModal" v-bind:user="currentlySelectedItem"></user-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
        <div class="row pb-2m align-items-end">
            <div class="col-8">
                <div class="d-flex">
                    <h2 class="pb-2">Nutzer</h2>
                    <img src="/svg/add.svg" style="height: 20px;" class="pl-2" @click="add">
                </div>
            </div>
        </div>
        <b-table ref="UserIndexTable" id="t1"
                 :items="myProvider"
                 :fields="fields"
                 :sort-by.sync="sortBy"
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
        data() {
            return {
                sortBy: 'startDate',
                sortDesc: false,
                fields: [
                    {key: 'username', label: 'Nutzername', sortable: false},
                    {key: 'role', label: 'Rolle', sortable: false},
                    { key: 'email', label: 'Betrag', sortable: false },
                    { key: 'actions', label: 'Aktionen' },
                ],
                currentPage: 1,
                totalRows: 1,
                pageOptions: [10, 25, 50],
                perPage: 10,
                currentlySelectedItem: null,
            }
        },
        methods: {
            myProvider() {
                let promise = axios.get('/users/get?page=' + this.currentPage + '&perPage=' + this.perPage);
                return promise.then(response => {
                    this.totalRows = response.data[1]*this.perPage
                    return response.data[0].data || [];
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


