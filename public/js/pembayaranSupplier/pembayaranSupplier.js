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

// ============================================================================================== \\

let searchTimeout = null;
function searchPembayaranSupplier() {
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
                allPembayaranSupplierPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembelian_id
                            jumlah_bayar
                            metode_bayar
                            tanggal_bayar
                            pembelianObat {
                                id
                                total_biaya
                                supplier {
                                    id
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
        renderPembayaranSupplierTable(
            dataActive?.data?.allPembayaranSupplierPaginate || {},
            "dataPembayaranSupplierAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allPembayaranSupplierArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            pembelian_id
                            jumlah_bayar
                            metode_bayar
                            tanggal_bayar
                            pembelianObat {
                                id
                                total_biaya
                                supplier {
                                    id
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
        renderPembayaranSupplierTable(
            dataArchive?.data?.allPembayaranSupplierArchive || {},
            "dataPembayaranSupplierArsip",
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
async function createPembayaranSupplier() {
    const pembelian_id = document.getElementById("create-supplier").value;
    const jumlah_bayar = document.getElementById("create-jumlah-bayar").value.replace(/\./g, "");
    const metode_bayar = document.getElementById("create-metode-bayar").value.trim();
    const tanggal_bayar = document.getElementById("create-tanggal").value.trim();

    if (!pembelian_id || !metode_bayar || !tanggal_bayar)
        return alert("Please fill in all required fields!");

    showLoading();

    // Check untuk duplikat data
    const checkDuplicateQuery = `
        query($pembelian_id: ID!, $tanggal_bayar: Date!) {
            checkDuplicatePembayaranSupplier(pembelian_id: $pembelian_id, tanggal_bayar: $tanggal_bayar)
        }
    `;

    try {
        const checkRes = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: checkDuplicateQuery,
                variables: {
                    pembelian_id,
                    jumlah_bayar,
                    metode_bayar,
                    tanggal_bayar,
                },
            }),
        });

        const checkData = await checkRes.json();
        const isDuplicate = checkData?.data?.checkDuplicatePembayaranSupplier;

        if (isDuplicate) {
            hideLoading();
            return alert(
                "Data pembayaran untuk supplier dan tanggal ini sudah ada!"
            );
        }

        // Jika tidak ada duplikat, lanjutkan create
        const mutationPembayaranSupplier = `
            mutation($input: CreatePembayaranSupplierInput!) {
                createPembayaranSupplier(input: $input) {
                            id
                            pembelian_id
                            jumlah_bayar
                            metode_bayar
                            tanggal_bayar
                            pembelianObat {
                                id
                                total_biaya
                                supplier {
                                    id
                                    nama_supplier
                                }
                            }
                }
            }
        `;
        const variablesPembayaranSupplier = {
            input: {
                pembelian_id,
                jumlah_bayar: parseInt(jumlah_bayar),
                metode_bayar,
                tanggal_bayar,
            },
        };

        const resPembayaranSupplier = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationPembayaranSupplier,
                variables: variablesPembayaranSupplier,
            }),
        });

        const resultPembayaranSupplier = await resPembayaranSupplier.json();
        const dataPembayaranSupplier =
            resultPembayaranSupplier?.data?.createPembayaranSupplier;

        if (dataPembayaranSupplier) {
            window.dispatchEvent(
                new CustomEvent("close-modal", {
                    detail: "create-pembayaranSupplier",
                })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultPembayaranSupplier.errors);
            alert("Failed to create data!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the user");
    } finally {
        hideLoading();
    }
}

function openEditModal(
    id,
    pembelian_id,
    metode_bayar,
    tanggal_bayar
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-supplier").value = pembelian_id;
    document.getElementById("edit-metode-bayar").value = metode_bayar;
    document.getElementById("edit-tanggal").value = tanggal_bayar;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-pembayaranSupplier" })
    );
}

