<template>
    <GeneralLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>

            <h2 class="black--text pt-5" style="text-align: center; margin-bottom: 10px"> {{this.user.name}}, ¡este es tu resultado!:</h2>

            <div class="d-flex flex-column align-end mb-7">
                <v-btn color="primario" class="grey--text text--lighten-4" @click="downloadResults()"> Descargar mis resultados </v-btn>
            </div>

            <h3 class="black--text pt-4" style="text-align: left; margin-bottom: 15px"> Clasificación por áreas de conocimiento:</h3>

            <div style="width: 60%; margin: 0 auto; position: relative">
            <canvas id="pieChart"></canvas>
            </div>


            <h3 class="black--text" style="text-align: left; margin-top: 75px; margin-bottom: 25px"> Programas de mayor interés </h3>
            <canvas id="barChart"></canvas>

            <v-dialog
                v-model="academicAreaMessageDialog"
                persistent
                max-width="650px"
            >
                <v-card>
                    <v-card-title style="justify-content: center">
                        <span>
                        </span>
                        <h3>{{this.selectedAcademicAreaName}}</h3>
                    </v-card-title>
                    <v-card-text>
                        <v-container style="text-align: center">
                            <h2> {{this.selectedAcademicAreaMessage}}</h2>
                        </v-container>
                    </v-card-text>
                    <v-card-actions style="justify-content: center">
                        <v-btn
                            color="primario"
                            class="grey--text text--lighten-4"
                            style="border-radius: 10px; padding: 10px 50px 10px 50px; align-self: center"
                            @click="academicAreaMessageDialog = false"
                        >
                            Cerrar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>


        </v-container>
    </GeneralLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";
import Snackbar from "@/Components/Snackbar";
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels"
Chart.register(ChartDataLabels);
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
            search: '',
            userName: '',
            identification: '',
            academicProgramsResults:[],
            academicAreasResults:[],
            academicAreasInfo:[],

            //charts
            barChart:'',
            pieChart:'',

            //Snackbars
            snackbar: {
                text: "",
                type: 'alert',
                status: false,
                timeout: 2000,
            },
            //Dialogs
            basicInformationDialog: true,
            isLoading: true,
            academicAreaMessageDialog: false,
            selectedAcademicAreaName: '',
            selectedAcademicAreaMessage: '',
        }
    },


    props: {
        user: Object,
    },


    async created() {
        await this.getAcademicAreasResult();
        this.getPieChart();
        await this.getAcademicProgramsResult();
        this.getBarChart();
        this.isLoading = false;
        // Add event listener for orientation change
        window.addEventListener("orientationchange", () => {
            window.location.reload();
        });

    },

    methods: {

        async getAcademicProgramsResult(){
            let request = await axios.post(route('results.academicPrograms'),{identification:this.user.identification});
            this.academicProgramsResults = request.data;
            console.log(this.academicProgramsResults);
        },

        async getAcademicAreasResult(){
            let request = await axios.post(route('results.academicAreas'),{identification:this.user.identification});
            this.academicAreasResults = request.data;
            this.academicAreasInfo = this.academicAreasResults.basicInfo
        },

        async downloadResults(){

            setTimeout(async () => {
                // Generate PDF
                // ... your existing code for PDF generation ...

                // If using async PDF generation, ensure proper handling of asynchronous operations
                try {
                    await this.generatePDF();
                } catch (error) {
                    console.error('Error generating PDF:', error);
                }
            }, 200); // Adjust delay as needed

        },

       async generatePDF(){
           const pieChart = document.getElementById('pieChart').toDataURL('image/png', 1.0);
           const barChart = document.getElementById('barChart').toDataURL('image/png', 1.0);
           const userName = this.user.name
           const charts = {pieChart, barChart};
           const data = {charts: charts, userName: userName}

           let request = await axios.post(route('results.userReportPDF'),{data},
               {responseType:'blob'}).then(response => {
               const url = window.URL.createObjectURL(new Blob([response.data]));
               const link = document.createElement('a');
               link.href = url;
               link.setAttribute('download','charts.pdf');
               document.body.appendChild(link);
               link.click();
           }).catch(error => {
               console.log('Error generando el PDF', error);
           })
        },

        destroyCharts() {
            // Destroy pie chart
            if (this.pieChart) {
                this.pieChart.destroy();
                this.pieChart = null;
            }

            // Destroy bar chart
            if (this.barChart) {
                this.barChart.destroy();
                this.barChart = null;
            }
        },


        getPieChart(){
            let chart = document.getElementById("pieChart").getContext("2d");
            Chart.register(ChartDataLabels);
            this.pieChart = new Chart(chart,{
                type:'pie',
                data:{
                    labels: this.academicAreasResults.labels,
                    datasets: [{
                        label: 'Grado de interés (%)',
                        data: this.academicAreasResults.results,
                        backgroundColor: ['#3498db', '#2ecc71', '#f1c40f', '#e67e22', '#9b59b6', '#00B0FF'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            display: true,
                            formatter: (value) => {
                                value = `${value}%`
                                return value;
                            },
                            font: {
                                weight: 'bold',
                                size: 19
                            }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const elementIndex = elements[0].index;
                            const label = this.pieChart.data.labels[elementIndex];
                            const value = this.pieChart.data.datasets[0].data[elementIndex];
                            this.showAcademicAreaInfo(label, value);
                        }
                    }
                },
            })
        },

        getBarChart(){

            let chart = document.getElementById("barChart").getContext("2d");

            this.barChart = new Chart(chart, {
                type:"bar",
                data:{
                    labels: this.academicProgramsResults.labels,
                    datasets: [
                        {data: this.academicProgramsResults.results,
                        // backgroundColor: ["#E91E63", "#2196F3", "#CDDC39", "#FF9800", '#3F51B5'],
                        backgroundColor: ["#dc143c", "#00bcd4", "#32cd32", "#ffbf00", '#6a5acd'],
                        hoverOffset: 4,
                        label: `Nivel de interés`,
                        }
                    ]
                },
                options: {
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            display: true,
                            formatter: (value) => {
                                return value;
                            },
                            font: {
                                weight: 'bold',
                                size: 19
                            }
                        }
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
                                    text: 'Programa académico',
                                    color: 'black',
                                    font: {
                                        size: 15,
                                        weight: 'bold',
                                        lineHeight: 1.2,
                                    },
                                },

                            }
                        ,
                        y:
                            {
                                display: true,

                                title: {
                                    display: true,
                                    text: 'Nivel de interés',
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

        showAcademicAreaInfo(label,value){
          this.academicAreasInfo.forEach(academicArea =>{
              if (label === academicArea.academic_area_name){
                  this.selectedAcademicAreaName = label;
                  this.selectedAcademicAreaMessage = academicArea.message
              }
          });

          this.academicAreaMessageDialog = true;
        },
    },
}
</script>








