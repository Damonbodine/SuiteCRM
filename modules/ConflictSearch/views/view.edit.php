<?php
  if (!defined("sugarEntry") || !sugarEntry) {
      die("Not A Valid Entry Point");
  }
  require_once "include/MVC/View/views/view.edit.php";
  class ConflictSearchViewEdit extends ViewEdit {
      public function display() {
          global $mod_strings;
          if (empty($this->bean->id)) {
              $this->bean->search_type = "comprehensive";
              $this->bean->modules_searched = "Contacts,Accounts,Cases";
              $this->bean->confidence_threshold = "medium";
          }
          parent::display();
      }
  }
