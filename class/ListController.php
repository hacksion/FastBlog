<?php
namespace TM;

class ListController
{
    private $options = [];

    public function __construct($options)
    {
        $this->options = $options;
    }
    public function lists()
    {
        $method = $this->options['method'];
        (new ListView((new ListModel($this->options))->$method()))->lists();
    }

    public function search()
    {
        (new ListView((new ListModel($this->options))->search()))->search();
    }

    public function admin()
    {
        $method = $this->options['category_page'];
        $AdminListModel = new AdminListModel($this->options);
        (new AdminListView($AdminListModel->$method()))->$method();
    }

}
