@props(['type' => 'info'])

@if($slot->isNotEmpty())
	<div {{ $attributes->class(['alert', 'alert-'.$type]) }} role="alert">
		{{ $slot }}
	</div>
@endif