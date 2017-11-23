<label for="create_{{ $field }}">{{ $field }}</label>
@switch($type)
  @case ('textarea')
<textarea type="{{ $type }}" name="{{ $field }}" class="form-control crudable-input crudable-{{ $type }}" id=create_"{{ $field }}">{{ old( $field ) }}</textarea>
  @break

  @case ('password')
<input type="{{ $type }}" name="{{ $field }}" value="" class="form-control crudable-input crudable-{{ $type }}" id="create_{{ $field }}">
  @break



  @default
<input type="{{ $type }}" name="{{ $field }}" value="{{ old( $field ) }}" class="form-control crudable-input crudable-{{ $type }}" id="create_{{ $field }}">
  @break
@endswitch
@if ($errors->create->has($field))
  <p class="text-danger crudable-error">{{ $errors->create->first ($field)}}</p>
@endif
