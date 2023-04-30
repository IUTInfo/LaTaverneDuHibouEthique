const items = document.getElementById("list");

function addItemToList(name, amount) {
    const p = document.createElement("p");
    p.innerHTML = `${amount} x ${name}`;
    items.prepend(p);
}

getBeers().then(async beers => {
    const currentOrder = getCurrentOrder();
    if (!currentOrder) {
        window.location.href = "/accueil.html";
        return;
    }

    const beerIds = Object.keys(currentOrder.beers);
    beerIds.forEach(beerId => {
        const beer = beers.find(beer => beer.id == beerId);
        addItemToList(beer.name, currentOrder.beers[beerId]);
    });

    setCurrentOrder(undefined);
});