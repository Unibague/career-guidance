<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-7">
                <h2 class="align-self-start">Gestionar Programas académicos </h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="syncAcademicPrograms"
                    >
                        Sincronizar programas académicos
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
                    :items="academicPrograms"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1"
                >

                    <template v-slot:item.actions="{ item }">
                        <v-tooltip top >
                            <template v-slot:activator="{on,attrs}">
                                <InertiaLink :href="route('api.academicPrograms.edit', {academicProgram:item.code})">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario--text"
                                    >
                                        mdi-school
                                    </v-icon>
                                </InertiaLink>
                            </template>
                            <span>Visualizar programa académico</span>
                        </v-tooltip>
                    </template>
                </v-data-table>
            </v-card>
            <!--Acaba tabla-->


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
    data: () => {
        return {
            //Table info
            search:'',
            headers: [
                {text: 'Código', value: 'code', align: 'center'},
                {text: 'Nombre', value: 'name'},
                {text: 'Preguntas', value: 'academic_program_questions.length'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            assessmentPeriods: [],
            academicPrograms: [],
            //Units models

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

        }
    },

    async created() {
        await this.getAcademicPrograms();
        // this.capitalize();
        this.isLoading = false;
    },

    methods: {

        syncAcademicPrograms: async function () {
            try {
                let request = await axios.post(route('api.academicPrograms.sync'));
                console.log(request);
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicPrograms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'alert', 7000);
            }
        },

        getAcademicPrograms: async function () {
            let request = await axios.get(route('api.academicPrograms.index'));
            this.academicPrograms = request.data
            console.log(this.academicPrograms);
        },

    },
}
</script>


