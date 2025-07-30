<script setup>
import { ref, onMounted } from 'vue';

const apiURL = import.meta.env.VITE_API_URL;
const news = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const res = await fetch(`${apiURL}/api/v1/news/ca`);
    if (!res.ok) throw new Error('Failed to fetch news');
    const data = await res.json();
    news.value = data.data;
  } catch (e) {
    error.value = e.message + res.message;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="font-['Jost'] min-h-screen bg-gray-50 py-10">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold mb-6 text-center text-myPrimaryLinkColor">
        Canada News
      </h1>
      <div
        v-if="loading"
        class="text-center text-gray-500 py-10"
      >
        Loading...
      </div>
      <div
        v-else-if="error"
        class="text-center text-red-500 py-10"
      >
        {{ error }}
      </div>
      <div v-else>
        <div
          v-if="news.length === 0"
          class="text-center text-gray-400"
        >
          No news found.
        </div>
        <div
          v-else
          class="grid gap-6"
        >
          <div
            v-for="article in news"
            :key="article.article_id"
            class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row gap-4"
          >
            <img
              v-if="article.image_url"
              :src="article.image_url"
              alt="news image"
              class="w-full md:w-48 h-32 object-cover rounded mb-4 md:mb-0"
            />
            <div class="flex-1">
              <a
                :href="article.link"
                target="_blank"
                class="text-xl font-semibold text-blue-700 hover:underline"
                >{{ article.title }}</a
              >
              <div class="text-sm text-gray-500 mt-1 mb-2">
                <span v-if="article.pubDate">{{
                  new Date(article.pubDate).toLocaleString()
                }}</span>
                <span v-if="article.source_name">
                  &middot; {{ article.source_name }}</span
                >
              </div>
              <p class="text-gray-700 mb-2">{{ article.description }}</p>
              <div
                v-if="article.keywords && article.keywords.length"
                class="flex flex-wrap gap-2 mt-2"
              >
                <span
                  v-for="kw in article.keywords"
                  :key="kw"
                  class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs"
                  >{{ kw }}</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
