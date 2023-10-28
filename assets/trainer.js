
import { createApp } from 'vue';
import Trainer from "./trainer/components/Trainer.vue";
import {createI18n} from 'vue-i18n'
import messages from "./trainer/locales/messages";

const app = createApp({
    components: {
        Trainer
    }
});
app.mount('#app');
app.use(createI18n({
    locale: document.getElementById('body').dataset.locale,
    messages
}))