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
          <LeftBar active="Pasien" />

          <main class="flex-1 p-6">
              <h1 class="text-[40px] font-bold mb-4 ml-7 container-nunito">
                  Edit Pasien
              </h1>

              <div class="ml-7 flex justify-between items-center max-w-7xl">
                  <h2 class="container-nunito text-[32px] font-bold mb-1">
                      {{ formData.name || 'Memuat...' }}
                  </h2>
                  <div
                      class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap"
                  >
                      #Biodata Pasien
                  </div>
              </div>
              <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]" />

              <!-- Loading State -->
              <div v-if="loading" class="text-center text-[#3393AD] mt-8">
                  Memuat data pasien...
              </div>

              <!-- Error State for Fetching -->
              <div v-else-if="fetchError" class="text-center text-red-600 mt-8">
                  Gagal memuat data pasien: {{ fetchError }}
                   <br>
                  <button @click="fetchPatientData(route.params.id)" class="mt-2 text-[#3393AD] underline">Coba Lagi</button>
              </div>

              <div
                  v-else
                  class="mx-auto grid gap-y-3 w-full max-w-6xl text-[20px] container-open-sans"
              >
                  <div
                      v-for="field in formFields"
                      :key="field.key"
                      class="grid grid-cols-2 gap-x-8 items-center"
                  >
                      <div class="text-right font-bold text-[#27394B]">
                          {{ field.label }}
                      </div>
                      <input
                          v-model="formData[field.key]"
                          :type="field.type || 'text'"
                          :disabled="saving"
                          class="bg-[#E6F6F9] px-3 py-1 rounded w-full disabled:opacity-75 disabled:cursor-not-allowed"
                          :class="{ 'border border-red-500': validationErrors[field.key] }"
                      />
                      <div v-if="validationErrors[field.key]" class="col-span-2 text-right text-red-500 text-sm mt-1">
                          {{ validationErrors[field.key][0] }}
                      </div>
                  </div>
                   <div v-if="validationErrors.birth_place" class="col-span-2 text-right text-red-500 text-sm mt-1">
                       {{ validationErrors.birth_place[0] }}
                   </div>
                    <div v-if="validationErrors.birth_date" class="col-span-2 text-right text-red-500 text-sm mt-1">
                       {{ validationErrors.birth_date[0] }}
                   </div>
              </div>

              <div
                  class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans"
              >
                  <button
                      @click="cancel"
                      :disabled="saving"
                      @mouseover="hoveringBatal = true"
                      @mouseleave="hoveringBatal = false"
                      :class="[
                          'border font-semibold px-6 py-2 rounded flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed',
                          hoveringBatal
                              ? 'bg-[#3393AD] text-white border-[#3393AD]'
                              : 'text-[#3393AD] border-[#3393AD]',
                      ]"
                  >
                      <img
                          :src="hoveringBatal ? batalHoverIcon : batalIcon"
                          class="h-5"
                      />
                      Batal
                  </button>

                  <button
                      @click="save"
                      :disabled="loading || saving || fetchError"
                      @mouseover="hoveringSimpan = true"
                      @mouseleave="hoveringSimpan = false"
                      :class="[
                          'border border-[#3393AD] text-[#3393AD] font-semibold px-6 py-2 rounded flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed',
                          hoveringSimpan
                              ? 'bg-[#E4EBF1] border-[#E4EBF1]'
                              : '',
                      ]"
                  >
                      <span v-if="saving">Menyimpan...</span>
                      <span v-else>
                          <img src="@/assets/simpan.svg" class="h-5" />
                          Simpan
                      </span>
                  </button>
              </div>
              <hr class="my-4 ml-7" />
              <p
                  class="ml-7 container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2"
              >
                  © 2025 OccuHelp. All Rights Reserved.
              </p>
          </main>
      </div>

      <!-- Success Popup -->
      <div
          v-if="showSuccessPopup"
          class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
      >
          <div
              class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
          >
              <img src="@/assets/circle-check.svg" class="w-20 mx-auto mb-4" />
              <p class="text-white font-medium mb-4">{{ successMessage }}</p>
              <div class="flex justify-center">
                  <button
                      @click="goToPasienList"
                      class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                  >
                      Tutup
                  </button>
              </div>
          </div>
      </div>

      <!-- Error Popup (for Save Errors) -->
      <div
          v-if="showErrorPopup"
          class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
      >
          <div
              class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
          >
              <img src="@/assets/cloud-x.svg" class="w-20 mx-auto mb-4" />
              <p class="text-white font-medium mb-4">{{ errorMessage }}</p>
              <div class="flex justify-center">
                  <button
                      @click="showErrorPopup = false"
                      class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                  >
                      Tutup
                  </button>
              </div>
          </div>
      </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import LeftBar from "../../../composables/LeftBar.vue";

