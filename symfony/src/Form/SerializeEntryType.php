<?php

namespace App\Form;

use App\Entity\TextEntry;
use App\Utils\Type\SerializeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This check, I do not agree with this exception.
 *
 * @author rhowe
 *
 * @since 2023-10-03
 *
 * @psalm-suppress MissingTemplateParam
 */
final class SerializeEntryType extends AbstractType {
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('data', TextareaType::class, ['attr' => ['class' => 'form-control font-monospace fs-7', 'rows' => 7, 'spellcheck' => false]])
            ->add('dump_type', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'json_encode()' => SerializeType::TYPE_JSON,
                    'serialize()' => SerializeType::TYPE_SERIALIZE,
                    'YAML' => SerializeType::TYPE_YAML,
                    'Url Encode' => SerializeType::TYPE_URL,
                ],
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => TextEntry::class,
        ]);
    }
}
