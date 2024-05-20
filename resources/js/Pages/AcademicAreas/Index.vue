<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-7">
                <h2 class="align-self-start">Gestionar Áreas de conocimiento </h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="setAcademicAreaDialogToCreateOrEdit('create')"
                    >
                        Crear nueva área
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
                    :items="academicAreas"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1"
                >

                    <template v-slot:item.academic_programs="{ item }">
                        {{mapAcademicPrograms(item.academic_programs)}}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <v-tooltip top >
                            <template v-slot:activator="{on,attrs}">
                                <InertiaLink :href="route('api.academicAreas.edit', {academicArea:item.id})">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario--text"
                                    >
                                        mdi-school
                                    </v-icon>
                                </InertiaLink>
                            </template
                            <span>Visualizar área</span>
                        </v-tooltip>

                        <v-tooltip top >
                            <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario--text"
                                        @click="setAcademicAreaDialogToCreateOrEdit('edit',item)"
                                    >
                                        mdi-pencil
                                    </v-icon>
                            </template>
                            <span>Editar programas académicos </span>
                        </v-tooltip>


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
                        <span class="text-h5">Crear/Editar área de conocimiento</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del área"
                                        required
                                        v-model="$data[createOrEditDialog.model].name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        label="Mensaje descriptivo del área"
                                        required
                                        v-model="$data[createOrEditDialog.model].message"
                                    ></v-textarea>
                                </v-col>

<!--                                <v-col cols="3" v-for="academicProgram in $data[createOrEditDialog.model].academicPrograms" :key="academicProgram.id">-->
<!--                                    <v-textarea-->
<!--                                        label="Mensaje descriptivo del área"-->
<!--                                        required-->
<!--                                        v-model="academicProgram.id"-->
<!--                                    ></v-textarea>-->
<!--                                </v-col>-->

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


        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";
import AcademicArea from "@/models/AcademicArea";


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
                {text: 'Nombre', value: 'name'},
                {text: 'Programas académicos', value: 'academic_programs'},
                {text: 'Descripción', value: 'message'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            academicAreas: [],
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

            newAcademicArea: new AcademicArea(),
            editedAcademicArea: new AcademicArea(),
            deletedAcademicAreaId: 0,

            deleteAcademicAreaDialog: false,
            createOrEditDialog: {
                model: 'newAcademicArea',
                method: 'createAcademicArea',
                dialogStatus: false,
            },

        }
    },

    async created() {
        await this.getAcademicAreas();
        // this.capitalize();
        this.isLoading = false;
    },

    methods: {

        getAcademicAreas: async function () {
            let request = await axios.get(route('api.academicAreas.index'));
            this.academicAreas = request.data
            console.log(this.academicAreas);
        },

        mapAcademicPrograms(academicProgramsArray){
           let mappedItems = academicProgramsArray.map(academicProgram => `${academicProgram.name}`)
           return mappedItems.join(', ');
        },

        handleSelectedMethod: function () {
            this[this.createOrEditDialog.method]();
        },

        editAcademicArea: async function () {
            //Verify request

            if (this.editedAcademicArea.hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            //Recollect information
            let data = this.editedAcademicArea.toObjectRequest();

            try {
                let request = await axios.patch(route('api.academicAreas.update', {'academicArea': this.editedAcademicArea.id}), data);
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicAreas();
                //Clear role information
                this.editedAcademicArea = new AcademicArea();
            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert');
            }
        },

        confirmDeleteAcademicArea: function (academicArea) {
            this.deletedAcademicAreaId = academicArea.id;
            this.deleteAcademicAreaDialog = true;
        },

        deleteAcademicArea: async function (academicArea) {
            try {
                let request = await axios.delete(route('api.academicAreas.destroy', {academicArea: academicArea}));
                this.deleteAcademicAreaDialog = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAcademicAreas();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 7000);
            }

        },

        setAcademicAreaDialogToCreateOrEdit(which, item = null) {
            if (which === 'create') {
                this.createOrEditDialog.method = 'createAcademicArea';
                this.createOrEditDialog.model = 'newAcademicArea';
                this.createOrEditDialog.dialogStatus = true;
            }

            if (which === 'edit') {
                this.editedAcademicArea = AcademicArea.fromModel(item);
                console.log(this.editedAcademicArea);
                this.createOrEditDialog.method = 'editAcademicArea';
                this.createOrEditDialog.model = 'editedAcademicArea';
                this.createOrEditDialog.dialogStatus = true;
            }

        },
        createAcademicArea: async function () {

            if (this.newAcademicArea.hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            let data = this.newAcademicArea.toObjectRequest();
            //Clear competence information
            this.newAcademicArea = new AcademicArea();

            try {
                let request = await axios.post(route('api.academicAreas.store'), data);
                this.createOrEditDialog.dialogStatus = false;
                showSnackbar(this.snackbar, request.data.message, 'success', 2000);
                await this.getAcademicAreas();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'alert', 3000);
            }
        },

    },
}
</script>


