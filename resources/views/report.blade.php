<table>
    <thead>
    <tr>
        <th>Nombre</th>
        <th>Sexo</th>
        <th>Edad</th>
        @foreach ($questions as $question)
            <th>{{ $question }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($tableData as $rowData)
        <tr>
            <!-- Add user information if needed -->
            @foreach ($rowData as $data)
                <td>{{ $data }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
