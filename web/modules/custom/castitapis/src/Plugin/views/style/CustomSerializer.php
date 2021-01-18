<?php

namespace Drupal\castitapis\Plugin\views\style;

use Drupal\Core\Annotation\Translation;
use Drupal\rest\Plugin\views\style\Serializer;
use Drupal\views\Annotation\ViewsStyle;

/**
 * Class CustomSerializer
 * @package Drupal\castitapis\Plugin\views\style
 *
 * @ViewsStyle(
 *   id = "custom_serializer",
 *   title = @Translation("Custom serializer"),
 *   help = @Translation("Serializes views row data and pager using the Serializer component."),
 *   display_types = {"data"}
 * )
 */
class CustomSerializer extends Serializer {

  // protected function defineOptions() {
  //   $options = parent::defineOptions();
  //   $options['path'] = array('default' => 'tardis');
  //   return $options;
  // }

  public function render() {
    $rows = [];
    $count = $this->view->pager->getTotalItems();
    $items_per_page = $this->view->pager->options['items_per_page'];
    $pages = ceil($count / $items_per_page);
    $current_page = $this->view->pager->getCurrentPage();

    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $rows[] = $this->view->rowPlugin->render($row);
    }

    unset($this->view->row_index);

    // Get the content type configured in the display or fallback to the default.

    if ((empty($this->view->live_preview))) {
      // TODO: From drupal-check: Call to an undefined method
      //         Drupal\views\Plugin\views\display\DisplayPluginBase::getContentType()
      $content_type = $this->displayHandler->getContentType();
    } else {
      $content_type = !empty($this->options['formats']) ? reset($this->options['formats']) : 'json';
    }

    return $this->serializer->serialize(
      [
        'results' => $rows,
        'pager' => [
          'count' => $count,
          'pages' => $pages,
          'items_per_page' => $items_per_page,
          'current_page' => $current_page
        ]
      ], $content_type);

  }
}
