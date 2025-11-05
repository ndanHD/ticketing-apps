// admin.js — centralized SweetAlert + delete confirmation
(function(){
    function showSuccess(msg){
        Swal.fire({ 
            icon: 'success', 
            title: 'Berhasil!', 
            text: msg,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }

    function showError(msg){
        Swal.fire({ 
            icon: 'error', 
            title: 'Error!', 
            text: msg,
            confirmButtonText: 'OK'
        });
    }

    function showValidationErrors(list){
        if(!list || !list.length) return;
        const html = '<ul style="text-align:left;margin:0;padding-left:20px;">' + 
            list.map(e => '<li>'+e+'</li>').join('') + '</ul>';
        Swal.fire({ 
            icon: 'error', 
            title: 'Validasi Error!', 
            html: html,
            confirmButtonText: 'OK'
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        try {
            const data = window.Laravel || {};
            if(data.success){ showSuccess(data.success); }
            if(data.error){ showError(data.error); }
            if(Array.isArray(data.errors) && data.errors.length){ showValidationErrors(data.errors); }
        } catch (e) {
            // ignore
            console.error(e);
        }

        // Attach delete confirmation handler for forms with .swal-delete
        document.querySelectorAll('form.swal-delete').forEach(function(form){
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const name = form.dataset.name || 'this item';
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    html: 'Data <strong>'+name+'</strong> yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
})();
