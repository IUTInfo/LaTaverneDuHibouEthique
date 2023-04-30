const title = document.querySelector('#miseEnAvant > h1');
const description = document.querySelector('#miseEnAvant > p');

getBeers().then(async beers => {
    const bestBeer = beers.find(beer => beer.id == 1);
    title.innerHTML = bestBeer.name;
    description.innerHTML = bestBeer.description;
});