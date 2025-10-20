const API_URL = "/graphql";
let currentPageActive = 1;
let currentPageArchive = 1;

function showLoading() {
    document.body.style.overflow = "hidden";
    const overlay = document.getElementById("loadingOverlay");
    if (overlay) overlay.classList.remove("hidden");
}

function hideLoading() {
    document.body.style.overflow = "";
    const overlay = document.getElementById("loadingOverlay");
    if (overlay) overlay.classList.add("hidden");
}

// create
async function createTenagaMedis() {
    const spesialisasi = document
        .getElementById("create-spesialisasi")
        .value.trim();
    const profile_id = document.getElementById("create-profile-id").value;
    const no_str = document.getElementById("create-noStr").value.trim();

    if (!spesialisasi || !no_str) {
        return alert("Please fill in all required fields.");
    }

    showLoading();

    const mutation = `
        mutation($input: CreateTenagaMedisInput!) {
            createTenagaMedis(input: $input) {
                id
                profile_id
                spesialisasi
                no_str
            }
    }`;

    const variables = {
        input: {
            profile_id,
            spesialisasi,
            no_str,
        },
    };

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: variables,
            }),
        });

        const result = await response.json();

        if (result.errors) {
            throw new Error(result.errors[0].message);
        }
        const newTenagaMedis = result?.data?.createTenagaMedis;

        if (!newTenagaMedis) {
            alert("Failed to create data");
            hideLoading();
            return;
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-tenaga-medis" })
        );

        window.location.reload();
        hideLoading();

    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the data");
        hideLoading();
    }
}
