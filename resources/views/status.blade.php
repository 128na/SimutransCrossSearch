@extends('layout')

@section('title', 'Cron Logs')

@section('content')
    <section class="container mb-4">
        <h2>Cron Logs</h2>
        <table class="table">
            <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Label</th>
                <th>DateTime</th>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    @php
                        $bg = 'bg-info';
                        if($log->status === 'success') {
                            $bg = 'bg-success';
                        }
                    @endphp
                    <tr>
                        <td class="{{$bg}}">
                            {{ $log->status }}
                        </td>
                        <td>
                            {{ $log->id }}
                        </td>
                        <td>
                            {{ $log->label }}
                        </td>
                        <td>
                            {{ $log->created_at->format('Y/m/d H:i:s') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </section>
@endsection
