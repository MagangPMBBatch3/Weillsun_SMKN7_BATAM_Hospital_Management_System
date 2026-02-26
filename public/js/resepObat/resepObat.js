const API_URL = "/graphql";
let currentPageActive = 1;
let currentPageArchive = 1;

// ============================================================================
// LOADING FUNCTIONS
// ============================================================================

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

// ============================================================================
// PAGINATION FUNCTIONS
// ============================================================================

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

// ============================================================================
// SEARCH FUNCTION
// ============================================================================

let searchTimeout = null;

function searchResepObat() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// ============================================================================
// NUMBER FORMATTING FUNCTIONS
// ============================================================================

function formatNumber(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatNumber(value) {
    return value.replace(/\./g, "");
}

function filterAngka(str) {
    return str.replace(/[^0-9.]/g, "");
}

// ============================================================================
// STOCK HELPER FUNCTION
// ============================================================================

function getSelectedStok(selectEl) {
    if (!selectEl) return 0;
    // Ambil pilihan yang lagi dipilih
    const option = selectEl.options[selectEl.selectedIndex];
    return parseInt(option?.dataset?.stok || 0);
}

// ============================================================================
// Biar obat yang sudah dipilih tidak bisa dipilih lagi di dropdown lain
// ============================================================================

function updateObatOptions() {
    const selects = document.querySelectorAll('#dynamic-container select[name="create-nama-obat[]"]');
    // Ubah daftar dropdown jadi array (biar bisa diproses)
    const selectedValues = Array.from(selects)
        // Ambil nilai obat dari tiap dropdown (termasuk yang kosong)
        .map((s) => s.value)
        // buang yang kosong (belum dipilih)
        .filter((v) => v !== "");

    selects.forEach((select) => {

        // Obat yang dipilih tetap boleh tampil
        // Tapi tidak boleh muncul di dropdown lain
        const currentValue = select.value;

        // Sekarang cek semua pilihan (option) di dropdown ini
        Array.from(select.options).forEach((option) => {
            if (!option.value) return;

            if (selectedValues.includes(option.value) && option.value !== currentValue) {
                option.disabled = true;
                option.hidden = true;
            } else {
                option.disabled = false;
                option.hidden = false;
            }
        });
    });
}

document.addEventListener("change", (e) => {
    if (e.target.name === "create-nama-obat[]") {
        updateObatOptions();
    }
});

// ============================================================================
// LOAD DATA FUNCTION
// ============================================================================

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
        // Query for Active Data
        const queryActive = `
            query($first: Int, $page: Int, $search: String) {
                allResepObatPaginate(first: $first, page: $page, search: $search){
                    data { 
                        id
                        pasien_id
                        tenaga_medis_id
                        obat_id
                        jumlah
                        aturan_pakai
                        pasien {
                            id
                            nama
                        }
                        obat {
                            id
                            nama_obat
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
            first: parseInt(isActive ? perPage : document.getElementById("perPage")?.value || 5),
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
        renderResepObatTable(
            dataActive?.data?.allResepObatPaginate || {},
            "dataResepObatAktif",
            true
        );

        // Query for Archive Data
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allResepObatArchive(first: $first, page: $page, search: $search){
                    data { 
                        id
                        pasien_id
                        tenaga_medis_id
                        obat_id
                        jumlah
                        aturan_pakai
                        pasien {
                            id
                            nama
                        }
                        obat {
                            id
                            nama_obat
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
            first: parseInt(!isActive ? perPage : document.getElementById("perPageArchive")?.value || 5),
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
        renderResepObatTable(
            dataArchive?.data?.allResepObatArchive || {},
            "dataResepObatArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// ============================================================================
// CREATE FUNCTION
// ============================================================================

async function createResepObat() {
    const tenaga_medis_id = document.getElementById("create-nickname").value;
    const pasien_id = document.getElementById("create-nama").value;

    if (!pasien_id || !tenaga_medis_id) {
        return alert("Please select Patient and Personnel!");
    }

    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");
    const prescriptions = [];

    for (const row of rows) {
        const selectObat = row.querySelector('select[name="create-nama-obat[]"]');
        const inputJumlah = row.querySelector('input[name="create-jumlah[]"]');
        const aturan = row.querySelector('textarea[name="create-aturan-pakai[]"]').value.trim();

        const obat_id = selectObat.value;
        const stok = getSelectedStok(selectObat);
        const jumlah = parseInt(inputJumlah.value.replace(/\./g, "") || 0);

        if (!obat_id || !jumlah || !aturan) continue;

        if (jumlah > stok) {
            alert(`Jumlah melebihi stok tersedia (${stok})`);
            inputJumlah.focus();
            return;
        }

        // Masukkan obat ini ke daftar resep
        prescriptions.push({
            obat_id,
            jumlah,
            aturan_pakai: aturan,
        });
    }

    if (prescriptions.length === 0) {
        return alert("Please fill at least one prescription!");
    }

    showLoading();

    const mutationResepObat = `
        mutation($input: CreateResepObatInput!) {
            createResepObat(input: $input) {
                id
                pasien_id
                tenaga_medis_id
                obat_id
                jumlah
                aturan_pakai
                pasien {
                    id
                    nama
                }
                obat {
                    id
                    nama_obat
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
        const results = await Promise.all(
            prescriptions.map((item) => {
                const variablesResepObat = {
                    input: {
                        pasien_id,
                        tenaga_medis_id,
                        obat_id: item.obat_id,
                        jumlah: item.jumlah,
                        aturan_pakai: item.aturan_pakai,
                    },
                };

                return fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        query: mutationResepObat,
                        variables: variablesResepObat,
                    }),
                }).then((res) => res.json());
            })
        );

        const errors = results.filter((r) => r.errors);
        if (errors.length > 0) {
            console.error("Some mutations failed:", errors);
            alert(
                `${prescriptions.length - errors.length} of ${prescriptions.length} prescriptions created`
            );
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-resepObat" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating prescription");
    } finally {
        hideLoading();
    }
}

