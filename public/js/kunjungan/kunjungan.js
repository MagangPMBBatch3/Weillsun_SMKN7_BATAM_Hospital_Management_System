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

// ---------------------------------------- Kunjungan --------------------------------------- \\

let searchTimeout = null;
function searchKunjungan() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// Load data User (Aktif & Arsip sekaligus)
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
                allKunjunganPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            poli_id
                            tanggal_kunjungan
                            keluhan
                            biaya_konsultasi
                            pasien {
                                id
                                nama
                            } 
                            poli {
                                id
                                nama_poli
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
        renderKunjunganTable(
            dataActive?.data?.allKunjunganPaginate || {},
            "dataKunjunganAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allKunjunganArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pasien_id
                            poli_id
                            tanggal_kunjungan
                            keluhan
                            biaya_konsultasi
                            pasien {
                                id
                                nama
                            } 
                            poli {
                                id
                                nama_poli
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
        renderKunjunganTable(
            dataArchive?.data?.allKunjunganArchive || {},
            "dataKunjunganArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// Format dan unformat number

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
async function createKunjungan() {
    const pasien_id = document.getElementById("create-pasien-id").value;
    const poli_id = document.getElementById("create-poli-id").value;
    const tanggal_kunjungan = document.getElementById(
        "create-tanggal-kunjungan"
    ).value;
    const keluhan = document.getElementById("create-keluhan").value.trim();
    const biaya_konsultasi = document
        .getElementById("create-biaya-konsultasi")
        .value.replace(/\./g, "");

    if (
        !pasien_id ||
        !poli_id ||
        !keluhan ||
        !tanggal_kunjungan ||
        !biaya_konsultasi
    )
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationKunjungan = `
        mutation($input: CreateKunjunganInput!) {
            createKunjungan(input: $input) {
                id
                pasien_id
                poli_id
                tanggal_kunjungan
                keluhan
                biaya_konsultasi
                pasien {
                    id
                    nama
                } 
                poli {
                    id
                    nama_poli
                } 
            }
        }
    `;
    const variablesKunjungan = {
        input: {
            pasien_id,
            poli_id,
            tanggal_kunjungan,
            keluhan,
            biaya_konsultasi: parseFloat(biaya_konsultasi),
        },
    };

    try {
        const resKunjungan = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationKunjungan,
                variables: variablesKunjungan,
            }),
        });

        const resultKunjungan = await resKunjungan.json();
        const dataKunjungan = resultKunjungan?.data?.createKunjungan;

        if (dataKunjungan) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-kunjungan" })
            );

            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultKunjungan.errors);
            alert("Failed to create Tenaga Medis!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the user");
    } finally {
        hideLoading();
    }
}

function openKunjunganUlangModal(kunjunganId) {
    document.getElementById("create-kunjungan-id").value = kunjunganId;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "create-kunjunganUlang" })
    );
}

async function createKunjunganUlang() {
    const kunjungan_id = document.getElementById("create-kunjungan-id").value;
    const tanggal_ulang = document.getElementById("create-tanggal_ulang").value;
    const jam_ulang = document.getElementById("create-jam_ulang").value;
    const catatan = document.getElementById("create-catatan").value.trim();

    if (!kunjungan_id || !tanggal_ulang || !jam_ulang || !catatan)
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationKunjunganUlang = `
        mutation($input: CreateKunjunganUlangInput!) {
            createKunjunganUlang(input: $input) {
                id
                kunjungan_id
                tanggal_ulang
                jam_ulang
                catatan
            }
        }
    `;

    const variablesKunjunganUlang = {
        input: {
            kunjungan_id,
            tanggal_ulang,
            jam_ulang,
            catatan
        },
    };

    try {
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationKunjunganUlang,
                variables: variablesKunjunganUlang,
            }),
        });

        const result = await res.json();

        if (result?.data?.createKunjunganUlang) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-kunjunganUlang" })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error(result.errors);
            alert("Failed to create return visit!");
        }
    } catch (error) {
        console.error(error);
        alert("Error while creating return visit");
    } finally {
        hideLoading();
    }
}

