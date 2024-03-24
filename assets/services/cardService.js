export async function fetchAllCards() {
    const response = await fetch('/api/card/all');
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function searchCards(name) {
    if (name.length < 3) throw new Error('Search query must be at least 3 characters long')
    const response = await fetch(`api/card/name/${name}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch cards');
    const cards = await response.json();
    return cards.slice(0, 20);
}