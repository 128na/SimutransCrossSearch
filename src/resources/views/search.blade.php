@extends('template')

@section('title', implode('、',$conds).'での検索結果')

@section('content')
@if (count($pages))
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>サイト名</th>
      <th>ページ名</th>
      <th>一致テキスト</th>
      <th>最終更新日</th>
    </tr>
  </thead>
@foreach($pages as $page)
  <tbody>
    <tr>
      <td>{{ $page->site_name }}</td>
      <td><a href="{{ $page->url }}" target="_blank"><span class="highlightable">{{ $page->title }}</span></a></td>
      <td><span class="highlightable">{{ $page->expectText($word) }}</span></td>
      <td>{{ $page->updated_at }}</td>
    </tr>
  </tbody>
@endforeach
</table>
@else
  <span>ないです</span>
@endif
@endsection

@section('script')
<script>
  $(function() {
    const word = '{{ $word }}'
    if (word) {
      $('.highlightable').toArray().map(n => {
        const $n = $(n)
        $n.html($n.text().replace(word, `<span class="highlight">${word}</span>`))
      })
    }
});
</script>
@endsection
