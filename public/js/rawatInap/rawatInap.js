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
                            biaya_inap
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
                            biaya_inap
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

// Fungsi untuk menghitung lama inap
function calculateLamaInap(tanggalMasuk, tanggalKeluar) {
    if (!tanggalMasuk || !tanggalKeluar) return 0;
    const masuk = new Date(tanggalMasuk);
    const keluar = new Date(tanggalKeluar);
    const selisihMs = keluar - masuk;
    const selisihHari = Math.floor(selisihMs / (1000 * 60 * 60 * 24));
    return selisihHari + 1; // +1 untuk menghitung hari keluar
}

// Fungsi untuk menghitung biaya inap otomatis (Create)
async function calculateBiayaInapCreate() {
    const ruangan_id = document.getElementById("create-ruangan").value;
    const tanggal_masuk = document.getElementById("create-tanggal-masuk").value;
    const tanggal_keluar = document.getElementById(
        "create-tanggal-keluar"
    ).value;
    const biayaInput = document.getElementById("create-biaya-inap");

    console.log("calculateBiayaInapCreate called", {
        ruangan_id,
        tanggal_masuk,
        tanggal_keluar,
    });

    if (!ruangan_id || !tanggal_masuk || !tanggal_keluar) {
        biayaInput.value = "0";
        return;
    }

    try {
        // Query untuk mendapatkan tarif per hari dari ruangan
        const query = `
            query($id: ID!) {
                Ruangan(id: $id) {
                    id
                    tarif_per_hari
                }
            }
        `;
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: query,
                variables: { id: ruangan_id },
            }),
        });

        const result = await res.json();
        const ruangan = result?.data?.Ruangan;

        console.log("Ruangan result:", ruangan);

        if (ruangan) {
            const lamaInap = calculateLamaInap(tanggal_masuk, tanggal_keluar);
            const tarifPerHari = parseFloat(ruangan.tarif_per_hari) || 0;
            const biayaInap = lamaInap * tarifPerHari;

            console.log("Calculated:", { lamaInap, tarifPerHari, biayaInap });

            biayaInput.value = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(biayaInap);
        }
    } catch (error) {
        console.error("Error calculating biaya inap:", error);
        biayaInput.value = "0";
    }
}

// Fungsi untuk menghitung biaya inap otomatis (Edit)
async function calculateBiayaInapEdit() {
    const ruangan_id = document.getElementById("edit-ruangan").value;
    const tanggal_masuk = document.getElementById("edit-tanggal-masuk").value;
    const tanggal_keluar = document.getElementById("edit-tanggal-keluar").value;
    const biayaInput = document.getElementById("edit-biaya-inap");

    if (!ruangan_id || !tanggal_masuk || !tanggal_keluar) {
        biayaInput.value = "0";
        return;
    }

    try {
        // Query untuk mendapatkan tarif per hari dari ruangan
        const query = `
            query($id: ID!) {
                Ruangan(id: $id) {
                    id
                    tarif_per_hari
                }
            }
        `;
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: query,
                variables: { id: ruangan_id },
            }),
        });

        const result = await res.json();
        const ruangan = result?.data?.Ruangan;

        if (ruangan) {
            const lamaInap = calculateLamaInap(tanggal_masuk, tanggal_keluar);
            const tarifPerHari = parseFloat(ruangan.tarif_per_hari) || 0;
            const biayaInap = lamaInap * tarifPerHari;

            biayaInput.value = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(biayaInap);
        }
    } catch (error) {
        console.error("Error calculating biaya inap:", error);
        biayaInput.value = "0";
    }
}

