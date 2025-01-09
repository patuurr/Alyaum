// Declare cart as a let variable to allow reassignment

/*
document.getElementById("menuButton").addEventListener("click", function() {
    toggleMenu();
  });

// Toggle Menu Functionality
function toggleMenu() {
    var menu = document.querySelector('slide-menu');
    var isOpen = menu.classList.contains('active');
    const button = document.getElementById('menuButton');
    alert("jalan");
    // Toggle the menu and overlay
    menu.classList.toggle('active');

    // Hide/show the menu button
    if (isOpen) {
        button.classList.remove('hidden');
    } else {
        button.classList.add('hidden');
    }
}

// Slider Functionality
const slider = document.getElementById('slider');
if (slider) {
    const slides = document.querySelectorAll('.slider img');
    if (slides.length > 0) {
        let currentIndex = 0;
        function autoSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        }
        setInterval(autoSlide, 3000);
    } else {
        console.error('No slides found.');
    }
}*/

// Add to Cart Functionality
/*const addToCartButtons = document.querySelectorAll('.add-to-cart');
addToCartButtons.forEach(button => {
    button.addEventListener('click', event => {
        event.preventDefault(); // Prevent form submission
        const productCard = event.target.closest('.product-card');
        const productName = productCard.querySelector('h3').textContent;
        const productPrice = productCard.querySelector('p').textContent;
        const quantity = productCard.querySelector('input[name="quantity"]').value;

        const cartItem = {
            name: productName,
            price: parseFloat(productPrice.replace('Rp.', '')),
            quantity: parseInt(quantity, 10),
        };

        // Send data to server to add to cart.csv
        addToCart(cartItem);
    });
});
// Declare cart as a let variable to allow reassignment

// Function to load cart items from cart.csv
function loadCart() {
    fetch('../data/cart.csv')
        .then(response => response.text())
        .then(data => {
            const rows = data.split('\n');
            cart = rows.map(row => {
                const columns = row.split(',');
                return {
                    name: columns[0],
                    price: parseFloat(columns[1]),
                    quantity: parseInt(columns[2], 10),
                };
            });
            updateCartDisplay();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Update Cart Display
function updateCartDisplay() {
    const cartItems = document.querySelector('.cart-items');
    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.classList.add('cart-item');
        itemElement.innerHTML = `
            <p>${item.name}</p>
            <p>Quantity: ${item.quantity}</p>
            <p>Price: ${item.price.toFixed(2)}</p>
        `;
        cartItems.appendChild(itemElement);
        total += item.price * item.quantity;
    });

    const totalElement = document.createElement('div');
    totalElement.classList.add('cart-item');
    totalElement.innerHTML = `<strong>Total:</strong><strong> ${total.toFixed(2)}</strong>`;
    cartItems.appendChild(totalElement);
}

// Function to add item to cart.csv
function addToCart(item) {
    const csvRow = `${item.name},${item.price},${item.quantity}\n`;
    fetch('../data/cart.csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'text/csv',
        },
        body: csvRow,
    })
        .then(response => response.text())
        .then(data => {
            loadCart();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Function to remove item from cart.csv
function removeFromCart(productName) {
    fetch('../data/cart.csv')
        .then(response => response.text())
        .then(data => {
            const rows = data.split('\n');
            const updatedRows = rows.filter(row => {
                const columns = row.split(',');
                return columns[0] !== productName;
            });
            const updatedCsv = updatedRows.join('\n');
            fetch('../data/cart.csv', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'text/csv',
                },
                body: updatedCsv,
            })
                .then(response => response.text())
                .then(data => {
                    loadCart();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Load cart items on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
});
*/
// Declare cart as a let variable to allow reassignment
let cart = [];

// Ensure DOM is fully loaded before running scripts
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    initializeEventListeners();
});

// Initialize all event listeners
function initializeEventListeners() {
    /*const menuButton = document.getElementById('menuButton');
    if (menuButton) {
        menuButton.addEventListener('click', toggleMenu);
    }*/

    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault(); // Prevent form submission
            addToCart(event);
        });
    });
}

