require("./bootstrap");

// Import modules...
import { createApp, h } from "vue";
import SecretComponent from "./components/SecretComponent.vue";

createApp({
    components: {
        SecretComponent,
    },
}).mount("#app");
