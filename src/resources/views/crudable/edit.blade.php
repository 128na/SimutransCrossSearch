<label for="edit_{{ $model->id }}_{{ $field }}">{{ $field }}</label>
@switch($type)
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
