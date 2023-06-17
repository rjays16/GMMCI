<div style="text-align:center; padding:4px"
	<canvas id="clock_{{$dashlet.id}}" class="CoolClock:{{$settings.clockSkin|default:Cold}}:{{$settings.clockRadius|default:80}}::{{$settings.gmtOffset|default:8}}{{if $settings.showDigital == 1}}:showDigital{{/if}}"></canvas>
</div>
<script type="text/javascript">
(function($) {
	if ('undefined' == typeof window.CoolClock)
	{
		$.ajax({
			url: '../../js/coolclock/coolclock.js',
			dataType: 'script',
			async: false
		});
		$.ajax({
			url: '../../js/coolclock/moreskins.js',
			dataType: 'script',
			async: false
		});
	}
	CoolClock.findAndCreateClocks();
})(jQuery);
</script>