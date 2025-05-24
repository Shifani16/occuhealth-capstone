<template>
    <div>
        <header
            class="w-full bg-[#CAEAF2] px-5 py-3 flex justify-between items-center fixed top-0 left-0 z-50"
        >
            <div class="flex items-center gap-6">
                <img
                    src="@/assets/occuhelp-full-logo.svg"
                    alt="OccuHelp Full Logo"
                    class="h-12"
                />
            </div>
        </header>

        <div class="mt-20 flex min-h-screen">
            <LeftBar active="Laporan" />

            <main class="flex-1 p-6">
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                    Laporan
                </h1>

                <div class="flex justify-end mb-6 mr-6"></div>

                <div
                    class="container-open-sans bg-[#F2FAFC] p-4 rounded-lg mx-7 flex items-center gap-4"
                >
                    <span class="min-w-41 text-[16px] font-semibold"
                        >Periode tanggal input:</span
                    >

                    <!-- Calendar Selection (From Date) -->
                    <div class="relative">
                        <input
                            type="date"
                            v-model="startDate"
                            class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded"
                        />
                        <img
                            src="@/assets/calendar-grouped.svg"
                            class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                            style="pointer-events: none"
                        />
                    </div>

                    <span class="text-[16px] font-semibold">Sampai</span>

                    <!-- Calendar Selection (To Date) -->
                    <div class="relative">
                        <input
                            type="date"
                            v-model="endDate"
                            class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded"
                        />
                        <img
                            src="@/assets/calendar-grouped.svg"
                            class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                            style="pointer-events: none"
                        />
                    </div>

                    <div class="ml-auto min-w-43">
                        <button
                            @mouseover="hoverLihat = true"
                            @mouseleave="hoverLihat = false"
                            class="border border-[#2293AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                            @click="generateReportWithCharts"
                            :disabled="isLoading || !startDate || !endDate"
                        >
                            <img src="@/assets/export.svg" class="h-5" />
                            {{ isLoading ? "Generating..." : "Export" }}
                        </button>
                    </div>
                </div>

                <div
                    id="charts-for-pdf"
                    style="position: absolute; left: -9999px; top: -9999px"
                >
                    <div v-if="chartDataMap">
                        <div
                            v-for="(chartData, category) in chartDataMap"
                            :key="category"
                            class="chart-container"
                            style="width: 800px; height: 500px"
                        >
                            <RekapChart
                                :dataChart="chartData"
                                :ref="
                                    (el) => {
                                        if (el) chartRefs[category] = el;
                                    }
                                "
                            />
                        </div>
                    </div>
                </div>
                <div class="mt-6 mx-7">
                    <div class="container-open-sans overflow-x-auto rounded-md">
                        <table class="table-auto text-left w-full">
                            <thead class="bg-white font-bold">
                                <tr>
                                    <th class="text-center px-4 py-3">No.</th>
                                    <th class="px-4 py-3">Nama File yang Diunduh</th>
                                    <th class="text-center px-4 py-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(report, index) in paginatedReports" :key="report.id" class="bg-[#F7F6FE]">
                                    <td class="text-center px-4 py-2">{{ index + 1 + (currentPage - 1) * entriesPerPage }}</td>
                                    <td class="px-4 py-2">{{ report.namaFile }}</td>
                                    <td class="text-center px-4 py-2">{{ formatDateDisplay(report.tanggal) }}</td>
                                </tr>
                                <tr v-if="reportLogs.length === 0">
                                    <td colspan="3" class="text-center px-4 py-2">Tidak ada laporan yang tersedia.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination controls -->
                    <div class="mt-4 flex justify-center items-center gap-3" v-if="totalPages = 1">
                         <span
                             class="text-[#299BB8] cursor-pointer"
                             :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                             @click="prevPage"
                         >
                             Sebelumnya
                         </span>
                         <span
                             v-for="page in totalPages"
                             :key="page"
                             @click="goToPage(page)"
                             class="cursor-pointer px-3 py-1 rounded"
                             :class="{
                                 'bg-[#8AD3E5] text-white' : currentPage === page,
                                 'bg-[#F2FAFC] text-black' : currentPage !== page
                             }"
                         >
                             {{ page }}
                         </span>
                         <span
                             class="text-[#299BB8] cursor-pointer"
                             :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                             @click="nextPage"
                         >
                             Selanjutnya
                         </span>
                    </div>
                </div>

                <hr class="my-4 ml-7" />
                <p
                    class="ml-7 container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2"
                >
                    © 2025 OccuHelp. All Rights Reserved.
                </p>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, reactive } from "vue";
