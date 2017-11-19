<style scoed>
  #rss-reader {
    max-height: 100rem;
  }
  .rss-feed ul {
    padding: 8px;
  }
  .rss-feed li {
    list-style: none;
  }
</style>

<template>
  <div v-if="loaded" id="rss-reader">
    <ul class="nav nav-tabs">
      <li class="active" @click="toggleList()">
        <a href="" data-toggle="tab">All <small>({{ total }})</small></a>
      </li>
      <li v-for="(site, sid) in sites" @click="toggleList(sid)">
        <a href="" data-toggle="tab">{{ site.name }} <small>({{ site.count }})</small></a>
      </li>
    </ul>
    <div class="tab-content rss-feed">
      <div class="tab-pane active">
        <ul v-if="hasList">
          <li
            v-for="(item, idx) in list"
            :key="idx"
          >
            <span>[{{ timeFormat(item.time) }}]</span>
            <a :href="item.link" target="_blank">{{ item.title }}</a>
            <span>( <a :href="site(item.sid).url" target="_blank">{{ site(item.sid).name }}</a> )</span>
          </li>
        </ul>
        <ul v-else>
          <li>データがありません</li>
        </ul>
      </div>
    </div>
  </div>
  <div v-else>読み込み中です...</div>
</template>

<script>
import moment from 'moment'
import api from './services/api'

export default {
  data() {
    return {
      loaded: false,
      sites: [],
      data: [],
      sid : null,
    }
  },
  created() {
    this.fetchRss()
  },
  methods: {
    async fetchRss() {
      try {
        const res = await api.fetchRss()
        console.log(res)
        this.sites = res.data.sites
        this.data  = res.data.data
        if (res.data.error.length) {
          console.warn(res.data.error)
        }
      } catch(err) {
        console.log(err)
      }
      this.loaded = true
    },

    toggleList(sid = null) {
      this.sid = sid
    },

    timeFormat(time) {
      return moment(time, 'X').format('YYYY/MM/DD HH:mm')
    },

    site(sid) {
      return this.sites[sid]
    },
  },
  computed: {
    list() {
      return this.sid ? this.data.filter(d => d.sid === this.sid) : this.data
    },
    hasList() {
      return this.list.length > 0
    },
    total() {
      return this.data.length
    }
  }
}
</script>
