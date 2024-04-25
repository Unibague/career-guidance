<template>
    <GeneralLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>

            <h2 class="black--text pt-5" style="text-align: center; margin-bottom: 30px"> {{this.user.name}}, ¡este es tu resultado!:</h2>

            <canvas id="graph" style=""></canvas>
            <!--Canvas para la gráfica-->
<!--            <v-card>-->
<!--                <v-card-text>-->

<!--                </v-card-text>-->

<!--                <v-container style="position: relative; ">-->

<!--                </v-container>-->
<!--            </v-card>-->

<!--
            <v-btn @click="getGraph">
                Crear
            </v-btn>

            <v-btn @click="destroyChart">
                Borrar
            </v-btn>
-->


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
        }
    },


    props: {
        user: Object,
    },


    async created() {
        await this.getAcademicProgramsResult();
        this.getGraph();
        this.isLoading = false;

    },

    methods: {

        async getAcademicProgramsResult(){
            let request = await axios.post(route('results.academicPrograms'),{identification:this.user.identification});
            this.academicProgramsResults = request.data;
            console.log(this.academicProgramsResults);
        },

        destroyChart(){
            this.chart.destroy();
        },

        getGraph(){

            let graph = document.getElementById("graph").getContext("2d");

            this.chart = new Chart(graph, {
                type:"bar",
                data:{
                    labels: this.academicProgramsResults.labels,
                    datasets: [
                        {data: this.academicProgramsResults.results,
                        backgroundColor: ["#E91E63", "#2196F3", "#CDDC39", "#FF9800", '#3F51B5'],
                        borderColor: ["#E91E63", "#2196F3", "#CDDC39", "#FF9800", '#3F51B5'],
                        borderWidth: 2,
                        borderDash: [5, 5],
                        label: `Nivel de interés`,
                        }
                    ]
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


    },
}
</script>








