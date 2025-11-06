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
function searchObat() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// Load data Obat (Aktif & Arsip sekaligus)
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
                allObatPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id nama_obat jenis_obat stok harga markup_persen harga_jual
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
        renderObatTable(
            dataActive?.data?.allObatPaginate || {},
            "dataObatAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allObatArchive(first: $first, page: $page, search: $search){
                    data { 
                            id nama_obat jenis_obat stok harga markup_persen harga_jual
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
        renderObatTable(
            dataArchive?.data?.allObatArchive || {},
            "dataObatArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data");
    } finally {
        hideLoading();
    }
}

// Create
async function createObat() {
    const nama_obat = document.getElementById("create-nama_obat").value.trim();
    const jenis_obat = document
        .getElementById("create-jenis_obat")
        .value.trim();
    const stok = document
        .getElementById("create-stok")
        .value.replace(/\./g, "");
    const harga = document
        .getElementById("create-harga")
        .value.replace(/\./g, "");
    const markup_persen = document
        .getElementById("create-markup_persen")
        .value.replace(/\./g, "");
    const harga_jual = document
        .getElementById("create-harga_jual")
        .value.replace(/\./g, "");

    if (!nama_obat || !jenis_obat || !stok || !harga || !markup_persen)
        return alert("Please fill in all required fields!");

    showLoading();

    const mutationObat = `
        mutation($input: CreateObatInput!) {
            createObat(input: $input) {
                id
                nama_obat
                jenis_obat
                stok
                harga
                markup_persen
                harga_jual
            }
        }
    `;
    const variablesObat = {
        input: {
            nama_obat,
            jenis_obat,
            stok: parseInt(stok),
            harga: parseFloat(harga),
            markup_persen: parseFloat(markup_persen),
            harga_jual: parseFloat(harga_jual),
        },
    };

    try {
        const resObat = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationObat,
                variables: variablesObat,
            }),
        });

        const resultObat = await resObat.json();
        const dataObat = resultObat?.data?.createObat;

        if (dataObat) {
            window.dispatchEvent(
                new CustomEvent("close-modal", { detail: "create-obat" })
            );
            loadDataPaginate(currentPageActive, true);
        } else {
            console.error("GraphQL Error:", resultObat.errors);
            alert("Failed to create Patient!");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while creating the medicine");
    } finally {
        hideLoading();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const hargaInput = document.getElementById("create-harga");
    const markupInput = document.getElementById("create-markup_persen");
    const hargaJualInput = document.getElementById("create-harga_jual");
    const stokInput = document.getElementById("create-stok");

    function formatNumber(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function unformatNumber(value) {
        return value.replace(/\./g, "");
    }

    function hitungHargaJual() {
        const harga = parseFloat(unformatNumber(hargaInput.value)) || 0;
        const markup = parseFloat(unformatNumber(markupInput.value)) || 0;
        const hargaJual = harga + (harga * markup) / 100;
        hargaJualInput.value = formatNumber(hargaJual.toFixed(0));
    }

    function filterAngka(str) {
        // hapus semua karakter selain angka dan titik
        return str.replace(/[^0-9.]/g, "");
    }

    // --- EVENT UNTUK HARGA ---
    hargaInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
        hitungHargaJual();
    });

    // --- EVENT UNTUK MARKUP ---
    markupInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));

        if (value) {
            let num = parseFloat(value);
            if (num < 1) num = 1;
            if (num > 100) num = 100;
            e.target.value = formatNumber(num);
        } else {
            e.target.value = "";
        }

        hitungHargaJual();
    });

    // --- EVENT UNTUK STOK ---
    stokInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function openEditModal(
    id,
    nama_obat,
    jenis_obat,
    stok,
    harga,
    markup_persen,
    harga_jual
) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-nama_obat").value = nama_obat;
    document.getElementById("edit-jenis_obat").value = jenis_obat;
    document.getElementById("edit-stok").value = stok;
    document.getElementById("edit-harga").value = harga;
    document.getElementById("edit-markup_persen").value = markup_persen;
    document.getElementById("edit-harga_jual").value = harga_jual;
    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-obat" })
    );
}

