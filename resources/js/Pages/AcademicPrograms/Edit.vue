<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-7">
                <h2 class="align-self-start">Visualizando el programa académico {{this.academicProgram.name}}</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="setAcademicProgramQuestionDialogToCreateOrEdit('create')"
                    >
                        Crear nueva pregunta
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
                    :items="academicProgramQuestions"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1"
                >

                    <template v-slot:item.actions="{ item }">
                        <v-icon
                            class="mr-2 primario--text"
                            @click="setAcademicProgramQuestionDialogToCreateOrEdit('edit',item)"
                        >
                            mdi-pencil
                        </v-icon>

                        <v-icon
                            class="primario--text"
                            @click="confirmDeleteAcademicProgramQuestion(item)"
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
                        <span class="text-h5">Crear/Editar pregunta</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la pregunta"
                                        required
                                        v-model="$data[createOrEditDialog.model].name"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                        <small>Los campos con * son obligatorios</small>
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

            <!--Confirmar borrar pregunta-->
            <confirm-dialog
                :show="deleteAcademicProgramQuestionDialog"
                @canceled-dialog="deleteAcademicProgramQuestionDialog = false"
                @confirmed-dialog="deleteAcademicProgramQuestion(deletedAcademicProgramQuestionId)"
            >
                <template v-slot:title>
                    Estás a punto de eliminar la pregunta seleccionada
                </template>

                ¡Cuidado! Ya no se podrá observar esta pregunta al generar el reporte de las respuestas obtenidas.

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>





<!--
            &lt;!&ndash;Crear o editar Compromiso&ndash;&gt;
            <v-dialog
                v-model="createQuestionDialog"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title>
                        <span>
                        </span>
                        <span class="text-h5">Crear/Editar pregunta</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Ingrese el nombre de la pregunta"
                                        v-model="question"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="createQuestionDialog=false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createQuestion()"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>-->


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
        academicProgram: Object
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
            academicProgramQuestions: [],

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs

            isLoading: true,

            newAcademicProgramQuestion: new AcademicProgramQuestion(),
            editedAcademicProgramQuestion: new AcademicProgramQuestion(),
            deletedAcademicProgramQuestionId: 0,

            deleteAcademicProgramQuestionDialog: false,
            createOrEditDialog: {
                model: 'newAcademicProgramQuestion',
                method: 'createAcademicProgramQuestion',
                dialogStatus: false,
            },

        }
    },

    async created() {
        await this.getAcademicProgramQuestions();
        // this.capitalize();
        this.isLoading = false;
    },

    methods: {

        handleSelectedMethod: function () {
            this[this.createOrEditDialog.method]();
        },

        getAcademicProgramQuestions: async function () {
            let request = await axios.get(route('api.academicProgramQuestions.index', {academicProgram:this.academicProgram.code}))
            this.academicProgramQuestions = request.data;
            console.log(this.academicProgram)
            console.log(request.data);
        },

        editAcademicProgramQuestion: async function () {
            //Verify request

            this.editedAcademicProgramQuestion.academic_program_code = this.academicProgram.code;
            if (this.editedAcademicProgramQuestion.hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            //Recollect information
            let data = this.editedAcademicProgramQuestion.toObjectRequest();

            try {
                let request = await axios.patch(route('api.academicProgramQuestions.update', {'academicProgramQuestion': this.editedAcademicProgramQuestion.id}), data);
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicProgramQuestions();
                //Clear role information
                this.editedAcademicProgramQuestion = new AcademicProgramQuestion();
            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert');
            }
        },

        confirmDeleteAcademicProgramQuestion: function (academicProgramQuestion) {
            this.deletedAcademicProgramQuestionId = academicProgramQuestion.id;
            this.deleteAcademicProgramQuestionDialog = true;
        },

        deleteAcademicProgramQuestion: async function (academicProgramQuestion) {
            try {
                let request = await axios.delete(route('api.academicProgramQuestions.destroy', {academicProgramQuestion: academicProgramQuestion}));
                this.deleteAcademicProgramQuestionDialog = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicProgramQuestions();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 7000);
            }

        },

        setAcademicProgramQuestionDialogToCreateOrEdit(which, item = null) {
            if (which === 'create') {
                this.createOrEditDialog.method = 'createAcademicProgramQuestion';
                this.createOrEditDialog.model = 'newAcademicProgramQuestion';
                this.createOrEditDialog.dialogStatus = true;
            }

            if (which === 'edit') {
                this.editedAcademicProgramQuestion = AcademicProgramQuestion.fromModel(item);
                this.createOrEditDialog.method = 'editAcademicProgramQuestion';
                this.createOrEditDialog.model = 'editedAcademicProgramQuestion';
                this.createOrEditDialog.dialogStatus = true;
            }

        },
        createAcademicProgramQuestion: async function () {

            this.newAcademicProgramQuestion.program_code = this.academicProgram.code;

            if (this.newAcademicProgramQuestion.hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }

            let data = this.newAcademicProgramQuestion.toObjectRequest();
            //Clear competence information
            this.newAcademicProgramQuestion = new AcademicProgramQuestion();

            try {
                let request = await axios.post(route('api.academicProgramQuestions.store'), data);
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success', 2000);
                await this.getAcademicProgramQuestions();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'alert', 3000);
            }
        },

    },


}
</script>


