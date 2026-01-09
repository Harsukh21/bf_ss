@push('scripts')
<script>
function toggleFilterDrawer() {
    const drawer = document.getElementById('filterDrawer');
    const overlay = document.getElementById('filterOverlay');

    drawer.classList.toggle('open');
    overlay.classList.toggle('active');
}
</script>
@endpush
