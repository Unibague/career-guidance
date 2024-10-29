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

<h3> Clasificación por áreas de conocimiento (%) </h3>

<div class="chart">
    <img src="{{$pieChartUrl}}" alt="Pie Chart" style="width: 70%;"/>
</div>

<h4 style="margin-bottom: 15px"> <strong> Descripción de las áreas de conocimiento: </strong></h4>

@foreach($academicAreas as $academicArea)
    <p> <strong> {{$academicArea->name}}:</strong> {{$academicArea->message}}</p>
@endforeach

<h3 style="margin-top: 45px"> Programas de mayor interés </h3>
<div class="chart">
    <img src="{{ $barChartUrl }}" alt="Bar Chart">
</div>
</body>

<script>
    window.addEventListener('load', function (){
        window.print();
    })
</script>

</html>




{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <title>Resultados Test Vocacional</title>--}}
{{--</head>--}}
{{--<body>--}}
{{--<h2>{{ $userName }}, ¡este es tu resultado!:</h2>--}}

{{--<h3>Clasificación por áreas de conocimiento:</h3>--}}
{{--<img src="https://quickchart.io/chart?c=%7B%22type%22%3A%22pie%22%2C%22data%22%3A%7B%22labels%22%3A%5B%22Label%201%22%2C%22Label%202%22%2C%22Label%203%22%5D%2C%22datasets%22%3A%5B%7B%22backgroundColor%22%3A%5B%22%233498db%22%2C%22%232ecc71%22%2C%22%23f1c40f%22%5D%2C%22data%22%3A%5B30%2C50%2C20%5D%7D%5D%7D%2C%22options%22%3A%7B%22plugins%22%3A%7B%22datalabels%22%3A%7B%22display%22%3Atrue%2C%22color%22%3A%22%23fff%22%2C%22font%22%3A%7B%22weight%22%3A%22bold%22%2C%22size%22%3A19%7D%7D%7D%7D%7D" alt="Pie Chart" style="width: 70%;"/>--}}

{{--<h3>Programas de mayor interés:</h3>--}}
{{--<img src="{{ $barChartUrl }}" alt="Bar Chart" style="width: 100%; max-width: 600px;"/>--}}
{{--</body>--}}

{{--<script>--}}
{{--    window.addEventListener('load', function (){--}}
{{--        window.print();--}}
{{--    })--}}
{{--</script>--}}

{{--</html>--}}


