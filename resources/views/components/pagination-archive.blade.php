<div id="paginationArchive" class="hidden flex justify-between px-6 pb-4 items-center mt-4">
    <div id="pageInfoArchive" class="text-sm text-gray-600 dark:text-gray-300"></div>

    <div class="flex items-center gap-4">
        <select id="perPageArchive" onchange="loadDataPaginate(1, false)"
            class="border py-1 px-5 rounded-full">
            <option value="5" selected>5</option>
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>

            <div class="flex gap-2">
            <button id="prevBtnArchive" onclick="prevPageArchive()"
                class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                <i class='text-white bx bx-arrow-big-left-line font-medium text-lg'></i>
            </button>
            <button id="nextBtnArchive" onclick="nextPageArchive()"
                class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                <i class='text-white bx bx-arrow-big-right-line font-medium  text-lg'></i>
            </button>
        </div>
    </div>
</div>
