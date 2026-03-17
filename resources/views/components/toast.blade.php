<div x-data="toastSystem()" x-on:notify.window="add($event.detail)" class="fixed bottom-5 right-0 z-50 space-y-3 ">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-10 opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-10 opacity-0"
            class="relative overflow-hidden rounded-l-xl shadow-2xl px-4 py-3 text-sm font-medium text-white flex items-center justify-between "
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-yellow-500 text-black': toast.type === 'warning',
                'bg-indigo-600': toast.type === 'info'
            }">
            <span x-text="toast.message" class="md:mr-15 mr-3"></span>

            <button @click="remove(toast.id)" class="md:mx-5 ml-5 text-white/80 hover:text-white font-semibold text-lg">
                ✕
            </button>

            <!-- Progress bar animada -->
            <div class="absolute bottom-0 left-0 h-1 w-full bg-white/20">
                <div class="h-full bg-white/70 animate-progress" :style="`animation-duration: ${toast.duration}ms`">
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    @keyframes progressShrink {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    .animate-progress {
        animation-name: progressShrink;
        animation-timing-function: linear;
        animation-fill-mode: forwards;
    }
</style>

<script>
    function toastSystem() {

        return {
            toasts: [],

            add(detail) {

                let id = Date.now() + Math.random();
                let duration = 3000;

                let toast = {
                    id: id,
                    message: detail.message || 'Notificación',
                    type: detail.type || 'info',
                    visible: true,
                    duration: duration
                };

                this.toasts.push(toast);

                setTimeout(() => {
                    this.remove(id);
                }, duration);
            },

            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }
    }
</script>
