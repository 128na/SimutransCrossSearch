@extends('template')

@section('title', 'RSS')

@section('content')
  <h2>シムトラ関連サイト更新情報</h2>
  <div id="app-user"></div>
  <script>
    window.sites = @json($rsses)
  </script>
@endsection
