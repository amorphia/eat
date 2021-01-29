<template>
    <form v-if="!hide" @submit.prevent="onSubmit" :enctype="hasFile" :class="classes">
        <template v-for="field in schema">
            <component
                :is="field.type ? 'vue-' + field.type : 'vue-input'"
                :field="field"
                :errors="form.errors"
                :sending="sending"
                :data.sync="form[field.name]">
                class="field.class"
            </component>
        </template>

        <div class="form-group">
            <input type="submit" class="form-submit form-element uppercase bg-hover-light button bg-side white secondary uppercase" :class="submitclass" value="submit">
        </div>
    </form>
</template>


<script>
    import Form from '../../partials/_form.js';

    export default {

        name: 'vue-form',
        props: ['method', 'action', 'schema', 'submitclass', 'hide', 'classes' ],

        data() {
            return {
                form : null,
                sending : false,
                hasFile : ''
            };
        },

        methods : {

            setInputs(){
                let inputs = {};
                let hasFiles = false;

                this.schema.forEach( item => {
                    inputs[item.name] = { value : item.value ?? '' };

                    if( item.type === 'file' ){
                        inputs[item.name].file = true;
                        hasFiles = true;
                    }
                });

                this.form = new Form( inputs, this.action, this.method );

                if( hasFiles ){
                    this.form.hasFiles();
                    this.hasFile = 'multipart/form-data';
                }

            },

            onSubmit(){
                this.sending = true;

                this.form.submit()
                    .then( response => this.$emit( 'success', response ) )
                    .catch( errors => this.$emit( 'error', errors ) )
                    .finally( () => this.sending = false );
            },
        },

        created(){
            this.setInputs();
        },

    }
</script>


<style>
    .loader-bar-shift {
        margin-top: -1em;
    }
</style>

