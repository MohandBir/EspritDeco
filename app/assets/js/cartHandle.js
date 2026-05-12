let clearCartBtn = document.getElementById('clearCart');
let cartLines = document.getElementById('cartLines');
let cartCount = document.getElementById('cartCount');
let increaseButtons = document.querySelectorAll('.increaseBtn');
let decreaseButtons = document.querySelectorAll('.decreaseBtn');
let totalPrice = document.getElementById('totalPrice');
console.log(totalPrice);

clearCartBtn.addEventListener("click" , (e) => {
    e.preventDefault();
    fetch('http://localhost:8080/cart/clear')
    .then(() => {
      clearCart();
    });
})

for (let btn of increaseButtons) {
    btn.addEventListener("click" , () => {
        let id = btn.dataset.id
        fetch('http://localhost:8080/cart/add/' + id)
        .then(() => {
           increaseQty(btn);
        });       
    
    })
}

for (let btn of decreaseButtons) {
    btn.addEventListener("click" , () => {
        let id = btn.dataset.id
        let td  = btn.closest('td');
        let qtyNumber = td.querySelector('.qtyNumber');
        if (Number(qtyNumber.textContent) == 1) {
            return;
        }
        fetch('http://localhost:8080/cart/decrease/' + id)
        .then(() => {
           decreaseQty(btn);
        });       
    
    })
}

function increaseQty(btn) {
    let productPrice = Number(btn.dataset.price);

    let td  = btn.closest('td');
    let qtyNumber = td.querySelector('.qtyNumber');
    let qty = Number(qtyNumber.textContent)
    qtyNumber.textContent = `${qty + 1}`;
    
    let tr = btn.closest('tr');
    let price = tr.querySelector('.price')
    let priceNum = Number(price.textContent);
    let finalP = (qty+1) * productPrice  
    price.textContent = `${Number(finalP.toFixed(2))}`;

    let finalTotalPrice =  Number((totalPrice.textContent)) + productPrice;
    totalPrice.textContent = `${Number(finalTotalPrice.toFixed(2))}`
}

function decreaseQty(btn) {
    let productPrice = Number(btn.dataset.price);

    let td  = btn.closest('td');
    let qtyNumber = td.querySelector('.qtyNumber');
    let qty = Number(qtyNumber.textContent)
    qtyNumber.textContent = `${qty - 1}`; 
    
    let tr = btn.closest('tr');
    let price = tr.querySelector('.price')
    let priceNum = Number(price.textContent);
    let finalP = (qty-1) * productPrice  
    price.textContent = `${Number(finalP.toFixed(2))}`;
    
    let finalTotalPrice =  Number((totalPrice.textContent)) - productPrice;
    totalPrice.textContent = `${Number(finalTotalPrice.toFixed(2))}`;
}

function clearCart() {
    cartLines.innerHTML = `
    <tr class="table-light">
        <td class="text-center fs-5">
            Votre panier est vide 🚫
        </td>
    </tr>
    `;        
    cartCount.textContent = 0;        
}


