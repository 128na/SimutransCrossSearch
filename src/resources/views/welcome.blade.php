@extends('template')

@section('title', 'Top')

@section('content')



  <h2>アドオン検索</h2>
  <p>
@foreach(config('const.sites') as $site)
    <a href="{{ $site['url'] }}" target="_blank">{{ $site['name'] }}</a>
@endforeach
  に投稿されているアドオンをまとめて検索できます。</p>
  <form class="form-inline" role="search" action="{{ route('search') }}" method="get">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="アドオン名" name="word" value="{{ $word ?? null }}">
    </div>
    <div class="form-group">
      <label class="sr-only" for="pak">選択</label>
      <select class="form-control" id="pak" name="pak">
@foreach(array_keys(config('const.pak')) as $name)
        <option value="{{ $name }}" {{ (($pak ?? null) == $name) ? 'selected' : '' }}>pak{{ $name }}</option>
@endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary">検索</button>
  </form>

  <h2>RSS(関連サイト)</h2>
  <p>Simutrans関連サイトのRSS情報一覧です。</p>
    <a href="{{ route('sites') }}">関連サイト更新情報</a>

  <h2>RSS（投稿サイト）</h2>
  <div id="app"></div>
@endsection
