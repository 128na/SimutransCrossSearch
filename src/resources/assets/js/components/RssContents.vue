<template>
  <ul v-show="hasContents">
    <li class="label-date"><slot>日付</slot></li>
    <li
      v-for="(item, idx) in contents"
      :key="idx"
    >
      <span>[{{ item.time.format('YYYY/MM/DD HH:mm') }}]</span>
      <a :href="item.link" target="_blank">{{ item.title }}</a>
      <span>(<a :href="item.site.url" target="_blank">{{ item.site.name }}</a>)</span>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    contents: Array,
  },
  computed: {
    hasContents() {
      return this.contents.length > 0
    }
  }
}
</script>
