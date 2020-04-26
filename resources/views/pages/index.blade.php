@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">アドオン横断検索</h2>
        @include('pages.form')
    </section>
    <section class="container mb-5">
        <h3 class="mb-3">
            最近更新されたページ
            <small class="ml-3"><a href="{{ route('pages.search') }}?page=1">更新順一覧</a></small>
        </h3>
        @include('pages.table')
    </section>
@endsection
