{{-- Scripts de Notificación --}}
<script>
document.addEventListener('livewire:initialized', () => {
    // Sonido de notificación
    Livewire.on('play-notification-sound', () => {
        playBeep();
    });
    
    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            
            // Primer beep
            playTone(ctx, 880, 0.3);
            
            // Segundo beep
            setTimeout(() => playTone(ctx, 1100, 0.3), 200);
        } catch (e) {
            console.log('Audio no disponible');
        }
    }
    
    function playTone(ctx, freq, duration) {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = freq;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + duration);
    }
});
</script>
