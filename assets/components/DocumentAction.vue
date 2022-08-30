<script>
import DocumentActionShare from "./DocumentActionShare.vue";
import DocumentActionRemove from "./DocumentActionRemove.vue";
import DocumentActionEdit from "./DocumentActionEdit.vue";
import DocumentActionChangeAccess from "./DocumentActionChangeAccess.vue";
import document from "./Document";
import axios from "axios";

export default {
  name: 'DocumentAction',
  props: ['document', 'rights', 'userId', 'locale'],
  data: function () {
    return {
      shareBtn: false,
      removeBtn: false,
      editBtn: false,
      changeAccessBtn: false,
      linkDownload: '',
      documentShared: [],
    }
  },
  components: {
    'document-action-share': DocumentActionShare,
    'document-action-remove': DocumentActionRemove,
    'document-action-edit': DocumentActionEdit,
    'document-action-change-access': DocumentActionChangeAccess,
  },
  methods: {
    onShareBtn(payload) {
      if(typeof payload !== 'undefined' && payload.access > 0) {
        this.document.access = payload.access;
      }
      this.shareBtn = !this.shareBtn;
    },
    onRemoveBtn() {
      this.removeBtn = !this.removeBtn;
    },
    onEditBtn() {
      this.editBtn = !this.editBtn;
    },
    onChangeAccessBtn() {
      this.changeAccessBtn = !this.changeAccessBtn;
      if(this.changeAccessBtn) {
        this.getReceiversAndDocumentAccess();
      }
    },
    getReceiversAndDocumentAccess() {
      axios.get(`/${this.locale}/app/document/shared/${this.document.safeName}/receivers`)
          .then((response) => {
            this.documentShared = response.data;
          })
          .catch(function (error) {
          });
    },
  },
  computed: {
    canEdit() {
      return (this.document.owner == this.userId) || (this.document.access == 2);
    },
    canRemoveOrShared() {
      return this.document.owner == this.userId;
    },
    canDownload() {
      return (this.document.owner == this.userId) || (this.document.access >= 1);
    },
    isShared() {
      return this.document.access >= 1
    },
  },
  created() {
    this.linkDownload = `/${this.locale}/app/document/${this.document.safeName}/download`;
  },
}
</script>
