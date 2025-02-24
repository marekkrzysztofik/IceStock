<template>
  <div class="p-4 overflow-y-auto max-h-[500px]">
    <div class="grid grid-cols-5 gap-3">
      <div 
        v-for="iceCream in iceCreams" 
        :key="iceCream.id" 
        class="relative p-2 rounded-xl backdrop-blur-lg bg-white/20 shadow-md border border-white/30 glass-card w-24 h-28 flex flex-col justify-between items-center">
        
        <!-- Nazwa smaku -->
        <h2 class="relative text-xs font-bold text-white drop-shadow-md tracking-wide text-center">
          {{ iceCream.name }}
        </h2>

        <!-- Kuwety jako ikonki -->
        <div class="flex flex-wrap justify-center gap-1">
          <span 
            v-for="n in iceCream.amount" 
            :key="n" 
            class="w-4 h-6 rounded-md bg-blue-300/50 border border-white/20 kuwet-shadow">
          </span>
        </div>

        <!-- Ilość jako liczba -->
        <div class="absolute top-1 right-2 text-sm font-bold text-white/80 drop-shadow-md">
          {{ iceCream.amount }}
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      iceCreams: Array.from({ length: 25 }, (_, i) => ({
        id: i + 1,
        name: `Lód ${i + 1}`,
        amount: Math.floor(Math.random() * 5) + 1 // Losowa ilość 1-5
      }))
    };
  }
};
</script>

<style>
.glass-card {
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(8px);
}

.kuwet-shadow {
  box-shadow: inset 1px 1px 3px rgba(255, 255, 255, 0.1), inset -1px -1px 3px rgba(0, 0, 0, 0.1);
}
</style>
