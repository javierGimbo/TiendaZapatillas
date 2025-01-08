function toggleCart() {
    var cartItems = document.getElementById('cartItems');
    if (cartItems.style.display === 'none' || cartItems.style.display === '') {
        cartItems.style.display = 'block';
    } else {
        cartItems.style.display = 'none';
    }
}
