
import { createApp } from 'vue';
import Configurator from "./configurator/components/Configurator.vue";

const app = createApp({
    components: {
        Configurator
    }
});
app.mount('#app');