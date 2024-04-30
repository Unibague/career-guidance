<table>
    <thead>
    <tr>
        <th>Sexo</th> <!-- You may need to add user information -->
        <th>Edad</th> <!-- You may need to add user information -->
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
