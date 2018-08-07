<?php

namespace Drupal\swfwmd\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * Views area text handler.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("filter_custom_list")
 */
class FilterCustomList extends AreaPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // Set the default to TRUE so it shows on empty pages by default.
    $options['empty']['default'] = TRUE;
    $options['result_title']['default'] = 'Search Results';
    $options['query_key']['default'] = '';
    $options['counter_singular']['default'] = '1 Item';
    $options['counter_plural']['default'] = '@count Items';
    $options['add_sort']['default'] = 1;
    $options['sort_key']['default'] = '';
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['result_title'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Result Title'),
      '#description' => $this->t('The page title to display in the search area.'),
      '#default_value' => $this->options['result_title'],
    ];

    $form['query_key'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Query key'),
      '#description' => $this->t('The key to look for in the views filter. You may separate keys by a comma (,).'),
      '#default_value' => $this->options['query_key'],
    ];

    $form['counter_singular'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Counter (Singular)'),
      '#description' => $this->t('The text pattern when there is only one (1) result. Use @count for result number token.'),
      '#default_value' => $this->options['counter_singular'],
    ];

    $form['counter_plural'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Counter (Plural)'),
      '#description' => $this->t('The text pattern when there is more than one (1) result. Use @count for result number token.'),
      '#default_value' => $this->options['counter_plural'],
    ];

    $form['add_sort'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add sort feature'),
      '#description' => $this->t('This will add a sort feature based on exposed sorting of the views.'),
      '#default_value' => $this->options['add_sort'],
    ];

    $form['sort_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sorting'),
      '#description' => $this->t('The key and title to look for in the views sort (format is: \'key|Title\').  You may separate keys by a comma (,).'),
      '#default_value' => $this->options['sort_key'],
      '#states' => [
        'visible' => [
          '[name="options[add_sort]"]' => array('checked' => TRUE),
        ],
        'required' => [
          '[name="options[add_sort]"]' => array('checked' => TRUE),
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->view->get_total_rows = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    $content = NULL;
    $clear = FALSE;
    $items = [];

    // Set default value of Area field.
    $keywords = \Drupal::request()->query->get('keywords');

    // Get search results texts and wordings.
    $search_results = trim($this->options['result_title']);
    $counter_singular = trim($this->options['counter_singular']);
    $counter_plural = trim($this->options['counter_plural']);

    if (!empty($keywords)) {
      $clear = TRUE;
      $content .= $this->t('<h2 class="block-title search-filter-keywords">@search_results for "@keywords"</h2>', ['@search_results' => $search_results, '@keywords' => $keywords]);
    }
    else {
      $content .= $this->t('<h2 class="block-title search-filter-keywords">@search_results</h2>', ['@search_results' => $search_results]);
    }

    $total = isset($this->view->total_rows) ? $this->view->total_rows : count($this->view->result);
    $items[0] = [
      'value' => [
        '#markup' => $this->formatPlural((int) $total, $counter_singular, $counter_plural),
      ],
      '#wrapper_attributes' => [
        'class' => ['search-filter-item-count'],
      ],
    ];

    // Set default value of Area field.
    $query = explode(',', trim($this->options['query_key']));

    if (!empty($query)) {
      foreach ($query as $query_text) {
        $queries = \Drupal::request()->query->get($query_text);

        if (!empty($queries)) {
          $clear = TRUE;
          $this->renderLinks($queries, $items, $query_text);
        }
      }
    }

    if ($clear) {
      // Add a column after the item count when there are filters.
      $items[0]['value']['#markup'] .= $this->t(':');

      // Add clear all link if there are filters.
      $items[] = [
        'value' => [
          '#type' => 'link',
          '#title' => $this->t('Clear Filters'),
          '#url' => Url::fromRoute('<current>'),
          '#attributes' => [
            'title' => 'Clear all selected filters',
          ],
        ],
        '#wrapper_attributes' => [
          'class' => ['search-filter-clear-link'],
        ],
      ];
    }

    // Add sort feature.
    $sort = $this->options['add_sort'];
    if ($sort) {
      $this->renderSort($items);
    }

    // Prepare item list render object for output use.
    $item_list = [
      '#theme' => 'item_list',
      '#items' => $items,
      '#attributes' => [
        'class' => ['search-filter'],
      ],
    ];

    // Add the rendered item list to the output's content.
    $content .= drupal_render($item_list);

    if (!empty($content)) {
      return [
        '#markup' => $content,
      ];
    }

    return [];
  }

  /**
   * Convert filters to links.
   *
   * @param array $queries
   *   The list of active query filters.
   * @param array $items
   *   The list of link items already added to the display.
   * @param string $query_text
   *   The query text user as key.
   */
  public function renderLinks(array $queries, array &$items, $query_text) {
    $count = count($queries);
    $position = 1;

    // Iterate through each query and create appropriate link.
    foreach ($queries as $key => $query) {
      // Get all query parameters.
      $query_options = \Drupal::request()->query->all();
      $class = ['search-filter-item-link'];

      // Add first and last class based on item position.
      if ($position == 1) {
        $class[] = 'first';
      }
      if ($position == $count) {
        $class[] = 'last';
      }

      // Remove the current term from the URL option parameter.
      unset($query_options[$query_text][$key]);

      // Add a link item that removes this current term from the search filter.
      $term_name = Term::load($query)->getName();
      $items[] = [
        'value' => [
          '#type' => 'link',
          '#title' => $term_name,
          '#url' => Url::fromRoute('<current>', $query_options),
          '#attributes' => [
            'title' => 'Remove ' . $term_name . ' from the selected filters',
          ],
        ],
        '#wrapper_attributes' => [
          'class' => $class,
        ],
      ];

      $position++;
    }
  }

  /**
   * Add a sort feature to links.
   *
   * @param array $items
   *   The list of link items already added to the display.
   */
  public function renderSort(array &$items) {
    $sort_keys = explode(',', trim($this->options['sort_key']));

    // Add sort links for the dropdown.
    $sort_items = [];
    $position = 1;
    foreach ($sort_keys as $sort_key) {
      $sort_key = explode('|', $sort_key);
      $query_options = \Drupal::request()->query->all();

      // Add some class list link element.
      $sort_items_class = [];
      if ((isset($query_options['sort_by']) && $sort_key[0] == $query_options['sort_by']) ||
        ((!isset($query_options['sort_by']) || empty($query_options['sort_by'])) && $position == 1)) {
        $sort_items_class[] = 'active';
      }

      $query_options['sort_by'] = $sort_key[0];
      $query_options['sort_order'] = (!isset($query_options['sort_order']) || empty($query_options['sort_order']) || $query_options['sort_order'] == 'DESC' ? 'ASC' : 'DESC');

      $sort_items[] = [
        'value' => [
          '#type' => 'link',
          '#title' => (isset($sort_key[1]) ? $sort_key[1] : $sort_key[0]),
          '#url' => Url::fromRoute('<current>', $query_options),
          '#attributes' => [
            'class' => $sort_items_class,
            'title' => 'Sort by ' . (isset($sort_key[1]) ? $sort_key[1] : $sort_key[0]),
          ],
        ],
      ];

      $position++;
    }

    if (!empty($sort_items)) {
      // Prepare item list render object for output use.
      $sort_items_list = [
        '#theme' => 'item_list',
        '#items' => $sort_items,
        '#attributes' => [
          'class' => ['dropdown-menu'],
        ],
      ];

      // Add a link item that removes this current term from the search filter.
      $items[] = [
        '#markup' => '<div class="dropdown">
          <a id="sort-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" title="Sort your search results - click here to see your sorting options">Sort</a>
          ' . drupal_render($sort_items_list) . '
        </div>',
        '#wrapper_attributes' => [
          'class' => ['search-filter-sort-option'],
        ],
      ];
    }
  }

}
