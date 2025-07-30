<script setup>
import { ref, onMounted, computed } from 'vue';

const apiURL = import.meta.env.VITE_API_URL;
const news = ref([]);
const nextPage = ref(null);
const loading = ref(true);
const error = ref(null);
const countries = [
  { name: 'Belgium', code: 'be', languages: ['nl'] },
  { name: 'Canada', code: 'ca', languages: ['en', 'fr'] },
  { name: 'France', code: 'fr', languages: ['fr'] },
  { name: 'Germany', code: 'de', languages: ['de'] },
  { name: 'United Kingdom', code: 'gb', languages: ['en'] },
];

const selectedCountry = ref('gb');
const selectedCountryName = computed(() => {
  const found = countries.find((c) => c.code === selectedCountry.value);
  return found ? found.name : '';
});

const fetchNews = async (countryCode, page = null, append = false) => {
  loading.value = true;
  error.value = null;
  try {
    let url = `${apiURL}/api/v1/news/${countryCode}`;
    if (page) url += `?page=${page}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('Failed to fetch news');
    const data = await res.json();
    // Handle nested data structure
    let articles = [];
    if (Array.isArray(data.data)) {
      articles = data.data;
    } else if (data.data && Array.isArray(data.data.data)) {
      articles = data.data.data;
    }
    if (append) {
      news.value = [...news.value, ...articles];
    } else {
      news.value = articles;
    }
    nextPage.value = data.nextPage || null;
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
};

function handleCountryClick(code) {
  selectedCountry.value = code;
  fetchNews(code);
  nextPage.value = null;
}

function loadMore() {
  if (nextPage.value) {
    fetchNews(selectedCountry.value, nextPage.value, true);
  }
}

onMounted(() => {
  fetchNews(selectedCountry.value);
});
</script>

<template>
  <div class="font-['Jost'] min-h-[120rem] bg-gray-50 py-10">
    <h1
      class="text-4xl font-medium flex items-center justify-center pt-8 pb-12"
    >
      Your Window to the World’s Latest News
    </h1>
    <div class="max-w-4xl mx-auto">
      <div class="flex flex-wrap gap-2 justify-center mb-8">
        <button
          v-for="country in countries"
          :key="country.code"
          @click="handleCountryClick(country.code)"
          :class="[
            'px-4 py-2 rounded-full border font-medium transition cursor-pointer',
            selectedCountry === country.code
              ? 'bg-blue-600 border-blue-600 shadow'
              : 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50',
            selectedCountry === country.code
              ? 'bg-gray-100 text-gray-900'
              : 'text-gray-900',
          ]"
        >
          {{ country.name }}
        </button>
      </div>
      <h1 class="text-2xl font-medium mb-6 text-center text-myPrimaryLinkColor">
        News from {{ selectedCountryName }}
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
        <div v-else>
          <div class="grid gap-6">
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
          <div
            v-if="nextPage"
            class="flex justify-center mt-8"
          >
            <button
              @click="loadMore"
              class="px-6 py-2 rounded bg-blue-600 text-white font-semibold hover:bg-blue-700 transition cursor-pointer"
            >
              Load More
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
