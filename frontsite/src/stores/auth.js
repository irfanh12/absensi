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
        karyawan: ['Administrator', 'Human Resource'],
        klien: ['Administrator', 'Human Resource'],
        timesheet_data: ['Administrator', 'Human Resource', 'Klien'],
        timesheet: ['Karyawan Outsource', 'Human Resource', 'Administrator', 'Klien'],
        menus: ['Administrator', 'Human Resource', 'Karyawan', 'Karyawan Outsource'],
        presensi_data: ['Administrator', 'Human Resource', 'Klien'],
        presensi: ['Administrator', 'Human Resource', 'Karyawan', 'Karyawan Outsource'],
        hasHumanResource: () => {
          return ['Human Resource'].includes(this.position)
        },
        hasKaryawan: () => {
          return ['Karyawan', 'Karyawan Outsource'].includes(this.position)
        },
        hasKlien: () => {
          return ['Klien'].includes(this.position)
        },
        hasApproveFrom: (length, position) => {
          if(position === 'Human Resource') {
            return true;
          } else {
            return length > 0;
          }
        },
      }      
    },
  }
});
