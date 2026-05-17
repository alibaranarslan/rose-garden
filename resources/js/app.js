import './bootstrap';

const scrollRail = () => ({
    canPrev: false,
    canNext: true,

    init() {
        const track = this.$refs.track;
        if (!track) {
            return;
        }

        const sync = () => this.sync(track);
        sync();

        track.addEventListener('scroll', sync, { passive: true });
        window.addEventListener('resize', sync, { passive: true });
    },

    sync(track) {
        const target = track ?? this.$refs.track;
        if (!target) {
            return;
        }

        this.canPrev = target.scrollLeft > 8;
        this.canNext = target.scrollLeft + target.clientWidth < target.scrollWidth - 8;
    },

    scrollBy(direction) {
        const track = this.$refs.track;
        if (!track) {
            return;
        }

        track.scrollBy({
            left: track.clientWidth * 0.88 * direction,
            behavior: 'smooth',
        });
    },

    scrollPrev() {
        this.scrollBy(-1);
    },

    scrollNext() {
        this.scrollBy(1);
    },
});

const registerAlpineData = (Alpine) => {
    window.scrollRail = scrollRail;
    Alpine.data('scrollRail', scrollRail);
};

const boot = async () => {
    const usesLivewire = document.body.dataset.livewire === 'true';

    if (usesLivewire) {
        const { Livewire, Alpine } = await import('../../vendor/livewire/livewire/dist/livewire.esm');

        window.Alpine = Alpine;
        registerAlpineData(Alpine);
        Livewire.start();

        return;
    }

    const { default: Alpine } = await import('alpinejs');

    window.Alpine = Alpine;
    registerAlpineData(Alpine);
    Alpine.start();
};

boot();
