<template>
    <div>
      <!-- This is where the component corresponding to the current route will be rendered -->
      <router-view></router-view>
    </div>
  </template>
  
  <script setup>
  // No script needed for this simple root component
  </script>
  
  <style>
  /* Add global styles here if needed */
  body {
    margin: 0;
    font-family: sans-serif; /* Example global style */
  }
  </style>