// Update
async function updateObat() {
    const id = document.getElementById("edit-id").value;
    const nama_obat = document.getElementById("edit-nama_obat").value.trim();
    const jenis_obat = document.getElementById("edit-jenis_obat").value.trim();
    const stok = document.getElementById("edit-stok").value.replace(/\./g, "");
    const harga = document
        .getElementById("edit-harga")
        .value.replace(/\./g, "");
    const markup_persen = document
        .getElementById("edit-markup_persen")
        .value.replace(/\./g, "");
    const harga_jual = document
        .getElementById("edit-harga_jual")
        .value.replace(/\./g, "");

    if (!nama_obat || !jenis_obat || !stok || !harga || !markup_persen)
        return alert("Please fill in all required fields!");
    showLoading();

    const mutation = `mutation($id: ID!, $input: UpdateObatInput!) { updateObat(id: $id, input: $input) 
                        { 
                            id 
                            nama_obat
                            jenis_obat
                            stok
                            harga
                            markup_persen
                            harga_jual
                        } 
                    }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: {
                    id,
                    input: {
                        nama_obat,
                        jenis_obat,
                        stok: parseInt(stok),
                        harga: parseFloat(harga),
                        markup_persen: parseFloat(markup_persen),
                        harga_jual: parseFloat(harga_jual),
                    },
                },
            }),
        });
        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-obat" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data");
        hideLoading();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const editHargaInput = document.getElementById("edit-harga");
    const editMarkupInput = document.getElementById("edit-markup_persen");
    const editHargaJualInput = document.getElementById("edit-harga_jual");
    const editStokInput = document.getElementById("edit-stok");

    function formatNumber(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function unformatNumber(value) {
        return value.replace(/\./g, "");
    }

    function filterAngka(str) {
        // hanya izinkan angka dan titik
        return str.replace(/[^0-9.]/g, "");
    }

    function hitungEditHargaJual() {
        const harga = parseFloat(unformatNumber(editHargaInput.value)) || 0;
        const markup = parseFloat(unformatNumber(editMarkupInput.value)) || 0;
        const hargaJual = harga + (harga * markup) / 100;
        editHargaJualInput.value = formatNumber(hargaJual.toFixed(0));
    }

    // --- EVENT UNTUK EDIT HARGA ---
    editHargaInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
        hitungEditHargaJual();
    });

    // --- EVENT UNTUK EDIT MARKUP ---
    editMarkupInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));

        if (value) {
            let num = parseFloat(value);
            if (num < 1) num = 1;
            if (num > 100) num = 100;
            e.target.value = formatNumber(num);
        } else {
            e.target.value = "";
        }

        hitungEditHargaJual();
    });

    // --- EVENT UNTUK EDIT STOK ---
    editStokInput.addEventListener("input", (e) => {
        let value = unformatNumber(filterAngka(e.target.value));
        if (value) e.target.value = formatNumber(value);
        else e.target.value = "";
    });
});

function renderObatTable(result, tableId, isActive) {
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

    items.forEach((item) => {
        let actions = "";
        const baseBtn = `
        inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-md font-semibold
        transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
    `;

        if (window.currentUserRole === "admin") {
            if (isActive) {
                actions = `
                <button onclick="openEditModal(${item.id}, '${item.nama_obat}', '${item.jenis_obat}', '${item.stok}', '${item.harga}', '${item.markup_persen}', '${item.harga_jual}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusObat(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreObat(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteObat(${item.id})"
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
                item.nama_obat
            }</td>
            <td class="p-4 text-center text-base font-semibold">
                ${item.jenis_obat}
            </td>
            
            <td class="p-4 text-center font-semibold capitalize">
                ${item.stok.toLocaleString("id-ID")}
            </td>

            <td class="p-4 text-center text-base font-semibold">
                Rp ${item.harga.toLocaleString("id-ID")}
            </td>
            <td class="p-4 text-center text-base font-semibold">
                ${item.markup_persen.toLocaleString("id-ID")}%
            </td>
            <td class="p-4 text-center text-base font-semibold">
                Rp ${item.harga_jual.toLocaleString("id-ID")}
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
async function hapusObat(id) {
    if (!confirm("Are you sure you want to add to the archive??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteObat(id: $id){ id } }`;
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
async function restoreObat(id) {
    if (!confirm("Are you sure you want to restore this data?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreObat(id: $id){ id } }`;
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
async function forceDeleteObat(id) {
    if (!confirm("Are you sure you want to delete this data??")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteObat(id: $id){ id } }`;
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
