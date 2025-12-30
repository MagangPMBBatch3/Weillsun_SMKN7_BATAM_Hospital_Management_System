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
                                pasien_id
                                tanggal_bayar
                                pasien {    
                                    id
                                    nama
                                    
                                }
                            }
                            obat{
                                id
                                nama_obat
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
            "cardActive",
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
                                pasien_id
                                tanggal_bayar
                                pasien {    
                                    id
                                    nama
                                }
                            }
                            obat{
                                id
                                nama_obat
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
            "cardArchive",
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

// Fetch obat untuk pasien (untuk edit modal)
async function loadObatByPasien(pasienId) {
    showLoading();

    const query = `
        query($pasien_id: ID!) {
            resepObatByPasien(pasien_id: $pasien_id) {
                id
                jumlah
                obat {
                    id
                    nama_obat
                    harga_jual
                }
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
        console.log("Resep Obat Data:", data);

        if (data.errors && data.errors.length > 0) {
            console.error("GraphQL Error:", data.errors);
            alert("Error loading obat: " + data.errors[0].message);
            hideLoading();
            return;
        }

        const resepObat = data?.data?.resepObatByPasien || [];
        console.log("Populated resepObat:", resepObat);
        populateObatDropdown(resepObat);
    } catch (error) {
        console.error("Error loading obat:", error);
        alert("Error loading obat: " + error.message);
    } finally {
        hideLoading();
    }
}

// Populate obat dropdown di edit modal
function populateObatDropdown(resepObatList) {
    const obatSelect = document.getElementById("edit-obat-select");
    if (!obatSelect) {
        console.warn("edit-obat-select not found!");
        return;
    }
 
    console.log("Populating dropdown with:", resepObatList);

    // Clear existing options
    obatSelect.innerHTML = '<option value="">Select Obat</option>';

    // Store resep obat data untuk reference
    window.resepObatData = {};

    // Add options dari resep obat
    if (!resepObatList || resepObatList.length === 0) {
        console.warn("No resep obat data available");
        return;
    }

    resepObatList.forEach((resepObat) => {
        try {
            const option = document.createElement("option");
            option.value = resepObat.id;
            option.textContent = resepObat.obat?.nama_obat || "Unknown Obat";
            obatSelect.appendChild(option);

            // Store data for later use
            window.resepObatData[resepObat.id] = {
                jumlah: resepObat.jumlah,
                harga_jual: resepObat.obat?.harga_jual || 0,
                obat_id: resepObat.obat?.id || null,
                nama_obat: resepObat.obat?.nama_obat || "Unknown",
            };
            console.log("Added option for:", resepObat.obat?.nama_obat);
        } catch (e) {
            console.error("Error adding option:", e, resepObat);
        }
    });

    console.log("Final resepObatData:", window.resepObatData);

    // Auto-select obat jika referensi_id sudah ada (edit mode)
    if (window.currentReferensiId) {
        // Find resep obat id based on referensi_id (obat_id)
        for (const resepObatId in window.resepObatData) {
            if (
                window.resepObatData[resepObatId].obat_id ==
                window.currentReferensiId
            ) {
                console.log(
                    "Auto-selecting obat with resepObat id:",
                    resepObatId
                );
                obatSelect.value = resepObatId;
                // Trigger change event untuk auto-fill jumlah dan harga
                obatSelect.dispatchEvent(
                    new Event("change", { bubbles: true })
                );
                break;
            }
        }
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
            "dynamic-row bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 " +
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

            <div>
                    <label class="text-sm font-medium">Subtotal</label>
                    <input type="text" name="create-subtotal[]"
                        class="border-2 border-green-600 py-2 px-3 w-full rounded-full mb-3 bg-gray-100 font-semibold"
                        placeholder="0" readonly>
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
    // const subtotal = document.getElementById("create-subtotal")?.value;

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
                const subtotalInput = row.querySelector(
                    'input[name="create-subtotal[]"]'
                );

                if (!tipoSelect || !jumlahInput || !hargaInput) {
                    console.warn("Missing input elements in row", row);
                    return null;
                }

                const tipe_biaya = tipoSelect.value || "";
                const jumlahValue = jumlahInput.value || "0";
                const hargaValue = hargaInput.value || "0";
                const subtotalValue = subtotalInput.value || "0";

                const jumlah = parseInt(jumlahValue.replace(/\./g, "")) || 0;
                const harga_satuan =
                    parseFloat(hargaValue.replace(/\./g, "")) || 0;
                const subtotal =
                    parseFloat(subtotalValue.replace(/\./g, "")) || 0;

                if (!tipe_biaya || jumlah === 0 || harga_satuan === 0) {
                    return null;
                }

                return {
                    tipe_biaya: tipe_biaya,
                    jumlah: jumlah,
                    harga_satuan: harga_satuan,
                    referensi_id: row.dataset.referensiId,
                    subtotal:
                        Number(row.dataset.subtotal) || jumlah * harga_satuan,
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
    subtotal,
    referensi_id,
    pasien_id
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pembayaran_id;
    document.getElementById("edit-tipe-biaya").value = tipe_biaya;
    document.getElementById("edit-jumlah").value = formatNumber(jumlah);
    document.getElementById("edit-harga-satuan").value =
        formatNumber(harga_satuan);
    document.getElementById("edit-subtotal").value = formatNumber(subtotal);

    // Store referensi_id dan pasien_id untuk reference
    document.getElementById("edit-id").dataset.referensiId = referensi_id;
    document.getElementById("edit-id").dataset.pasienId = pasien_id;

    // Load obat jika tipe_biaya = obat
    if (tipe_biaya === "obat" && pasien_id) {
        console.log("Loading obat for pasien_id:", pasien_id);
        loadObatByPasien(pasien_id);
        // Store referensi_id untuk auto-select setelah dropdown loaded
        window.currentReferensiId = referensi_id;
        document
            .getElementById("edit-obat-container")
            .classList.remove("hidden");
    } else {
        document.getElementById("edit-obat-container").classList.add("hidden");
    }

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-detailPembayaranPasien" })
    );
}

// Update
async function updateDetailPembayaranPasien() {
    const id = document.getElementById("edit-id").value;
    const referensi_id =
        document.getElementById("edit-id").dataset.referensiId || null;

    const pembayaran_id = document.getElementById("edit-nama").value;
    const tipe_biaya = document.getElementById("edit-tipe-biaya").value;
    const jumlah = document
        .getElementById("edit-jumlah")
        .value.replace(/\./g, "");
    const harga_satuan = parseFloat(
        document.getElementById("edit-harga-satuan").value.replace(/\./g, "")
    );
    const subtotal = document
        .getElementById("edit-subtotal")
        .value.replace(/\./g, "")
        .trim();
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
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: {
                    id,
                    input: {
                        pembayaran_id,
                        tipe_biaya,
                        referensi_id,
                        jumlah: parseInt(jumlah),
                        harga_satuan: harga_satuan,
                        subtotal: parseFloat(subtotal),
                    },
                },
            }),
        });
        const data = await res.json();

        if (data.errors && data.errors.length > 0) {
            console.error("Update failed:", data.errors);
            alert("Failed to update: " + data.errors[0].message);
            hideLoading();
            return;
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", {
                detail: "edit-detailPembayaranPasien",
            })
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
    const editPasienSelect = document.getElementById("edit-nama");
    const editTipeBiayaSelect = document.getElementById("edit-tipe-biaya");

    // Event untuk change pasien di create modal dan load unpaid costs
    if (pasienSelect) {
        pasienSelect.addEventListener("change", (e) => {
            const pasienId = e.target.value;
            if (pasienId) {
                loadUnpaidCosts(pasienId);
            }
        });
    }

    // Event untuk change pasien di edit modal dan load obat
    if (editPasienSelect) {
        editPasienSelect.addEventListener("change", (e) => {
            const pasienId = e.target.value;
            if (pasienId) {
                loadObatByPasien(pasienId);
            }
        });
    }

    // Event untuk change tipe_biaya di edit modal untuk show/hide obat dropdown
    if (editTipeBiayaSelect) {
        editTipeBiayaSelect.addEventListener("change", (e) => {
            const tipeBiaya = e.target.value;
            const obatContainer = document.getElementById(
                "edit-obat-container"
            );

            if (tipeBiaya === "obat") {
                obatContainer.classList.remove("hidden");
            } else {
                obatContainer.classList.add("hidden");
            }
        });
    }

    // Event untuk change obat select dan auto-fill jumlah & harga
    const editObatSelect = document.getElementById("edit-obat-select");
    if (editObatSelect) {
        editObatSelect.addEventListener("change", (e) => {
            const resepObatId = e.target.value;
            if (
                resepObatId &&
                window.resepObatData &&
                window.resepObatData[resepObatId]
            ) {
                const data = window.resepObatData[resepObatId];
                document.getElementById("edit-jumlah").value = formatNumber(
                    data.jumlah.toString()
                );
                document.getElementById("edit-harga-satuan").value =
                    formatNumber(data.harga_jual.toString());
                calculateEditSubtotal();
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
            calculateEditSubtotal();
        });
    }

    // Edit modal jumlah untuk calculate subtotal (gunakan 'input' untuk real-time)
    if (editJumlahInput) {
        editJumlahInput.addEventListener("input", () => {
            calculateEditSubtotal();
        });
    }
});

// Function to calculate subtotal
function updateSubtotal() {
    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");

    rows.forEach((row) => {
        const jumlahInput = row.querySelector('input[name="create-jumlah[]"]');
        const hargaInput = row.querySelector(
            'input[name="create-harga-satuan[]"]'
        );
        const subtotalInput = row.querySelector(
            'input[name="create-subtotal[]"]'
        );

        if (!jumlahInput || !hargaInput || !subtotalInput) return;

        const jumlah = parseInt(jumlahInput.value.replace(/\./g, "")) || 0;
        const harga = parseFloat(hargaInput.value.replace(/\./g, "")) || 0;

        const subtotal = jumlah * harga;

        subtotalInput.value = formatNumber(subtotal.toString());
    });
}

// Function to calculate edit subtotal
function calculateEditSubtotal() {
    console.log("calculateEditSubtotal called");
    const jumlahInput = document.getElementById("edit-jumlah");
    const hargaInput = document.getElementById("edit-harga-satuan");
    const subtotalInput = document.getElementById("edit-subtotal");

    if (!jumlahInput || !hargaInput || !subtotalInput) {
        console.warn("Edit modal inputs not found");
        return;
    }

    const jumlah = parseInt(jumlahInput.value.replace(/\./g, "")) || 0;
    const harga = parseFloat(hargaInput.value.replace(/\./g, "")) || 0;

    const subtotal = jumlah * harga;
    console.log("Calculated subtotal:", jumlah, "*", harga, "=", subtotal);

    subtotalInput.value = formatNumber(subtotal.toString());
}

function renderDetailPembayaranPasienTable(result, containerId, isActive) {
    const container = document.getElementById(containerId);
    container.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        container.innerHTML = `
            <div class="text-center py-6 text-red-500 font-semibold italic">
                No Data Available
            </div>
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

    // Kelompokkan berdasarkan pembayaran_id
    const grouped = items.reduce((acc, item) => {
        const key = item.pembayaran_id;

        if (!acc[key]) {
            acc[key] = {
                pembayaran_id: item.pembayaran_id,
                pasien_id: item.pembayaranPasien?.pasien_id || null,
                pasien: item.pembayaranPasien?.pasien || {},
                tanggal: item.pembayaranPasien?.tanggal_bayar || "-",
                tenagaMedis: item.pembayaranPasien?.tenagaMedis || {},
                details: [],
            };
        }

        acc[key].details.push({
            id: item.id,
            tipe_biaya: item.tipe_biaya,
            referensi_id: item.referensi_id,
            jumlah: item.jumlah,
            harga_satuan: item.harga_satuan,
            subtotal: item.subtotal,
            obat: item.obat,
            isPaid: item.isPaid,
        });

        return acc;
    }, {});

    const baseBtn = `
        inline-flex items-center justify-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold
        transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
    `;

    // Render setiap grup menjadi card
    Object.values(grouped).forEach((group) => {
        const totalSubtotal = group.details.reduce(
            (sum, d) => sum + d.subtotal,
            0
        );

        // Render rows detail dalam format grid 6 kolom
        const detailRows = group.details
            .map((detail) => {
                let detailActions = "";

                if (window.currentUserRole === "admin" || window.currentUserRole === "cashier") {
                    if (isActive) {
                        detailActions = `
                            <button onclick="openEditModal(${detail.id}, '${group.pembayaran_id}', '${detail.tipe_biaya}', '${detail.jumlah}', '${detail.harga_satuan}', '${detail.subtotal}', '${detail.referensi_id}', '${group.pasien_id}')"
                                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                Edit
                            </button>
                            <button onclick="hapusDetailPembayaranPasien(${detail.id})"
                                class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200">
                                Archive
                            </button>
                        `;
                    } else {
                        detailActions = `
                            <button onclick="restoreDetailPembayaranPasien(${detail.id})"
                                class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                Restore
                            </button>
                            <button onclick="forceDeleteDetailPembayaranPasien(${detail.id})"
                                class="${baseBtn} bg-red-100 text-red-700 hover:bg-red-200">
                                Delete
                            </button>
                        `;
                    }
                }

                const namaBiaya =
                    detail.tipe_biaya === "obat"
                        ? detail.obat?.nama_obat ?? "-"
                        : detail.tipe_biaya.charAt(0).toUpperCase() +
                          detail.tipe_biaya.slice(1);

                return `
                    <div class="grid grid-cols-6 py-2 text-sm border-dotted border-t-2 dark:text-gray-200">
                        <div class="font-semibold text-blue-600 dark:text-blue-400">
                            ${namaBiaya}

                        </div>
                        <div class="text-cyan-500">${detail.jumlah.toLocaleString(
                            "id-ID"
                        )}</div>
                        <div class="text-gray-400">${detail.harga_satuan.toLocaleString(
                            "id-ID"
                        )}</div>
                        <div class="text-orange-400">${detail.subtotal.toLocaleString(
                            "id-ID"
                        )}</div>
                        <div class="flex gap-1 flex-wrap">${detailActions}</div>
                    </div>
                `;
            })
            .join("");

        // CARD TEMPLATE
        container.innerHTML += `
            <div class="p-4 mb-4 rounded-xl shadow bg-slate-50 dark:bg-gray-800 border-dashed border-2 border-gray-200 dark:border-gray-700">

                <div class="flex justify-between items-center mb-3">
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-gray-500 dark:text-white">
                            Patient: <span class="text-blue-600 dark:text-blue-400">${
                                group.pasien?.nama || "N/A"
                            }</span>
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Items: <span class="font-semibold text-red-500">${
                                group.details.length
                            }</span>  
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total: <span class="font-semibold text-green-600">Rp ${totalSubtotal.toLocaleString(
                                "id-ID"
                            )} </span>
                        </p>
                    </div>

                    <div class="text-md tracking-widest font-semibold text-gray-500 dark:text-gray-400">
                        Date: ${group.tanggal.split("-").reverse().join("/")}
                    </div>

                </div>

                <div class="w-full border-t-2 pt-3 mt-3">

                    <div class="grid grid-cols-6 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                        <div>Cost Type</div>
                        <div>Qty</div>
                        <div>Unit Price</div>
                        <div>Total</div>
                        <div>Actions</div>
                    </div>

                    ${detailRows}

                </div>

            </div>
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
