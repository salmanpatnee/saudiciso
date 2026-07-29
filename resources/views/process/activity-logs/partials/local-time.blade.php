{{-- Timestamps are stored in UTC; render them in the viewer's own time zone. --}}
<script>
    document.querySelectorAll('[data-local-datetime]').forEach(function (el) {
        var date = new Date(el.getAttribute('data-local-datetime'));

        if (isNaN(date.getTime())) {
            return;
        }

        el.textContent = new Intl.DateTimeFormat(undefined, {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        }).format(date);
    });

    document.querySelectorAll('[data-local-time]').forEach(function (el) {
        var date = new Date(el.getAttribute('data-local-time'));

        if (isNaN(date.getTime())) {
            return;
        }

        el.textContent = new Intl.DateTimeFormat(undefined, {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
        }).format(date);
    });
</script>
