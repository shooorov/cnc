import { defineStore } from 'pinia'

export const useNotificationStore = defineStore('notification', {
  state: () => ({
    type: 'notification', // alert or notification
    show: false,
    success: false,
    message: ''
  }),
  actions: {
    clear() {
      this.show = false
      this.success = false
      this.message = ''
    },
    warn(message) {
      this.show = true
      this.success = false
      this.message = message
    },
    alert(success, message) {
      this.show = true
      this.success = success
      this.message = message
    }
  }
})
