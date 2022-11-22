document.addEventListener( "DOMContentLoaded", ()=> {
    let btn_submit = document.getElementById('btn_submit');
    let form = document.querySelector('form[name=install]');
    if(btn_submit && form){
        btn_submit.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            if (form.checkValidity()) {
                asyncPost(`${asyncUrl}install.php`, new FormData(form), null, json => {
                    if (json.result == 1) {
                        Swal.fire(swalOption('success', json.msg)).then( ret => {
                            location.href = dataUrl + adminUrl;
                        });
                        return;
                    }
                    Swal.fire(swalOption('error', json.msg)).then( ret => {
                        if (ret.value) window.location.reload();
                    });
                });
                return;
            }
            form.classList.add('was-validated');
        });
    }
});