// Toggle Menu Functionality
const navButton = document.querySelector('.nav-button');
const navMenu = document.querySelector('.slide-menu');

navButton.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

// Slider Functionality
const sliders = document.getElementById('slider');
const slides = document.querySelectorAll('.slider img');
let currentIndex = 0;

if (sliders && slides.length > 0) {
    function autoSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        sliders.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    setInterval(autoSlide, 3000); // Auto-slide every 3 seconds
} else {
    console.error('Slider or slides not found.');
}

// Add to Cart Functionality
function addToCart(event) {
    const productCard = event.target.closest('.product-card');
    if (!productCard) {
        console.error('Product card not found.');
        return;
    }

    const productName = productCard.querySelector('h3')?.textContent;
    const productPrice = productCard.querySelector('p')?.textContent;
    const quantityInput = productCard.querySelector('input[name="quantity"]');
    const quantity = quantityInput ? parseInt(quantityInput.value, 10) : 1;

    if (!productName || !productPrice || isNaN(quantity)) {
        console.error('Invalid product data.');
        return;
    }

    const cartItem = {
        name: productName,
        price: parseFloat(productPrice.replace('Rp.', '')),
        quantity: quantity,
    };

    // Send data to server to add to cart.csv
    addToCartOnServer(cartItem);
}

// Function to send cart item to the server
function addToCartOnServer(item) {
    fetch('../data/cart.csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'text/csv',
        },
        body: `${item.name},${item.price},${item.quantity}\n`,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to add item to cart.');
            }
            return response.text();
        })
        .then(() => {
            loadCart(); // Reload cart after adding item
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Function to load cart items from cart.csv
function loadCart() {
    fetch('../data/cart.csv')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load cart data.');
            }
            return response.text();
        })
        .then(data => {
            const rows = data.split('\n').filter(row => row.trim() !== ''); // Filter out empty rows
            cart = rows.map(row => {
                const columns = row.split(',');
                return {
                    name: columns[0],
                    price: parseFloat(columns[1]),
                    quantity: parseInt(columns[2], 10),
                };
            });
            updateCartDisplay();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Update Cart Display
function updateCartDisplay() {
    const cartItems = document.querySelector('.cart-items');
    if (!cartItems) {
        console.error('Cart items container not found.');
        return;
    }

    cartItems.innerHTML = ''; // Clear existing cart items
    let total = 0;

    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.classList.add('cart-item');
        itemElement.innerHTML = `
            <p>${item.name}</p>
            <p>Quantity: ${item.quantity}</p>
            <p>Price: ${item.price.toFixed(2)}</p>
            <button class="remove-item">Remove</button>
        `;
        cartItems.appendChild(itemElement);

        // Add event listener to the "Remove" button
        const removeButton = itemElement.querySelector('.remove-item');
        removeButton.addEventListener('click', () => {
            removeFromCart(item.name);
        });

        total += item.price * item.quantity;
    });

    const totalElement = document.createElement('div');
    totalElement.classList.add('cart-item');
    totalElement.innerHTML = `<strong>Total:</strong><strong> ${total.toFixed(2)}</strong>`;
    cartItems.appendChild(totalElement);
}

// Function to remove item from cart.csv
function removeFromCart(productName) {
    fetch('../data/cart.csv')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load cart data.');
            }
            return response.text();
        })
        .then(data => {
            const rows = data.split('\n').filter(row => row.trim() !== '');
            const updatedRows = rows.filter(row => {
                const columns = row.split(',');
                return columns[0] !== productName;
            });
            const updatedCsv = updatedRows.join('\n');

            return fetch('../data/cart.csv', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'text/csv',
                },
                body: updatedCsv,
            });
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update cart data.');
            }
            loadCart(); // Reload cart after removing item
        })
        .catch(error => {
            console.error('Error:', error);
        });
}