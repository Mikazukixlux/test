document.addEventListener("DOMContentLoaded", () => {
    // Fetch products from the server
    fetch('fetch_products.php')
        .then(response => response.json())
        .then(data => {
            const productsContainer = document.getElementById('products');
            data.forEach(product => {
                productsContainer.innerHTML += `
                    <div class="product">
                        <img src="${product.image_url}" alt="${product.name}" class="product-image">
                        <h3>${product.name}</h3>
                        <p>${product.description}</p>
                        <p><strong>Price:</strong> $${product.price}</p>
                        <p><strong>Stock:</strong> ${product.stock_quantity}</p>
                        <button onclick="addToCart(${product.product_id}, '${product.name}', ${product.price})">
                            Add to Cart
                        </button>
                    </div>
                `;
            });
        })
        .catch(error => console.error('Error fetching products:', error));
});

// Add to Cart functionality
let cart = [];
function addToCart(id, name, price) {
    const cartItem = cart.find(item => item.id === id);
    if (cartItem) {
        cartItem.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    updateCart();
}

function updateCart() {
    const cartPreview = document.getElementById('cart-preview');
    const cartItems = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');

    cartPreview.innerHTML = '';
    cartItems.innerHTML = '';
    let subtotal = 0;

    cart.forEach(item => {
        subtotal += item.price * item.quantity;
        cartPreview.innerHTML += `<div>${item.name} (${item.quantity}) - $${item.price}</div>`;
        cartItems.innerHTML += `
            <div class="cart-item">
                <span>${item.name} (${item.quantity})</span>
                <span>$${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `;
    });

    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    cartCount.innerText = cart.length;
    subtotalElement.innerText = subtotal.toFixed(2);
    taxElement.innerText = tax.toFixed(2);
    totalElement.innerText = total.toFixed(2);
}

function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('open');
}

function checkout() {
    alert('Checkout functionality coming soon!');
}
