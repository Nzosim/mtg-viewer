<script setup>
import { onMounted, ref } from 'vue';
import { fetchCodes } from '../services/cardService';

const codes = ref([]);
const loadingCards = ref(true);

async function loadCards() {
    loadingCards.value = true;
    codes.value = await fetchCodes();
    loadingCards.value = false;
}

onMounted(() => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Liste des codes d'édition</h1>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <ul v-else>
            <li class="card-result" v-for="code in codes" :key="code.setCode">
                {{ code.setCode }}
            </li>
        </ul>
    </div>
</template>
