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
function searchDetailPembelianObat() {
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
                allDetailPembelianObatPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembelian_id
                            obat_id
                            jumlah
                            harga_satuan
                            harga_beli
                            subtotal
                            obat {
                                id
                                nama_obat
                            }
                            pembelianObat {
                                id
                                tanggal
                                supplier {
                                    nama_supplier
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
        renderDetailPembelianObatCard(
            dataActive?.data?.allDetailPembelianObatPaginate || {},
            "cardActive",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allDetailPembelianObatArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembelian_id
                            obat_id
                            jumlah
                            harga_satuan
                            harga_beli
                            subtotal
                            obat {
                                id
                                nama_obat
                            }
                            pembelianObat {
                                id
                                tanggal
                                supplier {
                                    nama_supplier
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
        renderDetailPembelianObatCard(
            dataArchive?.data?.allDetailPembelianObatArchive || {},
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
async function createDetailPembelianObat() {
    const pembelian_id = document.getElementById("create-pembelian").value;

    if (!pembelian_id) {
        return alert("Please select Supplier Name!");
    }

    const rows = document.querySelectorAll("#dynamic-container .dynamic-row");

    const prescriptions = Array.from(rows)
        .map((row) => {
            const obat_id = row.querySelector(
                'select[name="create-nama-obat[]"]'
            ).value;
            const jumlah = parseInt(
                row
                    .querySelector('input[name="create-jumlah[]"]')
                    .value.replace(/\./g, "") || 0
            );
            const harga_satuan = parseFloat(
                row
                    .querySelector('input[name="create-harga-satuan[]"]')
                    .value.replace(/\./g, "") || 0
            );
            const harga_beli = parseFloat(
                row
                    .querySelector('input[name="create-harga-beli[]"]')
                    .value.replace(/\./g, "") || 0
            );
            const subtotal = parseFloat(
                row
                    .querySelector('input[name="create-subtotal[]"]')
                    .value.replace(/\./g, "") || 0
            );

            return {
                obat_id,
                jumlah,
                harga_satuan,
                harga_beli,
                subtotal: jumlah * harga_beli,
            };
        })
        .filter(
            (item) =>
                item.obat_id &&
                item.jumlah &&
                item.harga_satuan &&
                item.harga_beli
        );

    if (prescriptions.length === 0) {
        return alert("Please fill at least one prescription!");
    }

    showLoading();

    const mutationDetailPembelianObat = `
        mutation($input: CreateDetailPembelianObatInput!) {
            createDetailPembelianObat(input: $input) {
                id
                pembelian_id
                obat_id
                jumlah
                harga_satuan
                harga_beli
                subtotal
                obat {
                    id
                    nama_obat
                }
                pembelianObat {
                    id
                }
            }
        }
    `;

    try {
        const results = await Promise.all(
            prescriptions.map((item) => {
                return fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        query: mutationDetailPembelianObat,
                        variables: {
                            input: {
                                pembelian_id,
                                obat_id: item.obat_id,
                                jumlah: item.jumlah,
                                harga_satuan: item.harga_satuan,
                                harga_beli: item.harga_beli,
                                subtotal: item.subtotal,
                            },
                        },
                    }),
                }).then((res) => res.json());
            })
        );

        const errors = results.filter((r) => r.errors);
        if (errors.length > 0) {
            console.error("Some mutations failed:", errors);
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", {
                detail: "create-detailPembelianObat",
            })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating purchase details.");
    } finally {
        hideLoading();
    }
}

function openEditModal(
    id,
    pembelian_id,
    obat_id,
    jumlah,
    harga_satuan,
    harga_beli,
    subtotal
) {
    document.getElementById("edit-id").value = id;

    document.getElementById("edit-pembelian").value = pembelian_id;
    document.getElementById("edit-obat").value = obat_id;
    document.getElementById("edit-jumlah").value = formatNumber(jumlah);
    document.getElementById("edit-harga-satuan").value = formatNumber(harga_satuan);
    document.getElementById("edit-harga-beli").value = formatNumber(harga_beli);
    document.getElementById("edit-subtotal").value = formatNumber(subtotal);

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-detailPembelianObat" })
    );
}

// Update
async function updateDetailPembelianObat() {
    const id = document.getElementById("edit-id").value;

    const pembelian_id = document.getElementById("edit-pembelian").value;
    const obat_id = document.getElementById("edit-obat").value;
    const jumlah = document
        .getElementById("edit-jumlah")
        .value.replace(/\./g, "");
    const harga_satuan = document
        .getElementById("edit-harga-satuan")
        .value.replace(/\./g, "");
    const harga_beli = document
        .getElementById("edit-harga-beli")
        .value.replace(/\./g, "");
    const subtotal = document
        .getElementById("edit-subtotal")
        .value.replace(/\./g, "");

    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateDetailPembelianObatInput!) {
            updateDetailPembelianObat(id: $id, input: $input) {
                id
                pembelian_id
                obat_id
                jumlah
                harga_satuan
                harga_beli
                subtotal
                obat {
                    id
                    nama_obat
                }
                pembelianObat {
                    id
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
                        pembelian_id,
                        obat_id,
                        jumlah: parseInt(jumlah),
                        harga_satuan: parseInt(harga_satuan),
                        harga_beli: parseInt(harga_beli),
                        subtotal: parseInt(subtotal),
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", {
                detail: "edit-detailPembelianObat",
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
    const editHargaSatuanInput = document.getElementById("edit-harga-satuan");
    const editHargaBeliInput = document.getElementById("edit-harga-beli");
    const editSubtotalInput = document.getElementById("edit-subtotal");

    editJumlahInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
        hitungSubtotalEdit();
    });

    editHargaSatuanInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });

    editHargaBeliInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
        hitungSubtotalEdit();
    });

    editSubtotalInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });

});

