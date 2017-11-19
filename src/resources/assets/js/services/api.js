import axios from 'axios'

export default {
  fetchRss() {
    return axios.get('/api/rss')
  },

}
