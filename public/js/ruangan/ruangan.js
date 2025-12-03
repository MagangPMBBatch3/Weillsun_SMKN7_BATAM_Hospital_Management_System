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

function prevPage() {
    if (currentPageActive > 1) loadDataPaginate(currentPageActive - 1, true);
}
function nextPage() {
    loadDataPaginate(currentPageActive + 1, true);
}

function prevPageArchive() {
    if (currentPageArchive > 1) loadDataPaginate(currentPageArchive - 1, false);
}
function nextPageArchive() {
    loadDataPaginate(currentPageArchive + 1, false);
}

//  -------------------------------- Ruangan --------------------------------------- \\

let searchTimeout = null;
function searchRuangan() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// Load data Ruangan (Aktif & Arsip sekaligus)
async function loadDataPaginate(page = 1, isActive = true) {
    showLoading();

    if (isActive) {
        currentPageActive = page;
    } else {
        currentPageArchive = page;
    }

    const perPage = isActive
        ? document.getElementById("perPage")?.value || 5
        : document.getElementById("perPageArchive")?.value || 5;
    const searchValue = document.getElementById("search")?.value.trim() || "";

    try {
        // --- Query data Aktif ---
        const queryActive = `
            query($first: Int, $page: Int, $search: String) {
                allRuanganPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id nama_ruangan kapasitas tarif_per_hari
                        }
                            paginatorInfo { 
                                currentPage 
                                lastPage 
                                total 
                                hasMorePages 
                        }
                }
            }
        `;
        const variablesActive = {
            first: parseInt(
                isActive
                    ? perPage
                    : document.getElementById("perPage")?.value || 5
            ),
            page: currentPageActive,
            search: searchValue,
        };
        const resActive = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: queryActive,
                variables: variablesActive,
            }),
        });
        const dataActive = await resActive.json();
        renderRuanganTable(
            dataActive?.data?.allRuanganPaginate || {},
            "dataRuanganAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allRuanganArchive(first: $first, page: $page, search: $search){
                    data { 
                            id nama_ruangan kapasitas tarif_per_hari
                        }
                    paginatorInfo { 
                            currentPage 
                            lastPage 
                            total 
                            hasMorePages 
                    }
                }
            }
        `;
        const variablesArchive = {
            first: parseInt(
                !isActive
                    ? perPage
                    : document.getElementById("perPageArchive")?.value || 5
            ),
            page: currentPageArchive,
            search: searchValue,
        };
        const resArchive = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: queryArchive,
                variables: variablesArchive,
            }),
        });
        const dataArchive = await resArchive.json();
        renderRuanganTable(
            dataArchive?.data?.allRuanganArchive || {},
            "dataRuanganArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// Format dan Unformat Number
function formatNumber(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatNumber(value) {
    return value.replace(/\./g, "");
}

function filterAngka(str) {
    // hapus semua karakter selain angka dan titik
    return str.replace(/[^0-9.]/g, "");
}

// Create
async function createRuangan() {
    const nama_ruangan = document
        .getElementById("create-nama_ruangan")
        .value.trim();
    const kapasitas = document
        .getElementById("create-kapasitas")
        .value.replace(/\./g, "");
    const tarif_per_hari = document
        .getElementById("create-tarif_per_hari")
        .value.replace(/\./g, "");

    if (!nama_ruangan || !kapasitas || !tarif_per_hari)
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationRuangan = `
        mutation($input: CreateRuanganInput!) {
            createRuangan(input: $input) {
                id
                nama_ruangan
                kapasitas
                tarif_per_hari
            }
        }
    `;
    const variablesRuangan = {
        input: {
            nama_ruangan,
            kapasitas: parseInt(kapasitas),
            tarif_per_hari: parseFloat(tarif_per_hari),
        },
    };

    try {
        const resRuangan = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationRuangan,
                variables: variablesRuangan,
            }),
        });

        const resultRuangan = await resRuangan.json();
        const dataRuangan = resultRuangan?.data?.createRuangan;

        if (dataRuangan) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-ruangan" })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultRuangan.errors);
            alert("Failed to create room!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the room");
    } finally {
        hideLoading();
    }
}

function openEditModal(id, nama_ruangan, kapasitas, tarif_per_hari) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama_ruangan").value = nama_ruangan;
    document.getElementById("edit-kapasitas").value = formatNumber(kapasitas);
    document.getElementById("edit-tarif_per_hari").value =
        formatNumber(tarif_per_hari);

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-ruangan" })
    );
}

// Update
async function updateRuangan() {
    const id = document.getElementById("edit-id").value;
    const nama_ruangan = document
        .getElementById("edit-nama_ruangan")
        .value.trim();
    const kapasitas = document
        .getElementById("edit-kapasitas")
        .value.replace(/\./g, "");
    const tarif_per_hari = document
        .getElementById("edit-tarif_per_hari")
        .value.replace(/\./g, "");

    if (!nama_ruangan || !kapasitas || !tarif_per_hari)
        return alert("Please fill in all required fields!");
    showLoading();

    const mutation = `mutation($id: ID!, $input: UpdateRuanganInput!) { updateRuangan(id: $id, input: $input) 
                        { 
                            id 
                            nama_ruangan 
                            kapasitas
                            tarif_per_hari
                        } 
                    }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: {
                    id,
                    input: {
                        nama_ruangan,
                        kapasitas: parseInt(kapasitas),
                        tarif_per_hari: parseFloat(tarif_per_hari),
                    },
                },
            }),
        });
        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-ruangan" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
        hideLoading();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const kapasitasInput = document.getElementById("create-kapasitas");
    const tarifPerHariInput = document.getElementById("create-tarif_per_hari");

    const editKapasitasInput = document.getElementById("edit-kapasitas");
    const editTarifPerHariInput = document.getElementById(
        "edit-tarif_per_hari"
    );

    kapasitasInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });

    tarifPerHariInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });

    // EDIT

    editKapasitasInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });

    editTarifPerHariInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function renderRuanganTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);

    tbody.innerHTML = "";
    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="5">No data available.</td>
            </tr>
        `;
        const pageInfoEl = isActive
            ? document.getElementById("pageInfo")
            : document.getElementById("pageInfoArchive");
        const prevBtn = isActive
            ? document.getElementById("prevBtn")
            : document.getElementById("prevBtnArchive");
        const nextBtn = isActive
            ? document.getElementById("nextBtn")
            : document.getElementById("nextBtnArchive");

        if (pageInfoEl) {
            pageInfoEl.innerText = `Halaman ${pageInfo.currentPage || 1} dari ${
                pageInfo.lastPage || 1
            } (Total: 0)`;
        }
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;

        return;
    }

    items.forEach((item) => {
        let actions = "";
        const baseBtn = `
        inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-md font-semibold
        transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
    `;

        if (window.currentUserRole === "admin") {
            if (isActive) {
                actions = `
                <button onclick="openEditModal(${item.id}, '${item.nama_ruangan}', '${item.kapasitas}', '${item.tarif_per_hari}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusRuangan(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreRuangan(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteRuangan(${item.id})"
                    class="${baseBtn} bg-red-100 text-red-700 hover:bg-red-200 focus:ring-red-300">
                    <i class='bx bx-trash'></i> Delete
                </button>`;
            }
        }

        tbody.innerHTML += `
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800/50 dark:even:bg-gray-700/50 hover:bg-gray-300 dark:hover:bg-gray-600/50">
            <td class="p-4 text-center font-semibold">
                <span class="rounded-full text-white bg-green-500 py-1 px-2">${
                    item.id
                }</span>
            </td>
            <td class="p-4 text-center text-base font-semibold">${
                item.nama_ruangan
            }</td>
            <td class="p-4 text-center truncate max-w-24 text-base font-semibold">
                ${item.kapasitas.toLocaleString("id-ID")}
            </td>
            <td class="p-4 text-center truncate max-w-24 text-base font-semibold">
                <span class="font-bold text-green-600 bg-green-100 border border-green-300 px-3 py-1 rounded-full">
                    Rp${item.tarif_per_hari.toLocaleString("id-ID")}
                </span>
            </td>

            ${
                window.currentUserRole === "admin"
                    ? `<td class="flex p-4 justify-center items-center space-x-1">${actions}</td>`
                    : ""
            }
        </tr>
    `;
    });

    // Update tombol prev/next & info halaman
    const pageInfoEl = isActive
        ? document.getElementById("pageInfo")
        : document.getElementById("pageInfoArchive");
    const prevBtn = isActive
        ? document.getElementById("prevBtn")
        : document.getElementById("prevBtnArchive");
    const nextBtn = isActive
        ? document.getElementById("nextBtn")
        : document.getElementById("nextBtnArchive");

    if (pageInfoEl)
        pageInfoEl.innerText = `Halaman ${pageInfo.currentPage || 1} dari ${
            pageInfo.lastPage || 1
        } (Total: ${pageInfo.total || 0})`;
    if (prevBtn) prevBtn.disabled = (pageInfo.currentPage || 1) <= 1;
    if (nextBtn) nextBtn.disabled = !pageInfo.hasMorePages;
}

// Hapus
async function hapusRuangan(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteRuangan(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to delete data");
        hideLoading();
    }
}

// restore
async function restoreRuangan(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreRuangan(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed restore data");
        hideLoading();
    }
}

// force delete
async function forceDeleteRuangan(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteRuangan(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to delete permanent data");
        hideLoading();
    }
}

document.addEventListener("DOMContentLoaded", () => loadDataPaginate(1, true));
