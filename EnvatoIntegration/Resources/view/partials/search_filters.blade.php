@if (count($custom_fields))
    @foreach($custom_fields as $custom_field)
        <div class="col-sm-6 form-group @if (isset($filters[$custom_field->name])) active @endif" data-filter="{{ $custom_field->name }}">
            <label>{{ $custom_field->name }} <b class="remove" data-toggle="tooltip" title="{{ __('Remove filter') }}">×</b></label>        
                <input name="f[{{ $custom_field->name }}]" value="{{ $filters[$custom_field->name] ?? '' }}" class="form-control" @if (empty($filters[$custom_field->name])) disabled @endif />
        </div>
    @endforeach
@endif