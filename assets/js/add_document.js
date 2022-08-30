import Vue from 'vue';
import Dropzone from '../components/Dropzone.vue';

new Vue({
    el: '#import-document',
    data: {
        dropzoneOptions: {
            url: 'import',
            thumbnailWidth: 150,
            maxFilesize: 0.5,
        }
    },
    components: { 'dropzone': Dropzone },
    mounted() {
       this.dropzoneOptions.url = this.$refs.path.value;
    },
});
