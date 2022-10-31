document.addEventListener("DOMContentLoaded", () => {
    let RL = new RecordList({
        'form_name' : document.querySelector('body[data-cat]').dataset.cat,
        'list_area_id' : 'list_area',
        'nav_class' : 'list_nav',
        'lang' : document.documentElement.lang,
        'method': 'category',
        'page' : 'public'
    });
    RL.getList();
    RL.searchBtnEvt();
    RL.searchReset();
});
