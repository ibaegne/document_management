import Vue from 'vue';
import axios from 'axios';
import Document from "../components/Document.vue";

new Vue({
    el: '#documents',
    data: {
        documents: [],
        message: '',
        alertClass: ''
    },
    methods: {
        setDataAlert(message, classNames) {
            this.message = message;
            this.alertClass = classNames;
        },
        removeDocument(safeName) {
            const index = this.findIndexDocument(safeName);
            if(index > -1) {
                this.documents.splice(index,1)
            }
        },
        changeNameAndExtensionDocument(safeName, newName, extension) {
            const index = this.findIndexDocument(safeName);
            if(index > -1) {
                this.documents[index].name = newName;
                this.documents[index].extension = extension;
            }
        },
        findIndexDocument(safeName) {
            const document = (document) => document.safeName == safeName;

            return this.documents.findIndex(document);
        }
    },
    components: {
        'document': Document
    },
    mounted() {
        axios.get(`${this.$refs.path.value}`)
            .then((response) => {
                this.documents = response.data;
            })
            .catch(function (error) {
                this.message = error;
                this.alertClass = 'alert-danger show'
            });
        this.$root.$on('send-data-for-alert', (message, classNames) => {
            this.setDataAlert(message, classNames);
        });

        this.$root.$on('send-remove-document', (safeName) => {
            this.removeDocument(safeName);
        });

        this.$root.$on('send-change-document-name-extension', (safeName, newName, extension) => {
            this.changeNameAndExtensionDocument(safeName, newName, extension);
        });
    }
});
