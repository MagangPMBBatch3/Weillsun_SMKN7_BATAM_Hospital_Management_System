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
function searchPembelianObat() {
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
                allPembelianObatPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id
                            supplier_id
                            tanggal
                            total_biaya
                            status
                            supplier {
                                id
                                nama_supplier
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
        renderPembelianObatTable(
            dataActive?.data?.allPembelianObatPaginate || {},
            "dataPembelianObatAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allPembelianObatArchive(first: $first, page: $page, search: $search){
                    data { 
                            id
                            supplier_id
                            tanggal
                            total_biaya
                            status
                            supplier {
                                id
                                nama_supplier
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
        renderPembelianObatTable(
            dataArchive?.data?.allPembelianObatArchive || {},
            "dataPembelianObatArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

function formatNumber(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatNumber(value) {
    return value.replace(/\./g, "");
}

function filterAngka(str) {
    return str.replace(/[^0-9.]/g, "");
}

// Create
async function createPembelianObat() {
    const supplier_id = document.getElementById("create-supplier").value;
    const tanggal = document.getElementById("create-tanggal").value;
    const status = document.getElementById("create-status").value.trim();

    if (!supplier_id || !tanggal || !status)
        return alert("Please fill in all required fields!");

    showLoading();

    // Check untuk duplikat data
    const checkDuplicateQuery = `
        query($supplier_id: ID!, $tanggal: Date!) {
            checkDuplicatePembelianObat(supplier_id: $supplier_id, tanggal: $tanggal)
        }
    `;

    try {
        const checkRes = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: checkDuplicateQuery,
                variables: {
                    supplier_id,
                    tanggal,
                },
            }),
        });

        const checkData = await checkRes.json();
        const isDuplicate = checkData?.data?.checkDuplicatePembelianObat;

        if (isDuplicate) {
            hideLoading();
            return alert(
                "Data pembelian untuk supplier dan tanggal ini sudah ada di database!"
            );
        }

        // Jika tidak ada duplikat, lanjutkan create
        const mutationPembelianObat = `
            mutation($input: CreatePembelianObatInput!) {
                createPembelianObat(input: $input) {
                    id
                    supplier_id
                    tanggal
                    total_biaya
                    status
                    supplier {
                        id
                        nama_supplier
                    } 
                }
            }
        `;
        const variablesPembelianObat = {
            input: { supplier_id, tanggal, status },
        };

        const resPembelianObat = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationPembelianObat,
                variables: variablesPembelianObat,
            }),
        });

        const resultPembelianObat = await resPembelianObat.json();
        const dataPembelianObat =
            resultPembelianObat?.data?.createPembelianObat;

        if (dataPembelianObat) {
            window.dispatchEvent(
                new CustomEvent("close-modal", {
                    detail: "create-pembelianObat",
                })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultPembelianObat.errors);
            alert("Failed to create Tenaga Medis!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the user");
    } finally {
        hideLoading();
    }
}

function openEditModal(id, supplier_id, tanggal, total_biaya, status) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-supplier").value = supplier_id;
    document.getElementById("edit-tanggal").value = tanggal;
    document.getElementById("edit-total").value = formatNumber(total_biaya);
    document.getElementById("edit-status").value = status;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-pembelianObat" })
    );
}

// Update
async function updatePembelianObat() {
    const id = document.getElementById("edit-id").value;
    const supplier_id = document.getElementById("edit-supplier").value;
    const tanggal = document.getElementById("edit-tanggal").value.trim();
    const total = document
        .getElementById("edit-total")
        .value.replace(/\./g, "");
    const status = document.getElementById("edit-status").value;

    if (!id || !supplier_id || !tanggal || !status)
        return alert("Please fill in all required fields!");

    showLoading();

    // Check untuk duplikat data (exclude current record)
    const checkDuplicateQuery = `
        query($supplier_id: ID!, $tanggal: Date!, $exclude_id: ID) {
            checkDuplicatePembelianObat(supplier_id: $supplier_id, tanggal: $tanggal, exclude_id: $exclude_id)
        }
    `;

    try {
        const checkRes = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: checkDuplicateQuery,
                variables: {
                    supplier_id,
                    tanggal,
                    exclude_id: id,
                },
            }),
        });

        const checkData = await checkRes.json();
        const isDuplicate = checkData?.data?.checkDuplicatePembelianObat;

        if (isDuplicate) {
            hideLoading();
            return alert(
                "Data pembelian untuk supplier dan tanggal ini sudah ada di database!"
            );
        }

        // Jika tidak ada duplikat, lanjutkan update
        const mutation = `
            mutation($id: ID!, $input: UpdatePembelianObatInput!) {
                updatePembelianObat(id: $id, input: $input) {
                    id
                    supplier_id
                    tanggal
                    total_biaya
                    status
                    supplier {
                        id
                        nama_supplier
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
                    input: { supplier_id, tanggal, total, status },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-pembelianObat" })
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
    const totalInput = document.getElementById("edit-total");

    totalInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function renderPembelianObatTable(result, tableId, isActive) {
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
                <button onclick="openEditModal(${item.id}, '${item.supplier_id}', '${item.tanggal}', '${item.total_biaya}', '${item.status}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusPembelianObat(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restorePembelianObat(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeletePembelianObat(${item.id})"
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
                item.supplier?.nama_supplier || "-"
            }</td>
            <td class="p-4 text-center text-base font-semibold">${
                item.tanggal
            }</td>
            <td class="p-4 text-center text-base font-semibold">Rp ${item.total_biaya.toLocaleString(
                "id-ID"
            )}</td>
            <td class="p-4 text-center font-semibold capitalize">
                ${item.status}
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
async function hapusPembelianObat(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deletePembelianObat(id: $id){ id } }`;
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
async function restorePembelianObat(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restorePembelianObat(id: $id){ id } }`;
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
async function forceDeletePembelianObat(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeletePembelianObat(id: $id){ id } }`;
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
