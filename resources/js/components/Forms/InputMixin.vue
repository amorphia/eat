<script>
    export default {
        computed: {
            labelTitle(){
                let title = this.field.label ? _.startCase( this.field.label ) : _.startCase( this.field.name );
                title += this.field.optional && ! this.field.hideOptionalLabel ? ' (optional)' : '';
                return title;
            }
        }
    }
</script>
