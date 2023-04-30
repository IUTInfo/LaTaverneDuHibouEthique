const beerContainer = document.querySelector('.bieres');

function addBeerToContainer(id, imagePath, name, type, alcohol, price, description) {
    const isImportant = id === 1;

    const img = document.createElement('img');
    img.src = imagePath;
    img.alt = name;
    if (isImportant)
        img.classList.add('bouteilleImportante');

    beerContainer.appendChild(img);

    const nameAndType = document.createElement('div');
    nameAndType.classList.add('biere');
    if (isImportant)
        nameAndType.classList.add('bouteilleImportante');
    const nameh2 = document.createElement('h2');
    nameh2.innerText = name;

    const typeh4 = document.createElement('h4');
    typeh4.innerText = type;

    nameAndType.appendChild(nameh2);
    nameAndType.appendChild(typeh4);

    beerContainer.appendChild(nameAndType);

    const alcoholAndPrice = document.createElement('div');
    alcoholAndPrice.classList.add('pConteneur');
    if (isImportant)
        alcoholAndPrice.classList.add('bouteilleImportante');
    const alcoholp = document.createElement('p');
    alcoholp.innerText = `${alcohol}%`;

    const pricep = document.createElement('p');
    pricep.innerText = `${price} Or`;

    alcoholAndPrice.appendChild(alcoholp);
    alcoholAndPrice.appendChild(pricep);

    beerContainer.appendChild(alcoholAndPrice);

    const divButton = document.createElement('div');
    divButton.classList.add('button');
    if (isImportant)
        divButton.classList.add('bouteilleImportante');

    const arefEmplacement = document.createElement('a');
    arefEmplacement.href = '/biere?id=' + id;

    const button = document.createElement('button');
    button.innerText = 'Voir plus';
    button.type = 'button';

    arefEmplacement.appendChild(button);
    divButton.appendChild(arefEmplacement);

    beerContainer.appendChild(divButton);
}

getBeers().then(async beers => {
    beers.forEach(beer => {
        addBeerToContainer(beer.id, beer.imagePath, beer.name, beer.type, beer.alcohol, beer.price, beer.description);
    })
});