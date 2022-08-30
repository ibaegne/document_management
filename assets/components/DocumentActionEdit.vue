<script>
import Dropzone from '../components/Dropzone.vue';

export default {
  name: 'DocumentActionEdit',
  props: ['btnEdit', 'document', 'newFileMessage', 'locale'],
  data: function () {
    return {
      dropzoneOptions: {
        url: '',
        maxFiles: 1,
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        dictDefaultMessage: this.newFileMessage
      }
    }
  },
  methods: {
    sendValueBtnEdit() {
      this.$emit('change-value-btn-edit', this.btnEdit)
    },
    closeModal(payload) {
      this.sendValueBtnEdit();
      this.$root.$emit('send-change-document-name-extension', this.document.safeName, payload.name, payload.extension);
    },
  },
  components: { 'dropzone': Dropzone },
  created() {
    this.dropzoneOptions.url = `/${this.locale}/app/document/${this.document.safeName}/edit`;
  }
}
</script>
