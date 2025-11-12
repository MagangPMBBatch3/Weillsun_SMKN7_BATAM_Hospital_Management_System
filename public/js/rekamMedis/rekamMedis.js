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
function searchRekamMedis() {
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
                allRekamMedisPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            tenaga_medis_id
                            tanggal
                            diagnosis
                            tindakan
                            pasien {
                                id
                                nama
                            }
                            tenagaMedis {
                                id
                                profile {
                                    nickname
                                }
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
        renderRekamMedisTable(
            dataActive?.data?.allRekamMedisPaginate || {},
            "dataRekamMedisAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allRekamMedisArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            tenaga_medis_id
                            tanggal
                            diagnosis
                            tindakan
                            pasien {
                                id
                                nama
                            }
                            tenagaMedis {
                                id
                                profile {
                                    nickname
                                }
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
        renderRekamMedisTable(
            dataArchive?.data?.allRekamMedisArchive || {},
            "dataRekamMedisArsip",
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
async function createRekamMedis() {
    const tenaga_medis_id = document.getElementById("create-nickname").value;
    const pasien_id = document.getElementById("create-nama").value;
    const tanggal = document.getElementById("create-tanggal").value;
    const diagnosis = document.getElementById("create-diagnosis").value.trim();
    const tindakan = document.getElementById("create-tindakan").value.trim();

    if (!pasien_id || !tanggal || !diagnosis || !tenaga_medis_id || !tindakan)
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationRekamMedis = `
        mutation($input: CreateRekamMedisInput!) {
            createRekamMedis(input: $input) {
                id
                pasien_id
                tenaga_medis_id
                tanggal
                diagnosis
                tindakan
                pasien {
                    id
                    nama
                }
                tenagaMedis {
                    id
                    profile {
                        nickname
                    }
                }
            }
        }
    `;
    const variablesRekamMedis = {
        input: { pasien_id, tenaga_medis_id, tanggal, diagnosis, tindakan},
    };

    try {
        const resRekamMedis = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationRekamMedis,
                variables: variablesRekamMedis,
            }),
        });

        const resultRekamMedis = await resRekamMedis.json();
        const dataRekamMedis = resultRekamMedis?.data?.createRekamMedis;

        if (dataRekamMedis) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-rekamMedis" })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultRekamMedis.errors);
            alert("Failed to create Tenaga Medis!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the user");
    } finally {
        hideLoading();
    }
}

function openEditModal(id, pasien_id, tenaga_medis_id, tanggal, diagnosis, tindakan) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pasien_id;
    document.getElementById("edit-nickname").value = tenaga_medis_id;
    document.getElementById("edit-tanggal").value = tanggal;
    document.getElementById("edit-diagnosis").value = diagnosis;
    document.getElementById("edit-tindakan").value = tindakan;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-rekamMedis" })
    );
}

// Update
async function updateRekamMedis() {
    const id = document.getElementById("edit-id").value;
    const pasien_id = document.getElementById("edit-nama").value;
    const tenaga_medis_id = document.getElementById("edit-nickname").value;
    const tanggal = document.getElementById("edit-tanggal").value;
    const diagnosis = document.getElementById("edit-diagnosis").value.trim();
    const tindakan = document.getElementById("edit-tindakan").value.trim();

    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateRekamMedisInput!) {
            updateRekamMedis(id: $id, input: $input) {
                id
                pasien_id
                tenaga_medis_id
                tanggal
                diagnosis
                tindakan
                pasien {
                    id
                    nama
                }
                tenagaMedis {
                    id
                    profile {
                        nickname
                    }
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
                variables: { id, 
                    input: { pasien_id, tenaga_medis_id, tanggal, diagnosis, tindakan}
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-rekamMedis" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

function renderRekamMedisTable(result, tableId, isActive) {
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
                <button onclick="openEditModal(${item.id}, '${item.pasien_id}','${item.tenaga_medis_id}', '${item.tanggal}', '${item.diagnosis}', '${item.tindakan}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusRekamMedis(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreRekamMedis(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteRekamMedis(${item.id})"
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
                item.tenagaMedis?.profile?.nickname
            }</td>
            <td class="p-4 text-center text-base font-semibold">${
                item.tanggal
            }</td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.diagnosis}
            </td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.tindakan}
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
async function hapusRekamMedis(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteRekamMedis(id: $id){ id } }`;
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
async function restoreRekamMedis(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreRekamMedis(id: $id){ id } }`;
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
async function forceDeleteRekamMedis(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteRekamMedis(id: $id){ id } }`;
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