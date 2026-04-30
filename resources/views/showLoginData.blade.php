<div>
    <!-- When there is no desire, all things are at peace. - Laozi -->
    <table>
        <tbody>
            @php
            $count = 0;
            @endphp
            @foreach ($Data as $d)
            <tr>

                <td scope='col'>{{ $count += 1 }}</td>
                <td>{{ $d->USER_ID }}</td>
                <td>{{ $d->USER_PASSWD }}</td>
                <td>{{ $d->USER_DISABLE_FLAG }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>