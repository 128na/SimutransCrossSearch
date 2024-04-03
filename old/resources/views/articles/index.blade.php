@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">
            最近投稿されたメディア検索
        </h2>
        @include('articles.form')
        <h3 class="mb-3">
            検索結果一覧
        </h3>
        @if ($articles->isEmpty())
            <p>該当なし</p>
        @else
            {{ $articles->withQueryString()->links('vendor.pagination.simple-bootstrap-4') }}
            @include('articles.table')
            {{ $articles->withQueryString()->links('vendor.pagination.simple-bootstrap-4') }}
        @endif
    </section>
@endsection
