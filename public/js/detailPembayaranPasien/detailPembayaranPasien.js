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
                            kunjungan_id
                            rawat_inap_id
                            resep_id
                            radiologi_id
                            lab_id
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

                            resep{
                                id
                                obat_id
                                obat{
                                    id
                                    nama_obat
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
                    : document.getElementById("perPage")?.value || 5,
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
            true,
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allDetailPembayaranPasienArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembayaran_id
                            kunjungan_id
                            rawat_inap_id
                            resep_id
                            radiologi_id
                            lab_id
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

                            resep{
                                id
                                obat_id
                                obat{
                                    id
                                    nama_obat
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
                    : document.getElementById("perPageArchive")?.value || 5,
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
            false,
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
                // Try to get hidden input first, then select
                const tipoHiddenInput = row.querySelector(
                    'input[type="hidden"][name="create-tipe-biaya[]"]',
                );
                const tipoSelect = row.querySelector(
                    'select[name="create-tipe-biaya[]"]',
                );

                const jumlahInput = row.querySelector(
                    'input[name="create-jumlah[]"]',
                );
                const hargaInput = row.querySelector(
                    'input[name="create-harga-satuan[]"]',
                );
                const subtotalInput = row.querySelector(
                    'input[name="create-subtotal[]"]',
                );

                if (!jumlahInput || !hargaInput) {
                    console.warn("Missing input elements in row", row);
                    return null;
                }

                const tipe_biaya = tipoHiddenInput
                    ? tipoHiddenInput.value
                    : tipoSelect
                      ? tipoSelect.value
                      : "";
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

                // Map tipe_biaya to correct field
                const mappedData = {
                    jumlah: jumlah,
                    harga_satuan: harga_satuan,
                    subtotal:
                        Number(row.dataset.subtotal) || jumlah * harga_satuan,
                };

                // Map tipe_biaya to the correct field in database
                switch (tipe_biaya) {
                    case "kunjungan":
                        mappedData.kunjungan_id = row.dataset.referensiId;
                        break;
                    case "rawat_inap":
                        mappedData.rawat_inap_id = row.dataset.referensiId;
                        break;
                    case "resep_obat":
                        mappedData.resep_id = row.dataset.referensiId;
                        break;
                    case "radiologi":
                        mappedData.radiologi_id = row.dataset.referensiId;
                        break;
                    case "lab_pemeriksaan":
                        mappedData.lab_id = row.dataset.referensiId;
                        break;
                }

                return mappedData;
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
                kunjungan_id
                rawat_inap_id
                resep_id
                radiologi_id
                lab_id
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
                // Tambahkan pembayaran_id ke setiap item
                const input = {
                    pembayaran_id,
                    ...item,
                };

                // Buat variablesDetailPembayaranPasien untuk setiap item
                const variablesDetailPembayaranPasien = {
                    input: input,
                };

                return fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        query: mutationDetailPembayaranPasien,
                        variables: variablesDetailPembayaranPasien,
                    }),
                }).then((res) => res.json());
            }),
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
                    `${successCount} of ${detailPasien.length} items created successfully.\n\n${errors.length} failed:\n\n${errorDetailsText}`,
                );
            } else {
                alert(
                    `Failed to create payment details:\n\n${errorDetailsText}`,
                );
                hideLoading();
                return;
            }
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", {
                detail: "create-detailPembayaranPasien",
            }),
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
    pasien_id,
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama").value = pembayaran_id;
    document.getElementById("edit-jumlah").value = formatNumber(jumlah);
    document.getElementById("edit-harga-satuan").value =
        formatNumber(harga_satuan);
    document.getElementById("edit-subtotal").value = formatNumber(subtotal);

    // Store referensi_id dan pasien_id untuk reference
    document.getElementById("edit-id").dataset.referensiId = referensi_id;
    document.getElementById("edit-id").dataset.pasienId = pasien_id;

    // Map tipe_biaya ke label yang user-friendly
    const tipeBiayaLabels = {
        kunjungan: "Consultation",
        rawat_inap: "Inpatient Care",
        resep_obat: "Medicine",
        radiologi: "Radiology",
        lab_pemeriksaan: "Laboratory",
    };

    // Set displayed label (disabled input)
    const displayLabel = tipeBiayaLabels[tipe_biaya] || tipe_biaya;
    document.getElementById("edit-tipe-biaya-display").value = displayLabel;

    // Set actual value (hidden input for update)
    document.getElementById("edit-tipe-biaya-hidden").value = tipe_biaya;

    window.dispatchEvent(
        new CustomEvent("open-modal", {
            detail: "edit-detailPembayaranPasien",
        }),
    );
}

