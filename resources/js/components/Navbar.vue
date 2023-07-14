<script>
export default {
    setup() {
        function toggleMobileSidebar() {
            window.dispatchEvent( new Event('sidebar.toggle') );
        }

        return {
            toggleMobileSidebar
        }
    }
}
</script>
