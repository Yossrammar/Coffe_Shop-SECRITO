document.addEventListener("DOMContentLoaded", () => {

    /* ================= SLIDER BANNIÈRE ================= */

    const slides     = document.querySelectorAll(".slide");
    const rightArrow = document.querySelector(".arrow.right");
    const leftArrow  = document.querySelector(".arrow.left");
    let slideIndex   = 0;

    if (slides.length && rightArrow && leftArrow) {

        const showSlide = (i) => {
            slides.forEach(s => s.classList.remove("active"));
            slides[i].classList.add("active");
        };

        rightArrow.addEventListener("click", () => {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide(slideIndex);
        });

        leftArrow.addEventListener("click", () => {
            slideIndex = (slideIndex - 1 + slides.length) % slides.length;
            showSlide(slideIndex);
        });

        setInterval(() => {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide(slideIndex);
        }, 5000);
    }


    /* ================= PREVIEW IMAGES À PROPOS ================= */

    const aboutImages = document.querySelectorAll(".about-images img");
    const preview     = document.getElementById("imagePreview");
    const previewImg  = document.getElementById("previewImg");

    if (aboutImages.length && preview && previewImg) {
        aboutImages.forEach(img => {
            img.addEventListener("click", () => {
                previewImg.src        = img.src;
                preview.style.display = "flex";
            });
        });
        preview.addEventListener("click", () => {
            preview.style.display = "none";
        });
    }


    /* ================= PANIER ================= */

    const cartBtn            = document.getElementById("cart-btn");
    const cart               = document.getElementById("cart");
    const cartItemsContainer = document.querySelector(".cart-items");
    const cartTotalEl        = document.getElementById("cartTotal");
    const clearCartBtn       = document.getElementById("clearCart");
    const checkoutBtn        = document.getElementById("checkoutBtn");

    let cartItems = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart && cartBtn) {
        cartBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            cart.classList.toggle("active");
        });
        cart.addEventListener("click", (e) => e.stopPropagation());
        document.addEventListener("click", () => cart.classList.remove("active"));
        window.addEventListener("scroll",  () => cart.classList.remove("active"));
    }

    // Ajouter au panier
    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", () => {
            const card  = btn.closest(".menu-card");
            const name  = btn.dataset.nom  || card.querySelector("h3").innerText;
            const img   = btn.dataset.img  || card.querySelector("img").src;
            const price = parseFloat(btn.dataset.prix) || parseFloat(card.querySelector(".price").innerText.replace(/[^0-9.]/g, ""));
            const id    = btn.dataset.id   || null;

            const existing = cartItems.find(i => i.name === name);
            if (existing) { existing.qty++; }
            else { cartItems.push({ id, name, img, prix: price, qty: 1 }); }

            saveAndUpdate();
            if (cart) cart.classList.add("active");
        });
    });


    /* ================= FILTRAGE MENU ================= */

    const filterButtons = document.querySelectorAll(".menu-filters button");
    const menuCards     = document.querySelectorAll(".menu-card");

    if (filterButtons.length && menuCards.length) {
        filterButtons.forEach(button => {
            button.addEventListener("click", () => {
                filterButtons.forEach(b => b.classList.remove("active"));
                button.classList.add("active");
                const category = button.getAttribute("data-category");
                if (category === "all") {
                    menuCards.forEach(card => card.classList.remove("hide"));
                    return;
                }
                menuCards.forEach(card => {
                    card.classList.toggle("hide", card.getAttribute("data-category") !== category);
                });
            });
        });
    }


    /* ================= MISE À JOUR PANIER ================= */

    function saveAndUpdate() {
        localStorage.setItem("cart", JSON.stringify(cartItems));
        updateCart();
    }

    function updateCart() {
        if (!cartItemsContainer || !cartTotalEl) return;
        cartItemsContainer.innerHTML = "";
        let total = 0;

        cartItems.forEach((item, index) => {
            total += item.prix * item.qty;
            const div = document.createElement("div");
            div.className = "cart-item";
            div.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <div class="cart-info">
                    <strong class="cart-item-name">${item.name}</strong>
                    <div class="qty-price-row">
                        <span class="item-price">${item.prix} DT</span>
                        <div class="qty-controls">
                            <i class="fas fa-minus qty-minus" data-index="${index}"></i>
                            <span class="qty-number">${item.qty}</span>
                            <i class="fas fa-plus qty-plus" data-index="${index}"></i>
                        </div>
                    </div>
                </div>
                <i class="fas fa-trash cart-remove" data-index="${index}"></i>
            `;
            cartItemsContainer.appendChild(div);
        });

        cartTotalEl.textContent = total.toFixed(2);
    }

    if (cartItemsContainer) {
        cartItemsContainer.addEventListener("click", (e) => {
            const index = e.target.dataset.index;
            if (index === undefined) return;
            if (e.target.classList.contains("qty-plus")) {
                cartItems[index].qty++;
                saveAndUpdate();
            } else if (e.target.classList.contains("qty-minus")) {
                if (cartItems[index].qty > 1) { cartItems[index].qty--; }
                else { cartItems.splice(index, 1); }
                saveAndUpdate();
            } else if (e.target.classList.contains("cart-remove")) {
                cartItems.splice(index, 1);
                saveAndUpdate();
            }
        });
    }

    updateCart();

    if (clearCartBtn) {
        clearCartBtn.addEventListener("click", () => {
            cartItems = [];
            saveAndUpdate();
        });
    }


    /* ================= MODAL CONFIRMATION COMMANDE ================= */

    // Injection HTML — même style que le panier
    if (!document.getElementById("confirmModal")) {
        document.body.insertAdjacentHTML("beforeend", `
        <div id="confirmModal" class="thank-modal">
            <div class="thank-box" style="max-width:380px;width:90%;cursor:default;">

                <h3 style="font-size:2.2rem;margin-bottom:1.8rem;color:#264755;">
                    Confirmer la commande ?
                </h3>

                <!-- Ligne sous-total -->
                <div class="cart-total-box" style="margin-bottom:1rem;">
                    <span>Sous-total</span>
                    <strong><span id="confirmSousTotal">0.00</span> DT</strong>
                </div>

                <!-- Ligne livraison -->
                <div class="cart-total-box" style="margin-bottom:1rem;">
                    <span>Livraison</span>
                    <strong>3.00 DT</strong>
                </div>

                <!-- Ligne total final -->
                <div class="cart-total-box" style="margin-bottom:2rem;">
                    <span>Total</span>
                    <strong><span id="confirmTotal">3.00</span> DT</strong>
                </div>

                <!-- Boutons -->
                <button id="confirmOui" class="cart-btn primary">
                     Confirmer la commande
                </button>
                <button id="confirmNon" class="cart-btn secondary" style="margin-top:1rem;">
                     Annuler
                </button>

            </div>
        </div>`);
    }

    const confirmModal    = document.getElementById("confirmModal");
    const confirmOui      = document.getElementById("confirmOui");
    const confirmNon      = document.getElementById("confirmNon");
    const confirmSousTotal = document.getElementById("confirmSousTotal");
    const confirmTotal    = document.getElementById("confirmTotal");

    // Fermer si clic sur le fond sombre
    confirmModal.addEventListener("click", (e) => {
        if (e.target === confirmModal) confirmModal.style.display = "none";
    });

    // Bouton Annuler
    confirmNon.addEventListener("click", () => {
        confirmModal.style.display = "none";
    });

    // Clic "Passer la commande" → affiche la modal
    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", () => {
            if (cartItems.length === 0) {
                alert("Votre panier est vide !");
                return;
            }

            const sousTotal = cartItems.reduce((sum, i) => sum + i.prix * i.qty, 0);
            confirmSousTotal.textContent = sousTotal.toFixed(2);
            confirmTotal.textContent     = (sousTotal + 3).toFixed(2);

            confirmModal.style.display = "flex";
        });
    }

    // Bouton Confirmer → envoie la commande
    confirmOui.addEventListener("click", () => {
        confirmModal.style.display = "none";
        confirmOui.disabled        = true;
        confirmOui.textContent     = "Envoi en cours...";

        fetch("checkout_handler.php", {
            method:  "POST",
            headers: { "Content-Type": "application/json" },
            body:    JSON.stringify({ items: cartItems, nom: "Client", telephone: "" })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                cartItems = [];
                saveAndUpdate();
                if (cart) cart.classList.remove("active");

                const modal = document.getElementById("orderThankModal");
                if (modal) {
                    modal.style.display = "flex";
                    setTimeout(() => { modal.style.display = "none"; }, 3000);
                }
            } else {
                alert(data.message || "Erreur lors de la commande.");
            }
        })
        .catch(() => alert("Erreur réseau. Veuillez réessayer."))
        .finally(() => {
            confirmOui.disabled    = false;
            confirmOui.textContent = " Confirmer la commande";
        });
    });


    /* ================= CONTACT : MODAL MERCI ================= */

    const thankModal = document.getElementById("thankModal");
    if (thankModal) {
        thankModal.addEventListener("click", () => {
            thankModal.style.display = "none";
        });
    }

});