// Update
// Update function juga perlu disesuaikan
async function updateDetailPembayaranPasien() {
    const id = document.getElementById("edit-id").value;
    const referensi_id =
        document.getElementById("edit-id").dataset.referensiId || null;

    const pembayaran_id = document.getElementById("edit-nama").value;

    // Ambil tipe_biaya dari hidden input, bukan dari display input
    const tipe_biaya = document.getElementById("edit-tipe-biaya-hidden").value;

    const jumlah = document
        .getElementById("edit-jumlah")
        .value.replace(/\./g, "");
    const harga_satuan = parseFloat(
        document.getElementById("edit-harga-satuan").value.replace(/\./g, ""),
    );
    const subtotal = document
        .getElementById("edit-subtotal")
        .value.replace(/\./g, "")
        .trim();

    showLoading();

    // Map tipe_biaya ke field yang sesuai
    const input = {
        pembayaran_id,
        jumlah: parseInt(jumlah),
        harga_satuan: harga_satuan,
        subtotal: parseFloat(subtotal),
    };

    // Set field yang sesuai berdasarkan tipe_biaya
    switch (tipe_biaya) {
        case "kunjungan":
            input.kunjungan_id = referensi_id;
            break;
        case "rawat_inap":
            input.rawat_inap_id = referensi_id;
            break;
        case "resep_obat":
            input.resep_id = referensi_id;
            break;
        case "radiologi":
            input.radiologi_id = referensi_id;
            break;
        case "lab_pemeriksaan":
            input.lab_id = referensi_id;
            break;
    }

    const mutation = `
        mutation($id: ID!, $input: UpdateDetailPembayaranPasienInput!) {
            updateDetailPembayaranPasien(id: $id, input: $input) {
                id
                pembayaran_id
                kunjungan_id
                rawat_inap_id
                resep_id
                radiologi_id
                lab_id
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
                variables: { id, input },
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
            }),
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

// Function to load unpaid costs by pasien
async function loadUnpaidCosts(pasienId) {
    if (!pasienId) {
        console.log("No pasien selected");
        return;
    }

    showLoading();

    const query = `
        query($pasien_id: ID!) {
            getUnpaidCostsByPasien(pasien_id: $pasien_id) {
                id
                type
                type_label
                description
                jumlah
                harga_satuan
                subtotal
                tanggal
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

        if (data.errors && data.errors.length > 0) {
            console.error("Error loading unpaid costs:", data.errors);
            alert("Error loading unpaid costs: " + data.errors[0].message);
            hideLoading();
            return;
        }

        const unpaidCosts = data.data?.getUnpaidCostsByPasien || [];

        // Clear existing rows
        const container = document.getElementById("dynamic-container");
        container.innerHTML = "";

        if (unpaidCosts.length === 0) {
            // Show message jika tidak ada unpaid costs
            const emptyMsg = document.createElement("div");
            emptyMsg.className = "text-center py-4 text-gray-500 italic";
            emptyMsg.textContent = "No unpaid costs found for this patient";
            container.appendChild(emptyMsg);
            hideLoading();
            return;
        }

        // Create rows dari unpaid costs
        unpaidCosts.forEach((cost, index) => {
            const row = document.createElement("div");
            row.className =
                "dynamic-row bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 " +
                "p-4 rounded-xl shadow-sm space-y-3 transition-all";
            row.dataset.referensiId = cost.id;
            row.dataset.subtotal = cost.subtotal;

            const tanggalFormatted = new Date(cost.tanggal).toLocaleDateString(
                "id-ID",
            );

            row.innerHTML = `
                <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded">
                    <p class="text-xs text-gray-600 dark:text-gray-300">${cost.description}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Date: ${tanggalFormatted}</p>
                </div>

                <div>
                    <label class="text-sm font-medium">Cost Type</label>
                    <select name="create-tipe-biaya[]" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm" disabled>
                        <option value="${cost.type}" selected>${cost.type_label}</option>
                    </select>
                    <input type="hidden" name="create-tipe-biaya[]" value="${cost.type}">
                </div>

                <div>
                    <label class="text-sm font-medium">Amount</label>
                    <input type="text" name="create-jumlah[]" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm" value="${formatNumber(cost.jumlah.toString())}">
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input type="text" name="create-harga-satuan[]" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm" value="${formatNumber(cost.harga_satuan.toString())}">
                </div>

                <div>
                    <label class="text-sm font-medium">Subtotal</label>
                    <input type="text" name="create-subtotal[]" class="border-2 border-green-600 py-2 px-3 w-full rounded-full mb-3 bg-gray-100 font-semibold" value="${formatNumber(cost.subtotal.toString())}" readonly>
                </div>
            `;

            container.appendChild(row);
        });

        hideLoading();
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while loading unpaid costs");
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
                "edit-obat-container",
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
                    data.jumlah.toString(),
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
            'input[name="create-harga-satuan[]"]',
        );
        const subtotalInput = row.querySelector(
            'input[name="create-subtotal[]"]',
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
            kunjungan_id: item.kunjungan_id,
            rawat_inap_id: item.rawat_inap_id,
            resep_id: item.resep_id,
            radiologi_id: item.radiologi_id,
            lab_id: item.lab_id,
            jumlah: item.jumlah,
            harga_satuan: item.harga_satuan,
            subtotal: item.subtotal,
            resep: item.resep,
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
            0,
        );

        // Render rows detail dalam format grid 6 kolom
        const detailRows = group.details
            .map((detail) => {
                let detailActions = "";

                if (
                    window.currentUserRole === "admin" ||
                    window.currentUserRole === "cashier"
                ) {
                    if (isActive) {
                        // Determine tipe_biaya and referensi_id
                        let tipeBiaya = "";
                        let referensiId = "";

                        if (detail.kunjungan_id) {
                            tipeBiaya = "kunjungan";
                            referensiId = detail.kunjungan_id;
                        } else if (detail.rawat_inap_id) {
                            tipeBiaya = "rawat_inap";
                            referensiId = detail.rawat_inap_id;
                        } else if (detail.resep_id) {
                            tipeBiaya = "resep_obat";
                            referensiId = detail.resep_id;
                        } else if (detail.radiologi_id) {
                            tipeBiaya = "radiologi";
                            referensiId = detail.radiologi_id;
                        } else if (detail.lab_id) {
                            tipeBiaya = "lab_pemeriksaan";
                            referensiId = detail.lab_id;
                        }

                        detailActions = `
                            <button onclick="openEditModal(${detail.id}, '${group.pembayaran_id}', '${tipeBiaya}', '${detail.jumlah}', '${detail.harga_satuan}', '${detail.subtotal}', '${referensiId}', '${group.pasien_id}')"
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

                // Determine cost type label
                let costTypeLabel = "-";
                if (detail.kunjungan_id) costTypeLabel = "Konsultasi";
                else if (detail.rawat_inap_id) costTypeLabel = "Rawat Inap";
                else if (detail.resep_id)
                    costTypeLabel = detail.resep?.obat?.nama_obat || "Obat";
                else if (detail.radiologi_id) costTypeLabel = "Radiologi";
                else if (detail.lab_id) costTypeLabel = "Lab";

                return `
                    <div class="grid grid-cols-6 py-2 text-sm border-dotted border-t-2 dark:text-gray-200">
                        <div class="font-semibold text-blue-600 dark:text-blue-400">
                            ${costTypeLabel}
                        </div>
                        <div class="text-cyan-500">${detail.jumlah.toLocaleString(
                            "id-ID",
                        )}</div>
                        <div class="text-gray-400">Rp ${detail.harga_satuan.toLocaleString(
                            "id-ID",
                        )}</div>
                        <div class="text-orange-400">Rp ${detail.subtotal.toLocaleString(
                            "id-ID",
                        )}</div>
                        <div class="flex gap-1 flex-wrap col-span-2">${detailActions}</div>
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
                                "id-ID",
                            )}</span>
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
                        <div class="col-span-2">Actions</div>
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
