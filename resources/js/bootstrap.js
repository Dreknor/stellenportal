import axios from "axios";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Alpine = Alpine;

Alpine.plugin(collapse);
Alpine.start();
