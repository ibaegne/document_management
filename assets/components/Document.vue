<script>
import DocumentAction from "./DocumentAction.vue";

export default {
  name: 'Document',
  props: ['document'],
  data: function () {
    return {
      extensions: ['docx', 'odt', 'pdf', 'xls', 'xlsx'],
      dir: '/assets/images/document/'
    }
  },
  computed: {
    pathCardImg() {
      if(!this.extensions.includes(this.document.extension)) {
        return `${this.dir}other.png`
      }
      return `${this.dir}${this.document.extension}.png`
    }
  },
  components: {
    'document-action': DocumentAction
  }
}
</script>

<style src="../styles/document.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
.multiselect__content-wrapper {
  position:inherit;
}
div.modal-body h4.card-title {
  color: #475f7b !important;
}
</style>
