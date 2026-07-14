<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('governanceMobile', () => ({
            sheetOpen: false,
            lockScroll(locked) {
                document.documentElement.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('overflow-hidden', locked);
            },
            openSheet() {
                this.sheetOpen = true;
                this.lockScroll(true);
            },
            closeSheet() {
                this.sheetOpen = false;
                this.lockScroll(false);
            },
        }));
    });

    // Prevent iOS rubber-band on fixed/sticky UI; allow inner scroll containers
    (function () {
        var lastY = 0;
        document.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) lastY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            var el = e.target.closest('[data-overscroll-lock], .overflow-y-auto, .overscroll-y-contain, .snap-x');
            if (!el) return;
            if (el.dataset.overscrollLock === 'true') {
                e.preventDefault();
                return;
            }
            if (el.classList.contains('snap-x') || el.classList.contains('overflow-x-auto')) return;
            var atTop = el.scrollTop <= 0;
            var atBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 1;
            var y = e.touches[0].clientY;
            if ((atTop && y > lastY) || (atBottom && y < lastY)) e.preventDefault();
        }, { passive: false });
    })();
</script>
