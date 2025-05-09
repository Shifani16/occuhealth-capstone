<template>
    <div 
      
      class="container-open-sans fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] w-[600px] p-6 rounded-2xl shadow-md relative">
            <img
              src="@/assets/circle-x.svg"
              class="absolute top-5 right-5 cursor-pointer w-6 h-6"
              @click="closeModal"
            />

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
                :class="hoveringFileButton ? 'text-[#426180]' : 'text-white'"
                class="font-semibold"
              >
                Pilih File dari komputer
              </span>
              <input
                id="file-upload"
                type="file"
                accept=".xlsx"
                class="hidden"
                @change="handleFileUpload"
              />
            </label>

            <div
              v-if="selectedFile"
              class="bg-[#C9D6E3] text-[#27394B] px-4 py-2 rounded mt-4 flex justify-between items-center"
            >
              <span class="truncate max-w-[80%]">{{ selectedFile.name }}</span>
              <img
                src="@/assets/cross.svg"
                class="w-4 h-4 cursor-pointer"
                @click="selectedFile = null"
              />
            </div>

            <div class="flex justify-end mt-6">
              <button
                @click="submitUpload"
                class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
              >
                Upload
              </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue';
import uploadMcu from '@/assets/upload-mcu.svg';
import uploadMcuHover from '@/assets/upload-mcu-hover.svg';

const props = defineProps({ show: Boolean });
const emit = defineEmits(['close', 'uploadSuccess', 'uploadError']);

const selectedFile = ref(null);
const hoveringFileButton = ref(false);

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file && file.name.endsWith('.xlsx')) {
    selectedFile.value = file;
    event.target.value = '';
  } else {
    selectedFile.value = null
    emit('uploadError');
  }
}

function closeModal() {
  emit('close');
}

function submitUpload() {
  if (!selectedFile.value) {
    alert('Harap pilih file terlebih dahulu.');
    return;
  }

  const isValid = selectedFile.value.name.endsWith('.xlsx');

  if (isValid) {
    emit('uploadSuccess', selectedFile.value); 
  } else {
    emit('uploadError');
  }

  closeModal();
}

</script>

<style scoped>
input[type='file']::-webkit-file-upload-button {
  display: none;
}

.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}

</style>