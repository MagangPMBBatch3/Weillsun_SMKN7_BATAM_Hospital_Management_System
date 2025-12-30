const API_URL = "/graphql";
let currentPageActive = 1;
let existingDoctorIds = [];

/* ======================================================
   LOADING
====================================================== */
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

/* ======================================================
   PAGINATION
====================================================== */
function prevPage() {
    if (currentPageActive > 1) {
        loadDataPaginate(currentPageActive - 1);
    }
}

function nextPage() {
    loadDataPaginate(currentPageActive + 1);
}

/* ======================================================
   SEARCH
====================================================== */
let searchTimeout = null;
function searchJadwalTenagaMedis() {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDataPaginate(1);
    }, 500);
}

/* ======================================================
   LOAD DATA
====================================================== */
async function loadDataPaginate(page = 1) {
    showLoading();
    currentPageActive = page;

    const perPage = document.getElementById("perPage")?.value || 5;
    const searchValue = document.getElementById("search")?.value.trim() || "";

    try {
        const query = `
            query ($first: Int, $page: Int, $search: String) {
                allJadwalTenagaMedisPaginate(
                    first: $first
                    page: $page
                    search: $search
                ) {
                    data {
                        id
                        hari
                        jam_mulai
                        jam_selesai
                        tenagaMedis {
                            id
                            profile {
                                nickname
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

        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                query,
                variables: {
                    first: parseInt(perPage),
                    page: currentPageActive,
                    search: searchValue,
                },
            }),
        });

        const json = await res.json();
        renderJadwalTenagaMedisTable(
            json?.data?.allJadwalTenagaMedisPaginate || {},
            "dataJadwalTenagaMedisAktif"
        );
    } catch (error) {
        console.error(error);
        alert("Failed load data");
    } finally {
        hideLoading();
    }
}

/* ======================================================
   GROUP DATA BY DOCTOR
====================================================== */

function groupByDoctor(items) {
    const grouped = {};

    items.forEach((item) => {
        const dokterId = item.tenagaMedis.id;

        if (!grouped[dokterId]) {
            grouped[dokterId] = {
                tenaga_medis_id: dokterId,
                dokter: item.tenagaMedis.profile.nickname,
                jadwal: {},
            };
        }

        grouped[dokterId].jadwal[item.hari] = item;
    });

    return Object.values(grouped);
}

async function createJam() {
    const tenaga_medis_id = document.getElementById(
        "jam-tenaga_medis_id"
    ).value;
    const hari = document.getElementById("jam-hari").value;
    const jam_mulai = document.getElementById("jam-mulai").value;
    const jam_selesai = document.getElementById("jam-selesai").value;

    if (!tenaga_medis_id || !hari || !jam_mulai || !jam_selesai) {
        alert("Please complete all fields");
        return;
    }

    showLoading();

    const mutation = `
        mutation ($input: CreateJadwalTenagaMedisInput!) {
            createJadwalTenagaMedis(input: $input) {
                id
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
                    input: {
                        tenaga_medis_id: parseInt(tenaga_medis_id),
                        hari: parseInt(hari),
                        jam_mulai,
                        jam_selesai,
                    },
                },
            }),
        });

        // tutup modal
        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-jam" })
        );

        // reload table
        loadDataPaginate(currentPageActive);
    } catch (error) {
        console.error(error);
        alert("Failed create schedule");
    } finally {
        hideLoading();
    }
}

