<script>
export default {
    created: function () {
        if( !this.onAuth ) return;

        if( this.shared && this.shared.auth ){
            this.onAuth();
        } else {
            App.event.on( 'init', this.onAuth );
        }
    },
}
</script>
