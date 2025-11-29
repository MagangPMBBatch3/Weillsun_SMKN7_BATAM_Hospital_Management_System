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
        renderDetailPembelianObatTable(
            dataActive?.data?.allDetailPembelianObatPaginate || {},
            "dataDetailPembelianObatAktif",
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
        renderDetailPembelianObatTable(
            dataArchive?.data?.allDetailPembelianObatArchive || {},
            "dataDetailPembelianObatArsip",
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
    const pembelian_id = document.getElementById("create-supplier").value;
    const subtotal = document.getElementById("create-subtotal").value;

    if (!pembelian_id) {
        return alert("Please select Supplier Name!");
    }

   const rows = document.querySelectorAll('#dynamic-container .dynamic-row');

    // Map rows ke array of prescription objects, lalu filter yang valid
    const prescriptions = Array.from(rows)
        .map(row => ({
            obat_id: row.querySelector('select[name="create-nama-obat[]"]').value,
            jumlah: parseInt(row.querySelector('input[name="create-jumlah[]"]').value.replace(/\./g, "") || 0),
            harga_satuan: parseInt(row.querySelector('input[name="create-harga-satuan[]"]').value.replace(/\./g, "") || 0),
            harga_beli: parseInt(row.querySelector('input[name="create-harga-beli[]"]').value.replace(/\./g, "") || 0),
        }))
        .filter(item => item.obat_id && item.jumlah && item.harga_satuan && item.harga_beli);

    

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
                    supplier {
                        nama_supplier
                    }
                }
            }
        }
    `;
   

    try {
        const results = await Promise.all(
            prescriptions.map(item => {
                // Buat variablesDetailPembelianObat untuk setiap item
                const variablesDetailPembelianObat = {
                    input: {
                        pembelian_id,
                        subtotal,
                        obat_id: item.obat_id,
                        jumlah: item.jumlah,
                        harga_satuan: item.harga_satuan,
                        harga_beli: item.harga_beli
                    }
                };

                return fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        query: mutationDetailPembelianObat,
                        variables: variablesDetailPembelianObat
                    })
                }).then(res => res.json());
            })
        );

        // Cek apakah ada error
        const errors = results.filter(r => r.errors);
        if (errors.length > 0) {
            console.error("Some mutations failed:", errors);
            alert(`${prescriptions.length - errors.length} of ${prescriptions.length} prescriptions created`);
        }

        window.dispatchEvent(new CustomEvent("close-modal", { detail: "create-detailPembelianObat" }));
        // resetCreateForm();
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating prescription");
    } finally {
        hideLoading();
    }
}

function openEditModal(id, pembelian_id, obat_id, jumlah, harga_satuan, harga_beli, subtotal) {
    document.getElementById("edit-id").value = id;

    document.getElementById("edit-supplier").value = pembelian_id;
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

    const pembelian_id = document.getElementById("edit-supplier").value;
    const obat_id = document.getElementById("edit-obat").value;
    const jumlah = document.getElementById("edit-jumlah").value.replace(/\./g, "");
    const harga_satuan = document.getElementById("edit-harga-satuan").value.replace(/\./g, "");
    const harga_beli = document.getElementById("edit-harga-beli").value.replace(/\./g, "");
    const subtotal = document.getElementById("edit-subtotal").value.replace(/\./g, "");

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
                    supplier {
                        nama_supplier
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
                    input: { 
                        pembelian_id,
                        obat_id,
                        jumlah: parseInt(jumlah),
                        harga_satuan: parseInt(harga_satuan),
                        harga_beli: parseInt(harga_beli),
                        subtotal: parseInt(subtotal)
                    }
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-detailPembelianObat" })
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
    
    
    // Event delegation untuk semua input jumlah di dynamic-container
    const dynamicContainer = document.getElementById('dynamic-container');
    
    dynamicContainer.addEventListener("input", (e) => {
        if (e.target.name === "create-jumlah[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        }
    });

    dynamicContainer.addEventListener("input", (e) => {
        if (e.target.name === "create-harga-satuan[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        }
    });
    dynamicContainer.addEventListener("input", (e) => {
        if (e.target.name === "create-harga-beli[]") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        }
    });
    dynamicContainer.addEventListener("input", (e) => {
        if (e.target.name === "create-subtotal") {
            let value = unformatNumber(filterAngka(e.target.value));
            e.target.value = value ? formatNumber(value) : "";
        }
    });
    
    const editJumlahInput = document.getElementById('edit-jumlah');
    const editHargaSatuanInput = document.getElementById('edit-harga-satuan');
    const editHargaBeliInput = document.getElementById('edit-harga-beli');
    const editSubtotalInput = document.getElementById('edit-subtotal');

    editJumlahInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });

    editHargaSatuanInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        e.target.value = value ? formatNumber(value) : "";
    });

    // editHargaBeliInput.addEventListener("input", (e) => {
    //     let value = unformatNumber(filterAngka(e.target.value));
    //     e.target.value = value ? formatNumber(value) : "";
    // });

    // editSubtotalInput.addEventListener("input", (e) => {
    //     let value = unformatNumber(filterAngka(e.target.value));
    //     e.target.value = value ? formatNumber(value) : "";
    // });

});

function renderDetailPembelianObatTable(result, tableId, isActive) {
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
            pageInfoEl.innerText = `Halaman ${pageInfo.currentPage || 1} dari ${pageInfo.lastPage || 1} (Total: 0)`;
        }
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        return;
    }

    // Kelompokkan data berdasarkan apa
    const grouped = items.reduce((acc, item) => {
        const key = `${item.pembelian_id_id}`;
        
        if (!acc[key]) {
            acc[key] = {
                ids: [],
                pembelian_id: item.pembelian_id,
                obats: []
            };
        }
        
        acc[key].ids.push(item.id);
        acc[key].obats.push({
            id: item.id,
            obat_id: item.obat_id,
            nama_obat: item.obat?.nama_obat,
            jumlah: item.jumlah,
            harga_satuan: item.harga_satuan,
            harga_beli: item.harga_beli,
            subtotal: item.subtotal
        });
        
        return acc;
    }, {});

    const baseBtn = `
        inline-flex items-center justify-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold
        transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
    `;

    // Render grouped data
    Object.values(grouped).forEach(group => {
        // Render obat list dengan actions per baris obat
        const detailRows = group.details.map(detail => {
            let detailActions = "";
            
            if (window.currentUserRole === "admin") {
                if (isActive) {
                    detailActions = `
                        <div class="flex gap-1 flex-wrap">
                            <button onclick="openEditModal(${detail.id}, '${group.pembelian.id}', '${group.obat.id}', '${detail.jumlah}', '${detail.harga_satuan}', '${detail.harga_beli}', '${detail.subtotal}')"
                                class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                                <i class='bx bx-edit-alt'></i> Edit
                            </button>
                            <button onclick="hapusDetailPembelianObat(${detail.id})"
                                class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                                <i class='bx bx-archive'></i> Archive
                            </button>
                        </div>
                    `;
                } else {
                    detailActions = `
                        <div class="flex gap-1 flex-wrap">
                            <button onclick="restoreDetailPembelianObat(${detail.id})"
                                class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                                <i class='bx bx-refresh'></i> Restore
                            </button>
                            <button onclick="forceDeleteDetailPembelianObat(${detail.id})"
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
                        <span class="font-semibold text-blue-600 dark:text-blue-400">${detail.nama_obat}</span>
                        <span class="text-sm ml-2">Jumlah: <strong>${detail.jumlah.toLocaleString("id-ID")}</strong></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">| ${detail.harga_satuan}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">| ${detail.harga_beli}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">| ${detail.subtotal}</span>
                    </div>
                    ${window.currentUserRole === "admin" ? detailActions : ""}
                </div>
            `;
        }).join("");

        tbody.innerHTML += `
            <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800/50 dark:even:bg-gray-700/50 hover:bg-gray-300 dark:hover:bg-gray-600/50 align-top">
                <td class="p-4 text-center font-semibold align-middle">
                    <div class="flex flex-col gap-1 items-center">
                        ${group.ids.map(id => `<span class="rounded-full font-bold text-green-500 py-1 px-2 ">${id}</span>`).join("")}
                    </div>
                </td>
                <td class="p-4 text-center text-base border-x font-semibold align-middle">${group.pembelian?.id}</td>
                <td class="p-4">
                    ${detailRows}
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
        pageInfoEl.innerText = `Halaman ${pageInfo.currentPage || 1} dari ${pageInfo.lastPage || 1} (Total: ${pageInfo.total || 0})`;
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