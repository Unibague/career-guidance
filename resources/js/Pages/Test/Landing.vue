<template>
    <GeneralLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container class="d-flex flex-column justify-center align-center fill-height">
                <v-card>
                    <v-card-title class="justify-center align-center">
                        <span>
                        </span>
                        <span class="text-h5 ">Antes de comenzar, unos cuantos datos básicos</span>
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
                                        label="Teléfono"
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
            phone:'',
            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },

            validateAge:[
                v => !!v || "Campo requerido",
                v => ( v && v >= 1 ) || "Debe ser un  valor mayor a 1",
                v => ( v && v <= 99 ) || "Debe ser un valor menor que 100",
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
                return 'No escribas letras,comas,espacios o puntos.';
            }
            return true;
        },
/*        async redirect(){

            this.basicInformationDialog = false;
            let data = {userName:this.userName, identification:this.identification}
            let request = await axios.post(route('tests.startTest'), {data});
        },*/

        async validateSubmit(){
            if (!this.userName || !this.identification || !this.age || !this.sex || !this.phone) {
                showSnackbar(this.snackbar, 'Debes diligenciar todos los campos obligatorios', 'red', 2000);
                return;
            }

            // Validation passed
            // Submit the form programmatically

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

                let request = await axios.post(route('tests.validateInfo', formDataJson) )
                form.submit();

            } catch (e){

                let firstError = Object.values(e.response.data.errors)[0][0]

                showSnackbar(this.snackbar, firstError, 'red', 2000);
            }

        }

    },
}
</script>
