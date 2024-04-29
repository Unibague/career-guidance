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
                        @click="createQuestionDialog=true"
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

<!--                    <template v-slot:item.actions="{ item }">
                        <v-tooltip top >
                            <template v-slot:activator="{on,attrs}">
                                <InertiaLink :href="route('api.academicPrograms.edit', {academicProgram:item.code})">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario&#45;&#45;text"
                                    >
                                        mdi-school
                                    </v-icon>
                                </InertiaLink>
                            </template>
                            <span></span>
                        </v-tooltip>
                    </template>-->



                </v-data-table>
            </v-card>
            <!--Acaba tabla-->

            <!--Crear o editar Compromiso-->
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
            </v-dialog>


        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";


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
                {text: 'Nombre', value: 'question'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            assessmentPeriods: [],
            academicProgramQuestions: [],
            question:'',

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs
            deleteDependencyDialog: false,
            isLoading: true,
            createQuestionDialog: false,

        }
    },

    async created() {
        await this.getAcademicProgramQuestions();
        // this.capitalize();
        this.isLoading = false;
    },

    methods: {

        getAcademicProgramQuestions: async function () {
            let request = await axios.get(route('api.academicProgramQuestions.index', {academicProgram:this.academicProgram.code}))
            this.academicProgramQuestions = request.data;
            console.log(this.academicProgram)
            console.log(request.data);
        },

        async createQuestion(){
            let data = {academicProgram: this.academicProgram, question:this.question}
            let request = await axios.post(route('api.academicProgramQuestions.store'), data)
            showSnackbar(this.snackbar, request.data.message, 'success');
            await this.getAcademicProgramQuestions();
            this.createQuestionDialog = false;
            console.log(request.data);
        }

    },


}
</script>


