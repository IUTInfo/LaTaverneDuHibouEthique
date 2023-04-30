const beerContainer = document.querySelector("#beer-container");
const beerSum = document.querySelector("#beer-sum");
const orderForm = document.querySelector("#order-form");

function addBeerToContainer(beerName, beerAmount, beerPrice) {
    const span = document.createElement("span");

    const p1 = document.createElement("p");
    p1.innerHTML = `${beerAmount} x ${beerName}`;

    const p2 = document.createElement("p");
    p2.innerHTML = `${beerPrice * beerAmount} Or`;

    span.appendChild(p1);
    span.appendChild(p2);

    beerContainer.prepend(span);
}

function setBeerSum(totalPrice) {
    beerSum.innerHTML = `${totalPrice} Or`;
}

function hideCart() {
    orderForm.style.display = "none";
    document.querySelector("#commande").style.display = "none";
    document.querySelector("main > h1").innerHTML = "Votre panier est vide!";
}

orderForm.addEventListener("submit", async event => {
    event.preventDefault();

    const name = document.querySelector("[name=name]").value;
    const address = document.querySelector("[name=address]").value;
    const pigeonnumber = document.querySelector("[name=pigeonnumber]").value;

    const currentOrder = getCurrentOrder();
    if (!currentOrder)
        return;

    const order = {
        firstname: '_',
        lastname: name,
        pigeonnumber: `${pigeonnumber}`,
        address: address,
        beers: currentOrder.beers
    };
    try {
        await postOrder(order);
        window.location.href = "/remerciment.html";
    }
    catch (e) {
        console.error(e);
        alert("Nous sommes actuellement à cours d'un de vos produits");
    }
});

getBeers().then(async beers => {
    const currentOrder = getCurrentOrder();
    if (!currentOrder) {
        hideCart();
        return;
    }

    let totalPrice = 0;
    const beerIds = Object.keys(currentOrder.beers);
    beerIds.forEach(beerId => {
        const beer = beers.find(beer => beer.id == beerId);
        addBeerToContainer(beer.name, currentOrder.beers[beerId], beer.price);
        totalPrice += beer.price * currentOrder.beers[beerId];
    });
    setBeerSum(totalPrice);
});
