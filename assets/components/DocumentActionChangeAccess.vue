<script>
import axios from 'axios';
import Access from "./Access.vue";

export default {
  name: 'DocumentActionChangeAccess',
  props: ['btnChangeAccess', 'document', 'locale', 'data'],
  data: function () {
    return {
      indexSelected: -1,
    }
  },
  methods: {
    sendValueBtnChangeAccess() {
      this.$emit('change-value-btn-change-access', this.btnChangeAccess)
    },
    changeAccessValue(payload) {
      this.data[payload.index].access = payload.access;
    },
    update(key)
    {
      const data = this.data[key];
      axios.post(`/${this.locale}/app/document/shared/${data.id}/access-edit`, {
        access: data.access,
      })
          .then((response) => {
            this.$root.$emit('send-data-for-alert', response.data, 'alert-success show');
          })
          .catch((error) => {

          });
    },
  },
  components:{
    'access': Access,
  },
}
</script>
