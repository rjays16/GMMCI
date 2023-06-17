<textarea spellcheck="false" id="pad_{{$dashlet.id}}" style="width: 100%; font: normal 12px 'Tahoma'; border: 0; overflow:visible">{{$dashlet.content}}</textarea>
<script type="text/javascript">
(function($) {
	if ('undefined' == typeof $.fn.TextAreaExpander)
	{
		$.ajax({
			url: '../../js/jquery/plugins/jquery.textarea-expander.js',
			dataType: 'script',
			async: false
		});
	}

	$("#pad_{{$dashlet.id}}")
		.TextAreaExpander()

		.blur(function() {
			Dashboard.dashlets.sendAction("{{$dashlet.id}}", "save", {
				data: $('#pad_{{$dashlet.id}}').val()
			});
		});

})(jQuery);
</script>