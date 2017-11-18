@extends('template')

@section('title', 'Top')

@section('content')
  <p>{{ config('const.app.description') }}</p>
  <h2>対応サイト</h2>
  <ul>
@foreach(config('const.sites') as $site)
    <li><a href="{{ $site['url'] }}">{{ $site['name'] }} ({{ $site['url'] }})</a></li>
@endforeach
  </ul>
@endsection
