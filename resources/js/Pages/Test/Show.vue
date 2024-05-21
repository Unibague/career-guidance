<template>
    <GeneralLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>
            <v-row class="mb-2" justify="center">

                <v-col cols="12">
                    <h2 class="text-center ">
                        {{ test.name }}
                    </h2>
                </v-col>

                <v-col cols="12" md="8" align-self="center">
                    <div class="text-justify text-h6" style="white-space: pre-wrap">{{ test.description }}</div>
                </v-col>

            </v-row>

            <v-row class="mt-3" justify="center" dense>
                <v-col cols="12" md="8">
                    <v-form
                        ref="form"
                        lazy-validation
                        v-model="valid">
                        <v-card v-for="question in currentQuestions" :key="question.id" class="mb-8">
                            <v-card-text>
                                <div class="text-h6 text-justify mb-5">
                                    {{ question.name }}
                                </div>
                                <v-row>
                                    <v-col cols="12">
                                        <v-select
                                            v-if="question.type === 'multiple'"
                                            v-model="question.answer"
                                            required
                                            outlined
                                            placeholder="Por favor, selecciona una respuesta"
                                            :items="question.options"
                                            item-text="placeholder"
                                            item-value="value"
                                            :rules="selectRules"
                                        >
                                            <template v-slot:item="slotProps">
                                                {{ slotProps.item.placeholder }}
                                            </template>
                                        </v-select>
                                        <v-text-field v-model="question.answer" v-else outlined
                                                      required
                                                      placeholder="Por favor, ingresa tu respuesta en este campo"
                                                      :rules="typeRules"

                                        />
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-form>
                </v-col>
            </v-row>
            <v-row justify="center" v-if="canSend && isLastPage">
                <v-col cols="12" class="d-flex justify-center">
                    <v-btn color="primario"
                           large
                           class="grey--text text--lighten-4"
                           @click="validate"
                    >Enviar formulario
                    </v-btn>
                </v-col>
            </v-row>

            <v-row justify="center">
                <v-col cols="12" class="d-flex justify-center">
                    <v-pagination v-model="currentPage" :length="pageCount"></v-pagination>
                </v-col>
            </v-row>


        </v-container>
        <v-dialog
            v-model="dialog"
            persistent
            max-width="290"
        >
            <v-card>
                <v-spacer></v-spacer>
                <v-card-text class="text-h5" style="font-weight: bold; color: black; text-align: center">
                    Test de Orientación Vocacional
                </v-card-text>
                <v-card-text>El test ha sido diligenciado exitosamente. A continuación podrás evidenciar el puntaje obtenido
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>

                    <form :action="route('results.showGraph')" method="POST">
                        <input type="hidden" name="user" :value="JSON.stringify(user)">
                        <input type="hidden" name="answers" :value="JSON.stringify(this.test.questions)">
                        <v-btn
                            type="submit"
                            color="primario"
                            text
                        >
                            Continuar
                        </v-btn>
                    </form>

<!--                    <v-btn
                        color="green darken-1"
                        text
                        @click="redirect"
                    >
                        Aceptar
                    </v-btn>-->
                </v-card-actions>
            </v-card>
        </v-dialog>

    </GeneralLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";

export default {
    components: {
        GeneralLayout,
        ConfirmDialog,
        AuthenticatedLayout,
        InertiaLink,
        Snackbar,
    },
    data: () => {
        return {
            //Table info
            valid: false,
            selectRules: [
                v => !!v || 'Por favor, selecciona una opción de respuesta'
            ],
            typeRules:[
                v => !!v || 'Por favor, escribe tu respuesta',
            ],
            dialog: false,
            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            isLoading: true,

            currentPage: 1,
            questionsPerPage: 10,
        }
    },
    props: {
        test: Object,
        user: Object,
        canSend: Boolean
    },

    computed:{
        currentQuestions() {
            const start = (this.currentPage - 1) * this.questionsPerPage;
            const end = start + this.questionsPerPage;
            return this.test.questions.slice(start, end);
        },
        pageCount() {
            return Math.ceil(this.test.questions.length / this.questionsPerPage);
        },
        isLastPage() {
            return this.currentPage === this.pageCount;
        },
    },

    watch:{
        currentPage(){
            this.scrollToTop();
        }
    },

    async created() {
        this.parseQuestions()
        this.shuffleQuestions(this.test.questions);
        this.isLoading = false;
    },

    methods: {

        scrollToTop() {
            const form = this.$refs.form;
            if (form) {
                form.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                window.scrollTo(0,0);
            }

            window.scrollTo(0,0);

            console.log('perro hijueputaaaaa')
        },

        redirect: function () {
            window.location.href = route('tests.index.view');
        },
        sendForm: async function () {
            try {
                console.log(this.test.questions);
                await axios.post(route('api.tests.store'),
                    {
                        answers: this.test.questions,
                        form_id: this.test.id,
                        userInfo: this.user
                    });
                this.dialog = true;
            } catch (e) {
                showSnackbar(this.snackbar, prepareErrorText(e), 'alert')
            }
        },

        shuffleQuestions(array){
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1)); // Generate random index
                [array[i], array[j]] = [array[j], array[i]]; // Swap elements
            }
            return array;
        },

        parseQuestions() {
            this.test.questions = JSON.parse(this.test.questions);
        },
        validate() {
            let isValid = this.$refs.form.validate()
            if (isValid === true) {
                this.sendForm()
            }
            else{
                showSnackbar(this.snackbar,
                    'Debes contestar todas las preguntas para poder enviar el formulario', 'alert');
            }
        },
    },


}
</script>
