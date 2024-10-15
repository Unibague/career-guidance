<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>
        <v-container>
            <div class="d-flex mb-2" style="justify-content: center; flex-grow: 1">
<!--                <h2 class="align-self-start">Gestionar respuestas obtenidas</h2>-->
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="downloadSpecificReport"
                    >
                        Descargar reporte
                    </v-btn>
<!--                    <v-btn-->
<!--                        color="primario"-->
<!--                        class="grey&#45;&#45;text text&#45;&#45;lighten-4"-->
<!--                        @click="downloadExcel()"-->
<!--                    >-->
<!--                        Descargar reporte consolidado-->
<!--                    </v-btn>-->
            </div>
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
import Chart from "chart.js/auto"
import Question from "@/models/Question";
import Papa from 'papaparse';

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
            search: '',
            headers: [],
            //Display data
            academicPrograms:[],
            assessments: [],
            assessmentPeriod: '',
            assessmentPeriods: [],
            competences: [],
            dependency: '',
            dependencies:[],
            functionary: '',
            functionaries:[],
            grades: [],
            responseIdeals: [],
            role: "",
            roles: [],
            openAnswers: [],
            //selected
            graphFunctionary:'',
            graphFunctionaryDatasets: [],
            openAnswersFunctionary:'',
            results:[],
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Snackbars
            showChartDialog: false,
            showOpenAnswersDialog: false,
            //Dialogs
            isLoading: true,
            confirmSavePDF: false,
            isUserAdmin: true,
            datasets:[]
        }
    },

    async created() {
        await this.getResults();
        this.isLoading = false;
    },

    methods: {

        async downloadSpecificReport(){
            const request = await fetch(route('results.testSpecificReport'), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/octet-stream' // Assuming the response is binary
                }
            });
            const blob = await request.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Test_Vocacional_Desagregado_2024.xlsx');
            document.body.appendChild(link);
            link.click();
        },

        getAcademicProgramResultForItem(item,academicProgram){
            return this.findResultByAcademicProgramCode(item.academic_programs_result, 'academic_program_code',academicProgram.code)
        },

        findResultByAcademicProgramCode(array, key, value){
            let result = array.find(item => item[key] === value);
            return result !== undefined ? result.value: null;
        },

        async getResults(){
            let request = await axios.post(route('results.academicPrograms'));
            this.results = request.data;
            console.log(this.results);
        },

        async getAcademicPrograms() {
            let request = await axios.get(route('api.academicPrograms.index'));
            console.log(request.data,'ACADEMIC PROGRAMS')
            return request.data;
        },

        downloadExcel (){
            if (this.results.length === 0){
                showSnackbar(this.snackbar, "No hay datos para guardar", 'alert');
            }

            let excelInfo = this.results.map(item =>{
                let programsData = {}
                this.academicPrograms.forEach((academicProgram, index) =>{
                    // item[`c${competence.position}`].toLocaleString("pt-BR")
                    programsData[academicProgram.name] = this.getAcademicProgramResultForItem(item,academicProgram)
                })

                return {
                    ['Identificación']:item.identification,
                    ...programsData
                }
            })

            let csv = Papa.unparse(excelInfo, {delimiter:';'});
            var csvData = new Blob(["\uFEFF"+csv], {type: 'text/csv;charset=utf-8;'});
            var csvURL =  null;
            if (navigator.msSaveBlob)
            {
                csvURL = navigator.msSaveBlob(csvData, 'Resultados_Test_Vocacional_Consolidado.csv');
            }
            else
            {
                csvURL = window.URL.createObjectURL(csvData);
            }
            var tempLink = document.createElement('a');
            tempLink.href = csvURL;
            tempLink.setAttribute('download', 'Resultados_Test_Vocacional_Consolidado.csv');
            tempLink.click();
        },

        capitalize($field){
            return $field.toLowerCase().split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        },
    },
}
</script>
