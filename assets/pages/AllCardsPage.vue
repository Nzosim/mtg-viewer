<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const page = ref(1);
const setCode = ref('');

async function loadCards(isSetCode = false) {
    if(isSetCode) page.value = 1;
    loadingCards.value = true;
    cards.value = await fetchAllCards(page.value, setCode.value);
    loadingCards.value = false;
}

function nextPage() {
    page.value++;
    loadCards();
}

function previousPage() {
    if (page.value > 1) {
        page.value--;
        loadCards();
    }
}

onMounted(() => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
        <div>
            <input id="text" v-model="setCode" />
            <button @click="loadCards(true)">Trier par code d'édition (setcode)</button>
        </div>
        <div>
            <button @click="previousPage">Page précédente</button>
            <button @click="nextPage">Page suivante</button>
        </div>  
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>
