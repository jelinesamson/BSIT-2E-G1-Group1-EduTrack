document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch("../Api/dashboard.php");
        const data = await res.json();
        console.log("Dashboard Data:", data);

        // Total Sales
        document.getElementById("totalSales").textContent =
            "₱" + (data.total_sales ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });

        // Total Solds
        document.getElementById("totalSolds").textContent =
            (data.total_solds ?? 0);

        // Transactions Today
        document.getElementById("totalTransactions").textContent =
            (data.transactions ?? 0);

        // Total Products
        const totalProductsEl = document.getElementById("totalProducts");
        totalProductsEl.textContent = (data.total_products ?? 0);

        // Highlight low stock
        if ((data.total_products ?? 0) <= (data.low_stock_threshold ?? 10)) {
            totalProductsEl.classList.add("low-stock");
        } else {
            totalProductsEl.classList.remove("low-stock");
        }

    } catch (e) {
        console.error("Failed to load dashboard metrics:", e);
    }
});