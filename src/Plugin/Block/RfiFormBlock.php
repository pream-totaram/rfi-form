<?php

namespace Drupal\rfi_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use JetBrains\PhpStorm\ArrayShape;

/**
 * The block that contains the RFI Form
 *
 * @Block (
 *   id = "rfi_form_block",
 *   admin_label = @Translation ("RFI Form Block")
 * )
 *
 */
class RfiFormBlock extends BlockBase {
  const ARRAY_SHAPE = ['#type' => "string", '#markup' => "string"];

  /**
   * {@inheritdoc}
   */
  #[ArrayShape(self::ARRAY_SHAPE)] public function build() : array {
    return \Drupal::formBuilder()->getForm('\Drupal\rfi_form\Form\RFIForm');
  }

}