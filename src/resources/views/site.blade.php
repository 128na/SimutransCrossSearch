@extends('template')

@section('title', 'RSS')

@section('content')
  <h2>シムトラ関連サイト更新情報</h2>
  <h3>RSSを追加する</h3>
  <form method="post" action="{{ route('sites.store') }}" class="form-inline">
    {{ csrf_field() }}
    <input type="text" name="url" value="{{ old('url') }}" class="form-control">
    <button type="submit" class="btn btn-success">追加する</button>
@if ($errors->has('url'))
    <p class="text-danger">{{ $errors->first('url')}}</p>
@endif
  </form>
  <h3>更新情報</h3>
  <div id="app-user"></div>
  <script>
    window.sites = @json($rsses)
  </script>
@endsection
