<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container fluid>
            <div class="d-flex flex-column align-end mb-2" id="malparido">
                <h2 class="align-self-start">Gestionar respuestas obtenidas</h2>
                <div class="mt-5">

                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="downloadSpecificReport"
                    >
                        Descargar reporte desagregado
                    </v-btn>

                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="downloadExcel()"
                    >
                        Descargar reporte consolidado
                    </v-btn>
                </div>
            </div>


            <!--Inicia tabla-->
<!--            <v-card>
                <v-data-table
                    :search="search"
                    loading-text="Cargando, por favor espere..."
                    :loading="isLoading"
                    :headers="headers"
                    :items="results"
                    :items-per-page="20"
                    :footer-props="{
                        'items-per-page-options': [20,50,100,-1]
                    }"
                    class="elevation-1 mt-5"
                >

                    <template
                        v-for="(academicProgram,index) in academicPrograms"
                        v-slot:[`item.${academicProgram.code}`]="{ item }"
                    >
                        {{ getAcademicProgramResultForItem(item,academicProgram)}}
                    </template>

                </v-data-table>
            </v-card>-->
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

            //Graph data
            chartColors: [{role: 'autoevaluación', name: 'blue'}, {role: 'jefe', name: 'red'}, {role: 'par', name: 'green'},
                {role: 'cliente interno', name: 'purple'}, {role: 'cliente externo', name: '#00BFA5'}, {role: 'promedio final', name: 'black'}],
            datasets:[]
        }
    },

    async created() {
        // await this.getTableHeaders();
        await this.getResults();
        await this.getTableHeaders();
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

        getTableHeaders: async function (){
            this.headers.push({text:"Número de identificación", value:"identification"})
            this.academicPrograms = await this.getAcademicPrograms();
            this.academicPrograms.forEach(academicProgram=> {
                this.headers.push({text:academicProgram.name,value:academicProgram.code})
            })
            console.log(this.headers);
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

        getGraph(){

            let graph = document.getElementById("graph").getContext("2d");

            this.chart = new Chart(graph, {
                type:"line",
                data:{
                    labels: this.competences.map(competence => competence.name),
                    datasets: this.datasets
                },
                options: {
                    plugins: {
                        filler: {
                            propagate: false
                        },
                    },
                    layout:{
                        padding:{
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    },
                    legend: {
                        display: true,
                        /*   labels:{
                               padding:20
                           },*/
                        position: "bottom"
                    },
                    responsive: true,
                    tooltips: {
                        mode: "index",
                        intersect: false
                    },
                    hover: {
                        mode: "nearest",
                        intersect: false
                    },

                    scales: {
                        x:
                            {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Competencias',
                                    color: 'black',
                                    font: {
                                        size: 15,
                                        weight: 'bold',
                                        lineHeight: 1.2,
                                    },
                                },
                                position: 'top'
                            }
                        ,
                        y:
                            {
                                min: 0,
                                max: 5.4,
                                display: true,

                                title: {
                                    display: true,
                                    text: 'Valores obtenidos',
                                    color: 'black',
                                    font: {
                                        size: 15,
                                        weight: 'bold',
                                        lineHeight: 1.2,
                                    },
                                },

                                ticks:{
                                    callback: (value, index, values) => (index == (values.length-1)) ? undefined : value,
                                },
                            }
                    }
                },
            })
        },

        capitalize($field){
            return $field.toLowerCase().split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        },

        addAllElementSelectionItem(model, text) {
            model.unshift({user_id: '', name: text});
        },
    },

}
</script>
