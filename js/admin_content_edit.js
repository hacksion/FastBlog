document.addEventListener("DOMContentLoaded", () => {
    CKEDITOR.replace( 'html' );
    selectFile(1024000);
    setFlatpicker();
    fileTrash();
    editCreate();
    deleteEditRecord();
    contentsDeisabled();
    unslash();
});
