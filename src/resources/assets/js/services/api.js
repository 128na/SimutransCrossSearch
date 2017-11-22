import axios from 'axios'

axios.defaults.baseURL = window.base_url;
axios.defaults.timeout = 10000;

export default {
  fetchRss() {
    return axios.get('/api/rss')
  },

}
