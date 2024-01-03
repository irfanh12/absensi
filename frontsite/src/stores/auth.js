import { defineStore } from "pinia";
import router from "./../router/starter";

export const useAuth = defineStore({
  id: "auth",
  state: () => ({
    fullname: localStorage.getItem("fullname"),
    position: localStorage.getItem("position"),
    timesheets: [],
    presensi: {
      start_time: '--:--',
      end_time: '--:--',
    },
  }),
  actions: {
    login(fullname, position) {
      this.fullname = fullname;
      this.position = position;

      const token = localStorage.getItem("token");
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`
    },
    logout() {
      localStorage.clear()

      delete axios.defaults.headers.common["Authorization"];
      router.push({ name: "login" })
    },
    setPresensi(start_time, end_time) {
      this.presensi.start_time = start_time
      this.presensi.end_time = end_time
    },
    setTimesheet(timesheets) {
      this.timesheets = timesheets
    },
    permissions() {
      return {
        timesheet: ['Karyawan Outsource', 'Administrator'],
        presensi: ['Administrator', 'Klien', 'Human Resource', 'Karyawan', 'Karyawan Outsource'],
      }      
    },
  }
});
