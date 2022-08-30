<template>
  <vue-dropzone
      ref="myVueDropzone"
      id="dropzone"
      :options="dropzoneOptions"
      @vdropzone-success="vsuccess"
  >
  </vue-dropzone>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';

export default {
  name: 'app',
  props:['dropzoneOptions', 'deletefile', 'dictDefaultMessage'],
  components: {
    vueDropzone: vue2Dropzone
  },
  methods: {
    vsuccess(file, response) {
      this.$emit('send-add-file-success', {
        name: this.getNameWithoutExtension(file.name),
        extension: this.getFileExtension(file.name)
      })
      if(this.deletefile) {
        this.removeFile();
      }
    },
    getFileExtension(name) {
      return (name.split('.').pop()).toLowerCase();
    },
    getNameWithoutExtension(name) {
      return name.split('.').shift();
    },
    removeFile() {
      this.$refs.myVueDropzone.removeAllFiles();
    }
  },
  created() {
    this.dropzoneOptions.dictDefaultMessage = this.dictDefaultMessage;
  }
}
</script>

<style>
  .vue-dropzone > .dz-preview .dz-error-message {
    top: 0;
  }
</style>
