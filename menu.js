// Remove the window.onload function since we're handling menu items in PHP
function openPopup(popupId) {
    document.getElementById(popupId).style.display = 'block';
    document.querySelector('.overlay').style.display = 'block';
}

function closePopup() {
    document.querySelectorAll('.popup').forEach(popup => {
        popup.style.display = 'none';
    });
    document.querySelector('.overlay').style.display = 'none';
}

function submitOrder(event, productName, price) {
    event.preventDefault();
    const form = event.target;
    const selectedTopping = form.querySelector('input[type="radio"]:checked');
    
    if (!selectedTopping) {
        alert('Please select a topping');
        return;
    }
}
