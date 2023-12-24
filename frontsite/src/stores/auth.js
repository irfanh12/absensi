import { defineStore } from "pinia";
import router from "./../router/starter";

export const useAuth = defineStore({
  id: "auth",
  actions: {
    logout() {
      localStorage.clear()
      delete axios.defaults.headers.common["Authorization"];
      router.push({ name: "login" })
    },
  }
});