// AUTO SUBTOTAL — CREATE

document.addEventListener("input", function (e) {

    // Jika input jumlah berubah
    if (e.target.matches('input[name="create-jumlah[]"]')) {
        const row = e.target.closest(".dynamic-row");

        e.target.value = formatNumber(unformatNumber(e.target.value));

        hitungSubtotal(row);
    }

    if (e.target.matches('input[name="create-harga-beli[]"]')) {
        const row = e.target.closest(".dynamic-row");

        e.target.value = formatNumber(unformatNumber(e.target.value));

        hitungSubtotal(row);
    }
});


function hitungSubtotal(row) {
    const jumlahInput = row.querySelector('input[name="create-jumlah[]"]');
    const hargaBeliInput = row.querySelector('input[name="create-harga-beli[]"]');
    const subtotalInput = row.querySelector('input[name="create-subtotal[]"]');

    const jumlah = parseInt(unformatNumber(jumlahInput.value)) || 0;
    const hargaBeli = parseInt(unformatNumber(hargaBeliInput.value)) || 0;

    const subtotal = jumlah * hargaBeli;

    subtotalInput.value = formatNumber(subtotal);
}

// AUTO SUBTOTAL — EDIT

function hitungSubtotalEdit() {
    const jumlah = parseInt(unformatNumber(document.getElementById("edit-jumlah").value)) || 0;
    const hargaBeli = parseInt(unformatNumber(document.getElementById("edit-harga-beli").value)) || 0;

    const subtotalEdit = jumlah * hargaBeli;

    document.getElementById("edit-subtotal").value = formatNumber(subtotalEdit);
}

// AUTO HARGA SATUAN 

document.addEventListener("change", function (e) {

    if (e.target.matches('select[name="create-nama-obat[]"]')) {
        const selectedOption = e.target.selectedOptions[0];
        const harga = selectedOption.getAttribute("data-harga");

        const row = e.target.closest(".dynamic-row");

        row.querySelector('input[name="create-harga-satuan[]"]').value =
            harga ? formatNumber(parseInt(harga)) : "";
    }

    if (e.target.id === "edit-obat") {
        const selectedOption = e.target.selectedOptions[0];
        const harga = selectedOption.getAttribute("data-harga");

        document.getElementById("edit-harga-satuan").value =
            harga ? formatNumber(parseInt(harga)) : "";

        hitungSubtotalEdit();
    }
});



