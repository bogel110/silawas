@if($alumni->hasPages())
    <div class="d-flex justify-content-center justify-content-md-end">
        {{ $alumni->appends(['per_page' => $perPage])->links('pagination::bootstrap-5') }}
    </div>
@endif
