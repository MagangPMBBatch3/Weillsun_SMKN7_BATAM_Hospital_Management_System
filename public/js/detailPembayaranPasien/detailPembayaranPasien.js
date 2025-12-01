const API_URL = "/graphql";
let currentPageActive = 1;
let currentPageArchive = 1;
let unpaidCosts = []; // Store unpaid costs for the selected pasien

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
function searchDetailPembayaranPasien() {
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
                allDetailPembayaranPasienPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembayaran_id
                            tipe_biaya
                            referensi_id
                            jumlah
                            harga_satuan
                            subtotal
                            pembayaranPasien{
                                id
                                pasien {    
                                    id
                                    nama
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
        renderDetailPembayaranPasienTable(
            dataActive?.data?.allDetailPembayaranPasienPaginate || {},
            "dataDetailPembayaranPasienAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allDetailPembayaranPasienArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembayaran_id
                            tipe_biaya
                            referensi_id
                            jumlah
                            harga_satuan
                            subtotal
                            pembayaranPasien{
                                id
                                pasien {    
                                    id
                                    nama
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
        renderDetailPembayaranPasienTable(
            dataArchive?.data?.allDetailPembayaranPasienArchive || {},
            "dataDetailPembayaranPasienArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// Fetch unpaid costs for the selected pasien
async function loadUnpaidCosts(pasienId) {
    showLoading();

    const query = `
        query($pasien_id: ID!) {
            getUnpaidCostsByPasien(pasien_id: $pasien_id) {
                type
                referensi_id
                jumlah
                harga_satuan
                subtotal
                label
            }
        }
    `;

    try {
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: query,
                variables: { pasien_id: pasienId },
            }),
        });

        const data = await res.json();
        unpaidCosts = data?.data?.getUnpaidCostsByPasien || [];

        // Clear existing rows and populate with unpaid costs
        populateUnpaidCostsToForm(unpaidCosts);
    } catch (error) {
        console.error("Error loading unpaid costs:", error);
        alert("An error occurred while loading unpaid costs");
    } finally {
        hideLoading();
    }
}

// Populate unpaid costs to the form
function populateUnpaidCostsToForm(costs) {
    const container = document.getElementById("dynamic-container");

    // Clear existing rows except the first one
    const rows = container.querySelectorAll(".dynamic-row");
    rows.forEach((row, index) => {
        if (index > 0) {
            row.remove();
        }
    });

    // Reset first row
    const firstRow = container.querySelector(".dynamic-row");
    if (firstRow) {
        firstRow.querySelector('select[name="create-tipe-biaya[]"]').value = "";
        firstRow.querySelector('input[name="create-jumlah[]"]').value = "";
        firstRow.querySelector('input[name="create-harga-satuan[]"]').value =
            "";
    }

    // Reset subtotal when no costs
    if (costs.length === 0) {
        const subtotalInput = document.querySelector(
            'input[name="create-subtotal"]'
        );
        if (subtotalInput) {
            subtotalInput.value = "0";
        }
        return;
    }

    // Fill first row with first cost
    if (firstRow && costs.length > 0) {
        const cost = costs[0];
        firstRow.querySelector('select[name="create-tipe-biaya[]"]').value =
            cost.type;
        firstRow.querySelector('input[name="create-jumlah[]"]').value =
            formatNumber(cost.jumlah.toString());
        firstRow.querySelector('input[name="create-harga-satuan[]"]').value =
            formatNumber(cost.harga_satuan.toString());
        firstRow.dataset.tipeBiaya = cost.type;
        firstRow.dataset.referensiId = cost.referensi_id;
        firstRow.dataset.subtotal = Number(cost.subtotal);
    }

    // Add additional rows for remaining costs
    for (let i = 1; i < costs.length; i++) {
        const cost = costs[i];
        const newRow = document.createElement("div");
        newRow.className =
            "dynamic-row bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 " +
            "p-4 rounded-xl shadow-sm space-y-3 transition-all";

        newRow.innerHTML = `
            <div>
                <label class="text-sm font-medium">Cost Type</label>
                <select name="create-tipe-biaya[]"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                        focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    <option value="">Select Cost Type</option>
                    <option value="konsultasi">Konsultasi</option>
                    <option value="obat">Obat</option>
                    <option value="lab">Lab</option>
                    <option value="radiologi">Radiologi</option>
                    <option value="rawat_inap">Rawat Inap</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Amount</label>
                <input type="text" name="create-jumlah[]"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                        focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    placeholder="Enter Amount">
            </div>

            <div>
                <label class="text-sm font-medium">Unit Price</label>
                <input type="text" name="create-harga-satuan[]"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                        focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    placeholder="Enter Unit Price">
            </div>
        `;

        // Populate values
        newRow.querySelector('select[name="create-tipe-biaya[]"]').value =
            cost.type;
        newRow.querySelector('input[name="create-jumlah[]"]').value =
            formatNumber(cost.jumlah.toString());
        newRow.querySelector('input[name="create-harga-satuan[]"]').value =
            formatNumber(cost.harga_satuan.toString());
        newRow.dataset.tipeBiaya = cost.type;
        newRow.dataset.referensiId = cost.referensi_id;
        newRow.dataset.subtotal = cost.subtotal;

        container.appendChild(newRow);
    }

    // Update subtotal calculation
    updateSubtotal();
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
async function createDetailPembayaranPasien() {
    const pembayaran_id = document.getElementById("create-nama")?.value;
    const subtotal = document.getElementById("create-subtotal")?.value;

    if (!pembayaran_id) {
        return alert("Please select Patient!");
    }

    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");

    if (rows.length === 0) {
        return alert("No rows found! Please add at least one item.");
    }

    // Map rows ke array of prescription objects, lalu filter yang valid
    const detailPasien = Array.from(rows)
        .map((row) => {
            try {
                const tipoSelect = row.querySelector(
                    'select[name="create-tipe-biaya[]"]'
                );
                const jumlahInput = row.querySelector(
                    'input[name="create-jumlah[]"]'
                );
                const hargaInput = row.querySelector(
                    'input[name="create-harga-satuan[]"]'
                );

                if (!tipoSelect || !jumlahInput || !hargaInput) {
                    console.warn("Missing input elements in row", row);
                    return null;
                }

                const tipe_biaya = tipoSelect.value || "";
                const jumlahValue = jumlahInput.value || "0";
                const hargaValue = hargaInput.value || "0";

                const jumlah = parseInt(jumlahValue.replace(/\./g, "")) || 0;
                const harga_satuan =
                    parseFloat(hargaValue.replace(/\./g, "")) || 0;

                if (!tipe_biaya || jumlah === 0 || harga_satuan === 0) {
                    return null;
                }

                return {
                    tipe_biaya: tipe_biaya,
                    jumlah: jumlah,
                    harga_satuan: harga_satuan,
                    referensi_id: row.dataset.referensiId || null,
                    subtotal: Number(row.dataset.subtotal) || jumlah * harga_satuan,
                };
            } catch (error) {
                console.error("Error processing row:", error);
                return null;
            }
        })
        .filter((item) => item !== null);

    if (detailPasien.length === 0) {
        return alert("Please fill at least one item with valid data!");
    }

    showLoading();

    const mutationDetailPembayaranPasien = `
        mutation($input: CreateDetailPembayaranPasienInput!) {
            createDetailPembayaranPasien(input: $input) {
                id
                pembayaran_id
                tipe_biaya
                referensi_id
                jumlah
                harga_satuan
                subtotal
                pembayaranPasien{
                    id
                    pasien {    
                        id
                        nama
                    }
                }
            }
        }
    `;

    const markAsPaidMutation = `
        mutation($detail_id: ID!, $tipe_biaya: Tipe!, $referensi_id: ID!) {
            markDetailPembayaranPasienAsPaid(detail_id: $detail_id, tipe_biaya: $tipe_biaya, referensi_id: $referensi_id)
        }
    `;

    try {
        const results = await Promise.all(
            detailPasien.map((item) => {
                // Buat variablesDetailPembayaranPasien untuk setiap item
                const variablesDetailPembayaranPasien = {
                    input: {
                        pembayaran_id,
                        tipe_biaya: item.tipe_biaya,
                        referensi_id: item.referensi_id,
                        jumlah: item.jumlah,
                        harga_satuan: item.harga_satuan,
                        subtotal: item.subtotal,
                    },
                };

                return fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        query: mutationDetailPembayaranPasien,
                        variables: variablesDetailPembayaranPasien,
                    }),
                }).then((res) => res.json());
            })
        );

        // Mark each cost as paid in their respective tables
        const markPaidResults = await Promise.all(
            detailPasien.map((item, index) => {
                if (results[index]?.data?.createDetailPembayaranPasien?.id) {
                    const detailId =
                        results[index].data.createDetailPembayaranPasien.id;

                    return fetch(API_URL, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            query: markAsPaidMutation,
                            variables: {
                                detail_id: detailId,
                                tipe_biaya: item.tipe_biaya,
                                referensi_id: item.referensi_id || null,
                            },
                        }),
                    }).then((res) => res.json());
                }
                return null;
            })
        );

        // Cek apakah ada error
        const errors = results.filter((r) => r.errors && r.errors.length > 0);
        if (errors.length > 0) {
            console.error("Some mutations failed:", errors);
            let errorMessages = [];
            errors.forEach((err, idx) => {
                console.error(`Error ${idx}:`, err.errors);
                if (err.errors && err.errors.length > 0) {
                    err.errors.forEach((e) => {
                        if (e.message) {
                            errorMessages.push(e.message);
                        }
                    });
                }
            });
            const successCount = detailPasien.length - errors.length;
            const errorDetailsText =
                errorMessages.length > 0
                    ? errorMessages
                          .map((msg, idx) => `${idx + 1}. ${msg}`)
                          .join("\n")
                    : "Check console for details";

            if (successCount > 0) {
                alert(
                    `${successCount} of ${detailPasien.length} items created successfully.\n\n${errors.length} failed:\n\n${errorDetailsText}`
                );
            } else {
                alert(
                    `Failed to create payment details:\n\n${errorDetailsText}`
                );
                hideLoading();
                return;
            }
        } else {
            alert("Payment details created successfully!");
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", {
                detail: "create-detailPembayaranPasien",
            })
        );
        // resetCreateForm();
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating payment");
    } finally {
        hideLoading();
    }
}

