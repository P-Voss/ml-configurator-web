
import { createApp } from 'vue';
import Application from "./application/components/Application.vue";
import {createI18n} from 'vue-i18n'
import messages from "./application/locales/messages";

const app = createApp({
    components: {
        Application
    }
})

app.mount('#app');
app.use(createI18n({
    locale: document.getElementById('body').dataset.locale,
    messages,
    datetimeFormats: {
        en: {
            short: {
                // year: 'numeric',
                month: 'numeric',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            },
            long: {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }
        },
        de: {
            short: {
                // year: 'numeric',
                month: 'numeric',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            },
            long: {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }
        }
    }
}))