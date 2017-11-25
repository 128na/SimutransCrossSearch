@extends('template')

@section('title', $title)

@section('content')
<style>
  .crudable-form {
    display: inline;
  }
  .crudable-input {
    margin-bottom: 8px;
  }
  .crudable-list {
    list-style: none;
    padding-left: 0;
  }
</style>
<form method="post" action="{{ route($route.'.store') }}" class="">
  {{ csrf_field() }}
@foreach($fields as $field => $type)
  @include("{$view_name}.create")
@endforeach
  <button type="submit" class="btn btn-success pull-right">追加</button>
</form>
<h2>登録一覧</h2>
<ul class="crudable-list">
@foreach($models as $model)
  <li class="crudable-item">
    <div class="crudable-data">
      <form method="post" action="{{ route($route.'.update', ['id' => $model->id]) }}" class="crudable-form crudable-update">
        {{ method_field('put') }}
        {{ csrf_field() }}
  @foreach($fields as $field => $type)
    @include("{$view_name}.edit")
  @endforeach
        <button type="submit" class="btn btn-warning pull-right">更新</button>&emsp;
      </form>
      <form method="post" action="{{ route($route.'.destroy', ['id' => $model->id]) }}" class="crudable-form crudable-delete">
        {{ method_field('delete') }}
        {{ csrf_field() }}
        <button type="submit" class="btn btn-danger pull-right" onclick="return confirm('削除してよろしいですか？')">削除</button>
      </form>
    </div>
  </li>
@endforeach
</ul>
@endsection