// Create
async function createRawatInap() {
    const pasien_id = document.getElementById("create-nama").value;
    const ruangan_id = document.getElementById("create-ruangan").value;
    const tanggal_masuk = document.getElementById("create-tanggal-masuk").value;
    const tanggal_keluar = document
        .getElementById("create-tanggal-keluar")
        .value.trim();
    const status = document.getElementById("create-status").value.trim();

    if (
        !pasien_id ||
        !ruangan_id ||
        !tanggal_masuk ||
        !tanggal_keluar ||
        !status
    )
        return alert("Please fill in all required fields!");

    // Ambil biaya inap dari input yang sudah dihitung dan diformat
    const tarifStr = document.getElementById("create-biaya-inap").value;
    let biaya_inap = 0;

    // Parse currency format IDR - hapus semua karakter non-digit
    if (tarifStr) {
        const numStr = tarifStr.replace(/[^\d]/g, "");
        biaya_inap = parseFloat(numStr) || 0;
    }

    if (biaya_inap === 0) {
        return alert("Please select room and dates to calculate the fee!");
    }

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
                            biaya_inap
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
        input: {
            pasien_id,
            ruangan_id,
            tanggal_masuk,
            tanggal_keluar,
            status,
            biaya_inap,
        },
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

const statusSelect = document.getElementById('edit-status');
const roomSelect   = document.getElementById('edit-ruangan');

function toggleRoomSelect() {
    if (statusSelect.value === 'Pindah_Ruangan') {
        roomSelect.disabled = false;
        roomSelect.classList.remove('cursor-not-allowed');
    } else {
        roomSelect.disabled = true;
        roomSelect.classList.add('cursor-not-allowed');
    }
}

statusSelect.addEventListener('change', toggleRoomSelect);

function openEditModal(
    id,
    pasien_id,
    ruangan_id,
    tanggal_masuk,
    tanggal_keluar,
    status,
    biaya_inap = 0
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pasien_id;
    document.getElementById("edit-ruangan").value = ruangan_id;
    document.getElementById("edit-tanggal-masuk").value = tanggal_masuk;
    document.getElementById("edit-tanggal-keluar").value = tanggal_keluar;
    document.getElementById("edit-status").value = status;

    // Format biaya_inap untuk ditampilkan
    if (biaya_inap > 0) {
        document.getElementById("edit-biaya-inap").value =
            new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(biaya_inap);
    } else {
        document.getElementById("edit-biaya-inap").value = "0";
    }

    toggleRoomSelect();

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-rawatInap" })
    );

    // Trigger perhitungan biaya setelah modal terbuka
    setTimeout(() => {
        calculateBiayaInapEdit();
    }, 100);
}

// Update
async function updateRawatInap() {
    const id = document.getElementById("edit-id").value;
    const pasien_id = document.getElementById("edit-nama").value;
    const ruangan_id = document.getElementById("edit-ruangan").value;
    const tanggal_masuk = document.getElementById("edit-tanggal-masuk").value;
    const tanggal_keluar = document
        .getElementById("edit-tanggal-keluar")
        .value.trim();
    const status = document.getElementById("edit-status").value.trim();

    // Ambil biaya inap dari input yang sudah dihitung dan diformat
    const tarifStr = document.getElementById("edit-biaya-inap").value;
    let biaya_inap = 0;

    // Parse currency format IDR - hapus semua karakter non-digit
    if (tarifStr) {
        const numStr = tarifStr.replace(/[^\d]/g, "");
        biaya_inap = parseFloat(numStr) || 0;
    }

    if (biaya_inap === 0) {
        return alert("Please select room and dates to calculate the fee!");
    }

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
                            biaya_inap
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
                        status,
                        biaya_inap,
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
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="8">No data available.</td>
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
                <button onclick="openEditModal(${item.id}, '${item.pasien_id}','${item.ruangan_id}', '${item.tanggal_masuk}', '${item.tanggal_keluar}', '${item.status}', ${item.biaya_inap})"
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
            <td class="p-4 text-center capitalize">
                <span class="font-bold px-3 py-1 rounded-full text-green-600 bg-green-100 border border-green-300">
                    Rp ${item.biaya_inap.toLocaleString("id-ID")}
                </span>
            </td>
            <td class="p-4 text-center font-semibold capitalize">
                <span class="px-3 py-1 rounded-full font-semibold
                    ${
                        item.status === "Aktif"
                            ? "bg-green-100 text-green-700 border border-green-300"
                            : item.status === "Pulang"
                            ? "bg-red-100 text-red-700 border border-red-300"
                            : "bg-gray-200 text-gray-700 border border-gray-300"
                    }">
                    ${item.status.replace("_", " ")}

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

document.addEventListener("DOMContentLoaded", () => {
    loadDataPaginate(1, true);

    // Event listener untuk Create modal - perhitungan otomatis
    const createRuanganSelect = document.getElementById("create-ruangan");
    const createTanggalMasuk = document.getElementById("create-tanggal-masuk");
    const createTanggalKeluar = document.getElementById(
        "create-tanggal-keluar"
    );

    if (createRuanganSelect) {
        createRuanganSelect.addEventListener(
            "change",
            calculateBiayaInapCreate
        );
    }
    if (createTanggalMasuk) {
        createTanggalMasuk.addEventListener("change", calculateBiayaInapCreate);
    }
    if (createTanggalKeluar) {
        createTanggalKeluar.addEventListener(
            "change",
            calculateBiayaInapCreate
        );
    }

    // Event listener untuk Edit modal - perhitungan otomatis
    const editRuanganSelect = document.getElementById("edit-ruangan");
    const editTanggalMasuk = document.getElementById("edit-tanggal-masuk");
    const editTanggalKeluar = document.getElementById("edit-tanggal-keluar");

    if (editRuanganSelect) {
        editRuanganSelect.addEventListener("change", calculateBiayaInapEdit);
    }
    if (editTanggalMasuk) {
        editTanggalMasuk.addEventListener("change", calculateBiayaInapEdit);
    }
    if (editTanggalKeluar) {
        editTanggalKeluar.addEventListener("change", calculateBiayaInapEdit);
    }
});
