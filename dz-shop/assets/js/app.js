(() => {
    "use strict";
  
    // Utilitaire: créer un élément
    function el(tag, attrs = {}, children = []) {
      const node = document.createElement(tag);
      for (const [k, v] of Object.entries(attrs)) {
        if (k === "class") node.className = v;
        else if (k === "text") node.textContent = v;
        else if (k === "html") node.innerHTML = v;
        else if (k.startsWith("on") && typeof v === "function") node.addEventListener(k.slice(2), v);
        else node.setAttribute(k, v);
      }
      for (const child of children) node.appendChild(child);
      return node;
    }
  
    function moneyDA(value) {
      const n = Number(value);
      if (Number.isFinite(n)) return n.toFixed(2) + " DA";
      return (value ?? "") + " DA";
    }
  
    function clear(node) {
      while (node.firstChild) node.removeChild(node.firstChild);
    }
  
    async function fetchProducts() {
      // IMPORTANT: chemin API
      const url = "/dz-shop/api/products.php";
      const res = await fetch(url, { cache: "no-store" });
  
      if (!res.ok) {
        const txt = await res.text().catch(() => "");
        throw new Error(`API HTTP ${res.status} ${res.statusText}\n${txt.slice(0, 200)}`);
      }
  
      // Si jamais le serveur renvoie du HTML au lieu du JSON:
      const contentType = (res.headers.get("content-type") || "").toLowerCase();
      if (!contentType.includes("application/json")) {
        const txt = await res.text().catch(() => "");
        throw new Error(`API n'a pas renvoyé du JSON (content-type: ${contentType}). Début réponse:\n${txt.slice(0, 200)}`);
      }
  
      const data = await res.json();
      if (!Array.isArray(data)) {
        throw new Error("API JSON invalide: ce n'est pas un tableau.");
      }
      return data;
    }
  
    function renderProducts(products) {
      const container = document.getElementById("products");
      if (!container) return;
  
      clear(container);
  
      if (!products.length) {
        container.appendChild(el("div", { class: "col-12 text-muted", text: "Aucun produit trouvé." }));
        return;
      }
  
      for (const p of products) {
        const imgUrl = p.image_url && String(p.image_url).trim()
          ? p.image_url
          : "https://picsum.photos/seed/produit/600/400";
  
        const title = p.name ?? "Produit";
        const desc = p.description ?? "";
        const price = moneyDA(p.price);
  
        const img = el("img", {
          src: imgUrl,
          alt: title,
          class: "img-fluid rounded mb-2",
          style: "width:100%;height:180px;object-fit:cover;"
        });
  
        const card = el("div", { class: "card h-100 p-3" }, [
          img,
          el("h5", { class: "mb-1", text: title }),
          el("p", { class: "text-muted mb-2", text: desc }),
          el("div", { class: "d-flex justify-content-between align-items-center mt-auto" }, [
            el("strong", { text: price }),
            el("button", {
              class: "btn btn-dark btn-sm",
              text: "Ajouter au panier",
              onclick: () => addToCart(p.id)
            })
          ])
        ]);
  
        const col = el("div", { class: "col-12 col-sm-6 col-md-4" }, [card]);
        container.appendChild(col);
      }
    }
  
    function showToast(message) {
        let t = document.getElementById("toastCart");
        if (!t) {
          t = document.createElement("div");
          t.id = "toastCart";
          t.style.position = "fixed";
          t.style.right = "16px";
          t.style.top = "16px";
          t.style.zIndex = "9999";
          t.style.background = "#111";
          t.style.color = "#fff";
          t.style.padding = "12px 14px";
          t.style.borderRadius = "10px";
          t.style.boxShadow = "0 10px 30px rgba(0,0,0,.2)";
          t.style.opacity = "0";
          t.style.transition = "opacity .2s ease";
          document.body.appendChild(t);
        }
        t.textContent = message;
        t.style.opacity = "1";
        setTimeout(() => (t.style.opacity = "0"), 1400);
      }
      
      function addToCart(id) {
        window.location.href = "/dz-shop/panier.php?add=" + id;
      }
      
      
    function setupSearch(products) {
      const input = document.getElementById("searchInput");
      if (!input) return;
  
      input.addEventListener("input", () => {
        const q = input.value.toLowerCase().trim();
        const filtered = products.filter(p => {
          const name = String(p.name ?? "").toLowerCase();
          const desc = String(p.description ?? "").toLowerCase();
          return name.includes(q) || desc.includes(q);
        });
        renderProducts(filtered);
      });
    }
  
    async function init() {
      const container = document.getElementById("products");
      if (!container) return;
  
      // message chargement
      clear(container);
      container.appendChild(el("div", { class: "col-12 text-muted", text: "Chargement des produits…" }));
  
      try {
        const products = await fetchProducts();
        renderProducts(products);
        setupSearch(products);
      } catch (err) {
        console.error(err);
        clear(container);
        container.appendChild(
          el("div", { class: "col-12 text-danger", text: "Erreur lors du chargement des produits (voir console)." })
        );
      }
    }
  
    document.addEventListener("DOMContentLoaded", init);
  })();
  