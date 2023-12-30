import { defineStore } from "pinia";
import router from "./../router/starter";

export const useAuth = defineStore({
  id: "auth",
  state: () => ({
    fullname: localStorage.getItem("fullname"),
    position: localStorage.getItem("position"),
  }),
  actions: {
    login(fullname, position) {
      this.fullname = fullname;
      this.position = position;
    },
    logout() {
      localStorage.clear()
      delete axios.defaults.headers.common["Authorization"];
      router.push({ name: "login" })
    },
  }
});
