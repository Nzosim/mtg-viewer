export async function fetchAllCards(page = 1, setCode = null) {
    let url = `/api/card/all?page=${page}${setCode ? `&setCode=${setCode}` : ''}`;
    const response = await fetch(url);
    console.log(url)
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

export async function searchCards(name, setCode = null) {
    if (name.length < 3) throw new Error('Search query must be at least 3 characters long')
    const response = await fetch(`api/card/name/${name}${setCode ? `?setCode=${setCode}` : ''}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch cards');
    const cards = await response.json();
    return cards.slice(0, 20);
}