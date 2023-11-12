
import { createApp } from 'vue';
import Configurator from "./configurator/components/Configurator.vue";
import {createI18n} from 'vue-i18n'
import messages from "./configurator/locales/messages";


const app = createApp({
    components: {
        Configurator
    }
});

app.mount('#app');
app.use(createI18n({
    locale: document.getElementById('body').dataset.locale,
    messages
}))

