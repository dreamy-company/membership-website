<script>
    window.addEventListener('success', function (event) {
        Swal.fire({
            toast: true,
            icon: event.detail[0].type,
            title: event.detail[0].message,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    });

</script>