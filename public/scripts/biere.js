var url_string = window.location.href;
var url = new URL(url_string);
var paramValue = url.searchParams.get("id");
if (!paramValue) {
    window.location.href = "/index.html";
}
else {
    const id = parseInt(paramValue);
    if (isNaN(id)) {
        window.location.href = "/index.html";
    }
    else {
        getBeers().then(async beers => {
            const beer = beers.find(beer => beer.id === id);

            const beertitle = document.querySelector("#beertitle");
            const beerrank = document.querySelector("#beerrank");
            const beeralcohol = document.querySelector("#beeralcohol");
            const beerprice = document.querySelector("#prix");
            const beerimg = document.querySelector("#Im1_pagebiere");
            const beerdescription = document.querySelector("#description");

            beertitle.innerHTML = beer.name;
            beerrank.innerHTML = `${beer.mark}/5`;
            beeralcohol.innerHTML = `${beer.type} - ${beer.alcohol}%`;
            beerprice.innerHTML = `${beer.price} Or`;
            beerimg.src = beer.imagePath;
            beerimg.alt = beer.name;
            beerdescription.innerHTML = beer.description;

            const addPanier = document.querySelector("#addPanier");
            addPanier.addEventListener("click", async event => {
                const quantity = parseInt(document.querySelector("#nbbiere").value);
                if (isNaN(quantity) || quantity <= 0) {
                    alert("Veuillez entrer une quantité valide");
                    return;
                }

                if (quantity > beer.stock) {
                    const refuelTime = (await getRefuelTime(beer.id))['time'];

                    if (beer.stock === 0) {
                        alert('Nous sommes actuellement à cours de ce produit, nous recevrons une nouvelle livraison dans ' + refuelTime + ' temps');
                    }
                    else {
                        alert('Nous sommes actuellement à cours de ce produit, nous recevrons une nouvelle livraison dans ' + refuelTime + ' temps, vous pouvez commander ' + beer.stock + ' bières au maximum');
                    }
                    return;
                }

                let currentOrder = getCurrentOrder();
                if (!currentOrder) {
                    currentOrder = {
                        beers: {},
                    }
                }

                if (Object.keys(currentOrder.beers).indexOf(beer.id.toString()) !== -1) {
                    currentOrder.beers[beer.id] += quantity;
                }
                else {
                    currentOrder.beers[beer.id] = quantity;
                }
                setCurrentOrder(currentOrder);

                window.location.href = "/panier.html";
            });

            const oneClickOrder = document.querySelector("#oneClickOrder");
            oneClickOrder.addEventListener("click", async event => {
                const quantity = parseInt(document.querySelector("#nbbiere").value);
                if (isNaN(quantity) || quantity <= 0) {
                    alert("Veuillez entrer une quantité valide");
                    return;
                }

                if (quantity > beer.stock) {
                    const refuelTime = (await getRefuelTime(beer.id))['time'];

                    if (beer.stock === 0) {
                        alert('Nous sommes actuellement à cours de ce produit, nous recevrons une nouvelle livraison dans ' + refuelTime + ' temps');
                    }
                    else {
                        alert('Nous sommes actuellement à cours de ce produit, nous recevrons une nouvelle livraison dans ' + refuelTime + ' temps, vous pouvez commander ' + beer.stock + ' bières au maximum');
                    }
                    return;
                }

                let currentOrder = getCurrentOrder();
                if (!currentOrder) {
                    currentOrder = {
                        beers: {},
                    }
                }

                if (Object.keys(currentOrder.beers).indexOf(beer.id.toString()) !== -1) {
                    currentOrder.beers[beer.id] += quantity;
                }
                else {
                    currentOrder.beers[beer.id] = quantity;
                }
                setCurrentOrder(currentOrder);

                window.location.href = "/paiement.html";
            });
        });
    }
}