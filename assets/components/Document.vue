<script>
import DocumentAction from "./DocumentAction.vue";

export default {
  name: 'Document',
  props: ['document'],
  data: function () {
    return {

    }
  },
  computed: {
    pathCardImg() {
     return `/assets/images/document/${this.document.extension}.png`
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
