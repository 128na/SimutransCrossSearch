@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-4">
        <h2>横断検索</h2>
        @include('parts.search-form')
    </section>
    <section class="container mb-4">
        <h3>更新ページ</h3>
        @include('parts.pages')
    </section>
@endsection
