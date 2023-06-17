{{* This is the template for each headline item displayed on the headlines list *}}

<div class="news_img"><img {{$sHeadlineImg}} align="left" border=0 hspace=10 {{$sImgWidth}}></div>
{{$sHeadlineItemTitle}}

{{if $sPreface}}
	<br/>
	{{$sPreface}}
{{/if}}

{{if $sNewsPreview}}
	<p></p>
	{{$sNewsPreview}}
{{/if}}

<br>
{{$sEditorLink}}	