function openEditModal(
    id,
    pembayaran_id,
    tipe_biaya,
    jumlah,
    harga_satuan,
    subtotal
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pembayaran_id;
    document.getElementById("edit-tipe-biaya").value = tipe_biaya;
    document.getElementById("edit-jumlah").value = formatNumber(jumlah);
    document.getElementById("edit-harga-satuan").value =
        formatNumber(harga_satuan);
    document.getElementById("edit-subtotal").value = formatNumber(subtotal);

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-resepObat" })
    );
}

// Update
async function updateDetailPembayaranPasien() {
    const id = document.getElementById("edit-id").value;

    const pembayaran_id = document.getElementById("edit-nama").value;
    const tipe_biaya = document.getElementById("edit-tipe-biaya").value;
    const jumlah = document
        .getElementById("edit-jumlah")
        .value.replace(/\./g, "");
    const harga_satuan = parseFloat(
        document.getElementById("edit-harga-satuan").value.replace(/\./g, "")
    );
    const subtotal = document.getElementById("edit-subtotal").value.trim();
    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateDetailPembayaranPasienInput!) {
            updateDetailPembayaranPasien(id: $id, input: $input) {
                id
                pembayaran_id
                tipe_biaya
                referensi_id
                jumlah
                harga_satuan
                subtotal
                pembayaranPasien{
                    id
                    pasien {    
                        id
                        nama
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
                        pembayaran_id,
                        tipe_biaya,
                        jumlah: parseInt(jumlah),
                        harga_satuan: harga_satuan,
                        subtotal,
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
    const pasienSelect = document.getElementById("create-nama");

    // Event untuk change pasien dan load unpaid costs
    if (pasienSelect) {
        pasienSelect.addEventListener("change", (e) => {
            const pasienId = e.target.value;
            if (pasienId) {
                loadUnpaidCosts(pasienId);
            }
        });
    }

    // Event delegation untuk semua input jumlah di dynamic-container
    const dynamicContainer = document.getElementById("dynamic-container");

    dynamicContainer.addEventListener("input", (e) => {
        // Cek apakah yang di-input adalah field jumlah
        if (e.target.name === "create-jumlah[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
            updateSubtotal();
        }

        // Cek apakah yang di-input adalah field harga_satuan
        if (e.target.name === "create-harga-satuan[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
            updateSubtotal();
        }
    }); // Untuk edit modal (tetap pakai cara lama karena hanya 1 input)
    if (editJumlahInput) {
        editJumlahInput.addEventListener("input", (e) => {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        });
    }

    // Edit modal harga_satuan
    const editHargaInput = document.getElementById("edit-harga-satuan");
    if (editHargaInput) {
        editHargaInput.addEventListener("input", (e) => {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        });
    }
});

// Function to calculate subtotal
function updateSubtotal() {
    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");
    let total = 0;

    rows.forEach((row) => {
        const jumlahInput = row.querySelector('input[name="create-jumlah[]"]');
        const hargaInput = row.querySelector(
            'input[name="create-harga-satuan[]"]'
        );

        if (jumlahInput && hargaInput) {
            const jumlah = parseInt(jumlahInput.value.replace(/\./g, "") || 0);
            const harga = parseFloat(hargaInput.value.replace(/\./g, "") || 0);
            total += jumlah * harga;
        }
    });

    const subtotalInput = document.querySelector(
        'input[name="create-subtotal"]'
    );
    if (subtotalInput) {
        subtotalInput.value = formatNumber(total.toString());
    }
}

function renderDetailPembayaranPasienTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="8">No related data found</td>
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

    // Kelompokkan data
    const grouped = items.reduce((acc, item) => {
        const key = `${item.pembayaran_id}`;

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
            tipe_biaya: item.tipe_biaya,
            nama_obat: item.obat?.nama_obat,
            jumlah: item.jumlah,
            harga_satuan: item.harga_satuan,
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

                if (window.currentUserRole === "admin") {
                    if (isActive) {
                        obatActions = `
                        <div class="flex gap-1 flex-wrap">
                            <button onclick="openEditModal(${obat.id}, '${group.pasien.id}', '${group.tenagaMedis.id}', '${obat.tipe_biaya}', '${obat.jumlah}', '${obat.harga_satuan}')"
                                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                                <i class='bx bx-edit-alt'></i> Edit
                            </button>
                            <button onclick="hapusDetailPembayaranPasien(${obat.id})"
                                class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                                <i class='bx bx-archive'></i> Archive
                            </button>
                        </div>
                    `;
                    } else {
                        obatActions = `
                        <div class="flex gap-1 flex-wrap">
                            <button onclick="restoreDetailPembayaranPasien(${obat.id})"
                                class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                                <i class='bx bx-refresh'></i> Restore
                            </button>
                            <button onclick="forceDeleteDetailPembayaranPasien(${obat.id})"
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
                            obat.harga_satuan
                        }</span>
                    </div>
                    ${window.currentUserRole === "admin" ? obatActions : ""}
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
async function hapusDetailPembayaranPasien(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteDetailPembayaranPasien(id: $id){ id } }`;
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
async function restoreDetailPembayaranPasien(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreDetailPembayaranPasien(id: $id){ id } }`;
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
async function forceDeleteDetailPembayaranPasien(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteDetailPembayaranPasien(id: $id){ id } }`;
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
