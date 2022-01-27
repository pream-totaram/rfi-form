<?php

namespace Drupal\rfi_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rfi_form\Form\RFIForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The block that contains the RFI Form
 *
 * @Block (
 *   id = "rfi_form_block",
 *   admin_label = @Translation ("RFI Form Block")
 * )
 *
 */
class RFIFormBlock extends BlockBase implements  ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\rfi_form\Form\RFIForm
   */
  private RFIForm $form;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RFIForm $form) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->form = $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() : array {
    return \Drupal::formBuilder()->getForm(get_class($this->form));
  }

  public static function create(ContainerInterface $container,
                                array $configuration,
                                $plugin_id,
                                $plugin_definition): RFIFormBlock|static {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get(RFIForm::class));
  }

}