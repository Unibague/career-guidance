<template>
    <GeneralLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <!------------Seccion de dialogos ---------->
        <v-container class="d-flex flex-column justify-center align-center fill-height">
        <!--Crear o editar form -->
        <v-dialog
            v-model="disclaimerDialog"
            persistent
            max-width="650px"
        >
            <v-card>
                <v-card-title style="justify-content: center">
                        <span>
                        </span>
                    <h3>Consentimientos</h3>
                </v-card-title>
                <v-card-text>
                    <v-container style="text-align: center">
                        <h2> Hola, te damos la bienvenida a esta oportunidad de explorar algunos de tus intereses
                            sobre actividades en tu posible futuro como profesional.</h2>
                        <br>
                        <h2>  Ten en cuenta que no estás obligado a contestar, puedes dejar de responder en cualquier momento.
                            Garantizamos que tus datos y respuestas son confidenciales, anónimos
                            y están protegidos por la ley 1090 de 2006. </h2>
                    </v-container>
                </v-card-text>
                <v-card-actions style="justify-content: center">
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        style="border-radius: 10px; padding: 10px 50px 10px 50px; align-self: center"
                        @click="function () {disclaimerDialog = false }"
                    >
                        Autorizar y continuar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-card v-if="disclaimerDialog === false">
            <v-card-title class="justify-center align-center">
                <span>
                </span>
                <span class="text-h5 ">Por favor, ingresa tus datos a continuación </span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                label="Nombre completo"
                                :rules="[validateUserName]"
                                required
                                v-model="userName"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Número de identificación"
                                :rules="[validateIdentification]"
                                required
                                v-model="identification"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                                label="Sexo"
                                :items="['Hombre', 'Mujer']"
                                required
                                v-model="sex"
                            ></v-select>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Edad"
                                type="number"
                                min=1
                                max="99"
                                required
                                :rules="validateAge"
                                v-model="age"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Correo electrónico"
                                required
                                :rules="validateEmail"
                                placeholder="correo@dominio.com"
                                v-model="email"
                                type="email"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                label="Celular"
                                :rules="[validateIdentification]"
                                required
                                v-model="phone"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
            <v-card-actions class="justify-center">
                <form :action="route('tests.startTest')" method="POST" @submit.prevent="validateSubmit" id="form">
                    <input type="hidden" name="userName" :value="userName">
                    <input type="hidden" name="identification" :value="identification">
                    <input type="hidden" name="age" :value="age">
                    <input type="hidden" name="sex" :value="sex">
                    <input type="hidden" name="email" :value="email">
                    <input type="hidden" name="phone" :value="phone">
                    <v-btn
                        type="submit"
                        color="primario"
                        class="grey--text text--lighten-4"
                        style="border-radius: 10px; background-color: #db5523 ;padding: 10px 50px 10px 50px"
                    >
                        Continuar
                    </v-btn>
                 </form>

            </v-card-actions>
        </v-card>
        </v-container>
    </GeneralLayout>
</template>

<!--&lt;!&ndash; Meta Pixel Code &ndash;&gt;-->
<!--<script>-->
<!--!function(f,b,e,v,n,t,s)-->
<!--{if(f.fbq)return;n=f.fbq=function(){n.callMethod?-->
<!--    n.callMethod.apply(n,arguments):n.queue.push(arguments)};-->
<!--    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';-->
<!--    n.queue=[];t=b.createElement(e);t.async=!0;-->
<!--    t.src=v;s=b.getElementsByTagName(e)[0];-->
<!--    s.parentNode.insertBefore(t,s)}(window, document,'script',-->
<!--    'https://connect.facebook.net/en_US/fbevents.js');-->
<!--fbq('init', '187604011089503');-->
<!--fbq('track', 'PageView');-->
<!--</script>-->
<!--<noscript><img height="1" width="1" style="display:none"-->
<!--               src="https://www.facebook.com/tr?id=187604011089503&ev=PageView&noscript=1"-->
<!--/></noscript>-->
<!--&lt;!&ndash; End Meta Pixel Code &ndash;&gt;-->



<script>
import GeneralLayout from "@/Layouts/GeneralLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";

export default {
    components: {
        ConfirmDialog,
        GeneralLayout,
        InertiaLink,
        Snackbar,
    },
    data: () => {
        return {
            //Table info
            search: '',
            userName: '',
            identification: '',
            sex:'',
            age:'',
            email:'',
            phone:'',
            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            disclaimerDialog: true,
            validateAge:[
                v => !!v || "Campo requerido",
                v => ( v && v >= 1 ) || "Debe ser un  valor mayor a 1",
                v => ( v && v <= 99 ) || "Debe ser un valor menor que 100",
            ],
            validateEmail:[
                v => /.+@.+/.test(v) || 'Formato incorrecto'
            ],
            //Dialogs
            isLoading: true,
        }
    },
    props:{
        errors: Object
    },

    async created() {
        this.isLoading = false;
    },

    methods: {

        validateUserName(value) {
            const lettersRegex = /^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/;
            if (!value.match(lettersRegex)) {
                return 'Escribe únicamente letras';
            } else if (value.length < 3) {
                return 'Tu nombre debe contener mínimo tres caracteres';
            }
            return true;
        },
        validateIdentification(value) {
            const numbersRegex = /^[0-9]+$/;
            if (!value.match(numbersRegex)) {
                return 'No escribas letras, comas, espacios o puntos.';
            }
            return true;
        },
        async validateSubmit(){
            if (!this.userName || !this.identification || !this.age || !this.sex || !this.email || !this.phone) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }
            // Validation passed, therefore submit the form
            try{
                const form = document.getElementById('form'); // Replace 'yourFormId' with the actual ID of your form
                // console.log(form);
                const formData = new FormData(form);
                // Convert FormData object to JSON
                const formDataJson = {};
                formData.forEach((value, key) => {
                    formDataJson[key] = value;
                });
                console.log(formDataJson);
                // Now formDataJson contains all the form data
                await axios.post(route('tests.validateInfo', formDataJson) )
                form.submit();
            } catch (e){
                let firstError = Object.values(e.response.data.errors)[0][0]
                showSnackbar(this.snackbar, firstError, 'red', 2000);
            }
        }
    },
}
</script>