// ============================================================================
// EDIT FUNCTIONS
// ============================================================================

function openEditModal(id, pasien_id, tenaga_medis_id, obat_id, jumlah, aturan_pakai) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pasien_id;
    document.getElementById("edit-nickname").value = tenaga_medis_id;
    document.getElementById("edit-nama-obat").value = obat_id;
    document.getElementById("edit-jumlah").value = formatNumber(jumlah);
    document.getElementById("edit-aturan-pakai").value = aturan_pakai;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-resepObat" })
    );
}

async function updateResepObat() {
    const id = document.getElementById("edit-id").value;
    const tenaga_medis_id = document.getElementById("edit-nickname").value;
    const pasien_id = document.getElementById("edit-nama").value;
    const obat_id = document.getElementById("edit-nama-obat").value;
    const jumlah = document.getElementById("edit-jumlah").value.replace(/\./g, "");
    const aturan_pakai = document.getElementById("edit-aturan-pakai").value.trim();

    showLoading();

    const selectObat = document.getElementById("edit-nama-obat");
    const stok = getSelectedStok(selectObat);

    if (parseInt(jumlah) > stok) {
        alert(`Jumlah melebihi stok tersedia (${stok})`);
        hideLoading();
        return;
    }

    const mutation = `
        mutation($id: ID!, $input: UpdateResepObatInput!) {
            updateResepObat(id: $id, input: $input) {
                id
                pasien_id
                tenaga_medis_id
                obat_id
                jumlah
                aturan_pakai
                pasien {
                    id
                    nama
                }
                obat {
                    id
                    nama_obat
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
                variables: {
                    id,
                    input: {
                        pasien_id,
                        tenaga_medis_id,
                        obat_id,
                        jumlah: parseInt(jumlah),
                        aturan_pakai,
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-resepObat" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

// ============================================================================
// INPUT FORMATTING EVENT LISTENERS
// ============================================================================

document.addEventListener("DOMContentLoaded", () => {
    const editJumlahInput = document.getElementById("edit-jumlah");
    const dynamicContainer = document.getElementById("dynamic-container");

    dynamicContainer.addEventListener("input", (e) => {
        if (e.target.name === "create-jumlah[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";

            const row = e.target.closest(".dynamic-row");
            const selectObat = row.querySelector('select[name="create-nama-obat[]"]');
            const stok = getSelectedStok(selectObat);

            if (parseInt(value || 0) > stok) {
                alert(`Stok maksimal: ${stok}`);
                e.target.value = formatNumber(stok);
            }
        }
    });

    // For edit modal input
    editJumlahInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });
});

// ============================================================================
// RENDER TABLE FUNCTION
// ============================================================================

function renderResepObatTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);
    // Kosongkan isi tabel dulu, biar tidak double render
    tbody.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="4">
                    No data available.
                </td>
            </tr>
        `;
        updatePaginationInfo(isActive, pageInfo, true);
        return;
    }

    // Group data by patient and medical staff
    const grouped = items.reduce((acc, item) => {
        const key = `${item.pasien_id}-${item.tenaga_medis_id}`;

        // kalo tak ada grupnya, buat baru..
        if (!acc[key]) {
            acc[key] = {
                ids: [],
                pasien: item.pasien,
                tenagaMedis: item.tenagaMedis,
                obats: [],
            };
        }

        acc[key].ids.push(item.id);
        acc[key].obats.push({
            id: item.id,
            obat_id: item.obat_id,
            nama_obat: item.obat?.nama_obat,
            jumlah: item.jumlah,
            aturan_pakai: item.aturan_pakai,
        });

        return acc;
    }, {});

    const baseBtn = `
        inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold
        transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 
        focus:ring-offset-1 active:scale-95
    `;

    // Render grouped data
    Object.values(grouped).forEach((group) => {
        const obatRows = group.obats.map((obat) => {
            let obatActions = "";

            if (window.currentUserRole === "admin" || window.currentUserRole === "doctor") {
                if (isActive) {
                    obatActions = `
                        <div class="flex gap-2 flex-wrap justify-end">
                            <button onclick="openEditModal(${obat.id}, '${group.pasien.id}', '${group.tenagaMedis.id}', '${obat.obat_id}', '${obat.jumlah}', '${obat.aturan_pakai}')"
                                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                                <i class='bx bx-edit-alt'></i> Edit
                            </button>
                            <button onclick="hapusResepObat(${obat.id})"
                                class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                                <i class='bx bx-archive'></i> Archive
                            </button>
                        </div>
                    `;
                } else {
                    obatActions = `
                        <div class="flex gap-2 flex-wrap justify-end">
                            <button onclick="restoreResepObat(${obat.id})"
                                class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                                <i class='bx bx-refresh'></i> Restore
                            </button>
                            <button onclick="forceDeleteResepObat(${obat.id})"
                                class="${baseBtn} bg-red-100 text-red-700 hover:bg-red-200 focus:ring-red-300">
                                <i class='bx bx-trash'></i> Delete
                            </button>
                        </div>
                    `;
                }
            }

            return `
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-600 
                            py-3 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded transition-colors">
                    <div class="flex-1">
                        <div class="font-semibold text-blue-600 dark:text-blue-400 mb-1">
                            ${obat.nama_obat}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Qty:</span> 
                            <span class="font-bold text-gray-800 dark:text-gray-200">${obat.jumlah.toLocaleString("id-ID")}</span>
                            <span class="mx-2">•</span>
                            <span class="italic">${obat.aturan_pakai}</span>
                        </div>
                    </div>
                    ${window.currentUserRole === "admin" || window.currentUserRole === "doctor" ? obatActions : ""}
                </div>
            `;
        }).join("");

        tbody.innerHTML += `
            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700/50 
                       hover:bg-blue-50 dark:hover:bg-gray-600/50 transition-colors">
                <td class="p-4 text-center font-semibold align-middle">
                    <div class="flex flex-col gap-1.5 items-center">
                        ${group.ids.map(id => 
                            `<span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 
                                         font-bold py-1 px-3 rounded-full text-sm">${id}</span>`
                        ).join("")}
                    </div>
                </td>
                <td class="p-4 text-center border-x border-gray-200 dark:border-gray-600 align-middle">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">${group.pasien?.nama}</span>
                </td>
                <td class="p-4 text-center border-x border-gray-200 dark:border-gray-600 align-middle">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">${group.tenagaMedis?.profile?.nickname}</span>
                </td>
                <td class="p-4">
                    <div class="space-y-0">
                        ${obatRows}
                    </div>
                </td>
            </tr>
        `;
    });

    updatePaginationInfo(isActive, pageInfo, false);
}

// ============================================================================
// UPDATE PAGINATION INFO
// ============================================================================

function updatePaginationInfo(isActive, pageInfo, isEmpty) {
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
        } (Total: ${isEmpty ? 0 : pageInfo.total || 0})`;
    }
    if (prevBtn) prevBtn.disabled = (pageInfo.currentPage || 1) <= 1;
    if (nextBtn) nextBtn.disabled = !pageInfo.hasMorePages;
}

// ============================================================================
// DELETE FUNCTIONS
// ============================================================================

async function hapusResepObat(id) {
    if (!confirm("Are you sure you want to add to the archive?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteResepObat(id: $id){ id } }`;
    
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

async function restoreResepObat(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreResepObat(id: $id){ id } }`;
    
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

async function forceDeleteResepObat(id) {
    if (!confirm("Are you sure you want to delete this data permanently?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteResepObat(id: $id){ id } }`;
    
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

// ============================================================================
// INITIALIZE ON PAGE LOAD
// ============================================================================

document.addEventListener("DOMContentLoaded", () => loadDataPaginate(1, true));