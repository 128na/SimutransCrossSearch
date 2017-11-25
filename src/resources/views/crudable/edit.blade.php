<div class="form-group">
  <label for="edit_{{ $model->id }}_{{ $field }}">{{ $field }}</label>
@switch($type)
  @case ('radio')
    @foreach(array_get($options, $field, []) as $label => $value)
  <label>
    <input type="radio" name="{{ $field }}" value="{{ $value }}" class="crudable-input crudable-radio"{{ $value ==  old( $field, $model->$field ) ? ' checked' : ''}}>
    {{ $label }}
  </label>
    @endforeach
  @break

  @case ('checkbox')
    @foreach(array_get($options, $field, []) as $label => $value)
  <label>
    <input type="checkbox" name="{{ $field }}[]" value="{{ $value }}" class="crudable-input crudable-checkbox"{{ $value ==  old( $field, $model->$field ) ? ' checked' : ''}}>
    {{ $label }}
  </label>
    @endforeach
  @break

  @case ('select')
  <select type="select" name="{{ $field }}" class="form-control crudable-input crudable-password" id="edit_{{ $model->id }}_{{ $field }}"{{ $value ==  old( $field, $model->$field ) ? ' selected' : ''}}>
    @foreach(array_get($options, $field, []) as $label => $value)
    <option name="{{ $field }}" value="{{ $value }}">{{ $label }}</option>
    @endforeach
  </select>
  @break


  @case ('textarea')
  <textarea type="{{ $type }}" name="{{ $field }}" class="form-control crudable-input crudable-{{ $type }}" id="edit_{{ $model->id }}_{{ $field }}">{{ old( $field, $model->$field ) }}</textarea>
  @break

  @case ('password')
  <input type="{{ $type }}" name="{{ $field }}" value="" class="form-control crudable-input crudable-{{ $type }}" id="edit_{{ $model->id }}_{{ $field }}">
  @break

  @default
  <input type="{{ $type }}" name="{{ $field }}" value="{{ old( $field, $model->$field ) }}" class="form-control crudable-input crudable-{{ $type }}" id="edit_{{ $model->id }}_{{ $field }}">
  @break
@endswitch
@if ($errors->{"update.{$model->id}"}->has($field))
  <p class="text-danger crudable-error">{{ $errors->{"update.{$model->id}"}->first ($field)}}</p>
@endif
</div>
