@extends('layout')

@section('title', 'Schedule Logs')

@section('content')
    <section class="container mb-4">
        <h2>Schedule Logs</h2>
        <table class="table">
            <thead>
                <th>ID</th>
                <th>Status</th>
                <th>Label</th>
                <th>Created At</th>
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
                        <td>
                            {{ $log->id }}
                        </td>
                        <td class="{{$bg}}">
                            {{ $log->status }}
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
