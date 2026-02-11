<?php
if (!isset($absolute)) {
    $absolute = true;
}
$min = $absolute ? 'absolute top-0 text-center w-8 h-8 rounded-full' : 'rounded-full';
$isHexColour = Illuminate\Support\Str::startsWith($element->colour ?? '', '#');
$colourClass = $isHexColour ? '' : 'bg-' . ($element->colour ?? 'grey');
$colourStyle = $isHexColour ? 'background-color: ' . e($element->colour) . '; color: #fff;' : '';
?>

@if (!empty($element->icon))
    @if (Illuminate\Support\Str::startsWith($element->icon, '<i class='))
        @if ($isHexColour)
            {!! str_replace('<i class="', '<i style="' . $colourStyle . '" class="' . $min . ' ', $element->icon) !!}
        @else
            {!! str_replace('<i class="', '<i class="' . $colourClass . ' ' . $min . ' ', $element->icon) !!}
        @endif
    @else
        <i class="{{ $colourClass }} {{ $element->icon }} {{ $min }}" @if ($isHexColour) style="{{ $colourStyle }}" @endif aria-hidden="true"></i>
    @endif
@else
    <i class="fa fa-solid fa-hourglass-half {{ $colourClass }} {{ $min }}" @if ($isHexColour) style="{{ $colourStyle }}" @endif aria-hidden="true"></i>
@endif
