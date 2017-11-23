<style scoed>
  .rss-feed ul {
    padding: 8px;
  }
  .rss-feed li {
    list-style: none;
  }
  .rss-feed {
    border: 1px solid #ddd;
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
  <div id="rss-reader">
    <div class="tab-content rss-feed">
      <div class="tab-pane active">
        <ul v-if="hasList">
          <rss-contents :contents="listToday">今日</rss-contents>
          <rss-contents :contents="listThisWeek">今週</rss-contents>
          <rss-contents :contents="listLastWeek">先週以前</rss-contents>
        </ul>
        <ul v-else>
          <li>読み込み中です</li>
        </ul>
      </div>
    </div>
  </div>
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
      sites     : [],
      data      : [],
      today     : moment().startOf('day'),
      this_week : moment().subtract(7, 'days'),
    }
  },
  created() {
    this.importSites()
    this.fetchSites()
  },
  methods: {
    importSites()
    {
      this.sites = window.sites
    },
    fetchSites() {
      this.sites.map(async s => {
        try {
          const res = await api.fetchSite(s.id)
          res.data.time = moment(res.data.time, 'X')

          this.data.push(res.data)
        } catch(err) {
          console.log(err)
        }
      })
    },

    toggleList(sid = null) {
      this.sid = sid
    },
  },
  computed: {
    list() {
      return this.data.sort((a, b) => b.time - a.time)
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
