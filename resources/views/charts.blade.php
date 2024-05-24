<!-- resources/views/charts/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Charts PDF</title>

        <style>
            h4 {
                margin: 0;
            }
            .w-full {
                width: 100%;
            }
            .w-half {
                width: 50%;
            }
            .margin-top {
                margin-top: 1.25rem;
            }
            .footer {
                font-size: 0.875rem;
                padding: 1rem;
                background-color: rgb(241 245 249);
            }
            table {
                width: 100%;
                border-spacing: 0;
            }
            table.products {
                font-size: 0.875rem;
            }
            table.products tr {
                background-color: rgb(96 165 250);
            }
            table.products th {
                color: #ffffff;
                padding: 0.5rem;
            }
            table tr.items {
                background-color: rgb(241 245 249);
            }
            table tr.items td {
                padding: 0.5rem;
            }
            .total {
                text-align: right;
                margin-top: 1rem;
                font-size: 0.875rem;
            }

            .chart {
                text-align: center;
                margin: 20px 0;
            }
            body {
                font-family: "Helvetica Neue", sans-serif;
                margin: 25px 25px;
            }
            .chart img {
                max-width: 100%;
                height: auto;
            }
        </style>
</head>
<body>

<img src="https://sig.unibague.edu.co/documentos/comunicacion-interna-y-externa/otra-informacion-documentada-4/562-logo-unibague-acreditacion/file"
     style="max-height: 300px; max-width:300px; object-fit: contain" alt="Logo_universidad">


<p style="margin-top: 15px; font-weight: bold"> Test de Orientación Vocacional Unibagué </p>
<p style="margin-bottom: 30px"> Resultados para: <strong> {{$userName}} </strong></p>

<h3> Clasificación por áreas de conocimiento </h3>

<div class="chart">
    <img src="{{ $pieChart }}" alt="Pie Chart" style="margin-bottom: 50px; width: 70%">
</div>

<h4 style="margin-bottom: 15px"> <strong> Descripción de las áreas de conocimiento: </strong></h4>

@foreach($academicAreas as $academicArea)
    <p> <strong> {{$academicArea->name}}:</strong> {{$academicArea->message}}</p>
@endforeach


<h3 style="margin-top: 45px"> Programas de mayor interés </h3>
<div class="chart">
    <img src="{{ $barChart }}" alt="Bar Chart">
</div>
</body>
</html>