import axios from "axios";

import LeftBar from "../../../composables/LeftBar.vue";
import RekapChart from "../../../composables/RekapChart.vue"; 

// import lihatIcon from '@/assets/lihat-laporan.svg';
// import lihatHover from '@/assets/lihat-laporan-hover.svg';

const hoverLihat = ref(false);

const startDate = ref("");
const endDate = ref("");

const isLoading = ref(false); 

const recapData = ref(null);

const chartRefs = ref({});
 
const chartDataMap = computed(() => {
    if (!recapData.value) {
        return null;
    }

    const map = {};
    for (const category in recapData.value) {
        if (recapData.value.hasOwnProperty(category)) {
            const dataArray = recapData.value[category]; 

            if (dataArray && dataArray.length > 0) {
                const labels = [];
                const dataValues = [];
                let labelKey = "Kategori"; 
                let valueKey = "Jumlah"; 

                if (dataArray[0].hasOwnProperty("Kategori Penyakit")) {
                    labelKey = "Kategori Penyakit";
                } else if (
                    dataArray[0].hasOwnProperty("Gangguan Status Gizi")
                ) {
                    labelKey = "Gangguan Status Gizi";
                }

                dataArray.forEach((item) => {
                    if (
                        item.hasOwnProperty(labelKey) &&
                        item.hasOwnProperty(valueKey)
                    ) {
                        labels.push(item[labelKey]);
                        dataValues.push(item[valueKey]);
                    } else {
                        console.warn(
                            `Item in category "${category}" does not have expected keys "${labelKey}" or "${valueKey}":`,
                            item
                        );
                    }
                });

                if (labels.length > 0 && dataValues.length > 0) {
                    map[category] = {
                        labels: labels,
                        datasets: [
                            {
                                label: category, 
                                backgroundColor: "#2293AD",
                                data: dataValues,
                            },
                        ],
                    };
                } else {
                    console.warn(
                        `No valid data points extracted for category "${category}". Skipping chart.`
                    );
                }
            } else {
                console.warn(
                    `Data array for category "${category}" is empty or null. Skipping chart.`
                );
            }
        }
    }

    console.log("Generated Chart Data Map:", map);
    return Object.keys(map).length > 0 ? map : null; 
});

const reportLogs = ref([]);

const currentPage = ref(1);
const entriesPerPage = 10;
const totalPages = computed(() =>
    Math.ceil(reportLogs.value.length / entriesPerPage)
);
const paginatedReports = computed(() => {
    const start = (currentPage.value - 1) * entriesPerPage;
    return reportLogs.value.slice(start, start + entriesPerPage);
});

function prevPage() {
    if (currentPage.value > 1) currentPage.value--;
}
function nextPage() {
    if (currentPage.value < totalPages.value) currentPage.value++;
}
function goToPage(page) {
    currentPage.value = page;
}

