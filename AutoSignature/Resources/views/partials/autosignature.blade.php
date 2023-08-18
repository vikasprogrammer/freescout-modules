<input type="hidden" id="autosignature" value="{{ $autosignature->text }}">
@section('javascript')
    @parent
    $(document).ready(function(){
    var autoSignature=$('#autosignature').val();
	$('#editor_signature span').after(autoSignature);
	});
@endsection