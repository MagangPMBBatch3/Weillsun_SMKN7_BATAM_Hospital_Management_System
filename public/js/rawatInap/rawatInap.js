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

let searchTimeout = null;
function searchRawatInap() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// Load data (Aktif & Arsip sekaligus)
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
                allRawatInapPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            ruangan_id
                            tanggal_masuk
                            tanggal_keluar
                            status
                            pasien {
                                id
                                nama
                            }
                            ruangan {
                                id
                                nama_ruangan
                            }
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
        renderRawatInapTable(
            dataActive?.data?.allRawatInapPaginate || {},
            "dataRawatInapAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allRawatInapArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            ruangan_id
                            tanggal_masuk
                            tanggal_keluar
                            status
                            pasien {
                                id
                                nama
                            }
                            ruangan {
                                id
                                nama_ruangan
                            }
                        }
                    paginatorInfo { currentPage lastPage total hasMorePages }
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
        renderRawatInapTable(
            dataArchive?.data?.allRawatInapArchive || {},
            "dataRawatInapArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// Create
async function createRawatInap() {
    const pasien_id = document.getElementById("create-nama").value;
    const ruangan_id = document.getElementById("create-ruangan").value;
    const tanggal_masuk = document.getElementById("create-tanggal-masuk").value;
    const tanggal_keluar = document.getElementById("create-tanggal-keluar").value.trim();
    const status = document.getElementById("create-status").value.trim();

    if (
        !pasien_id ||
        !ruangan_id ||
        !tanggal_masuk ||
        !tanggal_keluar ||
        !status
    )
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationRawatInap = `
        mutation($input: CreateRawatInapInput!) {
            createRawatInap(input: $input) {
                            id
                            pasien_id
                            ruangan_id
                            tanggal_masuk
                            tanggal_keluar
                            status
                            pasien {
                                id
                                nama
                            }
                            ruangan {
                                id
                                nama_ruangan
                            }
            }
        }
    `;
    const variablesRawatInap = {
        input: { pasien_id, ruangan_id, tanggal_masuk, tanggal_keluar, status },
    };

    try {
        const resRawatInap = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationRawatInap,
                variables: variablesRawatInap,
            }),
        });

        const resultRawatInap = await resRawatInap.json();
        const dataRawatInap = resultRawatInap?.data?.createRawatInap;

        if (dataRawatInap) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-rawatInap" })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultRawatInap.errors);
            alert("Failed to create Tenaga Medis!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the user");
    } finally {
        hideLoading();
    }
}

function openEditModal(id, pasien_id, ruangan_id, tanggal_masuk, tanggal_keluar, status) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pasien_id;
    document.getElementById("edit-ruangan").value = ruangan_id;
    document.getElementById("edit-tanggal-masuk").value = tanggal_masuk;
    document.getElementById("edit-tanggal-keluar").value = tanggal_keluar;
    document.getElementById("edit-status").value = status;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-rawatInap" })
    );
}

// Update
async function updateRawatInap() {
    const id = document.getElementById("edit-id").value;
    const pasien_id = document.getElementById("edit-nama").value;
    const ruangan_id = document.getElementById("edit-ruangan").value;
    const tanggal_masuk = document.getElementById("edit-tanggal-masuk").value;
    const tanggal_keluar = document.getElementById("edit-tanggal-keluar").value.trim();
    const status = document.getElementById("edit-status").value.trim();

    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateRawatInapInput!) {
            updateRawatInap(id: $id, input: $input) {
                            id
                            pasien_id
                            ruangan_id
                            tanggal_masuk
                            tanggal_keluar
                            status
                            pasien {
                                id
                                nama
                            }
                            ruangan {
                                id
                                nama_ruangan
                            }
            }
        }
    `;

    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: {
                    id,
                    input: {
                        pasien_id,
                        ruangan_id,
                        tanggal_masuk,
                        tanggal_keluar,
                        status
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-rawatInap" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

function renderRawatInapTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);

    tbody.innerHTML = "";
    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="7">No related data found</td>
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
                <button onclick="openEditModal(${item.id}, '${item.pasien_id}','${item.ruangan_id}', '${item.tanggal_masuk}', '${item.tanggal_keluar}', '${item.status}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusRawatInap(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreRawatInap(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteRawatInap(${item.id})"
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
                item.pasien?.nama
            }</td>
            <td class="p-4 text-center text-base font-semibold">${
                item.ruangan?.nama_ruangan
            }</td>
            <td class="p-4 text-center text-base font-semibold">${
                item.tanggal_masuk
            }</td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.tanggal_keluar}
            </td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.status}
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
async function hapusRawatInap(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteRawatInap(id: $id){ id } }`;
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
async function restoreRawatInap(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreRawatInap(id: $id){ id } }`;
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
async function forceDeleteRawatInap(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteRawatInap(id: $id){ id } }`;
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
