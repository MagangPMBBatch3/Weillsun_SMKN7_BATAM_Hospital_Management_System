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

// Pagination controls
function prevPage() {
    if (currentPageActive > 1) {
        loadDataPaginate(currentPageActive - 1, true);
    }
}

function nextPage() {
    loadDataPaginate(currentPageActive + 1, true);
}

function prevPageArchive() {
    if (currentPageArchive > 1) {
        loadDataPaginate(currentPageArchive - 1, false);
    }
}

function nextPageArchive() {
    loadDataPaginate(currentPageArchive + 1, false);
}

// Search functionality
let searchTimeout = null;
function searchUsersProfile() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1, true);
        loadDataPaginate(1, false);
    }, 500);
}

// Load data UsersProfile (Aktif & Arsip sekaligus dengan Pagination)
async function loadDataPaginate(page = 1, isActive = true) {
    showLoading();

    if (isActive) {
        currentPageActive = page;
    } else {
        currentPageArchive = page;
    }

    const perPage = isActive
        ? document.getElementById("perPage")?.value || 6
        : document.getElementById("perPageArchive")?.value || 6;
    const searchValue = document.getElementById("search")?.value.trim() || "";

    try {
        // --- Query data Aktif ---
        const queryActive = `
            query($first: Int, $page: Int, $search: String) {
                allUsersProfilePaginate(first: $first, page: $page, search: $search) {
                    data {
                        id
                        user_id
                        nickname
                        
                        alamat
                        foto
                        telepon
                        user { role email }
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
                    : document.getElementById("perPage")?.value || 6
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

        if (dataActive.errors) {
            console.error("GraphQL Errors (Active):", dataActive.errors);
            throw new Error(dataActive.errors[0].message);
        }

        renderUsersProfileTable(
            dataActive?.data?.allUsersProfilePaginate || {},
            "dataUsersProfile",
            true
        );

        // --- Query data Arsip ---
        const queryArchive = `
            query($first: Int, $page: Int, $search: String) {
                allUsersProfileArchive(first: $first, page: $page, search: $search) {
                    data {
                        id
                        user_id
                        nickname
                        alamat
                        foto
                        telepon
                        user { role email }
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
                    : document.getElementById("perPageArchive")?.value || 6
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

        if (dataArchive.errors) {
            console.error("GraphQL Errors (Archive):", dataArchive.errors);
            throw new Error(dataArchive.errors[0].message);
        }

        renderUsersProfileTable(
            dataArchive?.data?.allUsersProfileArchive || {},
            "dataUsersProfileArchive",
            false
        );
    } catch (error) {
        console.error("Error loading data:", error);
        alert("An error occurred while loading data: " + error.message);
    } finally {
        hideLoading();
    }
}

// Render table UsersProfile
function renderUsersProfileTable(result, tableId, isActive) {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-white bg-red-500 rounded-md px-6 py-2 w-fit mx-auto text-lg italic font-semibold capitalize">No data available.</p>
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

    items.forEach((item) => {
        let actions = "";
        if (isActive) {
            actions = `
                <a onclick='openEditUsersProfileModal(${item.id}, "${
                item.nickname
            }", "${item.user?.email || ""}", "${item.telepon || ""}", "${
                item.alamat || ""
            }", "${item.user?.role || ""}")'>
                    <i class='bx bx-edit text-xl px-2 text-white bg-green-600 hover:text-green-600 hover:border-2 hover:border-green-600 rounded-full py-1 hover:bg-white hover:border-dashed'></i>
                </a>
                
                <a onclick="archiveUsersProfile(${item.id})">
                    <i class='bx bx-archive-arrow-down text-xl px-2 text-white bg-yellow-500 hover:text-yellow-600 hover:border-2 hover:border-yellow-600 rounded-full py-1 hover:bg-white hover:border-dashed'></i>
                </a>
            `;
        } else {
            actions = `
                <a onclick="restoreUsersProfile(${item.id})">
                    <i class='bx bx-undo text-xl px-2 text-white bg-yellow-600 hover:text-yellow-600 hover:border-2 hover:border-yellow-600 rounded-full py-1 hover:bg-white hover:border-dashed'></i>
                </a>
                
                <a onclick="forceDeleteUsersProfile(${item.id})">
                    <i class='bx bx-trash text-xl px-2 text-white bg-red-600 hover:text-red-600 hover:border-2 hover:border-red-600 rounded-full py-1 hover:bg-white hover:border-dashed'></i>
                </a>
            `;
        }

        tbody.innerHTML += `
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden hover:shadow-2xl transition duration-300 w-[12rem]">
                <div class="flex flex-col items-center p-4">
                    <div class="relative w-28 h-28 mx-auto">
                        <img src="${
                            item.foto
                                ? "/storage/" + item.foto
                                : "/default_pp.jpg"
                        }" 
                            id="usersProfileFotoPreview-${item.id}" 
                                class="w-28 h-28 rounded-full mx-auto object-cover" 
                                alt="User Photo">

                        <button onclick="handleUpdateFoto(${item.id})"
                            class="absolute -bottom-1 right-2 w-8 h-8 flex transition text-white hover:text-black items-center justify-center border-4 border-white bg-black rounded-full cursor-pointer hover:bg-gray-200">
                            <i class='bx bxs-edit-alt text-md'></i>
                        </button>

                        <input type="file" id="addUsersProfileFoto-${
                            item.id
                        }" name="foto" class="hidden" accept="image/*" />
                    </div>


                    <div class="text-center mt-3">
                        <p class="font-semibold text-lg text-gray-800 truncate">${
                            item.nickname || "-"
                        }</p>
                        <p class="text-sm text-gray-500 truncate max-w-44">${
                            item.user?.email || "-"
                        }</p>
                        <p class="text-sm capitalize tracking-widest mt-2 text-gray-400">- ${
                            item.user?.role || "-"
                        } -</p>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-gray-300 px-4 py-3 bg-gray-50">
                    <div class="flex justify-center gap-4">
                        ${actions}
                    </div>
                </div>
            </div>
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

// Handle update foto
function handleUpdateFoto(id) {
    const fileInput = document.getElementById(`addUsersProfileFoto-${id}`);
    const imgPreview = document.getElementById(`usersProfileFotoPreview-${id}`);

    fileInput.click();

    fileInput.onchange = async () => {
        const file = fileInput.files[0];
        if (file) {
            showLoading();

            const filename = await uploadFoto(file, id);
            if (filename) {
                imgPreview.src = "/storage/" + filename;
            } else {
                alert("Failed to upload photo.");
            }

            hideLoading();
        }
    };
}

// Upload foto
async function uploadFoto(file, id) {
    const formData = new FormData();
    formData.append("foto", file);
    formData.append("id", id);

    try {
        const response = await fetch("/upload-foto", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: formData,
        });

        const result = await response.json();
        return result.filename;
    } catch (err) {
        console.error("Upload gagal:", err);
        return null;
    }
}

// Archive UsersProfile
async function archiveUsersProfile(id) {
    if (!confirm("Move to archive?")) return;

    showLoading();

    const mutation = `
        mutation($id: ID!) {
            deleteUsersProfile(id: $id) {
                id
            }
        }
    `;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: { id },
            }),
        });

        const result = await response.json();

        if (result.errors) {
            throw new Error(result.errors[0].message);
        }

        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to archive data: " + error.message);
        hideLoading();
    }
}

// Restore UsersProfile
async function restoreUsersProfile(id) {
    if (!confirm("Restore this data?")) return;

    showLoading();

    const mutation = `
        mutation($id: ID!) {
            restoreUsersProfile(id: $id) {
                id
            }
        }
    `;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: { id },
            }),
        });

        const result = await response.json();

        if (result.errors) {
            throw new Error(result.errors[0].message);
        }

        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to restore data: " + error.message);
        hideLoading();
    }
}

// Force delete UsersProfile
async function forceDeleteUsersProfile(id) {
    if (!confirm("Permanently delete this data?")) return;

    showLoading();

    const mutation = `
        mutation($id: ID!) {
            forceDeleteUsersProfile(id: $id) {
                id
            }
        }
    `;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: { id },
            }),
        });

        const result = await response.json();

        if (result.errors) {
            throw new Error(result.errors[0].message);
        }

        loadDataPaginate(currentPageArchive, false);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to delete data: " + error.message);
        hideLoading();
    }
}

async function openEditUsersProfileModal(
    id,
    nickname,
    email,
    telepon,
    alamat,
    role
) {
    try {
        // Set form values
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-nickname").value = nickname;
        document.getElementById("edit-email").value = email || "";
        document.getElementById("edit-phone").value = telepon || "";
        document.getElementById("edit-address").value = alamat || "";
        document.getElementById("edit-role").value = role || "";

        // Open modal using Alpine.js event
        window.dispatchEvent(
            new CustomEvent("open-modal", { detail: "edit-user-profile" })
        );
    } catch (error) {
        console.error("Error opening modal:", error);
        alert("Failed to open edit form");
    }
}

async function updateUsersProfile() {
    const id = document.getElementById("edit-id").value;
    const nickname = document.getElementById("edit-nickname").value.trim();
    const telepon = document.getElementById("edit-phone").value.trim();
    const alamat = document.getElementById("edit-address").value.trim();

    if (!nickname) {
        alert("Name is required!");
        return;
    }

    showLoading();

    const mutation = `
        mutation($id: ID!, $input: UpdateUsersProfileInput!) {
            updateUsersProfile(id: $id, input: $input) {
                id
                nickname
                telepon
                alamat

            }
        }
    `;

    const variables = {
        id: id,
        input: {
            nickname,
            telepon,
            alamat,
        },
    };

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query: mutation,
                variables: variables,
            }),
        });

        const result = await response.json();

        if (result.errors) {
            throw new Error(result.errors[0].message);
        }

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "edit-user-profile" })
        );

        loadDataPaginate(currentPageActive, true);
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to update data: " + error.message);
    } finally {
        hideLoading();
    }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    loadDataPaginate(1, true);
});
