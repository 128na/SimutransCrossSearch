@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">横断検索</h2>
        @include('parts.search-form')
    </section>
    <section class="container mb-5">
        <h3 class="mb-3">
            最近更新されたページ
            <small class="ml-3"><a href="{{ route('search') }}?page=1">更新順一覧</a></small>
        </h3>
        @include('parts.pages')
    </section>
@endsection
