
import { createApp } from 'vue';
import Trainer from "./trainer/components/Trainer.vue";

const app = createApp({
    components: {
        Trainer
    }
});
app.mount('#app');