<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-7">
                <h2 class="align-self-start">Visualizando el área {{this.academicArea.name}}</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="setAcademicAreaProgramDialogToCreateOrEdit('create')"
                    >
                       Agregar nuevo programa al área
                    </v-btn>
                </div>
            </div>

            <!--Inicia tabla-->
            <v-card>
                <v-card-title>
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        label="Filtrar por nombre"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :search="search"
                    loading-text="Cargando, por favor espere..."
                    :loading="isLoading"
                    :headers="headers"
                    :items="academicAreaPrograms"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1"
                >

                    <template v-slot:item.actions="{ item }">
                        <v-icon
                            class="primario--text"
                            @click="confirmDeleteAcademicAreaProgram(item)"
                        >
                            mdi-delete
                        </v-icon>
                    </template>


                </v-data-table>
            </v-card>
            <!--Acaba tabla-->


            <!------------Seccion de dialogos ---------->

            <!--Crear o editar pregunta-->
            <v-dialog
                v-model="createOrEditDialog.dialogStatus"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title>
                        <span>
                        </span>
                        <span class="text-h5">Asignar programa académico </span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-select
                                        label="Nombre del programa académico"
                                        :items="filteredAcademicPrograms"
                                        :item-value="(academicProgram)=>academicProgram.code"
                                        :item-text="(academicProgram)=>academicProgram.name"
                                        v-model="selectedAcademicProgramCode"
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="createOrEditDialog.dialogStatus = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="handleSelectedMethod"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!--Confirmar borrar programa académico-->
            <confirm-dialog
                :show="deleteAcademicAreaProgramDialog"
                @canceled-dialog="deleteAcademicAreaProgramDialog = false"
                @confirmed-dialog="deleteAcademicAreaProgram(deletedAcademicAreaProgramId)"
            >
                <template v-slot:title>
                    Estás a punto de desasignar el programa académico seleccionado
                </template>

                Esta acción es irreversible

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>
        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";
import AcademicProgramQuestion from "@/models/AcademicProgramQuestion";


export default {
    components: {
        ConfirmDialog,
        AuthenticatedLayout,
        InertiaLink,
        Snackbar,

    },
    props:{
        academicArea: Object
    },
    data: () => {
        return {
            //Table info
            search:'',
            headers: [
                {text: 'Nombre', value: 'name'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            assessmentPeriods: [],
            academicAreaPrograms: [],
            academicPrograms:[],
            selectedAcademicProgramCode:'',

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs

            isLoading: true,

            newAcademicAreaProgram: new AcademicProgramQuestion(),
            editedAcademicAreaProgram: new AcademicProgramQuestion(),
            deletedAcademicAreaProgramId: 0,

            deleteAcademicAreaProgramDialog: false,
            createOrEditDialog: {
                model: 'newAcademicProgramQuestion',
                method: 'createAcademicProgramQuestion',
                dialogStatus: false,
            },

        }
    },

    computed:{
        filteredAcademicPrograms(){
            return this.academicPrograms.filter(academicProgram => {
                return academicProgram.academic_area_id === null;
            })
        }
    },

    async created() {
        await this.getAcademicPrograms();
        await this.getAcademicAreaPrograms();
        console.log(this.academicArea);
        // this.capitalize();
        this.isLoading = false;
    },

    methods: {

        handleSelectedMethod: function () {
            this[this.createOrEditDialog.method]();
        },

        getAcademicAreaPrograms: async function () {
            let request = await axios.get(route('api.academicAreas.index', {academicArea:this.academicArea.id}))
            this.academicAreaPrograms = request.data;
            console.log(this.academicArea)
            console.log(request.data);
        },

        getAcademicPrograms: async function () {
            let request = await axios.get(route('api.academicPrograms.index'))
            this.academicPrograms = request.data;
            console.log(this.academicPrograms)
            console.log(request.data);
        },

        confirmDeleteAcademicAreaProgram: function (academicAreaProgram) {
            this.deletedAcademicAreaProgramId = academicAreaProgram.code;
            this.deleteAcademicAreaProgramDialog = true;
        },

        deleteAcademicAreaProgram: async function (academicProgram) {
            try {
                let request = await axios.post(route('academicAreaPrograms.delete'), {academicAreaProgram: academicProgram});
                this.deleteAcademicAreaProgramDialog = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicAreaPrograms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 7000);
            }

        },

        setAcademicAreaProgramDialogToCreateOrEdit(which, item = null) {
            if (which === 'create') {
                this.createOrEditDialog.method = 'createAcademicProgramQuestion';
                this.createOrEditDialog.model = 'newAcademicProgramQuestion';
                this.createOrEditDialog.dialogStatus = true;
            }
        },
        createAcademicProgramQuestion: async function () {

            let data = {academicProgram: this.selectedAcademicProgramCode, academicArea: this.academicArea}
            try {
                let request = await axios.post(route('academicAreaPrograms.assign'), data);
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success', 2000);
                await this.getAcademicPrograms();
                await this.getAcademicAreaPrograms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'alert', 3000);
            }
        },

    },


}
</script>


