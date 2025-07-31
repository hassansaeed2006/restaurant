document.addEventListener('DOMContentLoaded', loadCart);

// Image mapping similar to menu.php
const imageMappings = {
  'strawberry mambo': 'mambo.jpeg',
  'habba dubai': 'dubai.jpeg',
  'Dubai Crepe': 'crepe.jpeg',
  'Qashtouta': 'qashtota.webp',
  'sweet Koshari': 'koshary.jpeg',
  'Plain Rice Pudding': 'rice.jpeg',
  'LOQA': 'LOQA.jpeg',
  'Farawlita dubai': 'farawlita dubai.jpeg',
  'Cheese Bomb': 'bomb.jpeg',
  'Mini Kabsa': 'Mini Kabsa.jpeg'
};a

function loadCart() {
  fetch('cart_operations.php?action=get')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayCart(data.cart);
      } else {
        alert('Error loading cart: ' + data.message);
      }
    })
    .catch(error => {
      alert('Error loading cart: ' + error);
    });
}

function displayCart(cart) {
  const cartContent = document.getElementById('cartContent');
  if (!cart || cart.length === 0) {
    cartContent.innerHTML = `
      <div class="empty-cart">
        <p>Your cart is empty</p>
        <button onclick="continueShopping()" class="btn btn-checkout">Continue Shopping</button>
      </div>`;
    return;
  }

  let total = 0;
  let tableRows = cart.map((item, i) => {
    total += item.price * item.quantity;
    // Robust image mapping: lowercase and trim product name
    const key = (item.product_name || '').toLowerCase().trim();
    const imageFile = imageMappings[key] || 'default.jpg';
    return `
      <tr>
        <td><img src="Photos/${imageFile}" alt="${item.product_name}" style="width:60px;height:60px;border-radius:8px;object-fit:cover;" /></td>
        <td>${item.product_name}</td>
        <td>${item.topping || ''}</td>
        <td>
          <input type="number" min="1" value="${item.quantity}" onchange="updateQuantity(${i}, this.value)">
        </td>
        <td>$${item.price.toFixed(2)}</td>
        <td>$${(item.price * item.quantity).toFixed(2)}</td>
        <td>
          <button onclick="deleteItem(${i})" class="btn btn-remove">Remove</button>
        </td>
      </tr>
    `;
  }).join('');

  cartContent.innerHTML = `
    <table class="cart-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Topping</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        ${tableRows}
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" style="text-align:right;"><strong>Total:</strong></td>
          <td colspan="2"><strong>$${total.toFixed(2)}</strong></td>
        </tr>
      </tfoot>
    </table>
    <div class="cart-buttons">
      <button onclick="clearCart()" class="btn">Clear Cart</button>
      <button onclick="checkout()" class="btn">Checkout</button>
      <button onclick="continueShopping()" class="btn">Continue Shopping</button>
    </div>`;
}

function updateQuantity(index, quantity) {
  fetch('cart_operations.php?action=update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ index, quantity })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) loadCart();
      else alert('Error updating quantity: ' + data.message);
    })
    .catch(error => {
      console.error('Error updating quantity:', error);
      alert('Error updating quantity');
    });
}

function deleteItem(index) {
  if (confirm("Remove this item from the cart?")) {
    fetch('cart_operations.php?action=remove', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ index })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) loadCart();
        else alert('Error removing item: ' + data.message);
      })
      .catch(error => {
        console.error('Error removing item:', error);
        alert('Error removing item');
      });
  }
}

function clearCart() {
  if (confirm("Clear your entire cart?")) {
    fetch('cart_operations.php?action=clear', { method: 'POST' })
      .then(response => response.json())
      .then(data => {
        if (data.success) loadCart();
        else alert('Error clearing cart: ' + data.message);
      })
      .catch(error => {
        console.error('Error clearing cart:', error);
        alert('Error clearing cart');
      });
  }
}

function checkout() {
  const submitBtn = document.querySelector('button[onclick="checkout()"]');
  const originalText = submitBtn.textContent;
  
  // Disable button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = 'Processing...';

  fetch('cart_operations.php?action=checkout', { 
    method: 'POST',
    credentials: 'same-origin'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.textContent = data.message;
        document.querySelector('.cart-container').insertBefore(alertDiv, document.getElementById('cartContent'));
        
        // Clear cart display
        document.getElementById('cartContent').innerHTML = `
          <div class="empty-cart">
            <p>Your order has been placed successfully!</p>
            <p>Order ID: ${data.order_id}</p>
            <button onclick="continueShopping()" class="btn btn-checkout">Continue Shopping</button>
          </div>`;
      } else {
        if (data.message === 'Please login to checkout') {
          // Redirect to login if not logged in
          window.location.href = 'login.html';
        } else {
          throw new Error(data.message || 'Checkout failed');
        }
      }
    })
    .catch(error => {
      console.error('Error during checkout:', error);
      // Show error message
      const alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-error';
      alertDiv.textContent = error.message || 'An error occurred during checkout. Please try again.';
      document.querySelector('.cart-container').insertBefore(alertDiv, document.getElementById('cartContent'));
    })
    .finally(() => {
      // Reset button state
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });
}

function continueShopping() {
  window.location.href = 'menu.php';
}

function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdownMenu');
  dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

function signOut() {
  window.location.href = 'login.html';
}