// Update
async function updatePembayaranSupplier() {
    const id = document.getElementById("edit-id").value;
    const pembelian_id = document.getElementById("edit-supplier").value;
    const metode_bayar = document.getElementById("edit-metode-bayar").value.trim();
    const tanggal_bayar = document.getElementById("edit-tanggal").value.trim();

    showLoading();

    // Check untuk duplikat data (exclude current record)
    const checkDuplicateQuery = `
        query($pembelian_id: ID!, $tanggal_bayar: Date!, $exclude_id: ID) {
            checkDuplicatePembayaranSupplier(pembelian_id: $pembelian_id, tanggal_bayar: $tanggal_bayar, exclude_id: $exclude_id)
        }
    `;

    try {
        const checkRes = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: checkDuplicateQuery,
                variables: {
                    pembelian_id,
                    tanggal_bayar,
                    exclude_id: id,
                },
            }),
        });

        const checkData = await checkRes.json();
        const isDuplicate = checkData?.data?.checkDuplicatePembayaranSupplier;

        if (isDuplicate) {
            hideLoading();
            return alert(
                "Data pembayaran untuk supplier dan tanggal ini sudah ada!"
            );
        }

        // Jika tidak ada duplikat, lanjutkan update
        const mutation = `
            mutation($id: ID!, $input: UpdatePembayaranSupplierInput!) {
                updatePembayaranSupplier(id: $id, input: $input) {
                    id
                            pembelian_id
                            
                            metode_bayar
                            tanggal_bayar
                            pembelianObat {
                                id
                                total_biaya
                                supplier {
                                    id
                                    nama_supplier
                                }
                            }
                }
            }
        `;

        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: {
                    id,
                    input: {
                        pembelian_id,
                        metode_bayar,
                        tanggal_bayar
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-pembayaranSupplier" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
    } finally {
        hideLoading();
    }
}

document.addEventListener("change", function (e) {

    if (e.target.id === "create-supplier") {
        const selectedOption = e.target.selectedOptions[0];
        const bayar = selectedOption.getAttribute("data-jumlah");


        document.getElementById("create-jumlah-bayar").value =
            bayar ? formatNumber(parseInt(bayar)) : "";
    }

    // if (e.target.id === "edit-supplier") {
    //     const selectedOption = e.target.selectedOptions[0];
    //     const bayar = selectedOption.getAttribute("data-jumlah");

    //     document.getElementById("edit-jumlah-bayar").value =
    //         bayar ? formatNumber(parseInt(bayar)) : "";

    // }
});

document.addEventListener("DOMContentLoaded", () => {
    const createJumlahBayarInput = document.getElementById("create-jumlah-bayar");
    // const editJumlahBayarInput = document.getElementById("edit-jumlah-bayar");

    // editJumlahBayarInput.addEventListener("input", (e) => {
    //     let value = unformatNumber(filterAngka(e.target.value));
    //     if (value) e.target.value = formatNumber(value);
    //     else e.target.value = "";
    // });

    createJumlahBayarInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function renderPembayaranSupplierTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);

    tbody.innerHTML = "";
    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 font-semibold text-lg italic text-red-500 capitalize" colspan="6">No data available.</td>
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
                <button onclick="openEditModal(${item.id}, '${item.pembelian_id}', '${item.metode_bayar}', '${item.tanggal_bayar}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusPembayaranSupplier(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restorePembayaranSupplier(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeletePembayaranSupplier(${item.id})"
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
                item.pembelianObat?.supplier?.nama_supplier || "-"
            }</td>
            <td class="p-4 text-center text-base font-semibold">${item.jumlah_bayar.toLocaleString(
                "id-ID"
            )}</td>
            <td class="p-4 text-center truncate max-w-24 font-semibold capitalize">
                <span class="${
                    item.metode_bayar === "transfer"
                        ? "text-blue-600 bg-blue-100 border border-blue-300"
                        : "text-rose-600 bg-rose-100 border border-rose-300"
                } px-3 py-1 rounded-full">
                    ${item.metode_bayar}
                </span>
            </td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.tanggal_bayar}
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
async function hapusPembayaranSupplier(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deletePembayaranSupplier(id: $id){ id } }`;
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
async function restorePembayaranSupplier(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restorePembayaranSupplier(id: $id){ id } }`;
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
async function forceDeletePembayaranSupplier(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeletePembayaranSupplier(id: $id){ id } }`;
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
