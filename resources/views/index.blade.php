@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">横断検索</h2>
        @include('parts.search-form')
    </section>
    <section class="container mb-5">
        <h3 class="mb-3">更新ページ</h3>
        @include('parts.pages')
    </section>
@endsection
