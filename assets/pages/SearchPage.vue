<script setup>
import { ref } from 'vue';
import { searchCards } from '../services/cardService';

const cards = ref({});
const loadingCard = ref(false);
const search = ref('');
const setCode = ref('');

async function loadCard(name) {
    if (name.length < 3) return;
    loadingCard.value = true;
    loadingCard.value = true;
    cards.value = await searchCards(name, setCode.value);
    loadingCard.value = false;
} 
</script>

<template>
    <div>
        <input id="text" v-model="setCode" />
        <button @click="loadCard(search)">Trier par code d'édition (setcode)</button>
    </div>
    <div>
        <h1>Rechercher une carte</h1>
        <input type="text" v-model="search" @input="loadCard(search)" />
    </div>
    <div class="card-list">
        <div v-if="loadingCard">Loading...</div>
        <div v-else>
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{ card.uuid }}
                </router-link>
            </div>
        </div>
    </div>
</template>
