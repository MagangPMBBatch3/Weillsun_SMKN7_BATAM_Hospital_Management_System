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

// ----------------------------------------------------------------------------------- \\
let searchTimeout = null;
function searchResepObat() {
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
        renderResepObatTable(
            dataActive?.data?.allResepObatPaginate || {},
            "dataResepObatAktif",
            true
        );

        // --- Query data Arsip ---
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

function getSelectedStok(selectEl) {
    if (!selectEl) return 0;
    const option = selectEl.options[selectEl.selectedIndex];
    return parseInt(option?.dataset?.stok || 0);
}

function updateObatOptions() {
    const selects = document.querySelectorAll(
        '#dynamic-container select[name="create-nama-obat[]"]'
    );

    // Ambil semua obat yang sedang dipilih
    const selectedValues = Array.from(selects)
        .map((s) => s.value)
        .filter((v) => v !== "");

    selects.forEach((select) => {
        const currentValue = select.value;

        Array.from(select.options).forEach((option) => {
            if (!option.value) return;

            // Disable jika dipilih di select lain
            if (
                selectedValues.includes(option.value) &&
                option.value !== currentValue
            ) {
                option.disabled = true;
                option.hidden = true; // optional (lebih rapi)
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


// Create
async function createResepObat() {
    const tenaga_medis_id = document.getElementById("create-nickname").value;
    const pasien_id = document.getElementById("create-nama").value;

    if (!pasien_id || !tenaga_medis_id) {
        return alert("Please select Patient and Personnel!");
    }

    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");

    const prescriptions = [];

    for (const row of rows) {
        const selectObat = row.querySelector(
            'select[name="create-nama-obat[]"]'
        );
        const inputJumlah = row.querySelector('input[name="create-jumlah[]"]');
        const aturan = row
            .querySelector('textarea[name="create-aturan-pakai[]"]')
            .value.trim();

        const obat_id = selectObat.value;
        const stok = getSelectedStok(selectObat);
        const jumlah = parseInt(inputJumlah.value.replace(/\./g, "") || 0);

        if (!obat_id || !jumlah || !aturan) continue;

        if (jumlah > stok) {
            alert(`Jumlah melebihi stok tersedia (${stok})`);
            inputJumlah.focus();
            return; // STOP submit
        }

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
    // const variablesResepObat = {
    //     input: {
    //         pasien_id,
    //         tenaga_medis_id,
    //         obat_id,
    //         jumlah:parseInt(jumlah),
    //         aturan_pakai},
    // };

    try {
        const results = await Promise.all(
            prescriptions.map((item) => {
                // Buat variablesResepObat untuk setiap item
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

        // Cek apakah ada error
        const errors = results.filter((r) => r.errors);
        if (errors.length > 0) {
            console.error("Some mutations failed:", errors);
            alert(
                `${prescriptions.length - errors.length} of ${
                    prescriptions.length
                } prescriptions created`
            );
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-resepObat" })
        );
        // resetCreateForm();
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating prescription");
    } finally {
        hideLoading();
    }
}

function openEditModal(
    id,
    pasien_id,
    tenaga_medis_id,
    obat_id,
    jumlah,
    aturan_pakai
) {
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

// Update
async function updateResepObat() {
    const id = document.getElementById("edit-id").value;

    const tenaga_medis_id = document.getElementById("edit-nickname").value;
    const pasien_id = document.getElementById("edit-nama").value;
    const obat_id = document.getElementById("edit-nama-obat").value;
    const jumlah = document
        .getElementById("edit-jumlah")
        .value.replace(/\./g, "");
    const aturan_pakai = document
        .getElementById("edit-aturan-pakai")
        .value.trim();
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

document.addEventListener("DOMContentLoaded", () => {
    const editJumlahInput = document.getElementById("edit-jumlah");

    // Event delegation untuk semua input jumlah di dynamic-container
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
;

    // Untuk edit modal (tetap pakai cara lama karena hanya 1 input)
    editJumlahInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });
});

function renderResepObatTable(result, tableId, isActive) {
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

    // Kelompokkan data berdasarkan pasien_id dan tenaga_medis_id
    const grouped = items.reduce((acc, item) => {
        const key = `${item.pasien_id}-${item.tenaga_medis_id}`;

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
        inline-flex items-center justify-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold
        transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
    `;

    // Render grouped data
    Object.values(grouped).forEach((group) => {
        // Render obat list dengan actions per baris obat
        const obatRows = group.obats
            .map((obat) => {
                let obatActions = "";

                if (window.currentUserRole === "admin" || window.currentUserRole === "doctor") {
                    if (isActive) {
                        obatActions = `
                        <div class="flex gap-1 flex-wrap">
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
                        <div class="flex gap-1 flex-wrap">
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
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-600 py-2 last:border-b-0">
                    <div class="flex-1">
                        <span class="font-semibold text-blue-600 dark:text-blue-400">${
                            obat.nama_obat
                        }</span>
                        <span class="text-sm ml-2">Jumlah: <strong>${obat.jumlah.toLocaleString(
                            "id-ID"
                        )}</strong></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">| ${
                            obat.aturan_pakai
                        }</span>
                    </div>
                    ${window.currentUserRole === "admin" || window.currentUserRole === "doctor" ? obatActions : ""}
                </div>
            `;
            })
            .join("");

        tbody.innerHTML += `
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800/50 dark:even:bg-gray-700/50 hover:bg-gray-300 dark:hover:bg-gray-600/50 align-top">
                <td class="p-4 text-center font-semibold align-middle">
                    <div class="flex flex-col gap-1 items-center">
                        ${group.ids
                            .map(
                                (id) =>
                                    `<span class="rounded-full font-bold text-green-500 py-1 px-2 ">${id}</span>`
                            )
                            .join("")}
                    </div>
                </td>
                <td class="p-4 text-center text-base border-x font-semibold align-middle">${
                    group.pasien?.nama
                }</td>
                <td class="p-4 text-center text-base border-x font-semibold align-middle">${
                    group.tenagaMedis?.profile?.nickname
                }</td>
                <td class="p-4">
                    ${obatRows}
                </td>
            </tr>
        `;
    });

    // Update pagination info
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
async function hapusResepObat(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

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

// restore
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

// force delete
async function forceDeleteResepObat(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

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

document.addEventListener("DOMContentLoaded", () => loadDataPaginate(1, true));
