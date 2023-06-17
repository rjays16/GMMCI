<div class="data-form">
	<div style="padding:10px">
		<span>There are no configuration options available for this dashlet!</span>
	</div>
	<div class="data-form-controls" align="right">
		<button id="ui-close-{{$dashlet.id}}">Back</button>
	</div>
</div>
<script type="text/javascript">
(function($) {
	$("#ui-close-{{$dashlet.id}}").button({
		icons: { primary: 'ui-icon-arrowreturnthick-1-w' }
	}).click(function() {
		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "setMode", {mode:'view'});
		return false;
	});
})(jQuery);
</script>