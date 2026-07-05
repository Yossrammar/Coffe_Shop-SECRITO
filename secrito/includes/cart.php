<?php // includes/cart.php ?>

<!-- PANIER -->
<div class="cart" id="cart">
    <h2>Votre panier</h2>
    <div class="cart-items"></div>
    <div class="cart-total-box">
        <span>Total</span>
        <strong><span id="cartTotal">0</span> DT</strong>
    </div>
    <button id="clearCart" class="cart-btn secondary">Vider le panier</button>
    <button id="checkoutBtn" class="cart-btn primary">Passer la commande</button>
</div>

<!-- MODAL COMMANDE CONFIRMÉE -->
<div class="thank-modal" id="orderThankModal">
    <div class="thank-box">
        <p>Commande envoyée avec succès ! Merci ☕</p>
    </div>
</div>
