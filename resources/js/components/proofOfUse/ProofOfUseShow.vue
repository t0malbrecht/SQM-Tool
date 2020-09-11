<template>
    <div class="row">
    <div class="col-9 pt-5">
        <div class="d-flex align-items-baseline">
            <h1 class="h4">Verwendungsnachweis</h1>
            <img src="/svg/edit.svg" style="height: 20px;" class="pl-1 pr-1" @click="edit()">
        </div>
        <div>
            <div class="pr-5">
                Einreicher: <strong>{{proofofuse.submitter}}</strong>
            </div>
            <div class="pr-5">
                Einreichungsdatum: <strong>{{proofofuse.submitDate}}</strong>
            </div>
            <div class="d-flex">
            <div class="pr-5">
                document:
            </div>
            <img src="/svg/show.svg" style="height: 20px;" class="pl-1 pr-1" @click="showDocument()">
            </div>
        </div>
    </div>
        <b-modal size="lg" id="create-vn" title="Verwendungsnachweis bearbeiten">
            <proofofuse-edit-formular @closeModal="closeModal" v-bind:proofofuse1="proofofuse"></proofofuse-edit-formular>
            <template v-slot:modal-footer="">
                <b></b>
            </template>
        </b-modal>
</div>
</template>

<script>
    export default {
        props: ['proofofuse'],
        data() {
            return {

            }
        },
        methods: {
            showDocument(){
                window.open('/proofOfUses/showDocument?path=' + this.proofofuse.document);
            },
            edit() {
                this.$bvModal.show('create-vn');
            },
            closeModal() {
                this.$bvModal.hide('create-vn');
            }
        }
    }

</script>
