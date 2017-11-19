import axios from 'axios'

axios.defaults.baseURL = window.base_url || '/';

export default {
  fetchRss() {
    return axios.get('/api/rss')
  },

}
