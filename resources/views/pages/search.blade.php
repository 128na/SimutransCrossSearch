@extends('layout')

@section('title', $title)

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">横断検索</h2>
        @include('pages.form')
    </section>
    <section class="container mb-5">
        <h3 class="mb-3">{{ $title }}</h3>
        @if ($pages->isEmpty())
            <p>該当なし</p>
        @else
            {{ $pages->withQueryString()->links('vendor.pagination.bootstrap-4') }}
            @include('pages.table')
            {{ $pages->withQueryString()->links('vendor.pagination.bootstrap-4') }}
        @endif
    </section>
@endsection
