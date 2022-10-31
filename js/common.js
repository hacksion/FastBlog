document.addEventListener("DOMContentLoaded", () => {
    navCurrentLink();
    navSearchBtn();
    navSearchBtnInput();
    scrollFunc();
    enterFalse();
    contactForm();
    withdrawalModal('withdrawal_modal', 3600000);
    let NRL = new RecordList({
        'post_file' : `${asyncUrl}search_content.php`,
        'form_name' : 'public_keyword_search',
        'search_btn_class' : 'nav_search_btn',
        'search_reset_class' : 'nav_search_reset',
        'list_area_id' : 'search_result',
        'nav_class' : 'search_result_nav',
        'lang' : document.documentElement.lang,
    });
    // NRL.getList();
    NRL.searchBtnEvt();
    NRL.searchReset();
});
