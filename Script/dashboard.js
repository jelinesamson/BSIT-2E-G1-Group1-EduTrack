document.addEventListener("DOMContentLoaded", () => {
  console.log("Dashboard loaded!");
});

const totalProducts = 20; // example stock value
const lowStockThreshold = 10; // define low stock limit
const productValueEl = document.getElementById("totalProducts");

productValueEl.textContent = totalProducts;

if(totalProducts <= lowStockThreshold) {
  productValueEl.classList.add("low-stock");
} else {
  productValueEl.classList.remove("low-stock");
}