document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById("open-payment-modal");
    const modal = document.getElementById("payment-modal");
    const closeBtn = document.getElementById("close-payment");
    const payBtn = document.getElementById("pay-btn");

    if (!openBtn || !modal || !closeBtn || !payBtn) {
        console.error("Payment elements missing");
        return;
    }

    const nameInput = modal.querySelector('input[placeholder="MOATEZ BEN SALEM"]');
    const cardInput = modal.querySelector('input[placeholder="4242 4242 4242 4242"]');
    const expInput = modal.querySelector('input[placeholder="MM/AA"]');
    const cvcInput = modal.querySelector('input[placeholder="123"]');

    // Ouvrir / fermer modal
    openBtn.onclick = () => modal.style.display = "flex";
    closeBtn.onclick = () => modal.style.display = "none";

    // Validation simple
    function isValidName(name){ return /^[A-Za-z ]{3,}$/.test(name); }
    function isVisa(card){ return /^4[0-9]{15}$/.test(card); }
    function isMaster(card){ return /^5[1-5][0-9]{14}$/.test(card); }
    function isValidExpiry(exp){
        if(!/^\d{2}\/\d{2}$/.test(exp)) return false;
        const [mm, yy] = exp.split("/").map(Number);
        const now = new Date(), year = now.getFullYear() % 100, month = now.getMonth()+1;
        return (yy > year) || (yy === year && mm >= month);
    }
    function isValidCVC(cvc){ return /^\d{3}$/.test(cvc); }

    payBtn.onclick = () => {
        const name = nameInput.value.trim();
        const card = cardInput.value.replace(/\s/g,"");
        const exp = expInput.value.trim();
        const cvc = cvcInput.value.trim();

        if(!isValidName(name)){ alert("Nom invalide"); return; }
        if(!isVisa(card) && !isMaster(card)){ alert("Carte invalide utilise carte Visa 4 Ou Mastercard 5"); return; }
        if(!isValidExpiry(exp)){ alert("Date expirée"); return; }
        if(!isValidCVC(cvc)){ alert("CVC invalide"); return; }

        payBtn.innerText = "Paiement en cours...";
        payBtn.disabled = true;

        const csrfToken = document.getElementById("csrf_token").value;

        // Appel backend pour créer la commande
        fetch("/cart/checkout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success){
                alert("Erreur lors de la commande");
                payBtn.disabled = false;
                payBtn.innerText = "Payer";
                return;
            }

            modal.innerHTML = `
                <div class="payment-box">
                    <h2>✅ Paiement réussi</h2>
                    <p>Carte : ${card.startsWith("4") ? "VISA" : "MASTERCARD"}<br>
                    Merci pour votre commande !</p>
                </div>
            `;

            console.log("Commande ID:", data.orderId);

            // Vider panier backend
            fetch("/cart/clear");

            // Redirection après 2 secondes
            setTimeout(() => window.location.href="/shop/merch", 2000);
        })
        .catch(err => {
            console.error(err);
            alert("Erreur serveur");
            payBtn.disabled = false;
            payBtn.innerText = "Payer";
        });
    };
});
