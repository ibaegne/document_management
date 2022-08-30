<script>
import axios from 'axios';
export default {
  name: 'DocumentActionRemove',
  props: ['btnRemove', 'document', 'locale'],
  data: function () {
    return {

    }
  },
  methods: {
    sendValueBtnRemove() {
      this.$emit('change-value-btn-remove', this.btnRemove)
    },
    sendRemove() {
      axios.post(`/${this.locale}/app/document/remove`, {
        documentName: this.document.safeName,
      })
          .then((response) => {
            this.$root.$emit('send-data-for-alert', response.data, 'alert-success show');
            this.$root.$emit('send-remove-document', this.document.safeName, 'alert-success show');
            this.sendValueBtnRemove();
          })
          .catch((error) => {
            this.$root.$emit('send-data-for-alert', error, 'alert-danger show')
          });
    }
  }
}
</script>
