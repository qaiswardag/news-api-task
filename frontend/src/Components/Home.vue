<script setup>
import { ref, onMounted, computed } from 'vue';

const apiURL = import.meta.env.VITE_API_URL;
const news = ref([]);
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

const fetchNews = async (countryCode) => {
  loading.value = true;
  error.value = null;
  try {
    const res = await fetch(`${apiURL}/api/v1/news/${countryCode}`);
    if (!res.ok) throw new Error('Failed to fetch news');
    const data = await res.json();
    console.log('oki:', data);
    // Handle nested data structure
    if (Array.isArray(data.data)) {
      news.value = data.data;
    } else if (data.data && Array.isArray(data.data.data)) {
      news.value = data.data.data;
    } else {
      news.value = [];
    }
    console.log('news value:', news.value);
    console.log('data:', data);
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
};

function handleCountryClick(code) {
  selectedCountry.value = code;
  fetchNews(code);
}

onMounted(() => {
  fetchNews(selectedCountry.value);
});
</script>

<template>
  <div class="font-['Jost'] min-h-[120rem] bg-gray-50 py-10">
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
      <h1 class="text-3xl font-bold mb-6 text-center text-myPrimaryLinkColor">
        {{ selectedCountryName }} News
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
