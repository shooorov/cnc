import { defineStore } from "pinia";
import { useLocalStorage } from "@vueuse/core";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

export const useUserSettingStore = defineStore("user-setting", {
    state: () => ({
        posLayout: useLocalStorage(appName + "/user-setting/posLayout", 'box'),
    }),
    actions: {
        set(view) {
            this.posLayout = view;
        },
    },
});
