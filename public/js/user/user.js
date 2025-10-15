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
                allUserPaginate(first: $first, page: $page, search: $search){
                    data { 
                            id name email role 
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
        renderUserTable(
            dataActive?.data?.allUserPaginate || {},
            "dataUserAktif",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allUserArchive(first: $first, page: $page, search: $search){
                    data { id name email role }
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
        renderUserTable(
            dataArchive?.data?.allUserArchive || {},
            "dataUserArsip",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("Terjadi kesalahan saat memuat data");
    } finally {
        hideLoading();
    }
}

// Hapus
async function hapusUser(id) {
    if (!confirm("Yakin ingin masukkan ke archive?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ deleteUser(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Gagal menghapus data");
        hideLoading();
    }
}

// restore
async function restoreUser(id) {
    if (!confirm("Yakin ingin restore data ini?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ restoreUser(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Gagal restore data");
        hideLoading();
    }
}

// force delete
async function forceDeleteUser(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;

    showLoading();
    const mutation = `mutation($id: ID!){ forceDeleteUser(id: $id){ id } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query: mutation, variables: { id } }),
        });
        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Gagal menghapus data permanen");
        hideLoading();
    }
}

// Create
async function createUser() {
    const name = document.getElementById("create-name").value.trim();
    const email = document.getElementById("create-email").value.trim();
    const password = document.getElementById("create-password").value.trim();
    const role = document.getElementById("create-role").value;

    if (!name || !email || !password) return alert("Semua field wajib diisi!");

    showLoading();

    const mutationUser = `
        mutation($input: CreateUserInput!) {
            createUser(input: $input) {
                id name email role
            }
        }
    `;
    const variablesUser = { input: { name, email, password, role } };

    try {
        const resUser = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationUser,
                variables: variablesUser,
            }),
        });

        const resultUser = await resUser.json();
        const newUser = resultUser?.data?.createUser;

        if (!newUser) {
            alert("Gagal membuat user");
            hideLoading();
            return;
        }

        const mutationProfile = `
            mutation($input: CreateUsersProfileInput!) {
                createUsersProfile(input: $input) {
                    id user_id nama
                }
            }
        `;
        const variablesProfile = {
            input: {
                user_id: newUser.id,
                nama: newUser.name,
                email: null,
                telepon: null,
                alamat: null,
                foto: null,
            },
        };

        const resProfile = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutationProfile,
                variables: variablesProfile,
            }),
        });

        const resultProfile = await resProfile.json();
        if (!resultProfile?.data?.createUsersProfile) {
            alert("User berhasil dibuat, tapi gagal membuat profile");
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-user" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat membuat user");
        hideLoading();
    }
}

// Update
async function updateUser() {
    const id = document.getElementById("edit-id").value;
    const name = document.getElementById("edit-name").value.trim();
    const email = document.getElementById("edit-email").value.trim();
    const role = document.getElementById("edit-role").value;

    showLoading();

    const mutation = `mutation($id: ID!, $input: UpdateUserInput!) { updateUser(id: $id, input: $input) { id name email role } }`;
    try {
        await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: { id, input: { name, email, role } },
            }),
        });
        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-user" })
        );
        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Gagal update data");
        hideLoading();
    }
}

function renderUserTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);

    tbody.innerHTML = "";
    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="text-center">
                <td class="px-6 py-4 text-red-500" colspan="5">Data tidak ditemukan</td>
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
                <button onclick="openEditModal(${item.id}, '${item.name}', '${item.email}', '${item.role}')"
                    class="${baseBtn} bg-indigo-100 text-indigo-700 hover:bg-indigo-200 focus:ring-indigo-300">
                    <i class='bx bx-edit-alt'></i> Edit
                </button>
                <button onclick="hapusUser(${item.id})"
                    class="${baseBtn} bg-rose-100 text-rose-700 hover:bg-rose-200 focus:ring-rose-300">
                    <i class='bx bx-archive'></i> Archive
                </button>`;
            } else {
                actions = `
                <button onclick="restoreUser(${item.id})"
                    class="${baseBtn} bg-emerald-100 text-emerald-700 hover:bg-emerald-200 focus:ring-emerald-300">
                    <i class='bx bx-refresh-ccw-alt'></i>  Restore
                </button>
                <button onclick="forceDeleteUser(${item.id})"
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
                item.name
            }</td>
            <td class="p-4 text-center text-base font-semibold">${
                item.email
            }</td>
            <td class="p-4 text-center font-semibold capitalize">
                <span class="px-2 py-1 rounded-full text-sm ${
                    item.role === "admin"
                        ? "bg-yellow-100 text-yellow-600"
                        : "bg-blue-100 text-blue-600"
                    }">
                    ${item.role}
                </span>
            </td>
            ${
                window.currentUserRole === "admin"
                    ? `<td class="p-4 text-center space-x-1">${actions}</td>`
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
function searchUser() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

function openEditModal(id, name, email, role) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-name").value = name;
    document.getElementById("edit-email").value = email;
    document.getElementById("edit-role").value = role;
    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-user" })
    );
}

document.addEventListener("DOMContentLoaded", () => loadDataPaginate(1, true));