import batalIcon from "@/assets/batal.svg";
import batalHoverIcon from "@/assets/batal-hover.svg";

// --- State ---
const hoveringBatal = ref(false);
const hoveringSimpan = ref(false);
const showSuccessPopup = ref(false);
const successMessage = ref("Data pasien berhasil diubah");
const showErrorPopup = ref(false);
const errorMessage = ref("Terjadi kesalahan saat menyimpan data.");

const loading = ref(true);
const saving = ref(false);
const fetchError = ref(null);
const validationErrors = ref({}); 

const route = useRoute();
const router = useRouter();

const formFields = [
  { key: "name", label: "Nama Pasien", type: "text" },
  { key: "examination_date", label: "Tanggal Pemeriksaan", type: "date" },
  { key: "med_record_id", label: "Nomor Rekam Medis", type: "text" },
  { key: "patient_id", label: "Nomor Pasien", type: "text" },
  { key: "gender", label: "Jenis Kelamin", type: "text" },
  { key: "age", label: "Umur", type: "number" },
  { key: "birth_info_combined", label: "Tempat dan Tanggal Lahir", type: "text" },
];

const formData = reactive({
  id: null,
  name: "",
  examination_date: "",
  med_record_id: "",
  patient_id: "",
  gender: "",
  age: "",
  birth_place: "",
  birth_date: "",
  birth_info_combined: "",
  address: "",
});

const patientId = computed(() => route.params.id);

// --- Data Fetching ---
async function fetchPatientData(id) {
  if (!id) {
      fetchError.value = "ID pasien tidak ditemukan di URL.";
      loading.value = false;
      return;
  }

  loading.value = true;
  fetchError.value = null;
  validationErrors.value = {};

  try {
      const response = await axios.get(`/api/patients/${id}`);
      console.log("Fetched patient data:", response.data);

      const patientData = response.data; 

      formData.id = patientData.id || id;
      formData.name = patientData.name || "";
      formData.med_record_id = patientData.med_record_id || "";
      formData.patient_id = patientData.patient_id || "";
      formData.gender = patientData.gender || "";
      formData.age = patientData.age?.toString() || "";
      formData.address = patientData.address || "";
      formData.birth_place = patientData.birth_place || "";

      if (patientData.birth_date) {
           try {
               const date = new Date(patientData.birth_date);
               if (!isNaN(date.getTime())) {
                   formData.birth_date = date.toISOString().split("T")[0]; 
               } else {
                   console.warn("Could not parse backend birth_date string:", patientData.birth_date);
                   formData.birth_date = '';
               }
           } catch (e) {
               console.warn("Error parsing backend birth_date:", patientData.birth_date, e);
               formData.birth_date = '';
           }
      } else {
           formData.birth_date = '';
      }


      if (patientData.examination_date) {
          try {
              const date = new Date(patientData.examination_date);
               if (!isNaN(date.getTime())) {
                   formData.examination_date = date.toISOString().split("T")[0];
              } else {
                   console.warn("Could not parse backend examination_date string:", patientData.examination_date);
                   formData.examination_date = '';
              }
          } catch (e) {
              console.warn("Error parsing backend examination_date:", patientData.examination_date, e);
              formData.examination_date = "";
          }
      } else {
           formData.examination_date = '';
      }


      const formattedBirthDateForDisplay = formData.birth_date
          ? new Date(formData.birth_date).toLocaleDateString('id-ID', {
               year: 'numeric',
               month: 'long',
               day: 'numeric'
           })
          : '';

      if (formData.birth_place && formattedBirthDateForDisplay) {
          formData.birth_info_combined = `${formData.birth_place}, ${formattedBirthDateForDisplay}`;
      } else if (formData.birth_place) {
          formData.birth_info_combined = formData.birth_place;
      } else if (formattedBirthDateForDisplay) {
           formData.birth_info_combined = formattedBirthDateForDisplay;
      } else {
           formData.birth_info_combined = '';
      }


  } catch (error) {
      console.error("Error fetching patient data:", error);
      fetchError.value = "Gagal memuat data pasien. Silakan coba lagi.";
      if (error.response && error.response.data) {
          if (error.response.data.message) {
              fetchError.value = error.response.data.message;
          } else if (error.response.data.error) {
              fetchError.value = error.response.data.error;
          } else if (typeof error.response.data === "string") {
              fetchError.value = error.response.data;
          }
      } else if (error.message) {
          fetchError.value = `Gagal memuat data: ${error.message}`;
      }
  } finally {
      loading.value = false;
  }
}