function openEditModal(
    id,
    pasien_id,
    poli_id,
    tanggal_kunjungan,
    keluhan,
    biaya_konsultasi
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-pasien-id").value = pasien_id;
    document.getElementById("edit-poli-id").value = poli_id;
    document.getElementById("edit-tanggal-kunjungan").value = tanggal_kunjungan;
    document.getElementById("edit-keluhan").value = keluhan;
    document.getElementById("edit-biaya-konsultasi").value =
        formatNumber(biaya_konsultasi);

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-kunjungan" })
    );
}

// Update
async function updateKunjungan() {
    const id = document.getElementById("edit-id").value;
    const pasien_id = document.getElementById("edit-pasien-id").value;
    const poli_id = document.getElementById("edit-poli-id").value;
    const tanggal_kunjungan = document.getElementById(
        "edit-tanggal-kunjungan"
    ).value;
    const keluhan = document.getElementById("edit-keluhan").value.trim();
    const biaya_konsultasi = document
        .getElementById("edit-biaya-konsultasi")
        .value.replace(/\./g, "");

    if (
        !pasien_id ||
        !poli_id ||
        !keluhan ||
        !tanggal_kunjungan ||
        !biaya_konsultasi
    )
        return alert("Please fill in all required fields!");

    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateKunjunganInput!) {
            updateKunjungan(id: $id, input: $input) {
                id
                pasien_id
                poli_id
                tanggal_kunjungan
                keluhan
                biaya_konsultasi
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
                        poli_id,
                        tanggal_kunjungan,
                        keluhan,
                        biaya_konsultasi: parseFloat(biaya_konsultasi),
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-kunjungan" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const biayaKonsultasiInput = document.getElementById(
        "create-biaya-konsultasi"
    );

    const editBiayaKonsultasiInput = document.getElementById(
        "edit-biaya-konsultasi"
    );

    biayaKonsultasiInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });

    editBiayaKonsultasiInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function renderKunjunganTable(result, tableId, isActive) {
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

        if (window.currentUserRole === "admin" || window.currentUserRole === "receptionist") {
            if (isActive) {
                actions = `
                <button onclick="openEditModal(${item.id}, ${item.pasien_id}, ${item.poli_id}, '${item.tanggal_kunjungan}', '${item.keluhan}', '${item.biaya_konsultasi}')"
                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>

                <button onclick="openKunjunganUlangModal(${item.id})"
                    class="${baseBtn} bg-amber-100 text-amber-700 hover:bg-amber-200 focus:ring-amber-300">
                    <i class='bx bx-repeat'></i> Return Visit
                </button>

                <button onclick="hapusKunjungan(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreKunjungan(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteKunjungan(${item.id})"
                    class="${baseBtn} bg-red-100 text-red-700 hover:bg-red-200 focus:ring-red-300">
                    <i class='bx bx-trash'></i> Delete
                </button>`;
            }
        }

        tbody.innerHTML += `
        <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800/50 dark:even:bg-gray-700/50 hover:bg-gray-300 dark:hover:bg-gray-600/50">
            <td class="p-4 text-center font-semibold">
                <span class="rounded-full text-white bg-green-500 py-1 px-2">
                    ${item.id}
                </span>
            </td>

            <td class="p-4 text-center text-base font-semibold">
                ${item.pasien?.nama}
            </td>

            <td class="p-4 text-center text-base font-semibold">
                ${item.poli?.nama_poli}
            </td>

            <td class="p-4 text-center font-semibold capitalize">
                ${item.tanggal_kunjungan}
            </td>

            <td class="p-4 text-center font-semibold capitalize">
                ${item.keluhan}
            </td>

            <td class="p-4 text-center capitalize">
                <span class="font-bold text-green-600 bg-green-100 border border-green-300 px-3 py-1 rounded-full">
                    Rp ${item.biaya_konsultasi.toLocaleString("id-ID")}
                </span>
            </td>

            ${
                window.currentUserRole === "admin" || window.currentUserRole === "receptionist"
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
async function hapusKunjungan(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteKunjungan(id: $id){ id } }`;
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
async function restoreKunjungan(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreKunjungan(id: $id){ id } }`;
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
async function forceDeleteKunjungan(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteKunjungan(id: $id){ id } }`;
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
