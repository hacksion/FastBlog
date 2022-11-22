document.addEventListener("DOMContentLoaded", () => {
    CKEDITOR.replace( 'html' );
    editCreate();
    selectFile(512000);
    fileTrash();
});