onMounted(() => {
  const id = route.params.id;
  if (id) {
      fetchPatientData(id);
  } else {
      fetchError.value = "ID pasien tidak ditemukan di URL.";
      loading.value = false;
  }
});

// --- Save Logic ---
async function save() {
  if (saving.value || loading.value) return;
  saving.value = true;
  errorMessage.value = "";
  validationErrors.value = {};

  let parsedBirthPlace = '';
  let parsedBirthDate = '';
  const combined = formData.birth_info_combined || '';
  const lastCommaIndex = combined.lastIndexOf(',');

  if (lastCommaIndex !== -1) {
      parsedBirthPlace = combined.substring(0, lastCommaIndex).trim();
      const datePart = combined.substring(lastCommaIndex + 1).trim();
       try {
           const dateObj = new Date(datePart);
           if (!isNaN(dateObj.getTime())) {
                parsedBirthDate = dateObj.toISOString().split('T')[0];
           } else {
                console.warn("Could not parse date part from combined string:", datePart);
                validationErrors.value.birth_date = validationErrors.value.birth_date || [];
                validationErrors.value.birth_date.push("Format tanggal lahir tidak valid.");
                errorMessage.value = 'Terdapat kesalahan pada input Tanggal Lahir.';
                showErrorPopup.value = true;
                saving.value = false;
                return; 
           }
       } catch (e) {
            console.warn("Error parsing date part from combined string:", datePart, e);
            validationErrors.value.birth_date = validationErrors.value.birth_date || [];
            validationErrors.value.birth_date.push("Format tanggal lahir tidak valid.");
            errorMessage.value = 'Terdapat kesalahan pada input Tanggal Lahir.';
            showErrorPopup.value = true;
            saving.value = false;
            return;
       }

  } else {
      parsedBirthPlace = combined.trim();
      parsedBirthDate = formData.birth_date;
  }

  const dataToSend = {
      id: formData.id,
      name: formData.name,
      examination_date: formData.examination_date,
      med_record_id: formData.med_record_id,
      patient_id: formData.patient_id,
      gender: formData.gender,
      age: formData.age ? parseInt(formData.age) : null,
      birth_place: parsedBirthPlace,
      birth_date: parsedBirthDate, 
      address: formData.address,
  };

   if (!dataToSend.name) {
        validationErrors.value.name = ['Nama Pasien tidak boleh kosong.'];
        errorMessage.value = 'Mohon lengkapi data yang wajib diisi.';
        showErrorPopup.value = true;
        saving.value = false;
        return;
   }


  try {
      const id = formData.id;

      if (!id) {
          console.error("Patient ID is missing in formData for save operation.");
          errorMessage.value = "ID pasien hilang, tidak dapat menyimpan.";
          showErrorPopup.value = true;
          return;
      }

      const response = await axios.put(`/api/patients/${id}`, dataToSend); // Assuming PUT or PATCH
      console.log("Save successful:", response.data);

      successMessage.value =
          response.data.message || "Data pasien berhasil diubah";
      showSuccessPopup.value = true;

  } catch (error) {
      console.error("Error saving patient data:", error);

      errorMessage.value = "Gagal menyimpan data pasien.";

      if (error.response) {
          console.error("Response data:", error.response.data);
          console.error("Response status:", error.response.status);

          if (error.response.status === 422 && error.response.data.errors) {
              validationErrors.value = error.response.data.errors;
               if (validationErrors.value.birth_place || validationErrors.value.birth_date) {
                    errorMessage.value = 'Terdapat kesalahan pada input Tempat atau Tanggal Lahir.';
               } else {
                    errorMessage.value = "Terdapat kesalahan pada input Anda. Mohon periksa kembali.";
               }

          } else if (error.response.data.message) {
              errorMessage.value = error.response.data.message;
          } else if (error.response.data.error) {
              errorMessage.value = error.response.data.error;
          } else if (typeof error.response.data === "string") {
              errorMessage.value = error.response.data;
          } else {
              errorMessage.value = `Server error: Status ${error.response.status}`;
          }

      } else if (error.request) {
          console.error("No response received:", error.request);
          errorMessage.value =
              "Tidak ada respon dari server. Pastikan server berjalan.";
      } else {
          console.error("Axios error:", error.message);
          errorMessage.value = `Terjadi kesalahan: ${error.message}`;
      }

      showErrorPopup.value = true;

  } finally {
      saving.value = false;
  }
}

// --- Navigation ---
function cancel() {
  router.go(-1);
}

function goToPasienList() {
  showSuccessPopup.value = false;
  router.push("/pasien");
}
</script>

<style scoped>
.container-nunito {
  font-family: "Nunito", sans-serif;
}
.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>