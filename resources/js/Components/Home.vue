<template>
    <div class="grid grid-cols-3 gap-6 p-6">
      <div 
        v-for="iceCream in iceCreams" 
        :key="iceCream.id" 
        class="p-6 rounded-3xl shadow-lg bg-gray-200 relative neumorphic">
        
        <!-- Nazwa smaku -->
        <h2 class="text-lg font-semibold text-gray-700 mb-2">{{ iceCream.name }}</h2>
        
        <!-- Kuwety (paski wypełnienia) -->
        <div class="relative flex items-center space-x-1">
          <div 
            v-for="n in iceCream.amount" 
            :key="n" 
            class="w-6 h-12 rounded-lg bg-gradient-to-b from-opacity-90 to-opacity-40"
            :class="iceCream.color">
          </div>
        </div>
  
        <!-- Ilość jako wytłoczona cyfra -->
        <div class="absolute top-2 right-4 text-4xl font-bold text-gray-400 opacity-40">
          {{ iceCream.amount }}
        </div>
  
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        iceCreams: [
          { id: 1, name: "Czekoladowe", amount: 4, color: "bg-brown-500" },
          { id: 2, name: "Truskawkowe", amount: 2, color: "bg-pink-400" },
          { id: 3, name: "Waniliowe", amount: 3, color: "bg-yellow-300" },
          { id: 4, name: "Pistacjowe", amount: 5, color: "bg-green-400" },
        ]
      };
    }
  };
  </script>
  
  <style>
  .neumorphic {
    box-shadow: 8px 8px 16px rgba(0, 0, 0, 0.1), -8px -8px 16px rgba(255, 255, 255, 0.6);
  }
  </style>
  