let currentOrder = getCurrentOrder();
if (!currentOrder) {
    currentOrder = {
        beers: {},
    }
    setCurrentOrder(currentOrder);
}

const panierContainer = document.querySelector("#panier > div");
const commandContainer = document.querySelector("#commande > div");

function refreshPanier(beers) {
    panierContainer.innerHTML = "";
    commandContainer.innerHTML = "";

    let totalPrice = 0;
    Object.keys(currentOrder.beers).forEach(async beerId => {
        const beerAmount = currentOrder.beers[beerId];
        const beer = beers.find(beer => beer.id === parseInt(beerId));
        totalPrice += beerAmount * beer.price;

        const trucSpan = document.createElement("span");
        const pQuantity = document.createElement("p");
        pQuantity.innerHTML = beerAmount + " x " + beer.name;
        trucSpan.appendChild(pQuantity);
        const pPrice1 = document.createElement("p");
        pPrice1.innerHTML = beerAmount * beer.price + " Or";
        trucSpan.appendChild(pPrice1);
        commandContainer.appendChild(trucSpan);

        const imgBeer = document.createElement("img");
        imgBeer.src = beer.imagePath;
        imgBeer.alt = beer.name;
        panierContainer.appendChild(imgBeer);

        const detailDiv = document.createElement("div");
        detailDiv.classList.add("infoBiere");

        const h3Title = document.createElement("h3");
        h3Title.innerHTML = beer.name;
        detailDiv.appendChild(h3Title);

        const pType = document.createElement("p");
        pType.innerHTML = beer.type;
        detailDiv.appendChild(pType);

        const divMoreDetail = document.createElement("div");
        const pAlcohol = document.createElement("p");
        pAlcohol.innerHTML = beer.alcohol + "%";
        divMoreDetail.appendChild(pAlcohol);

        const spanPrice = document.createElement("span");
        const pPrice = document.createElement("p");
        pPrice.innerHTML = `${beer.price}`;
        spanPrice.appendChild(pPrice);

        const pSuffix = document.createElement("p");
        pSuffix.innerHTML = " Or/Bouteille";
        spanPrice.appendChild(pSuffix);

        divMoreDetail.appendChild(spanPrice);

        detailDiv.appendChild(divMoreDetail);

        panierContainer.appendChild(detailDiv);

        const inputQuantity = document.createElement("input");
        inputQuantity.type = "number";
        inputQuantity.min = 1;
        inputQuantity.max = beer.stock;
        inputQuantity.value = beerAmount;
        inputQuantity.addEventListener("change", event => {
            const newValue = parseInt(inputQuantity.value);
            if (isNaN(newValue) || newValue <= 0) {
                delete currentOrder.beers[beerId];
                setCurrentOrder(currentOrder);
                refreshPanier(beers);
            }
            else {
                currentOrder.beers[beerId] = newValue;
                setCurrentOrder(currentOrder);
                refreshPanier(beers);
            }
        });
        panierContainer.appendChild(inputQuantity);

        const buttonDelete = document.createElement("button");
        buttonDelete.innerHTML = "Supprimer";
        buttonDelete.addEventListener("click", event => {
            delete currentOrder.beers[beerId];
            setCurrentOrder(currentOrder);
            refreshPanier(beers);
        });
        panierContainer.appendChild(buttonDelete);
    });

    const hr = document.createElement("hr");
    commandContainer.appendChild(hr);

    const totalSpan = document.createElement("span");
    const pTotal = document.createElement("p");
    pTotal.innerHTML = "Total";
    totalSpan.appendChild(pTotal);
    const pPrice2 = document.createElement("p");
    pPrice2.innerHTML = totalPrice + " Or";
    totalSpan.appendChild(pPrice2);

    const offerSpan = document.createElement("span");
    const pOffer = document.createElement("p");
    pOffer.innerHTML = "+ Echantillon offert par la maison de Kill Elves";
    offerSpan.appendChild(pOffer);
    commandContainer.appendChild(offerSpan);

    const payDiv = document.createElement("div");
    const payButton = document.createElement("button");
    payButton.innerHTML = "Procéder au paiement";
    payButton.addEventListener("click", event => {
        window.location.href = "paiement.html";
    });
    payDiv.appendChild(payButton);
    commandContainer.appendChild(payDiv);
}

function setSuggestion(beers, name, imagePath, price, alcohol, type) {
    const suggestion = document.querySelector("#suggestion > div");
    suggestion.querySelector("h3").innerHTML = name;
    suggestion.querySelector("p").innerHTML = type;
    suggestion.querySelector("img").src = imagePath;
    suggestion.querySelector("img").alt = name;
    suggestion.querySelector("div > p").innerHTML = alcohol + "%";
    suggestion.querySelector("div > span > p:first-child").innerHTML = price;

    suggestion.querySelector("input[type=number]").addEventListener("change", event => {
        const newValue = parseInt(event.target.value);
        if (isNaN(newValue) || newValue <= 0) {
            delete currentOrder.beers[1];
            setCurrentOrder(currentOrder);
            refreshPanier(beers);
        }
        else {
            currentOrder.beers[1] = newValue;
            setCurrentOrder(currentOrder);
            refreshPanier(beers);
        }
    });
}

getBeers().then(async beers => {
    refreshPanier(beers);

    const bestBeer = beers.find(beer => beer.id === 1);
    setSuggestion(beers, bestBeer.name, bestBeer.imagePath, bestBeer.price, bestBeer.alcohol, bestBeer.type);
});