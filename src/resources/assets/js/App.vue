<style scoed>
  .rss-feed ul {
    padding: 8px;
  }
  .rss-feed li {
    list-style: none;
  }
  .rss-feed {
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    max-height: 36rem;
    overflow-y: scroll;
    background-color: #fff;
  }

  .nav-tabs > li.active > a,
  .nav-tabs > li.active > a:hover,
  .nav-tabs > li.active > a:focus {
    color: #555555;
    border: 1px solid #ddd;
    background-color: #fff;
    border-bottom-color: transparent;
    cursor: default;
  }
  .label-date {
    border-bottom: solid 1px #ddd;
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
          <rss-contents :contents="listToday">今日</rss-contents>
          <rss-contents :contents="listThisWeek">今週</rss-contents>
          <rss-contents :contents="listLastWeek">先週以前</rss-contents>
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
import RssContents from './components/RssContents'
export default {
  components: {
    'rss-contents': RssContents
  },
  data() {
    return {
      loaded    : false,
      sites     : [],
      data      : [],
      sid       : null,
      today     : moment().startOf('day'),
      this_week : moment().subtract(7, 'days'),
    }
  },
  created() {
    this.fetchRss()
  },
  methods: {
    async fetchRss() {
      try {
        const res = await api.fetchRss()
        this.sites = res.data.sites
        this.data  = res.data.data.map(d => {
          d.time = moment(d.time, 'X')
          d.site = this.sites[d.sid]
          return d
        })
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
  },
  computed: {
    list() {
      return this.sid ? this.data.filter(d => d.sid === this.sid) : this.data
    },
    // 今日
    listToday() {
      return this.list.filter(d => this.today <= d.time)
    },
    // 今週
    listThisWeek() {
      return this.list.filter(d => this.this_week <= d.time && d.time < this.today)
    },
    // 1週間より前
    listLastWeek() {
      return this.list.filter(d => d.time < this.this_week)
    },

    total() {
      return this.data.length
    },
    hasList() {
      return this.list.length > 0
    }
  }
}
</script>
