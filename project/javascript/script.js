const cartItems = document.querySelector('.cart-items');
const addToCartButtons = document.querySelectorAll('.add-to-cart');
const checkoutButton = document.getElementById('checkout-button');
const cart = [];

function toggleMenu() {
    const menu = document.getElementById('slideMenu');
    const overlay = document.getElementById('overlay');
    const isOpen = menu.classList.contains('open');
    const button = document.getElementById('menuButton');

    // Toggle the menu visibility
    menu.classList.toggle('open');
    overlay.classList.toggle('show');
    if (isOpen) {
        button.classList.remove('hidden');
    }
    else {
        button.classList.add('hidden');
    }
}

const slider = document.getElementById('slider');
const slides = document.querySelectorAll('.slider img');
let currentIndex = 0;

function autoSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    slider.style.transform = `translateX(-${currentIndex * 100}%)`;
}

setInterval(autoSlide, 3000); // Auto-slide every 3 seconds

addToCartButtons.forEach(button => {
    button.addEventListener('click', event => {
        const productCard = event.target.parentElement;
        const productName = productCard.querySelector('h3').textContent;
        const productPrice = productCard.querySelector('p').textContent;

        const cartItem = {
            name: productName,
            price: parseFloat(productPrice.replace('Rp.', '')),
        };

        cart.push(cartItem);

        updateCartDisplay();
    });
});

function updateCartDisplay() {
    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.classList.add('cart-item');
        itemElement.innerHTML = `<p>${item.name}</p><p>Rp. ${item.price.toFixed(3)}</p>`;
        cartItems.appendChild(itemElement);
        total += item.price;
    });

    const totalElement = document.createElement('div');
    totalElement.classList.add('cart-item');
    totalElement.innerHTML = `<strong>Total:</strong><strong>Rp. ${total.toFixed(3)}</strong>`;
    cartItems.appendChild(totalElement);
}

/*checkoutButton.addEventListener('click', () => {
    if (cart.length > 0) {
        alert('Thank you for your purchase!');
        cart.length = 0; // Clear the cart
        updateCartDisplay();
    } else {
        alert('Your cart is empty!');
    }
});*/

checkoutButton.addEventListener('click', () => {
    if (cart.length > 0) {
        localStorage.setItem('cart', JSON.stringify(cart)); // Save cart to localStorage
        window.location.href = '../payment.html'; // Redirect to the payment page
    } else {
        alert('Your cart is empty!');
    }
});

document.getElementById('contact-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Gather form data
    const formData = new FormData(this);

    // Perform the AJAX request using Fetch API
    fetch('../send_message.php', { // Adjust URL to your backend script
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('form-message').style.display = 'block';
            document.getElementById('form-error').style.display = 'none';
            document.getElementById('contact-form').reset(); // Reset the form
            alert('Message sent successfully!')
        } else {
            // Show error message
            document.getElementById('form-error').style.display = 'block';
            document.getElementById('form-message').style.display = 'none';
            alert('There was an error sending your message. Please try again later.')
        }
    })
    .catch(error => {
        // Handle network or other errors
        document.getElementById('form-error').style.display = 'block';
        document.getElementById('form-message').style.display = 'none';
        console.error('Error:', error);
    });
});

// Fetch cart items and render them
async function fetchCart(userId) {
    try {
        const response = await fetch(`http://localhost:5000/api/cart/${userId}`);
        const cart = await response.json();

        if (response.ok) {
            renderCartItems(cart.products);
        } else {
            alert(cart.message || 'Error fetching cart');
        }
    } catch (error) {
        alert('Error connecting to the server.');
    }
}

// Render cart items
function renderCartItems(items) {
    const cartItemsContainer = document.querySelector('.cart-items');
    cartItemsContainer.innerHTML = ''; // Clear any existing content

    if (items.length === 0) {
        cartItemsContainer.innerHTML = '<p>No items in your cart yet!</p>';
        return;
    }

    items.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
            <div class="item-info">
                <h3>${item.productId.name}</h3>
                <p>Quantity: ${item.quantity}</p>
                <p>Price: $${item.productId.price}</p>
            </div>
            <button class="remove-button" data-product-id="${item.productId._id}">Remove</button>
        `;
        cartItemsContainer.appendChild(cartItem);
    });

    // Attach event listeners to remove buttons
    document.querySelectorAll('.remove-button').forEach(button => {
        button.addEventListener('click', event => {
            const productId = event.target.dataset.productId;
            removeFromCart('userId123', productId);
        });
    });
}

// Remove item from cart
async function removeFromCart(userId, productId) {
    try {
        const response = await fetch(`http://localhost:5000/api/cart/${userId}/${productId}`, {
            method: 'DELETE',
        });

        if (response.ok) {
            alert('Item removed from cart.');
            fetchCart(userId); // Refresh the cart display
        } else {
            const result = await response.json();
            alert(result.message || 'Error removing item.');
        }
    } catch (error) {
        alert('Error connecting to the server.');
    }
}

// Handle checkout button
document.getElementById('checkout-button').addEventListener('click', () => {
    alert('Proceeding to checkout...');
    // Add checkout functionality or redirect here
});

// Example: Load cart for a specific user on page load
document.addEventListener('DOMContentLoaded', () => {
    const userId = 'userId123'; // Replace with actual user ID
    fetchCart(userId);
});

document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners to "Add to Cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            const productCard = button.parentElement;
            const product = {
                id: index + 1, // Unique ID for the product
                name: productCard.querySelector('h3').textContent,
                price: parseFloat(productCard.querySelector('p').textContent.replace('$', '')),
                image: productCard.querySelector('img').src,
                quantity: 1, // Default quantity
            };
            addToCart(product);
        });
    });
});

// Add product to cart in localStorage
function addToCart(product) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if the product already exists in the cart
    const existingProduct = cart.find(item => item.id === product.id);
    if (existingProduct) {
        existingProduct.quantity += 1; // Increment quantity if product already exists
    } else {
        cart.push(product); // Add new product to the cart
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${product.name} has been added to your cart!`);
}

// Load cart items on the cart page
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('cart.html')) {
        loadCart();
    }
});

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsContainer = document.querySelector('.cart-items');
    cartItemsContainer.innerHTML = ''; // Clear existing content

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>No items in your cart yet!</p>';
        return;
    }

    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
            <div class="item-info">
                <img src="${item.image}" alt="${item.name}" class="item-image">
                <div>
                    <h3>${item.name}</h3>
                    <p>Price: $${item.price.toFixed(2)}</p>
                    <p>Quantity: ${item.quantity}</p>
                </div>
            </div>
            <button class="remove-button" data-id="${item.id}">Remove</button>
        `;
        cartItemsContainer.appendChild(cartItem);
    });

    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-button').forEach(button => {
        button.addEventListener('click', event => {
            const productId = parseInt(event.target.dataset.id);
            removeFromCart(productId);
        });
    });
}

// Remove product from cart
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart(); // Refresh the cart display
}