function formatDateDisplay(dateString) {
    if (!dateString) return "";
    try {
        let date;
        if (dateString.includes("-")) {
            date = new Date(dateString);
        } else if (dateString.includes("/")) {
            const parts = dateString.split("/");
            date = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`); 
        } else {
            date = new Date(dateString); 
        }

        if (isNaN(date.getTime())) {
            return dateString; 
        }
        const day = String(date.getDate()).padStart(2, "0");
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    } catch (error) {
        console.error("Error formatting date:", dateString, error);
        return dateString;
    }
}

async function fetchReportLogs() {
    try {
        console.log("Fetching report logs...");
        const response = await axios.get("/api/reports/logs");
        reportLogs.value = response.data;
        console.log("Report logs fetched:", reportLogs.value);
        currentPage.value = 1;
    } catch (error) {
        console.error("Error fetching report logs:", error);
    }
}

onMounted(() => {
    fetchReportLogs();
});

async function saveReportLog(filename) {
    try {
        console.log("Saving report log:", filename);
        await axios.post("/api/reports/log", { namaFile: filename });
        console.log("Report log saved successfully.");
        await fetchReportLogs();
    } catch (error) {
        console.error("Error saving report log:", error);
    }
}

async function getChartImageData() {
    const chartImages = {};
    const chartComponentKeys = Object.keys(chartRefs.value);

    if (chartComponentKeys.length === 0) {
        console.warn(
            "No chart components found in refs to capture. This might indicate an issue with chart rendering."
        );
        return chartImages;
    }

    for (const key of chartComponentKeys) {
        const chartComponent = chartRefs.value[key];
        if (!chartComponent) {
            console.warn(
                `Chart component ref for "${key}" is null/undefined. Skipping.`
            );
            continue;
        }

        const chartInstance = chartComponent?.barRef?.chart;

        if (chartInstance) {
            const canvas = chartInstance.canvas;
            if (canvas) {
                console.log(
                    `Capturing chart for ${key}. Canvas dimensions: Width=${canvas.width}, Height=${canvas.height}`
                );
                try {
                    if (canvas.width > 0 && canvas.height > 0) {
                        chartImages[key] = canvas.toDataURL("image/png");
                        console.log(
                            `Successfully captured image for ${key}. Data URL length: ${chartImages[key].length}`
                        );
                    } else {
                        console.error(
                            `Canvas for "${key}" has zero dimensions (width: ${canvas.width}, height: ${canvas.height}). Cannot capture image.`
                        );
                    }
                } catch (captureError) {
                    console.error(
                        `Error capturing image for "${key}":`,
                        captureError
                    );
                }
            } else {
                console.error(
                    `Canvas element not found for chart instance of "${key}".`
                );
            }
        } else {
            console.error(
                `Chart.js instance (barRef.chart) not found for RekapChart component "${key}". Ensure 'barRef' is correctly passed/exposed.`
            );
        }
    }

    console.log(
        "Total captured chart images:",
        Object.keys(chartImages).length
    );
    return chartImages;
}

async function generateReportWithCharts() {
    if (!startDate.value || !endDate.value) {
        alert("Mohon pilih periode tanggal terlebih dahulu.");
        return;
    }

    isLoading.value = true;
    recapData.value = null; 
    chartRefs.value = {}; 

    try {
        console.log("Fetching recap data...");
        const dataResponse = await axios.get("/api/reports/recap-data", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
            },
        });
        recapData.value = dataResponse.data;
        console.log("Recap data fetched:", recapData.value);

        await nextTick();
        console.log(
            "Vue DOM updated. Charts components should now be mounted in hidden div."
        );
        console.log(
            "Current chart refs BEFORE Chart.js render delay:",
            chartRefs.value
        );

        await new Promise((resolve) => setTimeout(resolve, 300)); 
        console.log("Proceeding after small delay for Chart.js rendering.");
        console.log(
            "Current chart refs AFTER Chart.js render delay:",
            chartRefs.value
        ); 

        console.log("Attempting to capture chart images...");
        const chartImagesData = await getChartImageData();

        const hasRecapData =
            recapData.value && Object.keys(recapData.value).length > 0;
        const hasChartImages = Object.keys(chartImagesData).length > 0;

        if (!hasRecapData && !hasChartImages) {
            alert(
                "Tidak ada data MCU atau grafik yang tersedia untuk periode yang dipilih."
            );
            return;
        }

        if (hasRecapData && !hasChartImages) {
            console.warn(
                "Recap data was fetched, but no chart images were successfully captured. PDF will be generated without charts."
            );
            alert(
                "Data rekap ditemukan, namun grafik gagal dibuat. Laporan PDF akan dibuat tanpa grafik."
            );
        } else if (!hasRecapData && hasChartImages) {
            console.warn(
                "Chart images were captured, but no recap data was fetched for tables. This scenario is unusual, check backend logic."
            );
            alert(
                "Grafik berhasil dibuat, namun data rekap untuk tabel tidak ditemukan. Laporan PDF akan dibuat hanya dengan grafik."
            );
        }

        console.log("Sending data and chart images for PDF generation...");
        const response = await axios({
            url: "/api/reports/generate",
            method: "POST",
            responseType: "blob",
            data: {
                startDate: startDate.value,
                endDate: endDate.value,
                chartImages: chartImagesData, 
            },
        });

        const blob = new Blob([response.data], { type: "application/pdf" });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        const filename = `laporan_MCU_${startDate.value.replace(
            /-/g,
            ""
        )}_sd_${endDate.value.replace(/-/g, "")}.pdf`;

        link.setAttribute("download", filename);

        document.body.appendChild(link);
        link.click();
        

        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);

        console.log("Report generated and downloaded successfully.");

        await saveReportLog(filename);
    } catch (error) {
        console.error("Error generating report:", error);
        let errorMessage = "Gagal membuat laporan.";

        if (error.response && error.response.data instanceof Blob) {
            const reader = new FileReader();
            reader.onload = function () {
                try {
                    const errorJson = JSON.parse(reader.result);
                    errorMessage =
                        errorJson.message ||
                        errorMessage +
                            " Detail: " +
                            (errorJson.error || "Unknown error from server.");
                    if (errorJson.errors) {
                        errorMessage += "\nErrors:\n";
                        for (const field in errorJson.errors) {
                            errorMessage += `- ${field}: ${errorJson.errors[
                                field
                            ].join(", ")}\n`;
                        }
                    }
                } catch (parseError) {
                    errorMessage =
                        errorMessage +
                        " Detail: Respons server tidak dapat dibaca atau bukan JSON.";
                }
                alert(errorMessage);
            };
            reader.onerror = function () {
                errorMessage =
                    errorMessage +
                    " Detail: Gagal membaca respons error dari server.";
                alert(errorMessage);
            };
            reader.readAsText(error.response.data); 
        } else if (
            error.response &&
            error.response.data &&
            error.response.data.message
        ) {
            errorMessage = error.response.data.message;
            if (error.response.data.errors) {
                errorMessage += "\nErrors:\n";
                for (const field in error.response.data.errors) {
                    errorMessage += `- ${field}: ${error.response.data.errors[
                        field
                    ].join(", ")}\n`;
                }
            }
            alert(errorMessage);
        } else {
            alert(
                errorMessage +
                    " Terjadi kesalahan jaringan atau server tidak merespons."
            );
        }
    } finally {
        isLoading.value = false;
        // recapData.value = null;
        // chartRefs.value = {}; 
    }
}
</script>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    pointer-events: all;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 1;
}

input[type="date"]::-moz-calendar-picker-indicator {
    opacity: 0;
    pointer-events: all;
    cursor: pointer;
    z-index: 1;
}

.container-nunito {
    font-family: "Nunito", sans-serif;
}
.container-open-sans {
    font-family: "Open Sans", sans-serif;
}

#charts-for-pdf .chart-container {
    width: 600px; 
    height: 400px;
    margin-bottom: 20px; 
}

/*
#charts-for-pdf .chart-container canvas {
    width: 100% !important;
    height: 100% !important;
}
*/
</style>
