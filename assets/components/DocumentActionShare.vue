<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect'
import Access from "./Access.vue";

export default {
  name: 'DocumentActionShare',
  props: ['btnShare', 'document', 'noItemsFoundMessage', 'locale'],
  data: function () {
    return {
      isLoading: false,
      value: null,
      access: 1,
      users: [],
      showMessageSelectUser: false,
      searchText: '',
      numberOfCharacters: 2
    }
  },
  components:{
    'multiselect': Multiselect,
    'access': Access
  },
  methods: {
    sendValueBtnShare(value) {
      this.$emit('change-value-btn-share', {access: value})
    },
    usersFind(query) {
      this.searchText = query
      if(query.length > this.numberOfCharacters) {
        axios.get(`/${this.locale}/app/user/search-receiver/${query}/${this.document.safeName}`)
            .then((response) => {
              this.users = response.data;
            })
            .catch(function (error) {
            });
      }
    },
    onSelect() {
      this.showMessageSelectUser = false;
    },
    changeAccessValue(payload) {
      this.access = payload.access;
    },
    getNotResultMessage() {
      return this.searchText.length > this.numberOfCharacters ? this.noItemsFoundMessage : '';
    },
    sendShare() {
      if (this.value != null) {
        axios.post(`/${this.locale}/app/document/shared`, {
          document: this.document.safeName,
          access: this.access,
          users: this.value,
        })
            .then((response) => {
              this.$root.$emit('send-data-for-alert', response.data, 'alert-success show');
              this.users = [];
              this.value = null;
            })
            .catch((error) => {
              this.$root.$emit('send-data-for-alert', error, 'alert-danger show')
            });

        this.sendValueBtnShare(this.access);
      } else {
        this.showMessageSelectUser = true;
      }
    }
  }
}
</script>
