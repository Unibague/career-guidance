<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <div class="d-flex flex-column align-end mb-5">
                <h2 class="align-self-start">Gestionar formularios</h2>
                <div class="d-flex justify-end mt-5">
                    <v-bottom-sheet v-model="sheet">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                class="mr-3"
                                color="red"
                                dark
                                v-bind="attrs"
                                v-on="on"
                            >
                                Otras opciones
                            </v-btn>
                        </template>
                        <v-list>
                            <v-subheader>Menú de otras opciones</v-subheader>
                            <v-list-item
                                @click="getFormsWithoutQuestions"
                            >
                                <v-list-item-avatar>
                                    <v-avatar
                                        size="32px"
                                        tile
                                    >
                                        <v-icon>
                                            mdi-file-question
                                        </v-icon>
                                    </v-avatar>
                                </v-list-item-avatar>
                                <v-list-item-title>Ver formularios sin preguntas</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                @click="getAllForms(true)"
                            >
                                <v-list-item-avatar>
                                    <v-avatar
                                        size="32px"
                                        tile
                                    >
                                        <v-icon>
                                            mdi-file-document-outline
                                        </v-icon>
                                    </v-avatar>
                                </v-list-item-avatar>
                                <v-list-item-title>Ver todos los formularios</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-bottom-sheet>
                    <v-btn
                        class="mr-3"
                        @click="openFormDialog('create','othersForm')"
                    >
                        Crear formulario
                    </v-btn>
                </div>
            </div>


            <h3 class="mt-10 mb-5">Formularios</h3>
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="othersForms"
                :items-per-page="20"
                :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                class="elevation-1"
                :item-class="getRowColor"
            >
                <template v-slot:item="{ item }">
                    <tr>
                        <td>
                            {{ item.name }}
                        </td>
                        <td>
                            {{ item.description === '' ? 'No proporcionada' : item.description }}
                        </td>

                        <td class="d-flex" style="gap: 5px">
                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mr-2 primario--text"
                                        @click="openFormDialog('edit','othersForm',item)"
                                    >
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <span>Editar formulario</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="primario--text"
                                        @click="copy(item.id)"
                                    >
                                        mdi-content-copy
                                    </v-icon>
                                </template>
                                <span>Copiar formulario</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <InertiaLink as="v-icon" class="primario--text"
                                                 v-bind="attrs"
                                                 v-on="on"
                                                 :href="route('forms.show.view',{form:item.id})">
                                        mdi-format-list-bulleted
                                    </InertiaLink>
                                </template>
                                <span>Editar preguntas</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        class="primario--text"
                                        @click="confirmDeleteForm(item)"
                                    >
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <span>Borrar formulario</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{on,attrs}">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on"
                                        v-if="!(item.active)"
                                        class="mr-2 primario--text"
                                        @click="setFormAsActive(item.id)"
                                    >
                                        mdi-cursor-default-click
                                    </v-icon>
                                </template>
                                <span>Definir como formulario activo</span>
                            </v-tooltip>
                        </td>
                    </tr>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear o editar form -->
            <v-dialog
                v-model="createOthersFormDialog"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title>
                        <span>
                        </span>
                        <span class="text-h5">Crear un nuevo formulario</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del formulario *"
                                        required
                                        v-model="othersForm.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        label="Descripción del formulario *"
                                        required
                                        rows="3"
                                        v-model="othersForm.description"
                                    ></v-textarea>
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
                            @click="createOthersFormDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createForm('othersForm')"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!--Confirmar borrar form-->
            <confirm-dialog
                :show="deleteFormDialog"
                @canceled-dialog="deleteFormDialog = false"
                @confirmed-dialog="deleteForm(deletedFormId)"
            >
                <template v-slot:title>
                    Estás a punto de eliminar el formulario seleccionado
                </template>

                ¡Cuidado! esta acción es irreversible

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
import Form from "@/models/Form";
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
            sheet: false,
            //Table info
            headers: [
                {text: 'Nombre', value: 'name'},
                {text: 'Descripción del formulario', value: 'description'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            forms: [],
            othersForms: [],
            //data for modals
            academicPeriods: [],
            assessmentPeriods: [],
            serviceAreas: [],
            dependencies: [],
            positions: [],
            roles: [],
            degrees: [],

            //Forms models
            studentForm: new Form(),
            isServiceAreaDisabled: false,
            isAcademicPeriodDisabled: false,
            othersForm: new Form(),
            isRoleDisabled: false,
            isTeacherLadderDisabled: false,
            isUnitDisabled: false,
            formMethod: 'create',
            deletedFormId: 0,

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },

            //Dialogs
            deleteFormDialog: false,
            createStudentFormDialog: false,
            editStudentFormDialog: false,
            createOthersFormDialog: false,
            editOthersFormDialog: false,
            migrateFormsDialog: false,
            assessmentPeriodsMigrateList : [],
            selectedAssessmentPeriod: [],
            isLoading: true,

        }
    },
    async created() {
        await this.getAllForms();
        this.isLoading = false;
    },

    methods: {

        getRowColor: function (item) {
            return !item.active  ? '#E91E63' : '';
        },

        async getFormsWithoutQuestions () {
            let request = await axios.get(route('api.forms.withoutQuestions'));
            this.forms = Form.createFormsFromArray(request.data);
            this.formatForms();
            showSnackbar(this.snackbar, 'Se han cargado los formularios sin preguntas', 'success');
            this.sheet = false;
        },

        setFormAsActive: async function (formId) {
            try {
                let request = await axios.post(route('api.forms.setActive', {form: formId}));
                showSnackbar(this.snackbar, request.data.message, 'success');
                await this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert');
            }
        },

        confirmDeleteForm: function (form) {
            this.deletedFormId = form.id;
            this.deleteFormDialog = true;
        },
        deleteForm: async function (formId) {
            try {
                let request = await axios.delete(route('api.forms.destroy', {form: formId}));
                this.deleteFormDialog = false;
                showSnackbar(this.snackbar, request.data.message, 'success');
                this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 3000);
            }

        },
        openFormDialog(method, model, form = null) {
            this.formMethod = method;
            if (method === 'edit') {
                this[model] = Form.copy(form);
            } else {
                this[model] = new Form();
            }

            if (model === 'studentForm') {
                this.createStudentFormDialog = true;
            }
            if (model === 'othersForm') {
                console.log(this.othersForm);
                this.createOthersFormDialog = true;
            }
        },

        getAllForms: async function (notify = false) {
            let request = await axios.get(route('api.forms.index'));
            this.forms = Form.createFormsFromArray(request.data);
            console.log(this.forms);
            this.formatForms();
            console.log(this.othersForms);
            if (notify) {
                showSnackbar(this.snackbar, 'Mostrando todos los formularios')
            }
        },
        copy: async function (formId) {
            try {
                await axios.post(route('api.forms.copy', {form: formId}));
                showSnackbar(this.snackbar, 'Formulario copiado exitosamente');
                await this.getAllForms();
            } catch (e) {
                showSnackbar(this.snackbar, e.response.data.message, 'red', 3000);
            }
        },

        formatForms: function () {
            const forms = this.forms;
            this.othersForms = [];
            forms.forEach((form) => {
                this.othersForms.push(form);
            })
        },

        createForm: async function (formModel) {
            if (this[formModel].hasEmptyProperties()) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            if (formModel === 'othersForm') {
                this[formModel].type = 'otros';
            }
            const endpoint = route('api.forms.store', {form: this[formModel].id});
            const axiosMethod = 'post';
            let data = this[formModel].toObjectRequest();
            if(data.position === "Todas"){
                data.position = null
            }

            try {
                let request = await axios[axiosMethod](endpoint, data);
                if (formModel === 'othersForm') {
                    this.createOthersFormDialog = false;
                }
                showSnackbar(this.snackbar, request.data.message, 'success', 2000);
                await this.getAllForms();
                //Clear form information
                this[formModel] = new Form();

            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert', 3000);
            }
        }
    },

}
</script>
