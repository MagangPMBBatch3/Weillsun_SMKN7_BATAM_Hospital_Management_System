<div id="paginationActive" class="flex justify-between px-6 pb-4 items-center mt-4">
    <div id="pageInfo" class="text-sm text-gray-600 dark:text-gray-300"></div>

    <div class="flex items-center gap-4">
        <select id="perPage" onchange="loadDataPaginate(1, true)" class="border py-1 px-5 rounded-full">
            <option value="5" selected>5</option>
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>

        <div class="flex gap-2">
            <button id="prevBtn" onclick="prevPage()"
                class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                <i class='text-white bx bx-arrow-big-left-line font-medium text-lg'></i>
            </button>
            <button id="nextBtn" onclick="nextPage()"
                class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                <i class='text-white bx bx-arrow-big-right-line font-medium  text-lg'></i>
            </button>
        </div>
    </div>
</div>