function renderDetailPembelianObatCard(result, containerId, isActive) {
    const container = document.getElementById(containerId);
    container.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    // Jika tidak ada data
    if (!items.length) {
        container.innerHTML = `
            <div class="text-center py-6 text-red-500 font-semibold italic">
                No Data Available
            </div>
        `;
        return;
    }

    // Kelompokkan berdasarkan pembelian_id
    const grouped = items.reduce((acc, item) => {
        const key = item.pembelian_id;

        if (!acc[key]) {
            acc[key] = {
                pembelian_id: item.pembelian_id,
                supplier:
                    item.pembelianObat?.supplier?.nama_supplier || "Unknown",
                tanggal: item.pembelianObat?.tanggal || "Unknown",
                details: [],
            };
        }

        acc[key].details.push({
            id: item.id,
            obat_id: item.obat_id,
            nama_obat: item.obat?.nama_obat,
            jumlah: item.jumlah,
            harga_satuan: item.harga_satuan,
            harga_beli: item.harga_beli,
            subtotal: item.subtotal,
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

        // Render rows obat dalam format grid 6 kolom
        const detailRows = group.details
            .map((detail) => {
                let detailActions = "";

                if (window.currentUserRole === "admin") {
                    if (isActive) {
                        detailActions = `
                            <button onclick="openEditModal(${detail.id}, '${group.pembelian_id}', '${detail.obat_id}', '${detail.jumlah}', '${detail.harga_satuan}', '${detail.harga_beli}', '${detail.subtotal}')"
                                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                Edit
                            </button>
                            <button onclick="hapusDetailPembelianObat(${detail.id})"
                                class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200">
                                Archive
                            </button>
                        `;
                    } else {
                        detailActions = `
                            <button onclick="restoreDetailPembelianObat(${detail.id})"
                                class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                Restore
                            </button>
                            <button onclick="forceDeleteDetailPembelianObat(${detail.id})"
                                class="${baseBtn} bg-red-100 text-red-700 hover:bg-red-200">
                                Delete
                            </button>
                        `;
                    }
                }

                return `
                    <div class="grid grid-cols-6 py-2 text-sm border-dotted border-t-2 dark:text-gray-200">
                        <div class="font-semibold text-blue-600 dark:text-blue-400">
                            ${detail.nama_obat}
                        </div>
                        <div class="text-cyan-500">${detail.jumlah.toLocaleString("id-ID")}</div>
                        <div class="text-gray-400">${detail.harga_satuan.toLocaleString("id-ID")}</div>
                        <div class="text-orange-400">${detail.harga_beli.toLocaleString("id-ID")}</div>
                        <div class="text-green-500">${detail.subtotal.toLocaleString("id-ID")}</div>
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
                            Supplier: <span class="text-orange-500">${group.supplier}</span>
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Items: <span class="font-semibold text-red-500">${group.details.length}</span>  
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total: <span class="font-semibold text-green-600">Rp ${totalSubtotal.toLocaleString("id-ID")} </span>
                        </p>
                    </div>

                    <div class="text-md tracking-widest font-semibold text-gray-500 dark:text-gray-400">
                        ${group.tanggal }
                    </div>

                </div>

                <div class="w-full border-t-2 pt-3 mt-3">

                    <div class="grid grid-cols-6 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">
                        <div>Medicine</div>
                        <div>Qty</div>
                        <div>Unit Price</div>
                        <div>Purchase</div>
                        <div>Total</div>
                        <div>Actions</div>
                    </div>

                    ${detailRows}

                </div>

            </div>
        `;
    });

    // Update Pagination
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
async function hapusDetailPembelianObat(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteDetailPembelianObat(id: $id){ id } }`;
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
async function restoreDetailPembelianObat(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreDetailPembelianObat(id: $id){ id } }`;
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
async function forceDeleteDetailPembelianObat(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteDetailPembelianObat(id: $id){ id } }`;
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