/* ======================================================
   RENDER TABLE
====================================================== */
function renderJadwalTenagaMedisTable(result, tableId) {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = "";

    const items = result.data || [];
    const pageInfo = result.paginatorInfo || {};

    if (!items.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-6 italic text-red-500">
                    No data available
                </td>
            </tr>
        `;
        return;
    }

    const grouped = groupByDoctor(items);
    existingDoctorIds = [];
    grouped.forEach((row) => {
        existingDoctorIds.push(String(row.tenaga_medis_id));
    });

    grouped.forEach((row) => {
        let cells = "";

        // LOOP HARI (1 = Senin, 7 = Minggu)
        for (let hari = 1; hari <= 7; hari++) {
            if (row.jadwal[hari]) {
                const j = row.jadwal[hari];

                cells += `
            <td class="text-center p-4">
                <div class="font-semibold">
                    ${j.jam_mulai} - ${j.jam_selesai}
                </div>

                ${
                    currentUserRole === "admin"
                        ? `
                            <div class="flex justify-center gap-2 mt-1">
                                <button
                                    onclick="openEditJamModal(${j.id}, '${j.jam_mulai}', '${j.jam_selesai}')"
                                    class="text-indigo-500">
                                    ✏️
                                </button>
                                <button
                                    onclick="deleteJadwal(${j.id})"
                                    class="text-red-500">
                                    🗑
                                </button>
                            </div>
                        `
                        : ``
                }
            </td>
        `;
            } else {
                cells += `
            <td class="text-center">
                ${
                    currentUserRole === "admin"
                        ? `
                            <button
                                onclick="openCreateJamModal(${row.tenaga_medis_id}, ${hari})"
                                class="text-green-600 text-xl font-bold">
                                +
                            </button>
                        `
                        : `-`
                }
            </td>
        `;
            }
        }

        tbody.innerHTML += `
            <tr>
                <td class="font-semibold p-4">
                    ${row.dokter}
                    <div>
                        <button
                            onclick="forceDeleteDokter(${row.tenaga_medis_id})"
                            class="text-red-500 text-sm mt-1">
                            Delete
                        </button>
                    </div>
                </td>
                ${cells}
            </tr>
        `;
    });

    // Pagination info
    document.getElementById(
        "pageInfo"
    ).innerText = `Page ${pageInfo.currentPage} of ${pageInfo.lastPage} (Total ${pageInfo.total})`;

    document.getElementById("prevBtn").disabled = pageInfo.currentPage <= 1;
    document.getElementById("nextBtn").disabled = !pageInfo.hasMorePages;
}

/* ======================================================
   CREATE DOCTOR ROW (MODAL CREATE)
====================================================== */
async function createJadwalTenagaMedis() {
    const tenaga_medis_id = document.getElementById("create-dokter_id").value;

    if (!tenaga_medis_id) {
        alert("Doctor required");
        return;
    }

    // 🚫 CEK DUPLIKAT
    if (existingDoctorIds.includes(String(tenaga_medis_id))) {
        alert("Doctor already exists!");
        return;
    }

    showLoading();

    const mutation = `
        mutation ($input: CreateJadwalTenagaMedisInput!) {
            createJadwalTenagaMedis(input: $input) {
                id
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
                    input: {
                        tenaga_medis_id,
                        hari: 1,
                        jam_mulai: "00:00",
                        jam_selesai: "00:00",
                    },
                },
            }),
        });

        window.dispatchEvent(
            new CustomEvent("close-modal", { detail: "create-kunjunganUlang" })
        );

        loadDataPaginate(1);
    } catch (e) {
        console.error(e);
    } finally {
        hideLoading();
    }
}

/* ======================================================
   CREATE JAM
====================================================== */
function openCreateJamModal(tenaga_medis_id, hari) {
    document.getElementById("jam-tenaga_medis_id").value = tenaga_medis_id;
    document.getElementById("jam-hari").value = hari;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "create-jam" })
    );
}

/* ======================================================
   EDIT JAM
====================================================== */
function openEditJamModal(id, jam_mulai, jam_selesai) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-jam_mulai").value = jam_mulai;
    document.getElementById("edit-jam_selesai").value = jam_selesai;

    window.dispatchEvent(
        new CustomEvent("open-modal", { detail: "edit-kunjunganUlang" })
    );
}

/* ======================================================
   DELETE JADWAL
====================================================== */
async function deleteJadwal(id) {
    if (!confirm("Delete this schedule?")) return;

    showLoading();

    const mutation = `
        mutation ($id: ID!) {
            forceDeleteJadwalTenagaMedis(id: $id) {
                id
            }
        }
    `;

    await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ query: mutation, variables: { id } }),
    });

    loadDataPaginate(currentPageActive);
}

/* ======================================================
   DELETE DOCTOR (ALL SCHEDULE)
====================================================== */
async function forceDeleteDokter(tenaga_medis_id) {
    if (!confirm("Delete all schedules for this doctor?")) return;

    showLoading();

    const mutation = `
        mutation ($id: ID!) {
            forceDeleteJadwalByTenagaMedis(tenaga_medis_id: $id)
        }
    `;

    await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            query: mutation,
            variables: { id: tenaga_medis_id },
        }),
    });

    hideLoading();
    loadDataPaginate(1);
}

/* ======================================================
   INIT
====================================================== */
document.addEventListener("DOMContentLoaded", () => {
    loadDataPaginate(1);
});
