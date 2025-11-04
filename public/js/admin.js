// admin.js — centralized SweetAlert + delete confirmation
(function(){
    function showSuccess(msg){
        Swal.fire({ icon: 'success', title: 'Success', text: msg });
    }

    function showError(msg){
        Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }

    function showValidationErrors(list){
        if(!list || !list.length) return;
        const html = '<ul style="text-align:left;">' + list.map(e => '<li>'+e+'</li>').join('') + '</ul>';
        Swal.fire({ icon: 'error', title: 'Validation error', html: html });
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
                    title: 'Are you sure?',
                    html: 'This will permanently delete <strong>'+name+'</strong>.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
})();
