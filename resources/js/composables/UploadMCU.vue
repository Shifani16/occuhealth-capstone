<template>
  <div
      class="container-open-sans fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
  >
      <div class="bg-[#27394B] w-[600px] p-6 rounded-2xl shadow-md relative">
          <img
              src="@/assets/circle-x.svg"
              class="absolute top-5 right-5 cursor-pointer w-6 h-6"
              @click="closeModal"
              title="Tutup"
          />

          <h2 class="text-white text-2xl font-bold mb-4 text-center">
              Upload Hasil MCU Excel
          </h2>

          <div class="h-[1px] bg-white w-full mt-8 mb-6"></div>

          <label
              for="file-upload"
              class="border border-white rounded-lg px-5 py-3 flex items-center gap-3 justify-center cursor-pointer hover:bg-[#C9D6E3] group transition"
              @mouseover="hoveringFileButton = true"
              @mouseleave="hoveringFileButton = false"
          >
              <img
                  :src="hoveringFileButton ? uploadMcuHover : uploadMcu"
                  class="w-5 h-5"
              />
              <span
                  :class="
                      hoveringFileButton ? 'text-[#426180]' : 'text-white'
                  "
                  class="font-semibold"
              >
                  Pilih File dari komputer (.xlsx)
              </span>
              <input
                  id="file-upload"
                  type="file"
                  accept=".xlsx"
                  class="hidden"
                  @change="handleFileUpload"
                  ref="fileInput"
              />
          </label>

          <div
              v-if="selectedFile"
              class="bg-[#C9D6E3] text-[#27394B] px-4 py-2 rounded mt-4 flex justify-between items-center truncate"
          >
              <span class="truncate max-w-[80%] font-medium">{{
                  selectedFile.name
              }}</span>
              <img
                  src="@/assets/cross.svg"
                  class="w-4 h-4 cursor-pointer"
                  @click="clearSelectedFile"
                  title="Hapus file"
              />
          </div>

          <div v-if="uploading" class="text-white text-center mt-4">
              Mengunggah... Harap tunggu.
          </div>

          <div
              v-if="uploadErrorMessage"
              class="text-red-400 text-center mt-4 text-sm"
          >
              {{ uploadErrorMessage }}
          </div>

          <div class="flex justify-end mt-6">
              <button
                  @click="submitUpload"
                  :disabled="!selectedFile || uploading"
                  class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
              >
                  Upload
              </button>
          </div>
      </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps, ref, watch } from "vue";
import axios from "axios";
import uploadMcu from "@/assets/upload-mcu.svg";
import uploadMcuHover from "@/assets/upload-mcu-hover.svg";

const props = defineProps({
  show: Boolean,
});
const emit = defineEmits(["close", "uploadSuccess", "uploadError"]);

const fileInput = ref(null);
const selectedFile = ref(null);
const hoveringFileButton = ref(false);
const uploading = ref(false);
const uploadErrorMessage = ref("");

function handleFileUpload(event) {
  const file = event.target.files ? event.target.files[0] : null;
  if (file) {
      uploadErrorMessage.value = ""; 

      if (file.name.endsWith(".xlsx")) {
          selectedFile.value = file;
      } else {
          selectedFile.value = null;
          uploadErrorMessage.value =
              "Format file tidak valid. Harap pilih file .xlsx";
          if (fileInput.value) {
              fileInput.value.value = "";
          }
      }
  } else {
      selectedFile.value = null;
      uploadErrorMessage.value = "";
  }
}

function clearSelectedFile() {
  selectedFile.value = null;
  uploadErrorMessage.value = "";
  if (fileInput.value) {
      fileInput.value.value = ""; 
  }
}

function closeModal() {
  selectedFile.value = null;
  uploading.value = false;
  uploadErrorMessage.value = "";
  if (fileInput.value) {
      fileInput.value.value = ""; 
  }
  emit("close");
}

async function submitUpload() {
  if (!selectedFile.value) {
      uploadErrorMessage.value = "Harap pilih file terlebih dahulu.";
      return;
  }

  if (!selectedFile.value.name.endsWith(".xlsx")) {
      uploadErrorMessage.value =
          "Format file tidak valid. Harap pilih file .xlsx";
      return;
  }

  uploading.value = true;
  uploadErrorMessage.value = ""; 

  const formData = new FormData();
  formData.append("file", selectedFile.value);

  try {
      const response = await axios.post("/api/import/mcu", formData, {
          headers: {
              "Content-Type": "multipart/form-data",
          },
      });

      console.log("Upload successful:", response.data);
      emit("uploadSuccess", response.data); 
  } catch (error) {
      console.error("Upload failed:", error);

      let message = "Terjadi kesalahan saat mengunggah file.";
      if (error.response && error.response.data) {
          if (error.response.data.message) {
              message = error.response.data.message; 
          } else if (error.response.data.error) {
              message = error.response.data.error; 
              if (error.response.data.messages && Array.isArray(error.response.data.messages)) {
                   message += ": " + error.response.data.messages.join(", ");
              }
          } else if (error.response.data.messages && Array.isArray(error.response.data.messages)) {
              message = error.response.data.messages.join(", ");
          } else if (typeof error.response.data === 'string') {
               message = error.response.data;
          }
      } else if (error.message) {
          message = `Upload gagal: ${error.message}`;
      }

      uploadErrorMessage.value = message; 
      emit("uploadError", new Error(message));
  } finally {
      uploading.value = false;
  }
}
</script>

<style scoped>
input[type="file"]::-webkit-file-upload-button {
  display: none;
}

.container-nunito {
  font-family: "Nunito", sans-serif;
}

.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>