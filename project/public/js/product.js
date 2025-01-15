document.addEventListener("DOMContentLoaded", () => {
    const urlField = document.querySelector("#product_url");
    const nameField = document.querySelector("#product_name");
    const priceField = document.querySelector("#product_price");
    const imageField = document.querySelector("#product_image");
    const descriptionField = document.querySelector("#product_description");
    const loadingIndicator = document.querySelector("#loading-indicator");

    if (urlField) {
        urlField.addEventListener("blur", async () => {
            const url = urlField.value;

            if (url) {
                try {
                    loadingIndicator.style.display = "block";

                    const response = await fetch("/selenium-scrape", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ url }),
                    });

                    if (!response.ok) {
                        throw new Error("Failed to fetch data");
                    }

                    const result = await response.json();
                    loadingIndicator.style.display = "none";
                    if (result.status === "success") {
                        const data = result.data;
                        nameField.value = data.name || "";
                        priceField.value = data.price || "";
                        imageField.value = data.image || "";
                        descriptionField.value = data.description || "";
                    } else {
                        console.error("Error fetching data:", result.message);
                    }
                } catch (error) {
                    console.error("An error occurred:", error);
                } finally {
                    loadingIndicator.style.display = "none";
                }
            }
        });
    }
});
