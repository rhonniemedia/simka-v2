@props([
'id' => 'mainModal',
'size' => '',
'scrollable' => false,
'centered' => true
])

@php
$classes = ['modal-dialog'];

if ($size) {
$classes[] = $size;
}

if ($centered) {
$classes[] = 'modal-dialog-centered';
}

if ($scrollable) {
$classes[] = 'modal-dialog-scrollable';
}

// Unique content ID untuk setiap modal
$contentId = $id . '-content';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" x-ref="modal">
    <div class="{{ implode(' ', $classes) }}">
        <div class="modal-content" id="{{ $contentId }}">
            {{ $slot ?? '' }}
        </div>
    </div>
</div>