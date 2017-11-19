import axios from 'axios'

axios.defaults.baseURL = window.base_url;
axios.defaults.timeout = 2500;

export default {
  fetchRss() {
    return axios.get('/api/rss')
  },

}
