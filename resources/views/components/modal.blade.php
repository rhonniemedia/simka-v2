<!-- Modal Component -->
<div class="modal fade" id="{{ $id ?? 'mainModal' }}" tabindex="-1" x-ref="modal">
    <div class="modal-dialog modal-dialog-centered {{ $size ?? '' }}">
        <div class="modal-content" id="modal-content">
            {{ $slot ?? '' }}
        </div>
    </div>
</div>