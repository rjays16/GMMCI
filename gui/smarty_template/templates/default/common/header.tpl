{{$HTMLtag}}
<HEAD>
	<TITLE>{{$sWindowTitle}} - {{$Name}}</TITLE>
	{{include file="common/metaheaders.tpl"}}
	{{$setCharSet}}
	{{foreach from=$JavaScript item=currentJS}}
	{{$currentJS}}
	{{/foreach}}
	{{$yhScript}}
</HEAD>
<BODY bgcolor="#FFFFFF" {{$class}} {{$sLinkColors}} {{$sOnLoadJs}} {{$sOnUnloadJs}}>
