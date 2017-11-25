<div class="form-group">
  <label for="create_{{ $field }}">{{ $field }}</label>
@switch($type)
  @case ('radio')
    @foreach(array_get($options, $field, []) as $label => $value)
  <label>
    <input type="radio" name="{{ $field }}" value="{{ $value }}" class="crudable-input crudable-radio"{{ $value ==  old( $field ) ? ' checked' : ''}}>
    {{ $label }}
  </label>
    @endforeach
  @break

  @case ('checkbox')
    @foreach(array_get($options, $field, []) as $label => $value)
  <label>
    <input type="checkbox" name="{{ $field }}[]" value="{{ $value }}" class="crudable-input crudable-checkbox"{{ $value ==  old( $field ) ? ' checked' : ''}}>
    {{ $label }}
  </label>
    @endforeach
  @break

  @case ('select')
  <select type="select" name="{{ $field }}" class="form-control crudable-input crudable-password" id="create_{{ $field }}"{{ $value ==  old( $field ) ? ' selected' : ''}}>
    @foreach(array_get($options, $field, []) as $label => $value)
    <option name="{{ $field }}" value="{{ $value }}">{{ $label }}</option>
    @endforeach
  </select>
  @break

  @case ('textarea')
  <textarea type="{{ $type }}" name="{{ $field }}" class="form-control crudable-input crudable-{{ $type }}" id=create_"{{ $field }}">{{ old( $field ) }}</textarea>
  @break

  @case ('password')
  <input type="password" name="{{ $field }}" value="" class="form-control crudable-input crudable-password" id="create_{{ $field }}">
  @break

  @default
  <input type="{{ $type }}" name="{{ $field }}" value="{{ old( $field ) }}" class="form-control crudable-input crudable-{{ $type }}" id="create_{{ $field }}">
  @break
@endswitch
@if ($errors->create->has($field))
    <p class="text-danger crudable-error">{{ $errors->create->first ($field)}}</p>
@endif
</div>
