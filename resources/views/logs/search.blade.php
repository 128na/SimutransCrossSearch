@extends('layout')

@section('title', 'Search Logs')

@section('content')
    <section class="container mb-4">
        <h2>Search Logs</h2>
        {{ $logs->withQueryString()->links('vendor.pagination.simple-bootstrap-4') }}
        <table class="table">
            <thead>
                <th>ID</th>
                <th>Count</th>
                <th>Query</th>
                <th>Updated At</th>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>
                            {{ $log->id }}
                        </td>
                        <td>
                            {{ $log->count }}
                        </td>
                        <td>
                            <a href="{{ route('pages.search') }}?{{ $log->query }}">{{ urldecode($log->query) }}</a>
                        </td>
                        <td>
                            {{ $log->updated_at->format('Y/m/d H:i:s') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $logs->withQueryString()->links('vendor.pagination.simple-bootstrap-4') }}

    </section>
@endsection
