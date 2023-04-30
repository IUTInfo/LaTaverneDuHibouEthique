async function getBeers() {
    var requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    return await (await fetch("/api/beers", requestOptions)).json();
}

async function getBeer(id) {
    var requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    return await (await fetch(`/api/beer/${id}`, requestOptions)).json();
}

/**
 * @param beer: {id: number, name: string, type: string, alcohol: number, price: number, description: string, imagePath: string, stock: number}
 * @returns {Promise<void>}
 */
async function updateBeer(beer) {
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    var requestOptions = {
        method: 'PUT',
        headers: myHeaders,
        body: JSON.stringify(beer),
        redirect: 'follow'
    };

    await fetch("/api/beer", requestOptions);
}

async function sync() {
    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
    };

    await fetch("/api/sync", requestOptions);
}

async function getOrder(orderId) {
    var requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    return await (await fetch("/api/order/" + orderId, requestOptions)).json();
}

/**
 *
 * @param order: {firstname: string, lastname: string, pigeonnumber: string, address: string, beers: [id: number]: number}]}
 * @returns {Promise<void>}
 */
async function postOrder(order) {
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: JSON.stringify(order),
        redirect: 'follow'
    };

    await fetch("/api/order", requestOptions);
}
