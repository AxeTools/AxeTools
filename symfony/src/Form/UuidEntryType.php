<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This check, I do not agree with this exception.
 *
 * @author rhowe
 *
 * @since 2023-10-03
 *
 * @psalm-suppress MissingTemplateParam
 */
final class UuidEntryType extends AbstractType {
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('uuid', TextType::class, ['attr' => ['class' => 'form-control font-monospace fs-7', 'spellcheck' => false]]);
    }
}
